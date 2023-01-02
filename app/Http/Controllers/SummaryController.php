<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Alaouy\Youtube\Facades\Youtube;

class SummaryController extends Controller
{
	public function Process(Request $request)
	{
		$video = Youtube::getVideoInfo($request->videoId);
		$return = array('errors' => array(), 'data' => array());


		if ($video->snippet->channelId != 'UC2D2CMWXMOVWx7giW1n3LIg') {
			$return['errors'][0] = "This videos is not from Huberman Lab's channel 🙃";
			return $return;
		}

		$return['data']['title'] = $video->snippet->title;

		$process = new Process(['python', '../resources/external_scripts/transcription.py', $request->videoId]);
		$process->run();

		if (!$process->isSuccessful()) {
			return 'Error processing the requested video 🫠';
		}

		// If $process->getOutput() is not a valid JSON string, or starts with 'Error:', then return the error message
		if (substr($process->getOutput(), 0, 6) === 'Error:') {
			return "No transcript for the requested video was found 😵";
		}

		$json = json_decode($process->getOutput());

		// dd($json);
		return $return;


		// $description = $video->snippet->description;
		// preg_match_all('/(?:\d\d?)?:?(?:\d\d?):(?:\d\d?)\s.+?\n/', $description, $description_timestamp_matches);

		// $timestamps = array();
		// foreach ($description_timestamp_matches[0] as $timestamp) {
		// 	preg_match('/^((?:\d\d?)?:?(?:\d\d?):(?:\d\d?))\s(.+?)$/', $timestamp, $timestamp_matches);
		// 	$timestamps[] = array(
		// 		'time' => $timestamp_matches[1],
		// 		'title' => $timestamp_matches[2]
		// 	);
		// }
	}

	private function summarize($json){
		// Extract the text key from each of the JSON objects and append them together with a space in between
		$text = implode(' ', array_map(function ($obj) {
			return $obj->text;
		}, $json));


		$text = substr($text, 0, 5000);

		$api_key = "";
		$prompt = "Summarize the following text in actionable steps: " . $text;
		$model = "davinci";
		$maxChars = 1000;
		$endpoint = "https://api.openai.com/v1/completions";

		// Set the headers for the API call
		$headers = array(
			"Content-Type: application/json",
			"Authorization: Bearer " . $api_key
		);

		// Set the data for the API call
		$data = array(
			"model" => $model,
			"prompt" => $prompt,
			"max_tokens" => $maxChars
		);

		// Encode the data as JSON
		$data = json_encode($data);

		// Set up the cURL request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		// Execute the cURL request and get the response
		$response = curl_exec($ch);

		// Decode the response as JSON
		$response = json_decode($response, true);
		dd($response);

		// Check if the API call was successful
		// if ($response["error"]) {
		// There was an error, handle it here
		// echo "Error: " . $response["error"];
		// } else {
		// The API call was successful, output the generated text
		// echo $response["data"]["text"];
		// }
	}
}

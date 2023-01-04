<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Alaouy\Youtube\Facades\Youtube;

use App\Models\YoutubeVideo;
use App\Models\Section;

class SummaryController extends Controller
{
	public function Process(Request $request)
	{
		$return = array('errors' => array(), 'data' => array());

		$youtube_video = YoutubeVideo::where('video_id', '=', $request->videoId);
		if($youtube_video->exists()){
			$youtube_video = $youtube_video->first();
			$sections = $youtube_video->sections();
			if(count($sections->get()) > 0){
				$return['data']['title'] = $youtube_video->title;
				$return['data']['cached'] = true;
				$return['data']['sections'] = array();
				foreach($sections->get() as $section){
					$return['data']['sections'][$section->section_number] = array(
						'title' => $section->title,
						'section_number' => $section->section_number,
						'text' => $section->text,
					);
				}
				return $return;
			}
		}

		$video_fetch = Youtube::getVideoInfo($request->videoId);

		// Check if the video is a valid YouTube video
		if (!$video_fetch) {
			$return['errors'][0] = "Can't find the requested video 🔭";
			return $return;
		}

		if ($video_fetch->snippet->channelId != 'UC2D2CMWXMOVWx7giW1n3LIg') {
			$return['errors'][0] = "This video is not from Huberman Lab's channel 🙃";
			return $return;
		}
		$youtube_video = YoutubeVideo::firstOrCreate(
			['video_id' => $video_fetch->id],
		);

		$youtube_video->title = $video_fetch->snippet->title;
		$youtube_video->save();

		$return['data']['title'] = $video_fetch->snippet->title;

		$process = new Process([(app()->environment('local')) ? 'python' : 'python3', '../resources/external_scripts/transcription.py', $request->videoId]);
		$process->run();


		if (!$process->isSuccessful()) {
			$return['errors'][0] = "Error retrieving the transcript from YouTube 🫠";
			return $return;
		}

		// If $process->getOutput() is not a valid JSON string, or starts with 'Error:', then return the error message
		if (substr($process->getOutput(), 0, 6) === 'Error:') {
			$return['errors'][0] = "No transcript for the requested video was found 😵";
			return $return;
		}

		$transcript_json = json_decode($process->getOutput());
		$timestamps = $this->get_timestamps_from_description($video_fetch->snippet->description);
		$sections = $this->generate_sections($transcript_json, $timestamps);

		foreach ($sections as $key => $section) {
			$section = Section::firstOrNew([
				'section_number' => $key,
				'youtube_video_id' => $youtube_video->id,
				'title' => $section['title'],
				'text' => $section['text'],
			]);

			$section->save();
		}

		$return['data']['sections'] = $sections;
		return $return;
	}


	private function generate_sections($transcript_json, $timestamps)
	{
		// For each $timestamp, find the first $transcript_json object whose start time is closest to the $timestamp's time
		$closest = array_map(function ($timestamp) use ($transcript_json) {
			$closest = array_reduce($transcript_json, function ($carry, $item) use ($timestamp) {
				if ($carry === null) {
					return $item;
				}
				return abs($item->start - $timestamp['time']) < abs($carry->start - $timestamp['time']) ? $item : $carry;
			});

			return array(
				'title' => $timestamp['title'],
				'start' => $closest->start,
				'text' => $closest->text
			);
		}, $timestamps);

		// Make a new array where each element is the combination of all text keys from every $transcript_json objects between each of $closest's start times
		$sections = array_map(function ($item, $index) use ($closest, $transcript_json) {
			$end = $closest[$index + 1]['start'] ?? null;
			$section = array_reduce($transcript_json, function ($carry, $obj) use ($item, $end) {
				if ($obj->start >= $item['start'] && ($end === null || $obj->start < $end)) {
					$carry .= $obj->text . ' ';
				}
				return $carry;
			}, '');

			return array(
				'title' => $item['title'],
				'section_number' => $index,
				'text' => $section
			);
		}, $closest, array_keys($closest));

		return $sections;
	}

	private function get_timestamps_from_description($description)
	{
		preg_match_all('/(?:\d\d?)?:?(?:\d\d?):(?:\d\d?)\s.+?\n/', $description, $description_timestamp_matches);

		$timestamps = array();
		foreach ($description_timestamp_matches[0] as $timestamp) {
			preg_match('/^((?:\d\d?)?:?(?:\d\d?):(?:\d\d?))\s(.+?)$/', $timestamp, $timestamp_matches);
			$timestamps[] = array(
				// the time key is of the format hh:mm:ss, convert it to seconds and add it to the timestamps array
				'time' => array_reduce(explode(':', $timestamp_matches[1]), function ($carry, $item) {
					return $carry * 60 + $item;
				}),
				'title' => $timestamp_matches[2]
			);
		}

		return $timestamps;
	}

	private function summarize($json)
	{
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

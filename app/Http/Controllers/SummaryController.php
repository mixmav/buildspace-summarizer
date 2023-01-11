<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Alaouy\Youtube\Facades\Youtube;

use App\Models\YoutubeVideo;
use App\Models\Section;
use App\Models\Summary;

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
						'section_id' => $section->id,
						'section_number' => $section->section_number,
						// 'has_summary' => (count($section->summaries()->get()) > 0),
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
			$section = Section::firstOrCreate([
				'section_number' => $key,
				'youtube_video_id' => $youtube_video->id,
				'title' => $section['title'],
				'text' => $section['text'],
			]);

			$sections[$key]['section_id'] = $section->id;
		}

		$return['data']['sections'] = $sections;
		return $return;
	}


	public function SummarizeSection(Request $request){
		$section = Section::findOrFail($request->section_id);

		// $youtube_video = YoutubeVideo::where('video_id', '=', $request->videoId);
		// 	if($youtube_video->exists()){
		$summaries = $section->summaries()->get();
		if(count($summaries) > 0){
			// Summary already in database, return it.
			return $summaries[0]->summary;
		}

		$generated_summary = $this->Summarize($section->title, $section->text);
		$summary = new Summary;
		$summary->section_id = $request->section_id;
		$summary->summary = $generated_summary['summary'];
		$summary->completion_id = $generated_summary['completion_id'];
		$summary->save();

		return $summary->summary;
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

	private function Summarize($title, $text)
	{
		$api_key = env('OPENAI_API_KEY', 'ABC123');
		$prompt = <<<EOD
Summarize the the most important information from the following transcript in dotpoints.
Title: $title
Text: $text.
Summary:
EOD;

		$headers = array(
			"Content-Type: application/json",
			"Authorization: Bearer " . $api_key
		);

		$data = array(
			"model" => 'text-davinci-003',
			"prompt" => $prompt,
			"max_tokens" => 500
		);

		$data = json_encode($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/completions');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch);

		$response = json_decode($response, true);

		return array(
			'summary' => trim($response['choices'][0]['text']),
			'completion_id' => $response['id']
		);
	}
}

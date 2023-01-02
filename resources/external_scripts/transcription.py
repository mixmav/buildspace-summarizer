from youtube_transcript_api import YouTubeTranscriptApi
import json
import sys


transcript_list = YouTubeTranscriptApi.list_transcripts(sys.argv[1]);

try:
	# Iterate over transcript_list and extract all values that begin with 'en'
	transcript = [transcript for transcript in transcript_list if transcript.language_code.startswith('en')][0]
	print(json.dumps(transcript.fetch()))
except:
	print("Error: can't fetch transcript")

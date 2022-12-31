from youtube_transcript_api import YouTubeTranscriptApi
import json
import sys


transcription = YouTubeTranscriptApi.get_transcript(sys.argv[1]);

# transcriptionDict = { 'transcript': transcription }
print(json.dumps(transcription));

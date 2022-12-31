<template>
	<div class="prose daisy-prose mx-auto">
		<h2>A podcast summarizer powered by AI ⚡</h2>
		<p>As a proof of concept, it works on <a class="daisy-link daisy-link-primary" href="https://hubermanlab.com/"
				target="_blank">Huberman Lab</a> episodes via a YouTube link.</p>

		<input @keyup.enter="processUrl" v-model="url" type="text" placeholder="Enter a YouTube URL"
			class="w-full daisy-input daisy-input-bordered" autofocus />
		<button class="daisy-btn daisy-btn-primary mt-6 w-full" :class="{ 'daisy-loading': loading }" :disabled="loading"
			@click="processUrl">TL;dr</button>

		<div v-show="transcription.length > 0" class="w-full mb-8x">
			<h2 clas="mt-4">Transcription</h2>
			<div class="daisy-mockup-window border bg-base-300 w-full p-4">
				<!-- <div class=""> -->
					<p v-for="(transcript, key) in transcription" :key="key">{{ transcript.text }}</p>
				<!-- </div> -->
			</div>
		</div>
	</div>
</template>

<script setup>

import { ref } from 'vue';
import $ from 'jquery';

let url = ref('');
let videoId = ref('');
let loading = ref(false);
let transcription = ref([]);


let processUrl = () => {
	if (loading.value) {
		return;
	}
	let match = url.value.match(/\?v\=([a-zA-Z0-9]+?)[^a-zA-Z0-9]?$/);
	if (match != null) {
		videoId.value = match[1];
	} else {
		alert('Invalid URL');
		return;
	}

	$.ajax({
		url: '/api/summarize',
		method: 'POST',
		data: {
			videoId: videoId.value
		},

		beforeSend: () => {
			loading.value = true;
		},

		success: (response) => {
			transcription.value = response;
		},

		complete: () => {
			loading.value = false;
		},
	})
}

</script>
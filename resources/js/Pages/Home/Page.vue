<template>
	<div class="prose daisy-prose mx-auto flex flex-col max-w-full">
		<div class="flex justify-between items-end">
			<div>
				<h2>A podcast summarizer powered by AI ⚡</h2>
				<p>As a proof of concept, the app will <i>only</i> process <a class="daisy-link daisy-link-primary" href="https://hubermanlab.com/"
						target="_blank">Huberman Lab</a> episodes.</p>
			</div>
			<img :class="{'animate-spin': loading}" class="w-[70px] h-[70px] daisy-mask daisy-mask-hexagon mr-8" :src="hubermanJpg" alt="Face shot of Dr. Andrew Huberman">
		</div>

		<input @keyup.enter="processUrl" v-model="url" type="text" placeholder="Enter a YouTube URL"
			class="w-full daisy-input daisy-input-bordered" autofocus />

		<button @click="useRandomLink" class="daisy-btn daisy-btn-ghost mt-4 daisy-btn-xs gap-2 self-end"><i class="fa fa-shuffle"></i>Use a random link</button>
		<button class="daisy-btn daisy-btn-primary mt-6 w-full" :class="{ 'daisy-loading': loading }" :disabled="loading"
			@click="processUrl">Summarize</button>

		<div v-show="!$.isEmptyObject(summaryResponse)" class="w-full mb-8x">
			<h2 class="mt-6">{{ summaryResponse.title }}</h2>
			<h3 clas="mt-4"><span class="text-primary">#</span> Summary</h3>
			<div class="daisy-mockup-window border bg-base-300 w-full p-4 mt-4">
				<!-- <div class=""> -->
					<!-- <p v-for="(transcript, key) in summaryResponse" :key="key">{{ transcript.text }}</p> -->
				<!-- </div> -->
			</div>
		</div>
	</div>
</template>

<script setup>

import { ref } from 'vue';
import $ from 'jquery';
import { useToast } from "vue-toastification";
import hubermanJpg from '@/../img/huberman.jpg';

const toast = useToast();

let url = ref('');
let videoId = ref('');
let loading = ref(false);
let summaryResponse = ref({});


let processUrl = () => {
	if (loading.value) {
		return;
	}
	let match = url.value.match(/.*?\?v\=([a-zA-Z0-9_-]+?)[^a-zA-Z0-9_-]?$/);
	if (match != null) {
		videoId.value = match[1];
	} else {
		toast.error('👉 Not a valid URL');
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
			if(response.errors.length > 0){
				response.errors.forEach((error) => {
					toast.error(error);
				});
			} else {
				summaryResponse.value = response.data;
			}

		},

		complete: () => {
			setTimeout(() => {
				loading.value = false;
			}, 800)
		},
	})
}

let useRandomLink = () => {
	let vidoes = [
		'https://www.youtube.com/watch?v=LG53Vxum0as',
		'https://www.youtube.com/watch?v=iw97uvIge7c',
		'https://www.youtube.com/watch?v=gXvuJu1kt48',
		'https://www.youtube.com/watch?v=DkS1pkKpILY',
		'https://www.youtube.com/watch?v=ntfcfJ28eiU',
	]

	url.value = vidoes[Math.floor(Math.random() * vidoes.length)];
}

</script>
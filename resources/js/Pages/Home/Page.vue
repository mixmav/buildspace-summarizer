<template>
	<div class="prose daisy-prose mx-auto flex flex-col max-w-full">
		<div class="flex justify-between items-end">
			<div>
				<h1>A podcast summarizer powered by AI ⚡</h1>
				<p>As a proof of concept, the app will <i>only</i> process <a class="daisy-link daisy-link-primary"
						href="https://hubermanlab.com/" target="_blank">Huberman Lab</a> episodes.</p>
			</div>

			<img :class="{ 'animate-bounce': loading }"
				class="-translate-y-1/4 relative top-4 w-[70px] h-[70px] daisy-mask daisy-mask-hexagon mr-8"
				:src="hubermanJpg" alt="Face shot of Dr. Andrew Huberman">
		</div>

		<input @keyup.enter="processUrl" v-model="url" type="text" placeholder="Enter a YouTube URL"
			class="w-full daisy-input daisy-input-bordered" autofocus />

		<button @click="useRandomLink" class="daisy-btn daisy-btn-ghost mt-4 daisy-btn-xs gap-2 self-end"><i
				class="fa fa-shuffle"></i>Use a random link</button>
		<button class="daisy-btn daisy-btn-primary mt-6 w-full" :class="{ 'daisy-loading': loading }"
			:disabled="loading" @click="processUrl">Summarize</button>


		<sections-view></sections-view>
	</div>
</template>

<script setup>

import { ref } from 'vue';
import $ from 'jquery';
import { useToast } from "vue-toastification";
import hubermanJpg from '@/../img/huberman.jpg';
import sectionsView from './SectionsView.vue';

import { useApiResponse } from '@/stores/useApiResponse';

const apiResponse = useApiResponse();

const toast = useToast();

let url = ref('');
let videoId = ref('');
let loading = ref(false);

let processUrl = () => {
	if (loading.value) {
		return;
	}
	let match = url.value.match(/.*?\?v\=([a-zA-Z0-9_-]+?)[^a-zA-Z0-9_-]?$/);
	if (match != null) {
		videoId.value = match[1];
	} else {
		toast.info('Invalid URL 🥥');
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
			if (response.errors?.length > 0) {
				response.errors.forEach((error) => {
					toast.info(error);
				});
			} else {
				apiResponse.title = response.data.title;
				apiResponse.sections = response.data.sections;
				setTimeout(() => {
					$('html, body').animate({
						scrollTop: $("#video-title-heading").offset().top - 100
					}, 500);
				}, 1000)

			}
		},

		error: () => {
			toast.error('Something went wrong 🥥');
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
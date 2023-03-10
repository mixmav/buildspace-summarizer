<template>
	<div class="prose daisy-prose mx-auto flex flex-col max-w-full">
		<div class="flex flex-col md:flex-row justify-between items-center my-4">
			<div class="self-stretch flex flex-col items-start justify-between">
				<h2 class="my-0 text-xl md:text-2xl">A podcast summarizer powered by AI 🔭</h2>
				<p class="mt-4 md:my-0">As a proof of concept, the app <i>only</i> processes <a
						class="daisy-link daisy-link-primary" href="https://hubermanlab.com/" target="_blank">Huberman
						Lab</a> episodes.</p>
			</div>

			<div class="flex flex-col items-center justify-center w-full md:w-auto">
				<img :class="{ 'animate-bounce': app.loading }"
					class="-translate-y-1/4 md:block hidden relative my-0 w-[70px] h-[70px] daisy-mask daisy-mask-hexagon"
					:src="hubermanJpg" alt="Face shot of Dr. Andrew Huberman">
				<button @click="useRandomLink"
					class="daisy-btn daisy-btn-ghost text-primary daisy-btn-xs gap-2 self-end"><i
						class="fa fa-shuffle"></i>Use a random link</button>
			</div>
		</div>

		<input ref="urlInput" @keyup.enter="processUrl" v-model="url" type="text" placeholder="Enter a YouTube URL"
			class="w-full daisy-input daisy-input-bordered" autofocus />

		<button class="daisy-btn daisy-btn-primary mt-6 self-center w-full" :class="{ 'daisy-loading': app.loading }"
			:disabled="app.loading" @click="processUrl">Summarize</button>

		<Transition name="opacity" mode="out-in">
			<div v-if="$.isEmptyObject(apiResponse.sections)" class="mt-2">
				<h2 class="my-0 mt-6">🤖 Or choose one from this list</h2>
				<div class="flex gap-8 overflow-x-auto p-4">

					<img @click="useSpecificLink(event, pod.url)" v-for="(pod, key) in pods" :key="key" :src="pod.src"
						class="w-[250px] aspect-video rounded-xl m-0 hover:scale-110 transition-transform cursor-pointer active:scale-100"
						alt="Video thumbnail">

				</div>

				<h3 class="mt-16">🧪 Feature updates</h3>
				<p class="my-0 mb-4">Sign up if you'd like feature updates via emails. No spam 🫡</p>
				<subscriber-sign-up></subscriber-sign-up>
			</div>
			<sections-view ref="sectionsVue" v-else @startOver="clearUrlInput"></sections-view>
		</Transition>
	</div>
</template>

<script setup>

import { ref } from 'vue';
import $ from 'jquery';
import { useToast } from "vue-toastification";
import hubermanJpg from '@/../img/huberman.jpg';

import SubscriberSignUp from '@/Pages/layout/SubscriberSignUp.vue';
import sectionsView from './SectionsView.vue';

import { useApiResponse } from '@/stores/useApiResponse';
import { useApp } from '@/stores/useApp';

import Pod1 from '@/../img/podcast-thumbnails/6-focus.webp';
import Pod2 from '@/../img/podcast-thumbnails/101-caffeine.webp';
import Pod3 from '@/../img/podcast-thumbnails/86-alcohol.webp';
import Pod4 from '@/../img/podcast-thumbnails/92-cannabis.webp';
import Pod5 from '@/../img/podcast-thumbnails/10-stress.webp';


const apiResponse = useApiResponse();
const app = useApp();
const toast = useToast();

let urlInput = ref(null);
let url = ref('');
let videoId = ref('');
let sectionsVue = ref(null);

let processUrl = () => {
	if (app.loading) {
		return;
	}
	let match = url.value.match(/(?:.*?\?v\=(?<desk>[a-zA-Z0-9_-]+))|(?:youtu.be\/(?<mob>[a-zA-Z0-9_-]+))/);
	if (match != null) {
		if (typeof match.groups.mob != 'undefined') {
			videoId.value = match.groups.mob;
		} else {
			videoId.value = match.groups.desk;
		}
	} else {
		toast.info('Invalid URL 🫠');
		return;
	}

	$.ajax({
		url: '/api/process',
		method: 'POST',
		data: {
			videoId: videoId.value
		},

		beforeSend: () => {
			apiResponse.title = '';
			apiResponse.sections = [];
			apiResponse.summaries = [];

			app.loading = true;
			if (sectionsVue.value) {
				sectionsVue.value.activeSection = 0;
				sectionsVue.value.activeTab = 1;
			}
		},

		success: (response) => {
			if (response.errors?.length > 0) {
				response.errors.forEach((error) => {
					toast.error(error);
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
				app.loading = false;
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
	urlInput.value.focus();
}

let clearUrlInput = () => {
	url.value = '';
	urlInput.value.focus();
}

let pods = [
	{
		src: Pod1,
		url: 'https://www.youtube.com/watch?v=LG53Vxum0as'
	},
	{
		src: Pod2,
		url: 'https://www.youtube.com/watch?v=iw97uvIge7c'
	},
	{
		src: Pod3,
		url: 'https://www.youtube.com/watch?v=DkS1pkKpILY'
	},
	{
		src: Pod4,
		url: 'https://www.youtube.com/watch?v=gXvuJu1kt48'
	},
	{
		src: Pod5,
		url: 'https://www.youtube.com/watch?v=ntfcfJ28eiU'
	},
]

let useSpecificLink = (event, link) => {
	url.value = link;
	urlInput.value.focus();
	processUrl();
}
</script>
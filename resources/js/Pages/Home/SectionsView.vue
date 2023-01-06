<template>
	<div class="w-full mb-[80px]">
		<h2 class="mt-12" id="video-title-heading">{{ apiResponse.title }}</h2>
		<h3 clas="mt-4"><span class="text-primary">#</span>&nbsp;This podcast contains <span class="text-primary">
				{{ apiResponse.sections.length }} sections</span>.</h3>
		<button class="daisy-btn daisy-btn-sm my-4 gap-2" @click="startOver"><i class="fa fa-sync"></i>Start over</button>
		<div class="daisy-tabs">
			<span class="daisy-tab daisy-tab-bordered daisy-tab-lg" :class="{ 'daisy-tab-active': activeTab == 0 }"
				@click="activeTab = 0">Overall summary</span>
			<span class="daisy-tab daisy-tab-bordered daisy-tab-lg" :class="{ 'daisy-tab-active': activeTab == 1 }"
				@click="activeTab = 1">Summary by section</span>
		</div>
		<div v-show="activeTab == 0" class="mt-6">
			<div class="w-full mt-8">
				<h3 class="mt-0">I'm still working on this <i class="fa fa-yin-yang animate-rotate fa-spin"></i></h3>
				<p class="my-0 mb-4">Sign up if you'd like feature updates via emails. No spam 🫡</p>
				<subscriber-sign-up></subscriber-sign-up>
			</div>
		</div>
		<div v-show="activeTab == 1">
			<div class="daisy-tabs-boxed mt-6">
				<a class="daisy-tab daisy-tab-lg lifted transition-[background-color,color,border-radius] ease-linear"
					:class="{ 'daisy-tab-active': (key == activeSection) }"
					v-for="(section, key) in apiResponse.sections" :key="key"
					@click="activeSection = (app.loading) ? activeSection : key">{{ section.section_number + 1 }}</a>
			</div>
			<div class="p-2">
				<div class="mt-4 flex justify-between items-center py-2">
					<h2 class="my-0">{{ activeSectionData?.title }}</h2>
					<button class="daisy-btn daisy-btn-primary daisy-btn-sm gap-2"
						:class="{ 'daisy-loading': app.loading }"
						:disabled="(app.loading || activeSectionSummaryIndex > -1)" @click="summarizeSection"><i
							class="fa fa-layer-group"></i>Summarize this section</button>
				</div>

				<div class="daisy-badge daisy-badge-sm" v-show="activeSectionData?.section_number == 0">Often a general
					introduction</div>
				<div v-if="activeSectionSummaryIndex > -1">
					<h3>Summary 🤖<br><a class="daisy-link daisy-link-primary text-xs" @click="removeSummary">View original</a></h3>
					<p class="my-0" v-html="apiResponse.summaries[activeSectionSummaryIndex].summary"></p>
				</div>
				<p v-else>{{ activeSectionData?.text }}</p>
			</div>
		</div>
	</div>
</template>

<script setup>

import { ref, computed } from 'vue';
import $ from 'jquery';
import { useApiResponse } from '@/stores/useApiResponse';
import { useApp } from '@/stores/useApp';
import { useToast } from "vue-toastification";
import SubscriberSignUp from '@/Pages/layout/SubscriberSignUp.vue';


const emit = defineEmits(['startOver'])
const apiResponse = useApiResponse();
const app = useApp();
const toast = useToast();


let activeSection = ref(0);
let activeTab = ref(1);

defineExpose({
	activeSection,
	activeTab
});

let activeSectionData = computed(() => {
	return apiResponse.sections[activeSection.value];
});

let activeSectionSummaryIndex = computed(() => {
	return apiResponse.summaries.findIndex((summary) => summary.section_id == activeSectionData.value.section_id);
});

let summarizeSection = () => {
	$.ajax({
		url: '/api/summarize/section',
		method: 'POST',
		data: {
			section_id: activeSectionData.value.section_id
		},
		beforeSend: () => {
			app.loading = true;
		},

		success: (response) => {
			if (response.errors?.length > 0) {
				response.errors.forEach((error) => {
					toast.error(error);
				});
			} else {
				// If the apiResponse.summaries array has an object with the same section_id as activeSectionData.value.section_id, replace it, otherwise create a new element
				if (activeSectionSummaryIndex.value > -1) {
					apiResponse[activeSectionSummaryIndex.value] = {
						section_id: activeSectionData.value.section_id,
						text: response
					}
				} else {
					apiResponse.summaries.push({
						section_id: activeSectionData.value.section_id,
						summary: (response + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + '<br />' + '$2')
					});
				}
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

let startOver = () => {
	apiResponse.title = '';
	apiResponse.sections = [];
	apiResponse.summaries = [];
	emit('startOver');
}

let removeSummary = () => {
	// Remove the summary from the apiResponse.summaries array
	apiResponse.summaries.splice(activeSectionSummaryIndex.value, 1);
}
</script>
<template>
	<div class="w-full mb-[80px]">
		<h2 class="mt-12" id="video-title-heading">{{ apiResponse.title }}</h2>
		<h3 clas="mt-4"><span class="text-primary">#</span>&nbsp;This podcast has <span class="text-primary">
				{{ apiResponse.sections.length }} sections</span>.</h3>
		<button class="daisy-btn daisy-btn-sm my-4 gap-2" @click="startOver"><i class="fa fa-sync"></i>Start
			over</button>

		<div class="daisy-tabs">
			<span class="daisy-tab daisy-tab-bordered daisy-tab-md md:daisy-tab-lg"
				:class="{ 'daisy-tab-active': activeTab == 0 }" @click="activeTab = 0">Overall summary</span>
			<span class="daisy-tab daisy-tab-bordered daisy-tab-md md:daisy-tab-lg"
				:class="{ 'daisy-tab-active': activeTab == 1 }" @click="activeTab = 1">Summary by section</span>
		</div>
		<div v-show="activeTab == 0" class="mt-6">
			<div class="w-full mt-8">
				<h3 class="mt-0">Still working on this <i class="fa fa-yin-yang animate-rotate fa-spin"></i></h3>
				<p class="my-0 mb-4">Sign up if you'd like feature updates via emails. No spam 🫡</p>
				<subscriber-sign-up></subscriber-sign-up>
			</div>
		</div>
		<div v-show="activeTab == 1">
			<div class="daisy-collapse relative">
				<input type="checkbox" class="peer" v-model="listViewOpen"/>
				<i class="fa fa-caret-down absolute z-20 right-10 top-10 peer-checked:-rotate-180 transition-transform duration-300"></i>
				<div class="daisy-collapse-title bg-base-300 rounded-xl mt-4">
					<h4 class="my-0"><i class="fa fa-clipboard-list mr-4"></i>List view</h4>
				</div>

				<div class="daisy-collapse-content peer-checked:p-4">
					<div @click="activeSection = (app.loading) ? activeSection : key" :class="{'!daisy-badge-accent animate-bounce cursor-default': (key == activeSection)}" class="daisy-badge daisy-badge-primary odd:daisy-badge-secondary ml-2 cursor-pointer" v-for="(section, key) in apiResponse.sections" :key="key">{{ (section.title.length > 30) ? section.title.slice(0, 50) + "...":section.title }}</div>
				</div>
			</div>

			<div class="daisy-tabs-boxed mt-6 overflow-x-auto" id="sections-tabs">
				<a class="daisy-tab inline daisy-tab-lg lifted transition-[background-color,color,border-radius] ease-linear"
					:class="{ 'daisy-tab-active': (key == activeSection) }"
					v-for="(section, key) in apiResponse.sections" :key="key"
					@click="activeSection = (app.loading) ? activeSection : key">{{ section.section_number + 1 }}</a>
			</div>

			<div class="p-2">
				<div class="mt-4 flex flex-col md:flex-row justify-between items-start md:items-center py-2">
					<h2 class="my-0">{{ activeSectionData?.title }}</h2>
					<button class="daisy-btn daisy-btn-primary daisy-btn-sm gap-2 mt-4 md:mt-0"
						:class="{ 'daisy-loading': app.loading }"
						:disabled="(app.loading || activeSectionSummaryIndex > -1)" @click="summarizeSection"><i
							class="fa fa-layer-group"></i>Summarize section</button>
				</div>

				<div class="daisy-badge hidden md:block daisy-badge-sm" v-show="activeSectionData?.section_number == 0">
					Often a general
					introduction</div>
				<div v-if="activeSectionSummaryIndex > -1">
					<h3 class="mb-0">Summary 🥥</h3>
					<a class="daisy-link daisy-link-primary text-xs" @click="removeSummary">View original</a>

					<p class="my-0 mt-4" v-html="apiResponse.summaries[activeSectionSummaryIndex].summary"></p>
				</div>
				<p v-else>{{ activeSectionData?.text }}</p>
			</div>
		</div>
	</div>
</template>

<script setup>

import { ref, computed, watch } from 'vue';
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
let listViewOpen = ref(true);

defineExpose({
	activeSection,
	activeTab,
});

// Watch the activeTab, and whenever it changes, scroll the .daisy-tab-active element into view
watch(activeSection, () => {
	setTimeout(() => {
		document.querySelector('#sections-tabs .daisy-tab-active').scrollIntoView({
			behavior: 'smooth',
			block: 'center',
		});
	}, 300)
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
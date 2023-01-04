<template>
	<div v-show="!$.isEmptyObject(apiResponse.sections)" class="w-full mb-[80px]">
		<h2 class="mt-12" id="video-title-heading">{{ apiResponse.title }}</h2>
		<h3 clas="mt-4"><span class="text-primary">#</span>&nbsp;This podcast contains <span class="text-primary">{{ apiResponse.sections.length }} sections</span>.</h3>

		<div class="daisy-tabs-boxed mt-6">
			<a class="daisy-tab daisy-tab-lg lifted transition-[background-color,color,border-radius] ease-linear" :class="{'daisy-tab-active': (key == activeTab)}" v-for="(section, key) in apiResponse.sections" :key="key" @click="activeTab = key">{{  section.section_number + 1 }}</a>
		</div>

		<div class="p-2">
			<h3>{{ activeSection?.title }}<div class="daisy-badge daisy-badge-primary ml-2" v-show="activeSection?.section_number == 0">Often a general introduction</div></h3>
			<p>{{ activeSection?.text }}</p>
		</div>
	</div>
</template>

<script setup>

import { ref, computed } from 'vue';
import $ from 'jquery';
import { useApiResponse } from '@/stores/useApiResponse';
const apiResponse = useApiResponse();


let activeTab = ref(0);

let activeSection = computed(() => {
	return apiResponse.sections[activeTab.value];
});

</script>
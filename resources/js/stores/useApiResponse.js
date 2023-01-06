import { defineStore } from "pinia";

export const useApiResponse = defineStore("apiResponse", {
	state: () => ({
		title: '',
		sections: [],
		summaries: []
	})
});
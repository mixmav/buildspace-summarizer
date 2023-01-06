<template>
	<form @submit.prevent="handleSubmit">
		<input name="email" v-model="email" type="email" class="daisy-input daisy-input-bordered w-full" placeholder="Your email"
			required>
		<button :disabled="app.loading" :class="{'daisy-loading': app.loading}" type="submit" class="daisy-btn mt-4 w-full">Subscribe</button>
	</form>
</template>


<script setup>
import { ref } from 'vue';
import $ from 'jquery';

import { useApp	} from '@/stores/useApp';
import { useToast } from "vue-toastification";

const toast = useToast();
const app = useApp();

let email = ref('');

let handleSubmit = () => {
	$.ajax({
		url: '/api/subscriber/subscribe',
		method: 'POST',
		data: {
			email: email.value
		},
		beforeSend: () => {
			app.loading = true;
		},
		success: (response) => {
			if (response.errors?.length > 0) {
				toast.error(response.errors[0]);
				return;
			}

			toast.success('Subscribed 🫡');
			email.value = '';
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

</script>
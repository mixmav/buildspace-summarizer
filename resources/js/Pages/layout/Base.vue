<template>

	<Head>
		<title>TL;dr</title>
	</Head>

	<div class="daisy-navbar bg-base-100 sticky top-0 border-b-2 border-b-base-200 z-50 p-2">
		<div class="daisy-navbar-start">
			<div class="daisy-dropdown">
				<label tabindex="0" class="daisy-btn daisy-btn-ghost daisy-btn-circle">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
						stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M4 6h16M4 12h16M4 18h7" />
					</svg>
				</label>
				<ul tabindex="0"
					class="daisy-menu daisy-menu-compact daisy-dropdown-content mt-3 p-2 shadow bg-base-100 rounded-box w-52">
					<li>
						<Link href="#">About</Link>
					</li>
				</ul>
			</div>
		</div>
		<div class="daisy-navbar-center">
			<Link href="/" class="daisy-btn daisy-btn-ghost normal-case text-xl">TL;dr</Link>
		</div>
		<div class="daisy-navbar-end">
		</div>
	</div>
	<div class="p-6">
		<slot />
	</div>
</template>

<script setup>
import { Head } from '@inertiajs/inertia-vue3'
import "@/../css/tailwind.css";
</script>
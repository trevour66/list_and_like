<script setup>
import { Head, Link } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout_new.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

defineProps({
	canLogin: {
		type: Boolean,
	},
	canRegister: {
		type: Boolean,
	},
	laravelVersion: {
		type: String,
		required: true,
	},
	phpVersion: {
		type: String,
		required: true,
	},
});
</script>

<template>
	<GuestLayout>
		<Head title="Welcome" />

		<div class="relative sm:flex sm:justify-center sm:items-center h-full">
			<div class="max-w-7xl mx-auto p-6 lg:p-8">
				<section class="relative py-12 lg:py-24 overflow-hidden">
					<div class="container mx-auto px-4 relative">
						<div class="max-w-3xl mx-auto text-center">
							<h1 class="font-heading text-5xl sm:text-6xl mb-8">About us</h1>
							<p class="text-xl text-gray-700 font-medium mb-10 text-left">
								Welcome to our innovative Instagram engagement application,
								designed specifically to empower users in cultivating meaningful
								connections within the platform. In today's fast-paced digital
								landscape, building and maintaining relationships is paramount
								for businesses, influencers, and creators alike.
							</p>
							<p class="text-xl text-gray-700 font-medium mb-10 text-left">
								Our app provides a comprehensive suite of tools to streamline
								this process, allowing you to effortlessly manage your current
								clients, past clients, leads, affiliates, and podcast guests.
								With a user-friendly interface, you can easily add or edit
								lists. The app also keeps you engaged by automatically tracking
								any posts or Reels published within the next 14 days, ensuring
								you never miss an opportunity to connect.
							</p>
						</div>
					</div>
				</section>
			</div>
		</div>
	</GuestLayout>
</template>

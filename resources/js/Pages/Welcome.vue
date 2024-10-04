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

		<section class="relative my-20 py-10 md:py-32 overflow-hidden">
			<div class="relative container px-4 mx-auto">
				<div class="max-w-2xl mx-auto mb-20 text-center">
					<span
						class="inline-block py-1 px-3 mb-5 text-xs font-semibold text-rose-900 bg-rose-50 rounded-full"
						>👋 MEET List and Like</span
					>
					<h1
						class="font-heading text-5xl xs:text-6xl font-extrabold text-gray-900 mb-10"
					>
						Ready to Take Your Instagram Engagement to the Next Level?
					</h1>
					<p class="text-xl text-gray-500 font-semibold">
						Build better relationships, and watch your influence grow—all while
						saving time. Sign up today to experience smarter Instagram
						engagement.
					</p>
				</div>
				<div class="flex flex-wrap justify-center -mx-4 gap-y-8">
					<div class="w-full md:w-1/2 lg:w-auto px-4 xl:px-10 mb-10 md:mb-0">
						<div
							class="max-w-sm mx-auto h-full p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500"
						>
							<div>
								<div
									class="h-16 w-16 bg-red-50 dark:bg-red-800/20 flex items-center justify-center rounded-full"
								>
									<ApplicationLogo />
								</div>

								<h2
									class="mt-6 text-md font-semibold text-gray-900 dark:text-white"
								>
									Engage Smarter: Boost Your Connections and Save Time
								</h2>

								<p
									class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed"
								>
									Our platform is designed to help Instagram users build deeper
									relationships with their audience and grow their influence—all
									in one easy-to-use web app.
								</p>
							</div>
						</div>
					</div>

					<div class="w-full md:w-1/2 lg:w-auto px-4 xl:px-10 mb-10 md:mb-0">
						<div
							class="max-w-sm mx-auto h-full p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500"
						>
							<div>
								<div
									class="h-16 w-16 bg-red-50 dark:bg-red-800/20 flex items-center justify-center rounded-full"
								>
									<ApplicationLogo />
								</div>

								<h2
									class="mt-6 text-md font-semibold text-gray-900 dark:text-white"
								>
									Track Top Engagers & Leads
								</h2>

								<p
									class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed"
								>
									Stay on top of your most important followers. Our app
									highlights your top engagers and leads, easing the stress of
									staying up-to-date with your top followers.
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</GuestLayout>
</template>

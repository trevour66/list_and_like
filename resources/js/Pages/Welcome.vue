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

		<div class="relative sm:flex sm:justify-center sm:items-center h-[80vh]">
			<div class="max-w-7xl mx-auto p-6 lg:p-8">
				<div>
					<div class="flex gap-6 lg:gap-8">
						<div
							class="max-w-[500px] scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500"
						>
							<div>
								<div
									class="h-16 w-16 bg-red-50 dark:bg-red-800/20 flex items-center justify-center rounded-full"
								>
									<ApplicationLogo />
								</div>

								<h2
									class="mt-6 text-xl font-semibold text-gray-900 dark:text-white"
								>
									Coming Soon
								</h2>

								<p
									class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed"
								>
									Lorem, ipsum dolor sit amet consectetur adipisicing elit.
									Voluptatum, aliquam esse. Nihil sed error animi, laborum nobis
									possimus temporibus nesciunt! Corporis, fugit? Itaque ratione
									saepe eum recusandae officia perferendis placeat delectus
									beatae dolorem ducimus, quia quo ullam ipsum possimus!
									Officiis placeat dolores minus odio earum quas obcaecati ipsam
									sed temporibus.
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</GuestLayout>
</template>

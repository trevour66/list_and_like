<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { initDropdowns } from "flowbite";
import { onMounted } from "vue";

defineProps({
	user_list: {
		type: Object,
		default: {},
	},
	ig_profiles: {
		type: Array,
		default: [],
	},
});

onMounted(() => {
	initDropdowns();
});
</script>

<template>
	<Head title="Dashboard" />

	<AuthenticatedLayout>
		<template #header>
			<div>
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">
					{{ user_list?.list_name ?? "" }}
				</h2>
			</div>
			<div>
				<div class="flex flex-wrap -mx-3">
					<div class="flex items-center md:ml-auto md:pr-4">
						<div
							class="relative flex flex-wrap items-stretch w-full transition-all rounded-lg ease"
						>
							<button
								type="button"
								class="text-white bg-red-800 focus:ring-4 focus:outline-none focus:ring-[#24292F]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2"
							>
								Delete List
							</button>
							<Link
								:href="route('user_lists.index')"
								class="text-white bg-gray-700 focus:ring-4 focus:outline-none focus:ring-[#24292F]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2"
							>
								Back to Lists
							</Link>
						</div>
					</div>
				</div>
			</div>
		</template>

		<template #content>
			<div
				class="grid lg:grid-cols-3 xl:grid-cols-4 grid-cols-1 pt-6 gap-3 mx-3"
			>
				<div v-for="(profile, index) in ig_profiles" :key="index">
					<div
						class="w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 px-5"
					>
						<div class="flex flex-col items-center py-10">
							<!-- <img
								class="w-24 h-24 mb-3 rounded-full shadow-lg"
								:src="profile.profile_pic"
								alt="Bonnie image"
							/> -->
							<h5
								class="mb-1 text-xl font-medium text-gray-900 dark:text-white"
							>
								{{ profile.ig_handle }}
							</h5>
							<div
								class="text-sm text-gray-500 my-2 h-[100px] flex items-center"
							>
								<span>{{ profile.bio }}</span>
							</div>
							<div class="flex my-3 gap-2">
								<span class="py-2 text-sm font-medium text-gray-700"
									>{{ profile.followers }}
									<span class="text-gray-500 text-xs">followers</span>
								</span>
								<span class="py-2 text-sm font-medium text-gray-700"
									>{{ profile.following }}
									<span class="text-gray-500 text-xs">following</span>
								</span>
								<span class="py-2 text-sm font-medium text-gray-700"
									>{{ profile.post_count }}
									<span class="text-gray-500 text-xs">posts</span>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</template>
	</AuthenticatedLayout>
</template>

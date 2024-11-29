<script setup>
import { Head, usePage, useForm } from "@inertiajs/vue3";
import { initDropdowns } from "flowbite";
import { computed, onMounted } from "vue";

const miniVersionActive = usePage().props.mini_version ?? false;

const props = defineProps({
	profile: {
		type: Object,
		required: true,
	},
	user_lists: {
		type: Array,
		required: false,
		default: [],
	},
});

const index = computed(() => {
	const seed = "ig_profile";
	const randomCharacter = Math.random().toString(36).charAt(2);

	return `${seed}__${randomCharacter}`;
});

onMounted(async () => {
	initDropdowns();
});
</script>
<template>
	<div
		class="h-full w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 p-5"
	>
		<!-- {{ profile }} -->
		<!-- {{ user_lists }} -->
		<div
			v-if="(user_lists ?? []).length > 0"
			class="flex justify-end px-4 pb-4"
		>
			<button
				:id="`${index}_dropdownButton`"
				:data-dropdown-toggle="`${index}_dropdown`"
				data-dropdown-placement="bottom-end"
				class="inline-block text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-1.5"
				type="button"
			>
				<span class="sr-only">Open dropdown</span>
				<svg
					class="w-5 h-5"
					aria-hidden="true"
					xmlns="http://www.w3.org/2000/svg"
					fill="currentColor"
					viewBox="0 0 16 3"
				>
					<path
						d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"
					/>
				</svg>
			</button>
			<!-- Dropdown menu -->
			<div class="relative">
				<div
					:id="`${index}_dropdown`"
					class="z-10 hidden text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700"
				>
					<div class="px-4 py-3 text-sm text-gray-700">
						<div class="font-bold truncate">Add to list</div>
					</div>
					<ul
						class="text-sm text-gray-700 dark:text-gray-200 h-36 overflow-y-auto"
						:aria-labelledby="`${index}_dropdownButton`"
					>
						<li v-for="(list, index) in user_lists" :key="index">
							<div
								@click="
									addUserToList(
										list._id,
										profile.ig_handle,
										profile?.user_list_ids ?? []
									)
								"
								class="h-full w-full px-4 py-4 hover:bg-gray-100 hover:cursor-pointer focus:bg-gray-100"
								:class="{
									'bg-gray-100': ig_profile_is_already_in_list(
										profile?.user_list_ids ?? [],
										list._id
									),
								}"
							>
								<!-- {{ list }} -->
								<span class="block">{{ list.list_name }}</span>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="flex flex-col items-center">
			<!-- <img
								class="w-24 h-24 mb-3 rounded-full shadow-lg"
								:src="profile.profile_pic"
								alt="Bonnie image"
							/> -->
			<h5
				class="mb-1 text-xl font-medium text-gray-900 dark:text-white break-all"
			>
				{{ profile.ig_handle }}
			</h5>
			<div
				v-if="!miniVersionActive"
				class="text-sm text-gray-500 my-2 h-[120px] flex items-center overflow-y-auto"
			>
				<span>{{ profile.bio }}</span>
			</div>
			<div v-if="!miniVersionActive" class="flex my-3 gap-2">
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
</template>

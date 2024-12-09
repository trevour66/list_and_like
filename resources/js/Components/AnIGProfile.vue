<script setup>
import { Head, usePage, useForm } from "@inertiajs/vue3";
import { initDropdowns } from "flowbite";
import { onUpdated, ref, computed, onMounted } from "vue";

const miniVersionActive = usePage().props.mini_version ?? false;

const props = defineProps({
	disable_view_post_button: {
		type: Boolean,
		default: false,
		required: false,
	},
	showed_from_A_list: {
		type: Boolean,
		default: false,
		required: false,
	},
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

const dropdownButtonInitator = ref(null);

const emits = defineEmits(["goToIGProfilePosts", "deleteFromList"]);

const index = computed(() => {
	const seed = "ig_profile";
	const randomCharacter = Math.random().toString(36);

	return `${seed}__${randomCharacter}`;
});

const init_goToIGProfilePosts = (ig_handle) => {
	// console.log(ig_handle);
	if ((ig_handle ?? "") === "") return;

	emits("goToIGProfilePosts", ig_handle);
};

const ig_profile_is_already_in_list = (user_list_ids, list_id) => {
	return (user_list_ids ?? []).find((item) => item == list_id) ?? false;
};

const addUserToList = (list_id, ig_handle, user_list_ids) => {
	if (ig_profile_is_already_in_list(user_list_ids, list_id)) return;

	const dropdownButton = dropdownButtonInitator;

	const form = useForm({
		ig_handle: ig_handle,
	});

	form.post(`/my-lists/${list_id}/add-profile`, {
		onFinish: () => {
			dropdownButton.value.click();

			window.alert("IG profile added to list");
			form.reset();
		},
	});
};

const deleteFromList = () => {
	emits("deleteFromList", props.profile);
};

onUpdated(() => {
	initDropdowns();
});

onMounted(() => {
	initDropdowns();
});
</script>
<template>
	<div
		class="h-full w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 p-5"
	>
		<!-- {{ profile }} -->
		<!-- {{ user_lists }} -->
		<div class="flex justify-end px-4 pb-4" v-if="showed_from_A_list">
			<button
				@click="deleteFromList"
				class="inline-block text-gray-500 bg-gray-200 hover:bg-gray-400 focus:ring-2 focus:outline-none focus:ring-gray-200 rounded-lg text-sm p-1.5"
				type="button"
			>
				<span class="sr-only">delete from list</span>

				<svg
					class="w-5 h-5"
					viewBox="0 0 24 24"
					fill="none"
					xmlns="http://www.w3.org/2000/svg"
				>
					<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
					<g
						id="SVGRepo_tracerCarrier"
						stroke-linecap="round"
						stroke-linejoin="round"
					></g>
					<g id="SVGRepo_iconCarrier">
						<path
							d="M10 11V17"
							stroke="#ff1111"
							stroke-width="2"
							stroke-linecap="round"
							stroke-linejoin="round"
						></path>
						<path
							d="M14 11V17"
							stroke="#ff1111"
							stroke-width="2"
							stroke-linecap="round"
							stroke-linejoin="round"
						></path>
						<path
							d="M4 7H20"
							stroke="#ff1111"
							stroke-width="2"
							stroke-linecap="round"
							stroke-linejoin="round"
						></path>
						<path
							d="M6 7H12H18V18C18 19.6569 16.6569 21 15 21H9C7.34315 21 6 19.6569 6 18V7Z"
							stroke="#ff1111"
							stroke-width="2"
							stroke-linecap="round"
							stroke-linejoin="round"
						></path>
						<path
							d="M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5V7H9V5Z"
							stroke="#ff1111"
							stroke-width="2"
							stroke-linecap="round"
							stroke-linejoin="round"
						></path>
					</g>
				</svg>
			</button>
		</div>
		<div
			v-if="(user_lists ?? []).length > 0"
			class="flex justify-end px-4 pb-4"
		>
			<button
				ref="dropdownButtonInitator"
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
										profile?.user_list_ids ?? [],
										index
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
								<span class="block uppercase text-xs font-semibold">{{
									list.list_name
								}}</span>
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
				class="mb-1 text-md font-semibold text-gray-900 dark:text-white break-all"
			>
				{{ profile.ig_handle }}
			</h5>
			<div
				v-if="!miniVersionActive"
				class="text-sm text-gray-500 my-2 h-[120px] flex items-center overflow-y-auto"
			>
				<span>{{ profile.bio }}</span>
			</div>
			<div v-if="!miniVersionActive" class="flex my-3 gap-4">
				<span
					class="py-2 text-sm font-medium text-gray-700 flex flex-col items-center"
				>
					<span>
						{{ profile?.followers ?? 0 }}
					</span>
					<span class="text-gray-500 text-xs">followers</span>
				</span>
				<span
					class="py-2 text-sm font-medium text-gray-700 flex flex-col items-center"
				>
					<span>
						{{ profile?.following ?? 0 }}
					</span>
					<span class="text-gray-500 text-xs">following</span>
				</span>
				<span
					class="py-2 text-sm font-medium text-gray-700 flex flex-col items-center"
				>
					<span>
						{{ profile?.post_count ?? 0 }}
					</span>
					<span class="text-gray-500 text-xs">posts</span>
				</span>
			</div>
		</div>
		<div
			v-if="!disable_view_post_button"
			class="flex mt-4 md:mt-6 w-full items-center justify-center"
		>
			<button
				@click="init_goToIGProfilePosts(profile.ig_handle)"
				class="inline-flex items-center px-4 py-2 text-sm font-medium text-center text-white bg-[#f24b54] rounded-lg hover:opacity-70 focus:ring-2 focus:outline-none focus:ring-[#f24b54] dark:bg-[#f24b54] dark:hover:bg-[#f24b54] dark:focus:ring-[#f24b54]"
			>
				View tracked posts
			</button>
		</div>
	</div>
</template>

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

const emits = defineEmits(["goToIGProfilePosts"]);

const index = computed(() => {
	const seed = "ig_profile";
	const randomCharacter = Math.random().toString(36).charAt(2);

	return `${seed}__${randomCharacter}`;
});

const init_goToIGProfilePosts = (ig_handle) => {
	if ((ig_handle ?? "") === "") return;

	emits("goToIGProfilePosts", ig_handle);
};

const ig_profile_is_already_in_list = (user_list_ids, list_id) => {
	return (user_list_ids ?? []).find((item) => item == list_id) ?? false;
};

const addUserToList = (list_id, ig_handle, user_list_ids) => {
	if (ig_profile_is_already_in_list(user_list_ids, list_id)) return;

	const form = useForm({
		ig_handle: ig_handle,
	});

	form.post(`/my-lists/${list_id}/add-profile`, {
		onFinish: () => {
			window.alert("IG profile added to list");
			form.reset();
		},
	});
};

onMounted(async () => {
	initDropdowns();
});
</script>
<template>
	<div
		class="h-full w-80 max-w-80 min-w-80 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 px-5 py-10"
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
				class="text-sm text-gray-600 my-2 h-[120px] flex items-center overflow-y-auto"
			>
				<span v-if="profile?.bio ?? '' !== ''">{{ profile.bio }}</span>
				<span v-else class="text-center text-gray-400 font-bold"
					>Data not available! Our system may still be working on this</span
				>
			</div>

			<div class="flex mt-4 md:mt-6">
				<button
					@click="init_goToIGProfilePosts(profile.ig_handle)"
					class="inline-flex items-center px-4 py-2 text-sm font-medium text-center text-white bg-[#f24b54] rounded-lg hover:opacity-70 focus:ring-2 focus:outline-none focus:ring-[#f24b54] dark:bg-[#f24b54] dark:hover:bg-[#f24b54] dark:focus:ring-[#f24b54]"
				>
					View tracked posts
				</button>
				<button
					v-if="false"
					class="py-2 px-4 ms-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-[#f24b54] focus:z-10 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
				>
					<svg
						class="h-5 w-5 fill-[#f24b54]"
						fill="#000000"
						viewBox="0 0 32 32"
						version="1.1"
						xmlns="http://www.w3.org/2000/svg"
					>
						<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
						<g
							id="SVGRepo_tracerCarrier"
							stroke-linecap="round"
							stroke-linejoin="round"
						></g>
						<g id="SVGRepo_iconCarrier">
							<title>instagram</title>
							<path
								d="M25.805 7.996c0 0 0 0.001 0 0.001 0 0.994-0.806 1.799-1.799 1.799s-1.799-0.806-1.799-1.799c0-0.994 0.806-1.799 1.799-1.799v0c0.993 0.001 1.798 0.805 1.799 1.798v0zM16 20.999c-2.761 0-4.999-2.238-4.999-4.999s2.238-4.999 4.999-4.999c2.761 0 4.999 2.238 4.999 4.999v0c0 0 0 0.001 0 0.001 0 2.76-2.237 4.997-4.997 4.997-0 0-0.001 0-0.001 0h0zM16 8.3c0 0 0 0-0 0-4.253 0-7.7 3.448-7.7 7.7s3.448 7.7 7.7 7.7c4.253 0 7.7-3.448 7.7-7.7v0c0-0 0-0 0-0.001 0-4.252-3.447-7.7-7.7-7.7-0 0-0 0-0.001 0h0zM16 3.704c4.003 0 4.48 0.020 6.061 0.089 1.003 0.012 1.957 0.202 2.84 0.538l-0.057-0.019c1.314 0.512 2.334 1.532 2.835 2.812l0.012 0.034c0.316 0.826 0.504 1.781 0.516 2.778l0 0.005c0.071 1.582 0.087 2.057 0.087 6.061s-0.019 4.48-0.092 6.061c-0.019 1.004-0.21 1.958-0.545 2.841l0.019-0.058c-0.258 0.676-0.64 1.252-1.123 1.726l-0.001 0.001c-0.473 0.484-1.049 0.866-1.692 1.109l-0.032 0.011c-0.829 0.316-1.787 0.504-2.788 0.516l-0.005 0c-1.592 0.071-2.061 0.087-6.072 0.087-4.013 0-4.481-0.019-6.072-0.092-1.008-0.019-1.966-0.21-2.853-0.545l0.059 0.019c-0.676-0.254-1.252-0.637-1.722-1.122l-0.001-0.001c-0.489-0.47-0.873-1.047-1.114-1.693l-0.010-0.031c-0.315-0.828-0.506-1.785-0.525-2.785l-0-0.008c-0.056-1.575-0.076-2.061-0.076-6.053 0-3.994 0.020-4.481 0.076-6.075 0.019-1.007 0.209-1.964 0.544-2.85l-0.019 0.059c0.247-0.679 0.632-1.257 1.123-1.724l0.002-0.002c0.468-0.492 1.045-0.875 1.692-1.112l0.031-0.010c0.823-0.318 1.774-0.509 2.768-0.526l0.007-0c1.593-0.056 2.062-0.075 6.072-0.075zM16 1.004c-4.074 0-4.582 0.019-6.182 0.090-1.315 0.028-2.562 0.282-3.716 0.723l0.076-0.025c-1.040 0.397-1.926 0.986-2.656 1.728l-0.001 0.001c-0.745 0.73-1.333 1.617-1.713 2.607l-0.017 0.050c-0.416 1.078-0.67 2.326-0.697 3.628l-0 0.012c-0.075 1.6-0.090 2.108-0.090 6.182s0.019 4.582 0.090 6.182c0.028 1.315 0.282 2.562 0.723 3.716l-0.025-0.076c0.796 2.021 2.365 3.59 4.334 4.368l0.052 0.018c1.078 0.415 2.326 0.669 3.628 0.697l0.012 0c1.6 0.075 2.108 0.090 6.182 0.090s4.582-0.019 6.182-0.090c1.315-0.029 2.562-0.282 3.716-0.723l-0.076 0.026c2.021-0.796 3.59-2.365 4.368-4.334l0.018-0.052c0.416-1.078 0.669-2.326 0.697-3.628l0-0.012c0.075-1.6 0.090-2.108 0.090-6.182s-0.019-4.582-0.090-6.182c-0.029-1.315-0.282-2.562-0.723-3.716l0.026 0.076c-0.398-1.040-0.986-1.926-1.729-2.656l-0.001-0.001c-0.73-0.745-1.617-1.333-2.607-1.713l-0.050-0.017c-1.078-0.416-2.326-0.67-3.628-0.697l-0.012-0c-1.6-0.075-2.108-0.090-6.182-0.090z"
							></path>
						</g>
					</svg>
				</button>
			</div>
		</div>
	</div>
</template>

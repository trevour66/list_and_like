<script setup>
import IGProfilePost from "@/Services/IGProfilePost";
import { usePage } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

const userAccessToken = usePage().props.auth.user.auth_token;

const props = defineProps({
	post: {
		type: Object,
		required: true,
	},
	IGAccessCodes: {
		type: Array,
		default: [],
	},
});

const postSkipped = ref(false);
const skippingPost = ref(false);

const truncateString = (str) => {
	const maxLength = 150;

	if (str.length > maxLength) {
		// Truncate the string and add "..."
		return str.slice(0, maxLength) + "...";
	} else {
		return str;
	}
};

const formURLWithAccessToken = (url) => {
	if (!url) {
		return "";
	}

	console.log(url);

	console.log(props.IGAccessCodes);
	const user_IGAccessCodes = props?.IGAccessCodes ?? [];

	if (user_IGAccessCodes.length === 0) {
		return "";
	}

	// const acc_token = user_IGAccessCodes[0]?.long_lived_access_token ?? "";
	const acc_token = user_IGAccessCodes[0]?.short_lived_access_token ?? "";

	if (!acc_token) {
		return "";
	}

	return url + "/?access_token=" + acc_token;
};

const skipPost = async (post_id) => {
	if (skippingPost.value) {
		return;
	}

	skippingPost.value = true;
	await IGProfilePost.skipPost(userAccessToken, post_id)
		.then(function (response) {
			// handle success
			console.log(response);
			const status = response?.data?.status ?? false;

			console.log(status);

			if (status == "success") {
				postSkipped.value = true;
			}

			skippingPost.value = false;
		})
		.catch(function (error) {
			// handle error
			console.log(error);
			skippingPost.value = false;
		});
};
</script>

<template>
	<!-- {{ post }} -->
	<div
		class="relative flex flex-col flex-auto min-w-0 p-4 mx-3 overflow-hidden break-words bg-white border-0 dark:bg-slate-850 dark:shadow-dark-xl shadow-xl rounded-2xl bg-clip-border"
	>
		<div class="w-full max-w-full mx-auto mt-4 sm:mr-0 md:flex">
			<div class="flex-none w-auto max-w-full my-auto">
				<div class="h-full">
					<h5 class="mb-0 leading-normal font-semibold">
						{{ post?.ig_profile_id }}
					</h5>
					<p class="mb-0 inline-flex text-sm gap-2">
						<span v-if="post?.likesCount ?? false">
							<i class="fas fa-thumbs-up text-gray-400"></i>
							<span class="text-gray-600 ml-1">{{ post.likesCount }}</span>
						</span>
						<span v-if="post?.commentsCount ?? false">
							<i class="fas fa-comments text-gray-400"></i>
							<span class="text-gray-600 ml-1">{{ post.commentsCount }}</span>
						</span>
					</p>
				</div>
			</div>
		</div>
		<div class="w-full max-w-full mx-auto mt-4 sm:mr-0 md:flex">
			<div
				class="md:w-6/12 w-full bg-gray-50 relative inline-flex items-center justify-center text-white transition-all duration-200 ease-in-out text-base h-60 rounded-xl"
			>
				<!-- <img
					:src="formURLWithAccessToken(post?.displayUrl ?? false)"
					alt="profile_image"
					class="w-full shadow-2xl rounded-xl"
				/> -->
				<!-- <img
					:src="post.displayUrl"
					alt="profile_image"
					class="w-full shadow-2xl rounded-xl"
				/> -->
				<div class="absolute top-1 right-1 p-4">
					<svg
						v-if="(post?.post_type ?? '') == 'Image'"
						class="w-5 h-5"
						fill="#999"
						viewBox="0 0 24 24"
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
								fill-rule="evenodd"
								d="M4,8 L14,8 C15.1045695,8 16,8.8954305 16,10 L16,20 C16,21.1045695 15.1045695,22 14,22 L4,22 C2.8954305,22 2,21.1045695 2,20 L2,10 C2,8.8954305 2.8954305,8 4,8 Z M4,10 L4,20 L14,20 L14,10 L4,10 Z M17,19 L17,8 C17,7.44771525 16.5522847,7 16,7 L5,7 C5,5.8954305 5.8954305,5 7,5 L17,5 C18.1045695,5 19,5.8954305 19,7 L19,17 C19,18.1045695 18.1045695,19 17,19 Z M20,16 L20,5 C20,4.44771525 19.5522847,4 19,4 L8,4 C8,2.8954305 8.8954305,2 10,2 L20,2 C21.1045695,2 22,2.8954305 22,4 L22,14 C22,15.1045695 21.1045695,16 20,16 Z"
							></path>
						</g>
					</svg>
					<svg
						v-if="(post?.post_type ?? '') == 'Video'"
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
								d="M22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22H15C20 22 22 20 22 15Z"
								stroke="#333"
								stroke-width="1.5"
								stroke-linecap="round"
								stroke-linejoin="round"
							></path>
							<path
								d="M2.51953 7.10986H21.4795"
								stroke="#333"
								stroke-width="1.5"
								stroke-linecap="round"
								stroke-linejoin="round"
							></path>
							<path
								d="M8.51953 2.10986V6.96986"
								stroke="#333"
								stroke-width="1.5"
								stroke-linecap="round"
								stroke-linejoin="round"
							></path>
							<path
								d="M15.4805 2.10986V6.51986"
								stroke="#333"
								stroke-width="1.5"
								stroke-linecap="round"
								stroke-linejoin="round"
							></path>
							<path
								opacity="0.4"
								d="M9.75 14.4501V13.2501C9.75 11.7101 10.84 11.0801 12.17 11.8501L13.21 12.4501L14.25 13.0501C15.58 13.8201 15.58 15.0801 14.25 15.8501L13.21 16.4501L12.17 17.0501C10.84 17.8201 9.75 17.1901 9.75 15.6501V14.4501V14.4501Z"
								stroke="#333"
								stroke-width="1.5"
								stroke-miterlimit="10"
								stroke-linecap="round"
								stroke-linejoin="round"
							></path>
						</g>
					</svg>
				</div>
			</div>
			<div class="md:w-6/12 w-full flex justify-center items-center px-3 py-4">
				<div class="h-[150px] scroll-y-auto">
					<p class="text-sm font-normal text-gray-500">
						{{ truncateString(post?.caption ?? "") }}
					</p>
				</div>
			</div>
		</div>

		<div
			class="w-full max-w-full mx-auto mt-4 sm:mr-0 relative flex items-center bg-gray-50 gap-3 p-1 rounded-xl hover:cursor-pointer hover:opacity-50"
			@click="skipPost(post?.post_id ?? '')"
			:class="{
				'cursor-not-allowed opacity-50': skippingPost || postSkipped,
			}"
		>
			<div
				class="z-30 flex gap-4 items-center justify-center w-full px-0 py-1 mb-0 transition-colors ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
			>
				<span>
					<i class="fas fa-walking"></i>
				</span>

				<p v-if="postSkipped">Post Skipped</p>
				<p v-else>Skip Post</p>
			</div>
		</div>
		<a
			class="w-full max-w-full mx-auto mt-4 sm:mr-0 bg-[#f24b54] relative flex items-center gap-3 p-1 rounded-xl hover:cursor-pointer hover:opacity-50"
			target="_blank"
			:href="post?.url ?? '#'"
		>
			<div
				class="z-30 flex gap-4 items-center justify-center w-full px-0 py-1 mb-0 transition-colors ease-in-out border-0 rounded-lg bg-inherit text-white"
			>
				<p>Interact with Post</p>
			</div>
		</a>
	</div>
</template>

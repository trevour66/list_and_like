<script setup>
import ModalStructure from "./ModalStructure.vue";
import useModalStore from "@/Store/ModalStore";
import { router, useForm } from "@inertiajs/vue3";

import InfinityScrollLoader from "@/Components/InfinityScrollLoader.vue";
import { onMounted, ref, watch, watchEffect } from "vue";
import IGProfilePost from "@/Services/IGProfilePost";
import IGPostRepresentation from "@/Components/IGPostRepresentation.vue";

import usePreferedIgAccountStore from "@/Store/preferedIgAccountStore";

const preferedIgAccountStore = usePreferedIgAccountStore();

const props = defineProps({
	ig_handle: {
		type: String,
		required: true,
	},
	business_account_id: {
		type: String,
		required: true,
	},
});

const modalStore = useModalStore();

const form = useForm({
	list_name: "",
	list_description: "",
});

const associated_user_posts = ref([]);
const next_page_url = ref("");

const Loading = ref(true);
const LoadingError = ref(false);
const hasMounted = ref(false);

watchEffect(() => {
	if (modalStore.getEndOfModalBodyReached && (next_page_url?.value ?? false)) {
		Loading.value = true;
		IGBusinessPostCommentsFetch();
		console.log("end reached");
		console.log(modalStore.getEndOfModalBodyReached);
	}
});

const closeModal = () => {
	modalStore.toggel_IGProfilePost_Modal(false);
};

const reAuth = async () => {
	await axios
		.get("/sanctum/csrf-cookie")
		.then((res) => {})
		.catch((err) => {
			console.log("Error reauth");
			console.log(err);
		});
};

const userPostsFetch = async () => {
	// console.log("response.data");
	await IGProfilePost.getIGProfilePosts(
		props.ig_handle,
		props.business_account_id,
		next_page_url.value
	)
		.then(function (response) {
			// handle success
			const associated_user_posts_data =
				response?.data?.associated_user_posts ?? false;
			const status = response?.data?.status ?? false;

			const posts = associated_user_posts_data?.data ?? [];
			// const prev_cursor = associated_user_posts_data?.prev_cursor ?? null;

			next_page_url.value = associated_user_posts_data?.next_page_url ?? null;

			if (posts && posts.length > 0) {
				Array.prototype.push.apply(associated_user_posts.value, posts);
			}

			Loading.value = false;
		})
		.catch(async function (error) {
			// handle error
			console.log(error);
			if (
				error.status == 419 ||
				error.status == 401 ||
				(error.response?.data?.message ?? "").indexOf("CSRF token mismatch") >=
					0
			) {
				await reAuth();
			}

			Loading.value = false;
		});
};

onMounted(async () => {
	await userPostsFetch();
	hasMounted.value = true;
});
</script>

<template>
	<ModalStructure :maximize="true">
		<template #header_text>
			<div class="flex gap-x-2 items-center">
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
				<h3
					class="text-xl font-semibold text-gray-900 dark:text-white uppercase"
				>
					{{ ig_handle }}
				</h3>
			</div>

			<button
				@click="closeModal"
				type="button"
				class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
			>
				<svg
					class="w-3 h-3"
					aria-hidden="true"
					xmlns="http://www.w3.org/2000/svg"
					fill="none"
					viewBox="0 0 14 14"
				>
					<path
						stroke="currentColor"
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="2"
						d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
					/>
				</svg>
				<span class="sr-only">Close modal</span>
			</button>
		</template>

		<template #body class="py-4 lg:py-8">
			<div class="bg-white antialiased">
				<div class="grid lg:grid-cols-2 grid-cols-1 pt-6 gap-x-4 gap-y-10 mx-3">
					<template v-if="(associated_user_posts ?? []).length > 0">
						<div v-for="(post, index) in associated_user_posts" :key="index">
							<!-- {{ post }} -->
							<IGPostRepresentation
								:post="post"
								:index="index"
								:user_lists="user_lists"
								@IGProfileAddedToAList="refreshCurrentView"
							/>
						</div>
					</template>
				</div>

				<template v-if="(associated_user_posts ?? []).length > 0">
					<template
						v-for="(comment, index) in associated_user_posts"
						:key="index"
					>
					</template>
				</template>
				<template v-if="!Loading && (associated_user_posts ?? []).length == 0">
					<div
						class="flex items-center justify-center w-full h-full bg-gray-50"
					>
						<div>
							<p class="text-md font-normal text-gray-500">No post found</p>
						</div>
					</div>
				</template>
			</div>
			<div v-if="Loading" class="flex items-center justify-center w-full h-32">
				<InfinityScrollLoader />
			</div>
		</template>

		<template #footer> </template>
	</ModalStructure>
</template>

<script setup>
import ModalStructure from "./ModalStructure.vue";
import useModalStore from "@/Store/ModalStore";
import InputError from "@/Components/InputError.vue";
import { router, useForm } from "@inertiajs/vue3";

import InfinityScrollLoader from "@/Components/InfinityScrollLoader.vue";
import { onMounted, ref, watch, watchEffect } from "vue";
import IGBusinessPost from "@/Services/IGBusinessPost";
import ACommentParent from "@/Components/ACommentParent.vue";

import useCommentStore from "@/Store/CommentStore";
import usePreferedIgAccountStore from "@/Store/preferedIgAccountStore";
import { onBeforeUnmount } from "vue";

const preferedIgAccountStore = usePreferedIgAccountStore();

const props = defineProps({
	activePostID: {
		type: String,
		required: true,
	},
});

const modalStore = useModalStore();
const commentStore = useCommentStore();

const form = useForm({
	list_name: "",
	list_description: "",
});

const success_submission = ref(false);

const associated_user_IG_Biz_post_comments = ref([]);
const next_page_url = ref("");

const Loading = ref(true);
const LoadingError = ref(false);
const retryCount = ref(0);
const hasMounted = ref(false);

const commentText = ref("");
const submitting = ref(false);

const commentOnPost = async () => {
	if ((commentText.value ?? "") == "" || submitting.value) return;

	submitting.value = true;

	const replyingTo_ig_username =
		commentStore.get_CommentData?.IG_username_replying_to ?? "";
	const replyingTo_ig_comment =
		commentStore.get_CommentData?.IG_comment_replying_to ?? "";
	const post_id = commentStore.get_CommentData?.IG_Post ?? "";

	const from =
		preferedIgAccountStore?.get_preferedIgBussinessAccount?.IG_username ?? "";

	commentStore.addText_CommentData(commentText.value);

	let commentAPIReq = null;

	if (replyingTo_ig_comment !== "") {
		commentAPIReq = IGBusinessPost.replyComments(
			post_id,
			replyingTo_ig_comment,
			commentText.value,
			from
		);
	} else {
		commentAPIReq = IGBusinessPost.newComments(
			post_id,
			commentText.value,
			from
		);
	}

	await commentAPIReq
		.then(function (response) {
			console.log(response);

			if (response?.data?.status ?? "") {
				success_submission.value = true;
			}

			// handle success
		})
		.catch(async (error) => {
			// handle error
			console.log(error);
			if (
				err.status == 419 ||
				err.status == 401 ||
				(err.response.data?.message ?? "").indexOf("CSRF token mismatch") >= 0
			) {
				await reAuth();
			}
		});

	submitting.value = false;
};

watch(commentText, (newValue) => {
	if (newValue) {
		let IG_Post = props.activePostID ?? "";
		let from_IG_username =
			preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_username ?? "";

		commentStore.set_CommentData_directComment(
			from_IG_username,
			IG_Post,
			newValue
		);
	}
});

watchEffect(() => {
	if (modalStore.getEndOfModalBodyReached && (next_page_url?.value ?? false)) {
		Loading.value = true;
		IGBusinessPostCommentsFetch();
		console.log("end reached");
		console.log(modalStore.getEndOfModalBodyReached);
	}
});

const closeModal = () => {
	commentStore.reset_CommentData();
	modalStore.toggelCommentsModal(false);
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

const IGBusinessPostCommentsFetch = async () => {
	// console.log("response.data");
	await IGBusinessPost.getIGBusinessPostComments(
		props.activePostID ?? "",
		next_page_url.value
	)
		.then(function (response) {
			// handle success
			const associated_user_IG_Biz_post_comments_data =
				response?.data?.associated_comments ?? false;
			const status = response?.data?.status ?? false;

			const posts = associated_user_IG_Biz_post_comments_data?.data ?? [];
			// const prev_cursor = associated_user_IG_Biz_post_comments_data?.prev_cursor ?? null;

			next_page_url.value =
				associated_user_IG_Biz_post_comments_data?.next_page_url ?? null;

			if (posts && posts.length > 0) {
				Array.prototype.push.apply(
					associated_user_IG_Biz_post_comments.value,
					posts
				);
			}

			Loading.value = false;
		})
		.catch(async (error) => {
			// handle error
			console.log(error);
			if (
				err.status == 419 ||
				err.status == 401 ||
				(err.response.data?.message ?? "").indexOf("CSRF token mismatch") >= 0
			) {
				await reAuth();
			}

			LoadingError.value = true;
			Loading.value = false;
		});
};

watch(commentStore.get_CommentData, (newValue) => {
	console.log(newValue);
});

watchEffect(() => {
	if (success_submission.value) {
		router.reload();

		setTimeout(() => {
			commentStore.reset_CommentData();

			success_submission.value = false;
		}, 3000);
	}
});

onMounted(async () => {
	await IGBusinessPostCommentsFetch();
	hasMounted.value = true;
});

onBeforeUnmount(() => {
	commentStore.reset_CommentData();
});
</script>

<template>
	<ModalStructure>
		<template #header_text>
			<h3 class="text-lg font-semibold text-gray-900 dark:text-white">
				Comments
			</h3>

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
				<template
					v-if="(associated_user_IG_Biz_post_comments ?? []).length > 0"
				>
					<template
						v-for="(comment, index) in associated_user_IG_Biz_post_comments"
						:key="index"
					>
						<ACommentParent
							:comment="comment"
							:index="index"
							:activePostID="activePostID"
						/>
					</template>
				</template>
				<template
					v-if="
						!Loading && (associated_user_IG_Biz_post_comments ?? []).length == 0
					"
				>
					<div
						class="flex items-center justify-center w-full h-full bg-gray-50"
					>
						<div>
							<p class="text-md font-normal text-gray-500">
								This post has no comment
							</p>
						</div>
					</div>
				</template>
			</div>
			<div v-if="Loading" class="flex items-center justify-center w-full h-32">
				<InfinityScrollLoader />
			</div>
		</template>

		<template #footer>
			<div
				class="py-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600 bg-gray-50"
			>
				<div
					v-if="commentStore.get_CommentData.IG_username_replying_to"
					class="text-gray-500 text-sm animate-pulse transition-all mb-2"
				>
					Replying to
					<span class="mx-1 py-1 px-2 bg-gray-200 rounded-lg">{{
						commentStore.get_CommentData.IG_username_replying_to
					}}</span>
				</div>
				<div class="flex items-center">
					<form class="w-full">
						<label for="chat" class="sr-only">Your message</label>
						<div
							class="flex items-center py-2 rounded-lg dark:bg-gray-700 w-full"
						>
							<textarea
								id="chat"
								rows="1"
								class="block mr-2 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
								placeholder="Your message..."
								v-model="commentText"
							></textarea>
							<div class="inline-block flex items-center gap-x-3">
								<button
									:disabled="(commentText ?? '') === '' || submitting"
									@click.prevent="commentStore.cancel_ReplyToSpecificUser()"
									class="inline-flex justify-center px-3 py-1.5 text-red-600 rounded-full cursor-pointer bg-red-100 hover:bg-red-200"
								>
									<i class="fas fa-times text-xl"></i>
									<span class="sr-only">cancel</span>
								</button>
								<button
									:disabled="submitting"
									@click.prevent="commentOnPost"
									type="submit"
									class="inline-flex justify-center px-3 py-1.5 text-blue-600 rounded-full cursor-pointer bg-blue-100 hover:bg-blue-200"
								>
									<i class="fas fa-paper-plane text-xl"></i>
									<span class="sr-only">Send message</span>
								</button>
							</div>
						</div>

						<span v-if="success_submission">Sent</span>
					</form>
				</div>
			</div>
		</template>
	</ModalStructure>
</template>

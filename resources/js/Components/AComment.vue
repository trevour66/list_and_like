<script setup>
import { router, usePage, useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { initDropdowns } from "flowbite";

import useCommentStore from "@/Store/CommentStore";
import usePreferedIgAccountStore from "@/Store/preferedIgAccountStore";

const preferedIgAccountStore = usePreferedIgAccountStore();
const commentStore = useCommentStore();

const props = defineProps({
	comment: {
		type: Object,
		required: true,
	},
	index: {
		type: Number,
		required: true,
	},
});

const emits = defineEmits(["viewReplies"]);

// const emits = defineEmits(["goToComments"]);

const truncateString = (str) => {
	const maxLength = 150;

	if (str.length > maxLength) {
		// Truncate the string and add "..."
		return str.slice(0, maxLength) + "...";
	} else {
		return str;
	}
};

const formatDate = (dateStr) => {
	try {
		if (!dateStr) return "";
		// Convert to a Date object
		const date = new Date(dateStr);

		// Format to US date format (MM/DD/YYYY hh:mm AM/PM)
		const usFormattedDate = date.toLocaleString("en-US", {
			year: "numeric",
			month: "2-digit",
			day: "2-digit",
			hour: "2-digit",
			minute: "2-digit",
			second: "2-digit",
			hour12: true,
		});

		return usFormattedDate;
	} catch (error) {
		return "";
	}
};

const initReply = () => {
	let IG_comment_replying_to = props.comment?.comment_id ?? "";
	let IG_username_replying_to = props.comment?.commenter_ig_username ?? "";
	let IG_Post = props.comment?.ig_business_account_posts_id ?? "";

	let from_IG_username =
		preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_username ?? "";

	commentStore.set_CommentData(
		from_IG_username,
		IG_username_replying_to,
		IG_comment_replying_to,
		IG_Post,
		""
	);
};

const initViewReplies = () => {
	emits("viewReplies");
};

onMounted(() => {
	console.log();
});
</script>

<template>
	{{ comment }}
	<article class="px-4 py-6 text-base bg-white rounded-lg dark:bg-gray-900">
		<footer class="flex justify-between items-center mb-2">
			<div class="flex items-center">
				<p
					class="inline-flex items-center mr-3 text-sm text-gray-900 dark:text-white font-semibold"
				>
					<span
						class="block mr-2 w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center"
					>
						<i class="fas fa-user"></i>
					</span>
					{{ comment?.commenter_ig_username ?? "" }}
				</p>
				<p class="text-sm text-gray-600 dark:text-gray-400">
					<span>{{ formatDate(comment?.timestamp ?? false) }}</span>
				</p>
			</div>
		</footer>
		<p class="text-gray-500 dark:text-gray-400">
			{{ truncateString(comment?.text ?? "") }}
		</p>
		<div class="flex items-center mt-4 space-x-4">
			<span v-if="comment?.likesCount ?? false">
				<i class="fas fa-thumbs-up text-gray-400"></i>
				<span class="text-gray-600 ml-1">{{ comment.likesCount }}</span>
			</span>
			<button
				v-if="
					preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_username !==
					(comment?.commenter_ig_username ?? '')
				"
				@click="initReply"
				type="button"
				class="flex items-center text-sm text-gray-500 hover:underline dark:text-gray-400 font-medium"
			>
				<svg
					class="mr-1.5 w-3.5 h-3.5"
					aria-hidden="true"
					xmlns="http://www.w3.org/2000/svg"
					fill="none"
					viewBox="0 0 20 18"
				>
					<path
						stroke="currentColor"
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="2"
						d="M5 5h5M5 8h2m6-3h2m-5 3h6m2-7H2a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h3v5l5-5h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1Z"
					></path>
				</svg>
				reply
			</button>
			<button
				@click="initViewReplies"
				type="button"
				class="flex items-center text-sm text-gray-500 hover:underline dark:text-gray-400 font-medium underline decoration-dotted"
			>
				view replies
			</button>
		</div>
	</article>
</template>

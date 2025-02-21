<script setup>
import { router, usePage, useForm } from "@inertiajs/vue3";
import { computed, onMounted, ref } from "vue";
import { initDropdowns } from "flowbite";
import IGBusinessPost from "@/Services/IGBusinessPost";
import AComment from "@/Components/AComment.vue";
import usePreferedIgAccountStore from "@/Store/preferedIgAccountStore";
import InfinityScrollLoader from "@/Components/InfinityScrollLoader.vue";
import { watch } from "vue";

const preferedIgAccountStore = usePreferedIgAccountStore();

const props = defineProps({
	comment: {
		type: Object,
		required: true,
	},
	index: {
		type: Number,
		required: true,
	},
	activePostID: {
		type: String,
		required: true,
	},
});

const childern = ref([]);
const hasChildern = ref(false);
const hasNoChildern = ref(false);

const LoadingError = ref(false);
const Loading = ref(false);

const reAuth = async () => {
	await axios
		.get("/sanctum/csrf-cookie")
		.then((res) => {})
		.catch((err) => {
			console.log("Error reauth");
			console.log(err);
		});
};

const viewReplies = async () => {
	Loading.value = true;

	let from_IG_username =
		preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_username ?? "";

	await IGBusinessPost.getReplies(
		props.activePostID,
		props.comment.comment_id,
		from_IG_username
	)
		.then(function (response) {
			// console.log(response);

			let replies = response.data?.replies ?? [];

			if (replies.length === 0) {
				hasNoChildern.value = true;
			} else {
				childern.value = replies;
				hasChildern.value = true;
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

			childern.value = [];
			hasNoChildern.value = true;

			LoadingError.value = true;
			Loading.value = false;
		});
};

watch(hasNoChildern, (newVal) => {
	if (newVal) {
		setTimeout(() => {
			hasNoChildern.value = false;
		}, 3000);
	}
});

onMounted(() => {
	initDropdowns();
});
</script>

<template>
	<!-- {{ post }} -->
	<div
		:class="{
			'border-t border-gray-200': index != 0,
		}"
		class="max-w-2xl mx-auto px-4"
	>
		<AComment
			:is_a_child="false"
			:comment="comment"
			:index="index"
			@view-replies="viewReplies"
		/>
	</div>
	<div v-if="Loading" class="flex items-center justify-center w-full h-16">
		<InfinityScrollLoader :noBackground="true" />
	</div>
	<template v-if="hasChildern">
		<div class="ml-6 lg:ml-10">
			<template v-for="(comment, index) in childern" :key="index">
				<AComment :is_a_child="true" :comment="comment" :index="index" />
			</template>
		</div>
	</template>
	<Transition name="slide-fade">
		<template v-if="hasNoChildern">
			<div class="max-w-2xl mx-auto px-8 mb-2">
				<p class="text-sm font-semibold text-red-600 dark:text-white">
					No more replies
				</p>
			</div>
		</template>
	</Transition>
</template>

<style scoped>
.slide-fade-enter-active {
	transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
	transition: all 0.4s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
	transform: translateX(20px);
	opacity: 0;
}
</style>

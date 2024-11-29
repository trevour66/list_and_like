<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InfinityScrollLoader from "@/Components/InfinityScrollLoader.vue";
import IGBizPost from "@/Components/IGBizPost.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";
import IGBusinessPost from "@/Services/IGBusinessPost";
import ActiveIGAccountSelector from "@/Components/ActiveIGAccountSelector.vue";
import usePreferedIgAccountStore from "@/Store/preferedIgAccountStore";

import useModalStore from "@/Store/ModalStore";
import CommentsModal from "@/Components/modals/CommentsModal.vue";

const modalStore = useModalStore();
const preferedIgAccountStore = usePreferedIgAccountStore();

defineProps({
	ig_data_fetch_process: {
		type: Array,
		default: [],
	},
	user_lists: {
		type: Array,
		default: [],
	},
});

const associated_user_IG_Biz_posts = ref([]);
const next_page_url = ref("");

const Loading = ref(true);
const LoadingError = ref(false);
const hasMounted = ref(false);

const activePostID = ref("");

const reAuth = async () => {
	await axios
		.get("/sanctum/csrf-cookie")
		.then((res) => {})
		.catch((err) => {
			console.log("Error reauth");
			console.log(err);
		});
};

const IGBusinessPostsFetch = async () => {
	// console.log("response.data");
	await IGBusinessPost.getIGBusinessPost(
		preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_username ?? "",
		next_page_url.value
	)
		.then(function (response) {
			// handle success
			const associated_user_IG_Biz_posts_data =
				response?.data?.associated_IG_posts ?? false;
			const status = response?.data?.status ?? false;

			const posts = associated_user_IG_Biz_posts_data?.data ?? [];
			// const prev_cursor = associated_user_IG_Biz_posts_data?.prev_cursor ?? null;

			next_page_url.value =
				associated_user_IG_Biz_posts_data?.next_page_url ?? null;

			if (posts && posts.length > 0) {
				Array.prototype.push.apply(associated_user_IG_Biz_posts.value, posts);
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

const goToComments = (post_id) => {
	// console.log(post_id);

	if (!(post_id ?? false)) return;

	activePostID.value = post_id;
	modalStore.toggelCommentsModal(true);
};

watch(
	preferedIgAccountStore.get_preferedIgBussinessAccount,
	async (newValue) => {
		// console.log(newValue);

		// console.log("called");
		let IG_username =
			preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_username ?? "";

		if (IG_username !== "" && hasMounted.value) {
			next_page_url.value = "";

			await IGBusinessPostsFetch();
		}
	}
);

onMounted(async () => {
	await IGBusinessPostsFetch();
	hasMounted.value = true;
});
</script>

<template>
	<Head title="Engagements" />

	<AuthenticatedLayout>
		<template #bits>
			<CommentsModal
				v-if="modalStore.getCommentsModalStatus"
				:activePostID="activePostID"
			/>
		</template>
		<template #header>
			<div>
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">
					Engagements
				</h2>
			</div>
			<div>
				<div class="flex flex-wrap -mx-3"></div>
			</div>
		</template>

		<template #content>
			<section
				class="w-full max-w-full my-6 relative flex flex-col md:flex-row min-w-0 break-words px-4 justify-between items-starts"
			>
				<div>
					<ActiveIGAccountSelector
						:ig_data_fetch_process="ig_data_fetch_process"
						:loadingData="Loading"
					/>
				</div>
			</section>

			<section
				id="top_five"
				class="max-w-full w-full overflow-x-auto"
			></section>
		</template>
	</AuthenticatedLayout>
</template>

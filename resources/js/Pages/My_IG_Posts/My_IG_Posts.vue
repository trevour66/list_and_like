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
	},
});

const associated_user_IG_Biz_posts = ref([]);
const next_page_url = ref("");

const Loading = ref(true);
const LoadingError = ref(false);
const retryCount = ref(0);
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

const handleInfiniteScroll = () => {
	const mainContainer = window.document.querySelector("#main");

	const endOfContainer =
		mainContainer.scrollHeight - mainContainer.scrollTop ===
		mainContainer.clientHeight;

	// console.log(endOfContainer);

	if (endOfContainer && (next_page_url?.value ?? false)) {
		Loading.value = true;
		IGBusinessPostsFetch();
	}
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
	console.log(post_id);

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
	window.document
		.querySelector("#main")
		.addEventListener("scroll", handleInfiniteScroll);

	await IGBusinessPostsFetch();
	hasMounted.value = true;
});
</script>

<template>
	<Head title="My Posts" />

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
					My Posts
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
					/>
				</div>
			</section>

			<div>
				<div
					class="grid lg:grid-cols-2 xl:grid-cols-3 grid-cols-1 pt-6 gap-x-4 gap-y-10 mx-3"
				>
					<template v-if="(associated_user_IG_Biz_posts ?? []).length > 0">
						<div
							v-for="(post, index) in associated_user_IG_Biz_posts"
							:key="index"
						>
							<!-- {{ post }} -->
							<IGBizPost
								:post="post"
								:index="index"
								@goToComments="goToComments(post._id)"
							/>
						</div>
					</template>
				</div>
				<div class="grid grid-cols-1 pt-6 gap-x-4 gap-y-10 mx-3">
					<template
						v-if="!Loading && (associated_user_IG_Biz_posts ?? []).length == 0"
					>
						<div
							class="flex items-center justify-center w-full h-full bg-gray-50"
						>
							<div>
								<p class="text-md font-normal text-gray-500">
									You have no post at the moment
								</p>
							</div>
						</div>
					</template>
				</div>
			</div>

			<div v-if="Loading" class="flex items-center justify-center w-full h-32">
				<InfinityScrollLoader />
			</div>
		</template>
	</AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InfinityScrollLoader from "@/Components/InfinityScrollLoader.vue";
import IGPostRepresentation from "@/Components/IGPostRepresentation.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import IGProfilePost from "@/Services/IGProfilePost";
import usePreferedIgAccountStore from "@/Store/preferedIgAccountStore";
import { useInstagramAccounts } from "@/Composables/useInstagramAccounts";

const props = defineProps({
	IGAccessCodes: {
		type: Array,
	},
	user_lists: {
		type: Array,
		default: [],
	},
});

const associated_user_posts = ref([]);
const next_page_url = ref("");
const preferedIgAccountStore = usePreferedIgAccountStore();
const Loading = ref(true);

const { preferedIGAccDropdownButton, allAccounts, switchAccount } =
	useInstagramAccounts(props);

const handleInfiniteScroll = () => {
	const mainContainer = window.document.querySelector("#main");

	const endOfContainer =
		mainContainer.scrollHeight - mainContainer.scrollTop ===
		mainContainer.clientHeight;

	// console.log(endOfContainer);

	if (endOfContainer) {
		Loading.value = true;
		userPostsFetch();
	}
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
	await IGProfilePost.getCommunityPosts(
		preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_username ?? "",
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

const refreshCurrentView = () => {
	router.reload();
};

const skipPost = (postID) => {
	const index = associated_user_posts.value.findIndex(
		(item) => item.post_id === postID
	);
	if (index !== -1) {
		associated_user_posts.value.splice(index, 1);
	}
};

onMounted(() => {
	window.document
		.querySelector("#main")
		.addEventListener("scroll", handleInfiniteScroll);

	userPostsFetch();
});
</script>

<template>
	<Head title="Community" />

	<AuthenticatedLayout>
		<template #header>
			<div>
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">
					Community
				</h2>
			</div>
			<div>
				<div class="flex flex-wrap -mx-3"></div>
			</div>
		</template>

		<template #content>
			<div>
				<div
					class="grid lg:grid-cols-2 xl:grid-cols-3 grid-cols-1 pt-6 gap-x-4 gap-y-10 mx-3"
				>
					<template v-if="(associated_user_posts ?? []).length > 0">
						<div
							v-for="(post, index) in associated_user_posts"
							:key="post.post_id"
						>
							<!-- {{ post }} -->
							<IGPostRepresentation
								:post="post"
								:index="post.post_id"
								:user_lists="user_lists"
								@IGProfileAddedToAList="refreshCurrentView"
								@postSkipped="skipPost"
							/>
						</div>
					</template>
				</div>
				<div class="grid grid-cols-1 py-10 gap-x-4 gap-y-10 mx-3">
					<template
						v-if="!Loading && (associated_user_posts ?? []).length == 0"
					>
						<div
							class="flex items-center justify-center w-full h-full bg-gray-50"
						>
							<div>
								<p class="text-md font-normal text-gray-500">
									You have no community post at the moment
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

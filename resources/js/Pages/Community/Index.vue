<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InfinityScrollLoader from "@/Components/InfinityScrollLoader.vue";
import IGPostRepresentation from "@/Components/IGPostRepresentation.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import IGProfilePost from "@/Services/IGProfilePost";

defineProps({
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

const Loading = ref(true);

const userAccessToken = usePage().props.auth.user.auth_token;

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

const userPostsFetch = async () => {
	// console.log("response.data");
	await IGProfilePost.getCommunityPosts(userAccessToken, next_page_url.value)
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
		.catch(function (error) {
			// handle error
			console.log(error);
			Loading.value = false;
		});
};

const refreshCurrentView = () => {
	router.reload();
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
			<div
				class="grid lg:grid-cols-2 xl:grid-cols-3 grid-cols-1 pt-6 gap-x-4 gap-y-10 mx-3"
			>
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
				<template v-if="!Loading && (associated_user_posts ?? []).length == 0">
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
			<div v-if="Loading" class="flex items-center justify-center w-full h-32">
				<InfinityScrollLoader />
			</div>
		</template>
	</AuthenticatedLayout>
</template>

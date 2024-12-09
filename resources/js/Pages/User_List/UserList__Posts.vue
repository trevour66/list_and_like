<script setup>
import { Head, useForm, Link, router } from "@inertiajs/vue3";
import { initDropdowns } from "flowbite";
import { onMounted, ref } from "vue";

import usePreferedIgAccountStore from "@/Store/preferedIgAccountStore";
import { useInstagramAccounts } from "@/Composables/useInstagramAccounts";
import InfinityScrollLoader from "@/Components/InfinityScrollLoader.vue";
import IGPostRepresentation from "@/Components/IGPostRepresentation.vue";

import useModalStore from "@/Store/ModalStore";
import { computed } from "vue";
import UserList from "@/Services/UserList";

const modalStore = useModalStore();
const preferedIgAccountStore = usePreferedIgAccountStore();
const instagramAccounts = useInstagramAccounts();

const props = defineProps({
	user_list: {
		type: Object,
		default: {},
		required: true,
	},
});

const Loading = ref(true);
const associated_user_posts = ref([]);
const next_page_url = ref("");

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
	const list_id = props?.user_list?._id;
	await UserList.getUserList_posts(
		list_id,
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

onMounted(() => {
	window.document
		.querySelector("#main")
		.addEventListener("scroll", handleInfiniteScroll);

	userPostsFetch();
});
</script>

<template>
	<div>
		<div
			class="grid lg:grid-cols-2 xl:grid-cols-3 grid-cols-1 pt-6 gap-x-4 gap-y-10 mx-3"
		>
			<template v-if="(associated_user_posts ?? []).length > 0">
				<div v-for="(post, index) in associated_user_posts" :key="index">
					<!-- {{ post }} -->
					<IGPostRepresentation :post="post" :index="index" />
				</div>
			</template>
		</div>
		<div class="grid grid-cols-1 py-10 gap-x-4 gap-y-10 mx-3">
			<template v-if="!Loading && (associated_user_posts ?? []).length == 0">
				<div class="flex items-center justify-center w-full h-full bg-gray-50">
					<div>
						<p class="text-md font-normal text-gray-500">No post found</p>
					</div>
				</div>
			</template>
		</div>
	</div>

	<div v-if="Loading" class="flex items-center justify-center w-full h-32">
		<InfinityScrollLoader />
	</div>
</template>

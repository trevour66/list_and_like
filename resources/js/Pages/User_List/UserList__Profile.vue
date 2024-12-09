<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link, router } from "@inertiajs/vue3";
import { initDropdowns } from "flowbite";
import { onMounted, ref } from "vue";
import AnIGProfile from "@/Components/AnIGProfile.vue";

import usePreferedIgAccountStore from "@/Store/preferedIgAccountStore";
import { useInstagramAccounts } from "@/Composables/useInstagramAccounts";
import InfinityScrollLoader from "@/Components/InfinityScrollLoader.vue";

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
	},
});

const Loading = ref(true);
const ig_profiles = ref([]);
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
		userProfilesFetch();
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

const userProfilesFetch = async () => {
	// console.log("response.data");
	const list_id = props?.user_list?._id;

	await UserList.getUserList_profiles(list_id, next_page_url.value)
		.then(function (response) {
			// console.log(response.data);

			// handle success
			const ig_profiles_data = response?.data?.ig_profiles ?? false;
			const status = response?.data?.status ?? false;

			const ig_profiles_new = ig_profiles_data?.data ?? [];

			next_page_url.value = ig_profiles_data?.next_page_url ?? null;

			if (ig_profiles_new && ig_profiles_new.length > 0) {
				Array.prototype.push.apply(ig_profiles.value, ig_profiles_new);
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
	Loading.value = true;
	ig_profiles.value = [];
	next_page_url.value = "";

	userProfilesFetch();
};

const deleteFromList = async (profile) => {
	// console.log(profile);
	// return

	const ig_profile_id = profile?._id ?? "";
	const list_id = props?.user_list?._id ?? "";

	await UserList.delete_IG_profile_from_list(list_id, ig_profile_id)
		.then(function (response) {
			// console.log(response.data);
			refreshCurrentView();
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

	// if (form.processing || (userListId ?? "") == "") return;

	// form.user_list_id = userListId;

	// form.post(`/my-lists/${userListId}/delete`, {
	// 	onSuccess: (response) => {
	// 		refreshCurrentView();
	// 	},
	// });
};

onMounted(() => {
	window.document
		.querySelector("#main")
		.addEventListener("scroll", handleInfiniteScroll);

	userProfilesFetch();
});
</script>

<template>
	<div>
		<div
			class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 grid-cols-1 pt-6 gap-x-4 gap-y-10 mx-3"
		>
			<div v-for="(profile, index) in ig_profiles" :key="index">
				<AnIGProfile
					:profile="profile"
					:disable_view_post_button="true"
					:showed_from_A_list="true"
					@deleteFromList="deleteFromList"
				/>
			</div>
		</div>
		<div class="grid grid-cols-1 py-10 gap-x-4 gap-y-10 mx-3">
			<template v-if="!Loading && (ig_profiles ?? []).length == 0">
				<div class="flex items-center justify-center w-full h-full bg-gray-50">
					<div>
						<p class="text-md font-normal text-gray-500">
							No Profile has been added to this list
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

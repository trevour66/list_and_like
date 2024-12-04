<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InfinityScrollLoader from "@/Components/InfinityScrollLoader.vue";
import { Head, usePage, useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import IGProfile from "@/Services/IGProfile";

import { initDropdowns } from "flowbite";
import AnIGProfile from "@/Components/AnIGProfile.vue";

import usePreferedIgAccountStore from "@/Store/preferedIgAccountStore";

import useModalStore from "@/Store/ModalStore";
import IGProfilePostsModal from "@/Components/modals/IGProfilePostsModal.vue";
import { useInstagramAccounts } from "@/Composables/useInstagramAccounts";

const modalStore = useModalStore();
const preferedIgAccountStore = usePreferedIgAccountStore();
const instagramAccounts = useInstagramAccounts();

defineProps({
	user_lists: {
		type: Array,
		default: [],
	},
});

const ig_profiles = ref([]);
const next_page_url = ref("");
const search___next_page_url = ref("");
const search___active = ref(false);

const Loading = ref(true);

const miniVersionActive = usePage().props.mini_version ?? false;

const ig_handle = ref("");
const ig_username_search = ref("");

const goToIGProfilePosts = (ig_handle_passedThrough) => {
	// console.log(ig_handle_passedThrough);

	if (!(ig_handle_passedThrough ?? false)) return;

	ig_handle.value = ig_handle_passedThrough;
	modalStore.toggel_IGProfilePost_Modal(true);
};

const handleInfiniteScroll = () => {
	const mainContainer = window.document.querySelector("#main");

	const endOfContainer =
		mainContainer.scrollHeight - mainContainer.scrollTop ===
		mainContainer.clientHeight;

	// console.log(endOfContainer);

	if (endOfContainer) {
		Loading.value = true;
		fetchProfiles();
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

const fetchProfiles = async () => {
	// console.log("response.data");
	await IGProfile.getProfiles(next_page_url.value)
		.then(function (response) {
			// console.log(response);

			// return;
			const ig_profiles_data = response?.data?.ig_profiles ?? false;
			const status = response?.data?.status ?? false;

			const profiles = ig_profiles_data?.data ?? [];
			// const prev_cursor = ig_profiles_data?.prev_cursor ?? null;

			next_page_url.value = ig_profiles_data?.next_page_url ?? null;

			if (profiles && profiles.length > 0) {
				Array.prototype.push.apply(ig_profiles.value, profiles);
			}

			initDropdowns();

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

const searchForIGUsername = async () => {
	// console.log("response.data");
	ig_profiles.value = [];
	Loading.value = true;

	await IGProfile.searchProfiles(
		search___next_page_url.value,
		ig_username_search.value
	)
		.then(function (response) {
			// console.log(response);

			// return;
			const ig_profiles_data = response?.data?.ig_profiles ?? false;
			const status = response?.data?.status ?? false;

			const profiles = ig_profiles_data?.data ?? [];
			// const prev_cursor = ig_profiles_data?.prev_cursor ?? null;

			search___next_page_url.value = ig_profiles_data?.next_page_url ?? null;

			if (profiles && profiles.length > 0) {
				Array.prototype.push.apply(ig_profiles.value, profiles);
			}

			initDropdowns();
			search___active.value = true;
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

const backToMainView = async () => {
	// console.log("response.data");
	ig_profiles.value = [];
	Loading.value = true;
	next_page_url.value = "";
	await fetchProfiles();
	search___active.value = false;
};

onMounted(async () => {
	window.document
		.querySelector("#main")
		.addEventListener("scroll", handleInfiniteScroll);

	await fetchProfiles();

	initDropdowns();
});
</script>

<template>
	<Head title="IG Profiles" />

	<AuthenticatedLayout>
		<template #bits>
			<IGProfilePostsModal
				v-if="modalStore.get_IGProfilePost_ModalStatus"
				:ig_handle="ig_handle"
				:business_account_id="
					preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_username ??
					''
				"
			/>
		</template>

		<template #header>
			<div>
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">
					IG Profiles
				</h2>
			</div>
			<div class="sm:min-w-full md:min-w-[300px]">
				<form @submit.prevent="">
					<label
						for="search"
						class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white"
						>Search</label
					>
					<div class="relative">
						<input
							v-model="ig_username_search"
							type="search"
							id="search"
							class="block w-full p-4 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
							placeholder="Enter IG Username"
							required
						/>
						<div class="absolute end-2.5 bottom-2.5 flex items-center gap-x-2">
							<button
								@click.prevent="searchForIGUsername"
								type="submit"
								class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
							>
								Search
							</button>
							<button
								v-if="search___active"
								@click.prevent="backToMainView"
								class="inline-flex justify-center px-2.5 py-1 text-red-600 rounded-full cursor-pointer bg-red-100 hover:bg-red-200"
							>
								<i class="fas fa-times text-xl"></i>
								<span class="sr-only">cancel</span>
							</button>
						</div>
					</div>
				</form>
			</div>
		</template>

		<template #content>
			<!-- {{ ig_profiles }} -->
			<div
				class="grid lg:grid-cols-3 xl:grid-cols-4 grid-cols-1 pt-6 gap-x-4 gap-y-3 mx-3 auto-rows-fr"
			>
				<div v-for="(profile, index) in ig_profiles" :key="index">
					<AnIGProfile
						:profile="profile"
						:user_lists="user_lists"
						@go-to-i-g-profile-posts="goToIGProfilePosts"
					/>
				</div>
			</div>
			<template v-if="!Loading && (ig_profiles ?? []).length == 0">
				<div
					class="flex items-center justify-center w-full h-full bg-gray-50 mt-10"
				>
					<div>
						<p class="text-md font-normal text-gray-500">
							You are not tracking any IG profiles at the moment
						</p>
					</div>
				</div>
			</template>
			<div v-if="Loading" class="flex items-center justify-center w-full h-32">
				<InfinityScrollLoader />
			</div>
		</template>
	</AuthenticatedLayout>
</template>

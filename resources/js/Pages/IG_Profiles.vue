<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InfinityScrollLoader from "@/Components/InfinityScrollLoader.vue";
import { Head, usePage, useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import IGProfile from "@/Services/IGProfile";

import { initDropdowns } from "flowbite";
import AnIGProfile from "@/Components/AnIGProfile.vue";

defineProps({
	user_lists: {
		type: Array,
		default: [],
	},
});

const ig_profiles = ref([]);
const next_page_url = ref("");

const Loading = ref(true);

const miniVersionActive = usePage().props.mini_version ?? false;

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
		.catch(function (error) {
			// handle error
			console.log(error);
			Loading.value = false;
		});
};

const ig_profile_is_already_in_list = (user_list_ids, list_id) => {
	return (user_list_ids ?? []).find((item) => item == list_id) ?? false;
};

const addUserToList = (list_id, ig_handle, user_list_ids) => {
	if (ig_profile_is_already_in_list(user_list_ids, list_id)) return;

	const form = useForm({
		ig_handle: ig_handle,
	});

	form.post(`/my-lists/${list_id}/add-profile`, {
		onFinish: () => {
			window.alert("IG profile added to list");
			form.reset();
		},
	});
};

// onUpdated(() => {

// });

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
		<template #header>
			<div>
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">
					IG Profiles
				</h2>
			</div>
			<div>
				<div class="flex flex-wrap -mx-3">
					<div v-show="false" class="flex items-center md:ml-auto md:pr-4">
						<div
							class="relative flex flex-wrap items-stretch w-full transition-all rounded-lg ease"
						>
							<input
								type="text"
								class="pl-3 text-sm focus:shadow-primary-outline ease w-1/100 leading-5.6 relative -ml-px block min-w-0 flex-auto rounded-lg border border-solid border-gray-300 dark:bg-slate-850 dark:text-white bg-white bg-clip-padding py-2 pr-9 text-gray-700 transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none focus:transition-shadow"
								placeholder="Type here..."
							/>
							<span
								class="text-md ease leading-5.6 absolute right-0 z-50 -ml-px flex h-full items-center whitespace-nowrap rounded-lg rounded-tr-none rounded-br-none border border-r-0 border-transparent bg-transparent py-2 px-2.5 text-center font-normal text-slate-500 transition-all"
							>
								<i class="fas fa-search"></i>
							</span>
						</div>
					</div>
				</div>
			</div>
		</template>

		<template #content>
			<!-- {{ ig_profiles }} -->
			<div
				class="grid lg:grid-cols-3 xl:grid-cols-4 grid-cols-1 pt-6 gap-x-4 gap-y-3 mx-3 auto-rows-fr"
			>
				<div v-for="(profile, index) in ig_profiles" :key="index">
					<AnIGProfile :profile="profile" :user_lists="user_lists" />
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

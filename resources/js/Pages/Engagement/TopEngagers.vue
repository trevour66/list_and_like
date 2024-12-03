<script setup>
import InfinityScrollLoader from "@/Components/InfinityScrollLoader.vue";
import { Head, usePage, useForm } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";
import UserEngagement from "@/Services/UserEngagement";
import usePreferedIgAccountStore from "@/Store/preferedIgAccountStore";

import AnIGProfile_TopEngager from "@/Components/AnIGProfile_TopEngager.vue";

defineProps({
	user_lists: {
		type: Array,
		default: [],
	},
});

const emits = defineEmits(["goToIGProfilePosts"]);

const ig_profiles = ref([]);

const Loading = ref(true);
const preferedIgAccountStore = usePreferedIgAccountStore();

const reAuth = async () => {
	await axios
		.get("/sanctum/csrf-cookie")
		.then((res) => {})
		.catch((err) => {
			console.log("Error reauth");
			console.log(err);
		});
};

const fetchTopFive = async () => {
	// console.log("response.data");
	await UserEngagement.getTopFive(
		preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_username ?? ""
	)
		.then(function (response) {
			// console.log(response);

			const status = response?.data?.status ?? false;
			const data = response?.data?.data ?? [];

			if (data && data.length > 0) {
				Array.prototype.push.apply(ig_profiles.value, data);
			}

			Loading.value = false;
		})
		.catch(async (error) => {
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

watch(
	preferedIgAccountStore.get_preferedIgBussinessAccount,
	async (newValue) => {
		// console.log(newValue);

		// console.log("called");
		let IG_username =
			preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_username ?? "";

		if (IG_username !== "") {
			ig_profiles.value = [];
			await fetchTopFive();
		}
	}
);

const passthrough_goToIGProfilePosts = (ig_handle) => {
	// console.log(ig_handle);
	if ((ig_handle ?? "") === "") return;

	emits("goToIGProfilePosts", ig_handle);
};

onMounted(async () => {
	await fetchTopFive();
});
</script>

<template>
	<section>
		<!-- {{ ig_profiles }} -->
		<div
			class="w-full max-w-full overflow-x-auto flex dark:bg-gray-800 dark:border-gray-700 py-4 px-4 gap-x-4"
		>
			<div v-for="(profile, index) in ig_profiles" :key="index">
				<AnIGProfile_TopEngager
					:profile="profile"
					:user_lists="user_lists"
					@goToIGProfilePosts="passthrough_goToIGProfilePosts"
				/>
			</div>
		</div>
		<template v-if="!Loading && (ig_profiles ?? []).length == 0">
			<div
				class="flex items-center justify-center w-full h-full bg-gray-50 mt-10"
			>
				<div>
					<p class="text-md font-normal text-gray-500">
						You does not have an item in this list
					</p>
				</div>
			</div>
		</template>
		<div v-if="Loading" class="flex items-center justify-center w-full h-32">
			<InfinityScrollLoader />
		</div>
	</section>
</template>

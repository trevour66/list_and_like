<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Analytics from "./parts/analytics.vue";
import { useForm, Head, router, Link } from "@inertiajs/vue3";
import IG_black from "@/Components/icons/IG_black.vue";
import { onMounted, ref, reactive, computed } from "vue";
import { initDropdowns } from "flowbite";

const props = defineProps({
	ig_data_fetch_process: {
		type: Array,
	},
});

const preferedIgBussinessAccount = reactive({
	IG_username: "",
	most_recent_sync: null,
});

const form = useForm({
	IG_account_id: "",
});

const allAccounts = computed(() => {
	const allBussinesAccounts = props?.ig_data_fetch_process ?? [];

	if (allBussinesAccounts.length > 0) {
		return allBussinesAccounts;
	}

	return [];
});

const resync_data = () => {
	if ((preferedIgBussinessAccount?.IG_account_id ?? "") == "") {
		return;
	}

	form.IG_account_id = preferedIgBussinessAccount.IG_account_id;

	form.post("/sync-data", {
		onSuccess: (response) => {
			// console.log("Form submitted successfully:", response);
			router.reload();
			getPreferedIgBussinessAccount();
		},
	});
};

const getPreferedIgBussinessAccount = () => {
	const allBussinesAccounts = props?.ig_data_fetch_process ?? [];

	if (allBussinesAccounts.length > 0) {
		preferedIgBussinessAccount.IG_username =
			allBussinesAccounts[0]?.IG_username;

		preferedIgBussinessAccount.IG_account_id =
			allBussinesAccounts[0]?.IG_account_id;

		preferedIgBussinessAccount.most_recent_sync =
			allBussinesAccounts[0]?.IG_data_fetch_process ?? null;
	}

	return null;
};

const getLastSyncedDate = computed(() => {
	const preferedIgBussinessAcc =
		preferedIgBussinessAccount?.most_recent_sync ?? null;

	if (preferedIgBussinessAcc == null) return "";

	const date = preferedIgBussinessAcc?.created_at ?? null;

	if (date == null) return "";

	const dateString = new Date(date);

	return dateString.toLocaleDateString();
});

const isMostRecentSyncStillInProcess = computed(() => {
	const preferedIgBussinessAcc_most_recent_sync =
		preferedIgBussinessAccount?.most_recent_sync ?? null;
	const preferedIgBussinessAcc_IG_account_id =
		preferedIgBussinessAccount?.IG_account_id ?? "";

	// preferedIgBussinessAccount?.IG_account_id

	if (
		preferedIgBussinessAcc_most_recent_sync == null &&
		preferedIgBussinessAcc_IG_account_id === ""
	)
		return null;

	if (
		(preferedIgBussinessAcc_most_recent_sync?.IDFP_status ?? "") == "processing"
	) {
		return true;
	}

	return false;
});

onMounted(() => {
	initDropdowns();
	getPreferedIgBussinessAccount();
});
</script>

<template>
	<Head title="Dashboard" />

	<AuthenticatedLayout>
		<template #content>
			<section
				class="w-full max-w-full my-6 relative flex flex-col md:flex-row min-w-0 break-words px-4 justify-between items-starts"
			>
				<div class="flex-1">
					<div
						v-if="preferedIgBussinessAccount?.IG_username"
						class="inline-flex flex-col items-center gap-y-2"
					>
						<p
							class="text-sm font-normal text-gray-500 dark:text-gray-400 my-2"
						>
							Active IG Business Account:
						</p>

						<!-- {{ allAccounts }} -->
						<button
							id="dropdownSelectPreferedIGAccButton"
							data-dropdown-toggle="dropdownSelectPreferedIGAcc"
							class="flex items-center text-md pe-1 font-medium text-gray-900 rounded-full hover:text-[#f24b54] md:me-0 focus:ring-4 focus:ring-gray-100"
							type="button"
						>
							<IG_black :class="`w-6 h-6 me-2`" />
							{{ preferedIgBussinessAccount?.IG_username }}
							<svg
								class="w-2.5 h-2.5 ms-3"
								aria-hidden="true"
								xmlns="http://www.w3.org/2000/svg"
								fill="none"
								viewBox="0 0 10 6"
							>
								<path
									stroke="currentColor"
									stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="m1 1 4 4 4-4"
								/>
							</svg>
						</button>

						<!-- Dropdown menu -->
						<div
							id="dropdownSelectPreferedIGAcc"
							class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600"
						>
							<ul
								class="py-2 text-sm text-gray-700 dark:text-gray-200"
								aria-labelledby="dropdownInformdropdownSelectPreferedIGAccButtonationButton"
							>
								<li v-for="(acc, index) in allAccounts" :key="index">
									<div
										class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white hover:cursor-pointer"
									>
										{{ acc.IG_username }}
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="">
					<template v-if="isMostRecentSyncStillInProcess !== null">
						<!-- {{ isMostRecentSyncStillInProcess }} -->
						<button
							v-if="isMostRecentSyncStillInProcess === true"
							class="float-right py-2 px-4 font-medium text-gray-900 focus:outline-none bg-white rounded-lg border-2 border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 hover:cursor-not-allowed animate-pulse"
						>
							<p class="text-gray-700 animate-bounce">. . .</p>
						</button>
						<button
							v-else
							@click="resync_data"
							class="float-right py-2 px-4 font-medium text-gray-900 focus:outline-none bg-white rounded-lg border-2 border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100"
						>
							<p class="text-black">sync data</p>
						</button>

						<div class="clear-both"></div>

						<p
							v-if="
								(preferedIgBussinessAccount?.most_recent_sync ?? null) !=
									null && isMostRecentSyncStillInProcess === false
							"
							class="text-sm font-normal text-gray-500 dark:text-gray-400 my-2"
						>
							Last sucessfully synced on {{ getLastSyncedDate }}
						</p>
					</template>
				</div>
			</section>

			<section class="my-6" v-if="preferedIgBussinessAccount?.IG_username">
				<Analytics :businessAccountUsed="preferedIgBussinessAccount" />
			</section>
			<section v-else>
				<!-- component -->
				<div class="flex items-center justify-center min-h-32">
					<div class="rounded-lg px-16 my-24 w-[50%]">
						<h3 class="my-4 text-center text-3xl font-semibold text-gray-700">
							No IG Bussiness Account connected!!
						</h3>
						<p class="text-center font-normal text-gray-600">
							To make the most of our application, it's essential to connect
							your Instagram Business account. Without this connection, some
							features and functionalities will be limited. Please take a moment
							to link your Instagram Business account to unlock all the tools
							and insights that can help you grow your online presence!
						</p>

						<div class="w-full inline-flex items-center justify-center my-3">
							<Link
								class="inline-flex items-center justify-center px-5 py-2.5 mt-4 mb-0 font-bold leading-normal text-center text-white align-middle transition-all bg-[#f24b54] border-0 rounded-lg cursor-pointer active:opacity-85 hover:shadow-xs text-sm ease-in tracking-tight-rem shadow-md bg-150 bg-x-25"
								:href="route('profile.edit')"
							>
								Add new IG bussines account
							</Link>
						</div>
					</div>
				</div>
			</section>
		</template>
	</AuthenticatedLayout>
</template>

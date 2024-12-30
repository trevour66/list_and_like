<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Analytics from "./parts/analytics.vue";
import { useForm, Head, router, Link } from "@inertiajs/vue3";
import { onMounted, ref, reactive, computed, watchEffect } from "vue";

import ActiveIGAccountSelector from "@/Components/ActiveIGAccountSelector.vue";
import usePreferedIgAccountStore from "@/Store/preferedIgAccountStore";
const preferedIgAccountStore = usePreferedIgAccountStore();

const props = defineProps({
	ig_data_fetch_process: {
		type: Array,
	},
});

const loadingData = ref(false);

const form = useForm({
	IG_account_id: "",
});

const resync_data = () => {
	if (
		(preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_account_id ??
			"") == ""
	) {
		return;
	}

	form.IG_account_id =
		preferedIgAccountStore.get_preferedIgBussinessAccount.IG_account_id;

	form.post("/sync-data", {
		onSuccess: (response) => {
			// console.log("Form submitted successfully:", response);
			window.location.reload();
		},
	});
};

const getLastSyncedDate = computed(() => {
	const preferedIgBussinessAcc =
		preferedIgAccountStore.get_preferedIgBussinessAccount?.most_recent_sync ??
		null;
	// console.log("called");
	if (preferedIgBussinessAcc == null) return "";

	const date = preferedIgBussinessAcc?.created_at ?? null;

	if (date == null) return "";

	const dateString = new Date(date);

	return dateString.toLocaleDateString();
});

const isMostRecentSyncStillInProcess = computed(() => {
	const preferedIgBussinessAcc_most_recent_sync =
		preferedIgAccountStore.get_preferedIgBussinessAccount?.most_recent_sync ??
		null;
	const preferedIgBussinessAcc_IG_account_id =
		preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_account_id ?? "";

	// preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_account_id
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
</script>

<template>
	<Head title="Dashboard" />

	<AuthenticatedLayout>
		<template #content>
			<section
				class="w-full max-w-full my-6 relative flex flex-col md:flex-row min-w-0 break-words px-4 justify-between items-starts"
			>
				<div class="flex-1">
					<ActiveIGAccountSelector
						:ig_data_fetch_process="ig_data_fetch_process"
						:loadingData="loadingData"
					/>
				</div>
				<div class="">
					<!-- {{ isMostRecentSyncStillInProcess }} -->
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
								(preferedIgAccountStore.get_preferedIgBussinessAccount
									?.most_recent_sync ?? false) != (null || false) &&
								isMostRecentSyncStillInProcess === false
							"
							class="text-sm font-normal text-gray-500 dark:text-gray-400 my-2"
						>
							Last sucessfully synced on {{ getLastSyncedDate }}
						</p>
					</template>
				</div>
			</section>

			<section
				class="my-6"
				v-if="
					preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_username
				"
			>
				<Analytics
					:businessAccountUsed="
						preferedIgAccountStore.get_preferedIgBussinessAccount
					"
					@loading_starts="loadingData = true"
					@loading_finishes="loadingData = false"
				/>
			</section>
			<section v-else>
				<!-- component -->
				<div class="flex items-center justify-center min-h-32">
					<div class="rounded-lg px-16 my-24 w-[50%]">
						<h3 class="my-4 text-center text-3xl font-semibold text-gray-700">
							Welcome to List & Like!
						</h3>
						<p class="text-center font-normal text-gray-600">
							The first step is to connect your Instagram Business Account.
						</p>

						<p class="text-center font-normal text-gray-600">
							To make the most of our application, it's essential to connect
							your Instagram Business account. Without that connection, some
							features and functionalities will be limited. Please take a moment
							to link your Instagram Business account to unlock all the tools
							and insights that can help you grow your business!
						</p>

						<div class="w-full inline-flex items-center justify-center my-3">
							<Link
								class="inline-flex items-center justify-center px-5 py-2.5 mt-4 mb-0 font-bold leading-normal text-center text-white align-middle transition-all bg-[#f24b54] border-0 rounded-lg cursor-pointer active:opacity-85 hover:shadow-xs text-sm ease-in tracking-tight-rem shadow-md bg-150 bg-x-25"
								:href="route('profile.edit')"
							>
								Add Your Instagram Business Account
							</Link>
						</div>
					</div>
				</div>
			</section>
		</template>
	</AuthenticatedLayout>
</template>

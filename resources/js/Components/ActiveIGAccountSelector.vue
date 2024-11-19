<script setup>
import { useForm, Head, router, Link } from "@inertiajs/vue3";
import IG_black from "@/Components/icons/IG_black.vue";
import { onMounted, ref, reactive, computed } from "vue";
import { initDropdowns } from "flowbite";
import { useCookies } from "@vueuse/integrations/useCookies";
import { watchEffect } from "vue";

import usePreferedIgAccountStore from "@/Store/preferedIgAccountStore";

const preferedIgAccountStore = usePreferedIgAccountStore();

const props = defineProps({
	ig_data_fetch_process: {
		type: Array,
	},
});

const cookies_preferedIgBussinessAccount = useCookies([
	"preferedIgBussinessAccount",
]);

// console.log(
// 	cookies_preferedIgBussinessAccount.get("preferedIgBussinessAccount")
// );

const savedPreferedAccount_onInit =
	cookies_preferedIgBussinessAccount.get("preferedIgBussinessAccount") ?? null;

if (
	typeof savedPreferedAccount_onInit !== "undefined" &&
	savedPreferedAccount_onInit !== null &&
	(savedPreferedAccount_onInit?.IG_username ?? "") !== ""
) {
	const allBussinesAccounts = props?.ig_data_fetch_process ?? [];

	// console.log(allBussinesAccounts);

	const businessAccountOfConcern = allBussinesAccounts.find((elem) => {
		return (elem?.IG_username ?? "") == savedPreferedAccount_onInit.IG_username;
	});

	// console.log(businessAccountOfConcern);

	savedPreferedAccount_onInit.most_recent_sync =
		businessAccountOfConcern?.IG_data_fetch_process ?? null;

	// console.log(savedPreferedAccount_onInit);

	cookies_preferedIgBussinessAccount.set(
		"preferedIgBussinessAccount",
		JSON.stringify(savedPreferedAccount_onInit)
	);
}

const allAccounts = computed(() => {
	const allBussinesAccounts = props?.ig_data_fetch_process ?? [];

	if (allBussinesAccounts.length > 0) {
		return allBussinesAccounts;
	}

	return [];
});

const getPreferedIgBussinessAccount = () => {
	try {
		const allBussinesAccounts = props?.ig_data_fetch_process ?? [];

		if (allBussinesAccounts.length === 0) return null;

		if (allBussinesAccounts.length === 1) {
			preferedIgAccountStore.set_preferedIgBussinessAccount(
				allBussinesAccounts[0]?.IG_username ?? false,
				allBussinesAccounts[0]?.IG_data_fetch_process ?? null,
				allBussinesAccounts[0]?.IG_account_id ?? false
			);
		}

		if (allBussinesAccounts.length > 1) {
			let savedPreferedAccount =
				cookies_preferedIgBussinessAccount.get("preferedIgBussinessAccount") ??
				null;

			if (
				typeof savedPreferedAccount === "undefined" ||
				savedPreferedAccount == null
			) {
				preferedIgAccountStore.set_preferedIgBussinessAccount(
					allBussinesAccounts[0]?.IG_username ?? false,
					allBussinesAccounts[0]?.IG_data_fetch_process ?? null,
					allBussinesAccounts[0]?.IG_account_id ?? false
				);

				return;
			}

			preferedIgAccountStore.set_preferedIgBussinessAccount(
				savedPreferedAccount?.IG_username ?? false,
				savedPreferedAccount?.most_recent_sync ?? null,
				savedPreferedAccount?.IG_account_id ?? false
			);
		}
	} catch (error) {
		console.log(error);
		return null;
	}
};

const switchAccount = async (acc) => {
	if (loadingData.value) return;
	// console.log(acc);

	const IG_account_id = acc?.IG_account_id ?? false;
	const IG_data_fetch_process = acc?.IG_data_fetch_process ?? false;
	const IG_username = acc?.IG_username ?? false;

	if (!IG_account_id || !IG_username) {
		return;
	}

	if (
		preferedIgAccountStore.get_preferedIgBussinessAccount.IG_username ===
		IG_username
	)
		return;

	preferedIgAccountStore.set_preferedIgBussinessAccount(
		IG_username ?? false,
		IG_data_fetch_process ?? null,
		IG_account_id ?? false
	);

	preferedIGAccDropdownButton.value.click();
};

onMounted(() => {
	initDropdowns();
	getPreferedIgBussinessAccount();
});
</script>

<template>
	<div
		v-if="preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_username"
		class="inline-flex flex-col gap-y-2"
	>
		<p class="text-sm font-normal text-gray-500 dark:text-gray-400 my-2">
			Active IG Business Account:
		</p>

		<!-- {{ allAccounts }} -->
		<button
			id="preferedIGAccDropdownButton"
			ref="preferedIGAccDropdownButton"
			data-dropdown-toggle="PreferedIGAccDropdown"
			class="flex items-center text-md pe-1 font-medium text-gray-900 rounded-full hover:text-[#f24b54] md:me-0 focus:ring-4 focus:ring-gray-100"
			type="button"
		>
			<IG_black :class="`w-6 h-6 me-2`" />
			{{ preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_username }}
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
			id="PreferedIGAccDropdown"
			class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600"
		>
			<ul
				class="py-2 text-sm text-gray-700 dark:text-gray-200"
				aria-labelledby="dropdownInformpreferedIGAccDropdownButtonationButton"
			>
				<li v-for="(acc, index) in allAccounts" :key="index">
					<div
						:class="{
							'bg-gray-100 hover:cursor-not-allowed':
								((acc?.IG_username ?? '') !== '' &&
									(preferedIgAccountStore.get_preferedIgBussinessAccount
										.IG_username === acc?.IG_username ??
										'')) ||
								loadingData,
							'hover:cursor-pointer':
								(acc?.IG_username ?? '') !== '' &&
								(preferedIgAccountStore.get_preferedIgBussinessAccount
									.IG_username !== acc?.IG_username ??
									'') &&
								!loadingData,
						}"
						@click="switchAccount(acc)"
						class="block px-4 py-2 hover:bg-gray-100"
					>
						{{ acc.IG_username }}
					</div>
				</li>
			</ul>
		</div>
	</div>
</template>

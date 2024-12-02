import { ref, reactive, computed, onMounted } from "vue";
import { useCookies } from "@vueuse/integrations/useCookies";
import { initDropdowns } from "flowbite";
import usePreferedIgAccountStore from "@/Store/preferedIgAccountStore";

export function useInstagramAccounts(props) {
	const preferedIgAccountStore = usePreferedIgAccountStore();
	const cookies = useCookies(["preferedIgBussinessAccount"]);

	const preferedIGAccDropdownButton = ref(null);
	const allAccounts = computed(() => props?.ig_data_fetch_process ?? []);

	const initializeSavedAccount = () => {
		const savedAccount = cookies.get("preferedIgBussinessAccount") ?? null;

		// console.log(savedAccount);

		preferedIgAccountStore.set_preferedIgBussinessAccount(
			savedAccount.IG_username ?? false,
			savedAccount.IG_data_fetch_process ?? null,
			savedAccount.IG_account_id ?? false
		);

		if (savedAccount && (savedAccount?.IG_username ?? "") !== "") {
			const account = allAccounts.value.find(
				(elem) => elem?.IG_username === savedAccount.IG_username
			);

			if (account) {
				savedAccount.most_recent_sync = account?.IG_data_fetch_process ?? null;
				cookies.set("preferedIgBussinessAccount", JSON.stringify(savedAccount));
			}
		}
	};

	const getPreferedIgBussinessAccount = () => {
		try {
			if (allAccounts.value.length === 0) return;

			if (allAccounts.value.length === 1) {
				const account = allAccounts.value[0];
				preferedIgAccountStore.set_preferedIgBussinessAccount(
					account?.IG_username ?? false,
					account?.IG_data_fetch_process ?? null,
					account?.IG_account_id ?? false
				);
				return;
			}

			const savedAccount = cookies.get("preferedIgBussinessAccount") ?? null;

			if (!savedAccount) {
				const account = allAccounts.value[0];
				preferedIgAccountStore.set_preferedIgBussinessAccount(
					account?.IG_username ?? false,
					account?.IG_data_fetch_process ?? null,
					account?.IG_account_id ?? false
				);
				return;
			}

			preferedIgAccountStore.set_preferedIgBussinessAccount(
				savedAccount?.IG_username ?? false,
				savedAccount?.most_recent_sync ?? null,
				savedAccount?.IG_account_id ?? false
			);
		} catch (error) {
			console.error("Error fetching preferred Instagram account:", error);
		}
	};

	const switchAccount = (account) => {
		if (props.loadingData) return;

		const { IG_username, IG_data_fetch_process, IG_account_id } = account ?? {};

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

		preferedIGAccDropdownButton.value?.click();
	};

	onMounted(() => {
		initDropdowns();
		initializeSavedAccount();
		getPreferedIgBussinessAccount();
	});

	return {
		preferedIGAccDropdownButton,
		allAccounts,
		switchAccount,
	};
}

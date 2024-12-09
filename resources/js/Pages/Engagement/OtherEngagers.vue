<script setup>
import ExpandIcon from "@/Components/ExpandIcon.vue";
import InfinityScrollLoader from "@/Components/InfinityScrollLoader.vue";
import { Head, usePage, useForm } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";
import UserEngagement from "@/Services/UserEngagement";
import usePreferedIgAccountStore from "@/Store/preferedIgAccountStore";
import { onUpdated } from "vue";

const emits = defineEmits(["goToIGProfilePosts"]);

const Loading = ref(true);
const next_page_url = ref("");

const preferedIgAccountStore = usePreferedIgAccountStore();

const tableHeaders = ref(["Username", "Interaction Count"]);
const tableRows = ref([]);
const hasMounted = ref(false);

const other_engagers = ref(null);

const reAuth = async () => {
	await axios
		.get("/sanctum/csrf-cookie")
		.then((res) => {})
		.catch((err) => {
			console.log("Error reauth");
			console.log(err);
		});
};

const fetchOthers = async () => {
	// console.log("response.data");
	await UserEngagement.getOtherProfiles(
		preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_username ?? "",
		next_page_url.value
	)
		.then(function (response) {
			// console.log(response);

			const status = response?.data?.status ?? false;
			const data = response?.data?.data?.data ?? [];

			if (Array.isArray(data) && data.length > 0) {
				Array.prototype.push.apply(tableRows.value, data);
			} else if (typeof data === "object") {
				Array.prototype.push.apply(tableRows.value, Object.values(data));
			}

			next_page_url.value = response?.data?.data?.next_page_url ?? "";

			// console.log(next_page_url.value);
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

const passthrough_goToIGProfilePosts = (ig_handle) => {
	// console.log(ig_handle);
	if ((ig_handle ?? "") === "") return;

	emits("goToIGProfilePosts", ig_handle);
};

const handleInfiniteScroll = () => {
	// console.log("hhh");
	const mainContainer = window.document.querySelector("#other_engagers");

	const endOfContainer =
		mainContainer.scrollHeight - mainContainer.scrollTop ===
		mainContainer.clientHeight;

	// console.log(endOfContainer);

	if (endOfContainer) {
		Loading.value = true;
		fetchOthers();
	}
};

watch(other_engagers, (newval) => {
	// console.log(newval);

	if (newval) {
		newval.addEventListener("scroll", handleInfiniteScroll);
	}
});

watch(
	preferedIgAccountStore.get_preferedIgBussinessAccount,
	async (newValue) => {
		// console.log(newValue);

		// console.log("called");
		let IG_username =
			preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_username ?? "";

		if (IG_username !== "" && hasMounted.value) {
			tableRows.value = [];
			next_page_url.value = "";

			await fetchOthers();
		}
	}
);

onMounted(async () => {
	// window.document
	// 	.querySelector("#other_engagers") // from parent
	// 	.addEventListener("scroll", handleInfiniteScroll);

	await fetchOthers();
	hasMounted.value = true;
});
</script>

<template>
	<div class="relative shadow-md sm:rounded-lg p-2 bg-white">
		<template v-if="(tableRows ?? []).length == 0">
			<div
				class="flex items-center justify-center w-full h-full min-h-32 bg-gray-50 mt-10"
			>
				<div>
					<p class="text-md font-normal text-gray-500">
						You do not have an item in this list
					</p>
				</div>
			</div>
		</template>

		<template v-else>
			<div class="max-w-full overflow-x-auto">
				<div
					class="max-h-[600px] overflow-y-auto"
					id="other_engagers"
					ref="other_engagers"
				>
					<table
						class="table-auto w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
					>
						<thead class="sticky top-0 bg-gray-100">
							<tr>
								<th
									v-for="(header, index) in tableHeaders"
									scope="col"
									class="font-bold px-6 py-3"
									:class="{
										'bg-gray-50': index % 2 !== 1,
										'bg-white': index % 2 === 1,
									}"
								>
									{{ header }}
								</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="row in tableRows" class="border-b border-gray-200">
								<td
									scope="row"
									class="px-6 py-4 font-medium text-gray-600 whitespace-nowrap bg-gray-50"
								>
									<div
										class="inline-flex flex-row gap-x-4 items-center justify-center"
									>
										<span>
											{{ row?.ig_handle ?? "" }}
										</span>
										<span>
											<div
												@click="
													passthrough_goToIGProfilePosts(row?.ig_handle ?? '')
												"
												class="p-1 rounded-lg border-2 shadow-md hover:shadow-sm hover:cursor-pointer"
											>
												<ExpandIcon />
											</div>
										</span>
									</div>
								</td>

								<td class="px-6 py-4 bg-white">{{ row?.postCount ?? "" }}</td>
							</tr>
							<tr v-if="(tableRows ?? []).length == 0">
								<div class="flex items-center justify-center w-full h-20">
									No data
								</div>
							</tr>
							<tr v-if="Loading">
								<div class="flex items-center justify-center w-full h-20">
									<InfinityScrollLoader />
								</div>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</template>
	</div>
</template>

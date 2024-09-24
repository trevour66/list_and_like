<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head } from "@inertiajs/vue3";
import IG_black from "@/Components/icons/IG_black.vue";
import { onMounted, ref, reactive } from "vue";
import { initDropdowns } from "flowbite";
import { computed } from "vue";

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
		true;
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
		<template #header>
			<div class="flex-1">
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">
					Dashboard
				</h2>

				<!-- {{ form }}
				{{ preferedIgBussinessAccount.most_recent_sync }} -->
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
							(preferedIgBussinessAccount?.most_recent_sync ?? null) != null &&
							isMostRecentSyncStillInProcess === false
						"
						class="text-sm font-normal text-gray-500 dark:text-gray-400 my-2"
					>
						Last sucessfully synced on {{ getLastSyncedDate }}
					</p>
				</template>
			</div>
		</template>

		<template #content>
			<div class="px-4 py-4">
				<div
					v-if="preferedIgBussinessAccount?.IG_username"
					class="inline-flex items-center gap-x-2"
				>
					<p class="text-sm font-normal text-gray-500 dark:text-gray-400 my-2">
						Active IG Business Account:
					</p>
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

			<div class="grid lg:grid-cols-2 grid-cols-1 pt-6 gap-3">
				<div
					class="relative flex flex-col flex-auto min-w-0 p-4 mx-3 overflow-hidden break-words bg-white border-0 dark:bg-slate-850 dark:shadow-dark-xl shadow-3xl rounded-2xl bg-clip-border"
				>
					<div class="flex flex-wrap -mx-3">
						<div class="flex-none w-auto max-w-full px-3">
							<div
								class="relative inline-flex items-center justify-center text-white transition-all duration-200 ease-in-out text-base h-19 w-19 rounded-xl"
							>
								<!-- <img
												src="../assets/img/team-1.jpg"
												alt="profile_image"
												class="w-full shadow-2xl rounded-xl"
											/> -->
							</div>
						</div>
						<div class="flex-none w-auto max-w-full my-auto">
							<div class="h-full">
								<h5 class="mb-0 dark:text-white">Sayo Kravits</h5>
								<p
									class="mb-0 font-semibold leading-normal dark:text-white dark:opacity-60 text-sm"
								>
									Public Relations
								</p>
							</div>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0 md:flex">
						<div
							class="md:w-6/12 w-full bg-gray-50 relative inline-flex items-center justify-center text-white transition-all duration-200 ease-in-out text-base h-60 rounded-xl"
						></div>
						<div
							class="md:w-6/12 w-full flex justify-center items-center px-3 py-4"
						>
							<p>
								Lorem ipsum dolor sit amet consectetur adipisicing elit.
								Incidunt facere reiciendis aspernatur et quia.
							</p>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0">
						<div class="relative">
							<ul
								class="relative flex flex-wrap p-1 list-none bg-gray-50 rounded-xl"
								nav-pills
								role="tablist"
							>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-all ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										active
										href="javascript:;"
										role="tab"
										aria-selected="true"
									>
										<div>
											<i class="far fa-heart"></i>
										</div>
									</a>
								</li>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-all ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="fas fa-external-link-alt"></i>
										</div>
									</a>
								</li>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-colors ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="fas fa-walking"></i>
										</div>
									</a>
								</li>

								<li class="z-30 flex-auto text-center">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-colors ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="far fa-heart"></i>
											<i class="mx-2">+</i>
											<i class="fas fa-walking"></i>
										</div>
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0">
						<div
							class="relative flex flex-wrap p-1 bg-gray-50 rounded-xl w-full"
						>
							<form class="w-full">
								<label for="chat" class="sr-only">Your message</label>
								<div
									class="flex flex-col items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700"
								>
									<textarea
										id="chat"
										rows="1"
										class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
										placeholder="Your comment..."
									></textarea>
									<div class="w-full">
										<button
											type="submit"
											class="inline-flex justify-center p-2 text-slate-700 rounded-lg cursor-pointer hover:bg-blue-100 dark:text-blue-500 dark:hover:bg-gray-600 border-2 mt-2 float-right"
										>
											<div>
												<i class="far fa-heart"></i>
												<i class="mx-2">+</i>
												<i class="far fa-paper-plane"></i>
											</div>
											<span class="sr-only">comment and like</span>
										</button>
										<span class="clear-both"></span>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div
					class="relative flex flex-col flex-auto min-w-0 p-4 mx-3 overflow-hidden break-words bg-white border-0 dark:bg-slate-850 dark:shadow-dark-xl shadow-3xl rounded-2xl bg-clip-border"
				>
					<div class="flex flex-wrap -mx-3">
						<div class="flex-none w-auto max-w-full px-3">
							<div
								class="relative inline-flex items-center justify-center text-white transition-all duration-200 ease-in-out text-base h-19 w-19 rounded-xl"
							>
								<!-- <img
												src="../assets/img/team-1.jpg"
												alt="profile_image"
												class="w-full shadow-2xl rounded-xl"
											/> -->
							</div>
						</div>
						<div class="flex-none w-auto max-w-full my-auto">
							<div class="h-full">
								<h5 class="mb-0 dark:text-white">Sayo Kravits</h5>
								<p
									class="mb-0 font-semibold leading-normal dark:text-white dark:opacity-60 text-sm"
								>
									Public Relations
								</p>
							</div>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0 md:flex">
						<div
							class="md:w-6/12 w-full bg-gray-50 relative inline-flex items-center justify-center text-white transition-all duration-200 ease-in-out text-base h-60 rounded-xl"
						></div>
						<div
							class="md:w-6/12 w-full flex justify-center items-center px-3 py-4"
						>
							<p>
								Lorem ipsum dolor sit amet consectetur adipisicing elit.
								Incidunt facere reiciendis aspernatur et quia.
							</p>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0">
						<div class="relative">
							<ul
								class="relative flex flex-wrap p-1 list-none bg-gray-50 rounded-xl"
								nav-pills
								role="tablist"
							>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-all ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										active
										href="javascript:;"
										role="tab"
										aria-selected="true"
									>
										<div>
											<i class="far fa-heart"></i>
										</div>
									</a>
								</li>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-all ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="fas fa-external-link-alt"></i>
										</div>
									</a>
								</li>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-colors ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="fas fa-walking"></i>
										</div>
									</a>
								</li>

								<li class="z-30 flex-auto text-center">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-colors ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="far fa-heart"></i>
											<i class="mx-2">+</i>
											<i class="fas fa-walking"></i>
										</div>
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0">
						<div
							class="relative flex flex-wrap p-1 bg-gray-50 rounded-xl w-full"
						>
							<form class="w-full">
								<label for="chat" class="sr-only">Your message</label>
								<div
									class="flex flex-col items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700"
								>
									<textarea
										id="chat"
										rows="1"
										class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
										placeholder="Your comment..."
									></textarea>
									<div class="w-full">
										<button
											type="submit"
											class="inline-flex justify-center p-2 text-slate-700 rounded-lg cursor-pointer hover:bg-blue-100 dark:text-blue-500 dark:hover:bg-gray-600 border-2 mt-2 float-right"
										>
											<div>
												<i class="far fa-heart"></i>
												<i class="mx-2">+</i>
												<i class="far fa-paper-plane"></i>
											</div>
											<span class="sr-only">comment and like</span>
										</button>
										<span class="clear-both"></span>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="grid lg:grid-cols-2 grid-cols-1 pt-6 gap-3">
				<div
					class="relative flex flex-col flex-auto min-w-0 p-4 mx-3 overflow-hidden break-words bg-white border-0 dark:bg-slate-850 dark:shadow-dark-xl shadow-3xl rounded-2xl bg-clip-border"
				>
					<div class="flex flex-wrap -mx-3">
						<div class="flex-none w-auto max-w-full px-3">
							<div
								class="relative inline-flex items-center justify-center text-white transition-all duration-200 ease-in-out text-base h-19 w-19 rounded-xl"
							>
								<!-- <img
												src="../assets/img/team-1.jpg"
												alt="profile_image"
												class="w-full shadow-2xl rounded-xl"
											/> -->
							</div>
						</div>
						<div class="flex-none w-auto max-w-full my-auto">
							<div class="h-full">
								<h5 class="mb-0 dark:text-white">Sayo Kravits</h5>
								<p
									class="mb-0 font-semibold leading-normal dark:text-white dark:opacity-60 text-sm"
								>
									Public Relations
								</p>
							</div>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0 md:flex">
						<div
							class="md:w-6/12 w-full bg-gray-50 relative inline-flex items-center justify-center text-white transition-all duration-200 ease-in-out text-base h-60 rounded-xl"
						></div>
						<div
							class="md:w-6/12 w-full flex justify-center items-center px-3 py-4"
						>
							<p>
								Lorem ipsum dolor sit amet consectetur adipisicing elit.
								Incidunt facere reiciendis aspernatur et quia.
							</p>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0">
						<div class="relative">
							<ul
								class="relative flex flex-wrap p-1 list-none bg-gray-50 rounded-xl"
								nav-pills
								role="tablist"
							>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-all ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										active
										href="javascript:;"
										role="tab"
										aria-selected="true"
									>
										<div>
											<i class="far fa-heart"></i>
										</div>
									</a>
								</li>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-all ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="fas fa-external-link-alt"></i>
										</div>
									</a>
								</li>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-colors ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="fas fa-walking"></i>
										</div>
									</a>
								</li>

								<li class="z-30 flex-auto text-center">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-colors ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="far fa-heart"></i>
											<i class="mx-2">+</i>
											<i class="fas fa-walking"></i>
										</div>
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0">
						<div
							class="relative flex flex-wrap p-1 bg-gray-50 rounded-xl w-full"
						>
							<form class="w-full">
								<label for="chat" class="sr-only">Your message</label>
								<div
									class="flex flex-col items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700"
								>
									<textarea
										id="chat"
										rows="1"
										class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
										placeholder="Your comment..."
									></textarea>
									<div class="w-full">
										<button
											type="submit"
											class="inline-flex justify-center p-2 text-slate-700 rounded-lg cursor-pointer hover:bg-blue-100 dark:text-blue-500 dark:hover:bg-gray-600 border-2 mt-2 float-right"
										>
											<div>
												<i class="far fa-heart"></i>
												<i class="mx-2">+</i>
												<i class="far fa-paper-plane"></i>
											</div>
											<span class="sr-only">comment and like</span>
										</button>
										<span class="clear-both"></span>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div
					class="relative flex flex-col flex-auto min-w-0 p-4 mx-3 overflow-hidden break-words bg-white border-0 dark:bg-slate-850 dark:shadow-dark-xl shadow-3xl rounded-2xl bg-clip-border"
				>
					<div class="flex flex-wrap -mx-3">
						<div class="flex-none w-auto max-w-full px-3">
							<div
								class="relative inline-flex items-center justify-center text-white transition-all duration-200 ease-in-out text-base h-19 w-19 rounded-xl"
							>
								<!-- <img
												src="../assets/img/team-1.jpg"
												alt="profile_image"
												class="w-full shadow-2xl rounded-xl"
											/> -->
							</div>
						</div>
						<div class="flex-none w-auto max-w-full my-auto">
							<div class="h-full">
								<h5 class="mb-0 dark:text-white">Sayo Kravits</h5>
								<p
									class="mb-0 font-semibold leading-normal dark:text-white dark:opacity-60 text-sm"
								>
									Public Relations
								</p>
							</div>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0 md:flex">
						<div
							class="md:w-6/12 w-full bg-gray-50 relative inline-flex items-center justify-center text-white transition-all duration-200 ease-in-out text-base h-60 rounded-xl"
						></div>
						<div
							class="md:w-6/12 w-full flex justify-center items-center px-3 py-4"
						>
							<p>
								Lorem ipsum dolor sit amet consectetur adipisicing elit.
								Incidunt facere reiciendis aspernatur et quia.
							</p>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0">
						<div class="relative">
							<ul
								class="relative flex flex-wrap p-1 list-none bg-gray-50 rounded-xl"
								nav-pills
								role="tablist"
							>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-all ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										active
										href="javascript:;"
										role="tab"
										aria-selected="true"
									>
										<div>
											<i class="far fa-heart"></i>
										</div>
									</a>
								</li>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-all ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="fas fa-external-link-alt"></i>
										</div>
									</a>
								</li>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-colors ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="fas fa-walking"></i>
										</div>
									</a>
								</li>

								<li class="z-30 flex-auto text-center">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-colors ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="far fa-heart"></i>
											<i class="mx-2">+</i>
											<i class="fas fa-walking"></i>
										</div>
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0">
						<div
							class="relative flex flex-wrap p-1 bg-gray-50 rounded-xl w-full"
						>
							<form class="w-full">
								<label for="chat" class="sr-only">Your message</label>
								<div
									class="flex flex-col items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700"
								>
									<textarea
										id="chat"
										rows="1"
										class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
										placeholder="Your comment..."
									></textarea>
									<div class="w-full">
										<button
											type="submit"
											class="inline-flex justify-center p-2 text-slate-700 rounded-lg cursor-pointer hover:bg-blue-100 dark:text-blue-500 dark:hover:bg-gray-600 border-2 mt-2 float-right"
										>
											<div>
												<i class="far fa-heart"></i>
												<i class="mx-2">+</i>
												<i class="far fa-paper-plane"></i>
											</div>
											<span class="sr-only">comment and like</span>
										</button>
										<span class="clear-both"></span>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</template>
	</AuthenticatedLayout>
</template>

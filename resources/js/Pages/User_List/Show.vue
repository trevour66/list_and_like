<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link, router } from "@inertiajs/vue3";
import { initDropdowns } from "flowbite";
import { onMounted, ref } from "vue";
import AnIGProfile from "@/Components/AnIGProfile.vue";

import usePreferedIgAccountStore from "@/Store/preferedIgAccountStore";
import { useInstagramAccounts } from "@/Composables/useInstagramAccounts";

import useModalStore from "@/Store/ModalStore";
import IGProfilePostsModal from "@/Components/modals/IGProfilePostsModal.vue";
import UserListWebHookDetails from "@/Components/modals/UserListWebHookDetails.vue";
import { computed } from "vue";

const modalStore = useModalStore();
const preferedIgAccountStore = usePreferedIgAccountStore();
const instagramAccounts = useInstagramAccounts();

const props = defineProps({
	user_list: {
		type: Object,
		default: {},
	},
	ig_profiles: {
		type: Array,
		default: [],
	},
});

const form = useForm({
	user_list_id: "",
});

const ig_handle = ref("");

const goToIGProfilePosts = (ig_handle_passedThrough) => {
	// console.log(ig_handle_passedThrough);

	if (!(ig_handle_passedThrough ?? false)) return;

	ig_handle.value = ig_handle_passedThrough;
	modalStore.toggel_IGProfilePost_Modal(true);
};

const deleteList = (userListId) => {
	if (form.processing || (userListId ?? "") == "") return;

	form.user_list_id = userListId;

	form.post(`/my-lists/${userListId}/delete`, {
		onSuccess: (response) => {
			// console.log("Form submitted successfully:", response);
			// success_submission.value = true;
		},
	});
};

const getWebhookURL = computed(() => {
	if ((props.user_list?.list_webhook_id ?? "") == "") return false;

	return route("ingest_ighandle_webhook_entry", {
		list_webhook_id: props.user_list.list_webhook_id,
	});
});

onMounted(() => {
	initDropdowns();
});
</script>

<template>
	<Head :title="`${user_list?.list_name ?? ''}`" />

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

			<UserListWebHookDetails
				v-if="
					modalStore.get_UserListWebHookDetails_ModalStatus &&
					getWebhookURL &&
					(user_list?.list_name ?? '') !== ''
				"
				:list_name="user_list.list_name"
				:list_webhookURL="getWebhookURL"
			/>
		</template>
		<template #header>
			<div>
				<h2
					class="font-semibold text-xl text-gray-800 leading-tight capitalize"
				>
					{{ user_list?.list_name ?? "" }}
				</h2>
			</div>
			<div>
				<div class="flex flex-wrap">
					<div class="flex items-center md:ml-auto">
						<div
							class="relative flex flex-wrap items-stretch w-full transition-all rounded-lg ease"
						>
							<button
								@click="deleteList(user_list?._id ?? '')"
								type="button"
								class="text-white bg-red-800 focus:ring-2 focus:outline-none focus:ring-[#f24b54]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2"
							>
								<svg
									v-if="form.processing"
									aria-hidden="true"
									role="status"
									class="inline w-4 h-4 me-3 text-white animate-spin"
									viewBox="0 0 100 101"
									fill="none"
									xmlns="http://www.w3.org/2000/svg"
								>
									<path
										d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
										fill="#E5E7EB"
									/>
									<path
										d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
										fill="currentColor"
									/>
								</svg>
								Delete List
							</button>
							<Link
								:href="route('user_lists.index')"
								class="text-white bg-gray-700 focus:ring-2 focus:outline-none focus:ring-[#f24b54]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center"
							>
								Back to Lists
							</Link>
						</div>
					</div>
				</div>
			</div>
		</template>

		<template #content>
			<!-- {{ user_list }}
			{{ getWebhookURL }} -->
			<div class="my-6 w-full">
				<div class="float-right mx-4">
					<button
						@click="modalStore.toggel_UserListWebHookDetails_Modal(true)"
						class="py-2 px-4 ms-2 text-xs font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-[#f24b54] focus:z-10 focus:ring-2 focus:ring-gray-100 flex items-center justify-center gap-x-2"
					>
						<svg
							class="h-7 w-7 fill-[#f24b54]"
							viewBox="0 0 24 24"
							xmlns="http://www.w3.org/2000/svg"
						>
							<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
							<g
								id="SVGRepo_tracerCarrier"
								stroke-linecap="round"
								stroke-linejoin="round"
							></g>
							<g id="SVGRepo_iconCarrier">
								<title>webhook</title>
								<rect width="24" height="24" fill="none"></rect>
								<path
									d="M10.46,19a4.59,4.59,0,0,1-6.37,1.15,4.63,4.63,0,0,1,2.49-8.38l0,1.43a3.17,3.17,0,0,0-2.36,1.36A3.13,3.13,0,0,0,5,18.91a3.11,3.11,0,0,0,4.31-.84,3.33,3.33,0,0,0,.56-1.44v-1l5.58,0,.07-.11a1.88,1.88,0,1,1,.67,2.59,1.77,1.77,0,0,1-.83-1l-4.07,0A5,5,0,0,1,10.46,19m7.28-7.14a4.55,4.55,0,1,1-1.12,9,4.63,4.63,0,0,1-3.43-2.21L14.43,18a3.22,3.22,0,0,0,2.32,1.45,3.05,3.05,0,1,0,.75-6.06,3.39,3.39,0,0,0-1.53.18l-.85.44L12.54,9.2h-.22a1.88,1.88,0,1,1,.13-3.76A1.93,1.93,0,0,1,14.3,7.39a1.88,1.88,0,0,1-.46,1.15l1.9,3.51a4.75,4.75,0,0,1,2-.19M8.25,9.14A4.54,4.54,0,1,1,16.62,5.6a4.61,4.61,0,0,1-.2,4.07L15.18,9a3.17,3.17,0,0,0,.09-2.73A3.05,3.05,0,1,0,9.65,8.6,3.21,3.21,0,0,0,11,10.11l.39.21-3.07,5a1.09,1.09,0,0,1,.1.19,1.88,1.88,0,1,1-2.56-.83,1.77,1.77,0,0,1,1.23-.17l2.31-3.77A4.41,4.41,0,0,1,8.25,9.14Z"
								></path>
							</g>
						</svg>

						Add IG Profile (Webhook)
					</button>
				</div>
				<div class="clear-both"></div>
			</div>
			<div
				class="grid lg:grid-cols-3 xl:grid-cols-4 grid-cols-1 pt-6 gap-3 mx-3"
			>
				<div v-for="(profile, index) in ig_profiles" :key="index">
					<AnIGProfile
						:profile="profile"
						@go-to-i-g-profile-posts="goToIGProfilePosts"
					/>
				</div>
			</div>
		</template>
	</AuthenticatedLayout>
</template>

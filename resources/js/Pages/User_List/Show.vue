<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { initDropdowns } from "flowbite";
import { onMounted, ref } from "vue";
import AnIGProfile from "@/Components/AnIGProfile.vue";

import usePreferedIgAccountStore from "@/Store/preferedIgAccountStore";
import { useInstagramAccounts } from "@/Composables/useInstagramAccounts";

import useModalStore from "@/Store/ModalStore";
import IGProfilePostsModal from "@/Components/modals/IGProfilePostsModal.vue";

const modalStore = useModalStore();
const preferedIgAccountStore = usePreferedIgAccountStore();
const instagramAccounts = useInstagramAccounts();

defineProps({
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
				<div class="flex flex-wrap -mx-3">
					<div class="flex items-center md:ml-auto md:pr-4">
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
								class="text-white bg-gray-700 focus:ring-2 focus:outline-none focus:ring-[#f24b54]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2"
							>
								Back to Lists
							</Link>
						</div>
					</div>
				</div>
			</div>
		</template>

		<template #content>
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

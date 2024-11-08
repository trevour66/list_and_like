<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DeleteUserForm from "./Partials/DeleteUserForm.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import UpdateIGAccountsInformationForm from "./Partials/UpdateIGAccountsInformationForm.vue";
import { initDismisses } from "flowbite";

import { Head } from "@inertiajs/vue3";
import { onMounted } from "vue";

defineProps({
	mustVerifyEmail: {
		type: Boolean,
	},
	AuthError: {
		type: Boolean,
		default: false,
	},
	status: {
		type: String,
	},
	userAccessCodes: {
		type: Array,
	},
});

onMounted(() => {
	initDismisses();
});
</script>

<template>
	<Head title="Profile" />

	<AuthenticatedLayout>
		<template #header>
			<h2 class="font-semibold text-xl text-gray-800 leading-tight">Profile</h2>
		</template>

		<template #content>
			<div class="py-12">
				<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
					<div class="p-4 sm:p-8" v-if="AuthError">
						<div
							id="alert-auth-error"
							class="flex items-center p-4 mb-4 text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400"
							role="alert"
						>
							<svg
								class="flex-shrink-0 w-4 h-4"
								aria-hidden="true"
								xmlns="http://www.w3.org/2000/svg"
								fill="currentColor"
								viewBox="0 0 20 20"
							>
								<path
									d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"
								/>
							</svg>
							<span class="sr-only">Info</span>
							<div class="ms-3 text-sm font-medium">
								There was an error while connecting your IG Business account.
								Please try again
							</div>
							<button
								type="button"
								class="ms-auto -mx-1.5 -my-1.5 bg-blue-50 text-blue-500 rounded-lg focus:ring-2 focus:ring-blue-400 p-1.5 hover:bg-blue-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-blue-400 dark:hover:bg-gray-700"
								data-dismiss-target="#alert-auth-error"
								aria-label="Close"
							>
								<span class="sr-only">Close</span>
								<svg
									class="w-3 h-3"
									aria-hidden="true"
									xmlns="http://www.w3.org/2000/svg"
									fill="none"
									viewBox="0 0 14 14"
								>
									<path
										stroke="currentColor"
										stroke-linecap="round"
										stroke-linejoin="round"
										stroke-width="2"
										d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
									/>
								</svg>
							</button>
						</div>
					</div>
				</div>
				<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
					<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
						<UpdateIGAccountsInformationForm
							:must-verify-email="mustVerifyEmail"
							:userAccessCodes="userAccessCodes"
							class="max-w-xl"
						/>
					</div>
					<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
						<UpdateProfileInformationForm
							:must-verify-email="mustVerifyEmail"
							:status="status"
							class="max-w-xl"
						/>
					</div>

					<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
						<UpdatePasswordForm class="max-w-xl" />
					</div>

					<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
						<DeleteUserForm class="max-w-xl" />
					</div>
				</div>
			</div>
		</template>
	</AuthenticatedLayout>
</template>

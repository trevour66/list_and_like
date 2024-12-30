<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";

const props = defineProps({
	mustVerifyEmail: Boolean,
	userAccessCodes: Array,
});

const user = usePage().props.auth.user;

const newIGConnForm = useForm({
	email: user.email,
});

const subscribeToWebhookForm = useForm({
	IG_APP_SCOPED_ID: "",
	IG_USERNAME: "",
});

const submitNewAuthRequest = async () => {
	newIGConnForm.post(route("authRequest.store"));
};

const submiNewSubscribeToWebhookRequest = (userAccessCode) => {
	if (
		subscribeToWebhookForm.processing ||
		(userAccessCode.IG_APP_SCOPED_ID ?? "") == "" ||
		(userAccessCode.IG_USERNAME ?? "") == ""
	) {
		return;
	}

	subscribeToWebhookForm.IG_APP_SCOPED_ID = userAccessCode.IG_APP_SCOPED_ID;
	subscribeToWebhookForm.IG_USERNAME = userAccessCode.IG_USERNAME;

	subscribeToWebhookForm.post(route("webhook.store"), {
		onSuccess: (response) => {
			// console.log("Form submitted successfully:", response);
		},
	});
};
</script>

<template>
	<section>
		<header class="flex justify-between">
			<div>
				<h2 class="text-lg font-medium text-gray-900">IG Accounts</h2>

				<p class="mt-1 text-sm text-gray-600">
					Manage your Instagram Business Accounts.
				</p>
			</div>
		</header>
		<div class="mt-6 space-y-6">
			<template v-if="userAccessCodes.length > 0">
				<div
					v-for="userAccessCode in userAccessCodes"
					aria-label="clio-account"
					class="p-4 border border-gray-300 rounded-md"
				>
					<div class="flex-col items-center space-y-3 mb-1">
						<div>
							<div
								v-if="(userAccessCode?.webhook_status ?? '') === 'active'"
								class="inline-flex text-xs leading-6 transition-colors duration-200 text-indigo-500 items-center"
							>
								<div
									class="inline w-3 h-3 me-2 bg-indigo-200 animate-pulse rounded-full"
								></div>
								Webhook Active
							</div>
						</div>
						<div class="mr-auto space-y-2">
							<p class="font-medium text-sm text-gray-900 leading-none">
								Business account
							</p>
							<p class="font-normal text-gray-500 leading-none">
								{{ userAccessCode.IG_USERNAME }}
							</p>
						</div>
						<div class="mr-auto space-y-2 mb-1">
							<p class="font-medium text-sm text-gray-900 leading-none">
								Date connected
							</p>
							<p class="font-normal text-gray-500 leading-none">
								{{
									userAccessCode.updated_at
										? new Date(userAccessCode.updated_at)
										: ""
								}}
							</p>
						</div>
						<div>
							<div class="float-right flex items-center space-x-3">
								<button
									@click="submitNewAuthRequest"
									type="button"
									class="inline-flex text-sm px-3 leading-6 py-1.5 rounded-md bg-indigo-50 hover:bg-indigo-400/80 transition-colors duration-200 text-indigo-500 font-medium"
								>
									Refresh token
								</button>

								<!-- WEBHOOK FUNCTIONALITY REMOVED FOR NOW -->

								<button
									:disabled="subscribeToWebhookForm.processing"
									v-if="(userAccessCode?.webhook_status ?? '') === 'inactive'"
									@click="submiNewSubscribeToWebhookRequest(userAccessCode)"
									type="button"
									class="inline-flex text-sm px-3 leading-6 py-1.5 rounded-md hover:bg-indigo-400/80 transition-colors duration-200 text-indigo-50 font-medium bg-indigo-500 items-center"
								>
									<svg
										v-if="subscribeToWebhookForm.processing"
										aria-hidden="true"
										role="status"
										class="inline w-4 h-4 me-3 text-gray-200 animate-spin dark:text-gray-600"
										viewBox="0 0 100 101"
										fill="none"
										xmlns="http://www.w3.org/2000/svg"
									>
										<path
											d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
											fill="currentColor"
										/>
										<path
											d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
											fill="#1C64F2"
										/>
									</svg>
									Activate webhook
								</button>
							</div>
							<div class="clear-both"></div>
						</div>
					</div>
				</div>
			</template>
			<template v-else>
				<div class="mr-auto space-y-2">
					<p class="font-normal text-gray-500 leading-none">
						Make your first connection!
					</p>
				</div>
			</template>
		</div>

		<div class="flex items-center gap-4 mt-6">
			<div class="inline-block">
				<span
					v-if="newIGConnForm.errors.general"
					class="mx-3 text-red-900 text-sm"
					>{{ newIGConnForm.errors.general }}</span
				>

				<PrimaryButton
					:disabled="newIGConnForm.processing"
					@click="submitNewAuthRequest"
					type="button"
					:class="{
						'cursor-not-allowed': newIGConnForm.processing,
					}"
				>
					Add new Instagram Business Account
				</PrimaryButton>
			</div>

			<Transition
				enter-active-class="transition ease-in-out"
				enter-from-class="opacity-0"
				leave-active-class="transition ease-in-out"
				leave-to-class="opacity-0"
			>
			</Transition>
		</div>
	</section>
</template>

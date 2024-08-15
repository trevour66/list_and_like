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

const submitNewAuthRequest = async () => {
	newIGConnForm.post(route("authRequest.store"));
};
</script>

<template>
	<section>
		<header class="flex justify-between">
			<div>
				<h2 class="text-lg font-medium text-gray-900">IG Accounts</h2>

				<p class="mt-1 text-sm text-gray-600">Manage your IG connections</p>
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
									class="inline-flex text-sm px-3 leading-6 py-1.5 rounded-md bg-indigo-50 hover:bg-indigo-50/80 transition-colors duration-200 text-indigo-500 font-medium"
								>
									Refresh token
								</button>
								<!-- <button
									type="button"
									class="inline-flex text-sm px-3 leading-6 py-1.5 rounded-md bg-red-50 hover:bg-red-50/80 transition-colors duration-200 text-red-500 font-medium"
								>
									Delete
								</button> -->
							</div>
							<div class="clear-both"></div>
						</div>
					</div>
				</div>
			</template>
			<template v-else>
				<div class="mr-auto space-y-2">
					<p class="font-normal text-gray-500 leading-none">
						Make you first connection!
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
					>Add new IG bussines account
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

<script setup>
import { computed } from "vue";
import GuestLayout from "@/Layouts/GuestLayout_new.vue";

import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
	status: {
		type: String,
	},
});

const form = useForm({});

const submit = () => {
	form.post(route("verification.send"));
};

const verificationLinkSent = computed(
	() => props.status === "verification-link-sent"
);
</script>

<template>
	<GuestLayout>
		<Head title="Email Verification" />

		<section>
			<div
				class="relative flex items-center justify-center min-h-screen p-0 bg-center bg-cover"
			>
				<div class="container z-1 flex justify-center items-center">
					<div
						class="flex flex-col w-full max-w-full px-3 mx-auto lg:mx-0 shrink-0 md:flex-0 md:w-7/12 lg:w-5/12 xl:w-4/12"
					>
						<div
							class="relative flex flex-col min-w-0 break-words bg-transparent border-0 shadow-none lg:py4 dark:bg-gray-950 rounded-2xl bg-clip-border"
						>
							<div class="p-6 pb-0 mb-0">
								<div class="mb-4 text-sm text-gray-600">
									Thanks for signing up! Before getting started, could you
									verify your email address by clicking on the link we just
									emailed to you? If you didn't receive the email, we will
									gladly send you another.
								</div>
								<div
									class="mb-4 font-medium text-sm text-green-600"
									v-if="verificationLinkSent"
								>
									A new verification link has been sent to the email address you
									provided during registration.
								</div>
							</div>
							<div class="flex-auto p-4">
								<form @submit.prevent="submit">
									<div class="mt-4 flex items-center justify-between">
										<PrimaryButton
											:class="{ 'opacity-25': form.processing }"
											:disabled="form.processing"
										>
											Resend Verification Email
										</PrimaryButton>

										<Link
											:href="route('logout')"
											method="post"
											as="button"
											class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
											>Log Out</Link
										>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</GuestLayout>
</template>

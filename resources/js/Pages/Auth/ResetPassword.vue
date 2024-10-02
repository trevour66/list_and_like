<script setup>
import GuestLayout from "@/Layouts/GuestLayout_new.vue";

import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
	email: {
		type: String,
		required: true,
	},
	token: {
		type: String,
		required: true,
	},
});

const form = useForm({
	token: props.token,
	email: props.email,
	password: "",
	password_confirmation: "",
});

const submit = () => {
	form.post(route("password.store"), {
		onFinish: () => form.reset("password", "password_confirmation"),
	});
};
</script>

<template>
	<GuestLayout>
		<Head title="Reset Password" />

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
							<div class="flex-auto p-4">
								<form @submit.prevent="submit">
									<div>
										<InputLabel for="email" value="Email" />

										<TextInput
											id="email"
											type="email"
											class="mt-1 block w-full"
											v-model="form.email"
											required
											autofocus
											autocomplete="username"
										/>

										<InputError class="mt-2" :message="form.errors.email" />
									</div>

									<div class="mt-4">
										<InputLabel for="password" value="Password" />

										<TextInput
											id="password"
											type="password"
											class="mt-1 block w-full"
											v-model="form.password"
											required
											autocomplete="new-password"
										/>

										<InputError class="mt-2" :message="form.errors.password" />
									</div>

									<div class="mt-4">
										<InputLabel
											for="password_confirmation"
											value="Confirm Password"
										/>

										<TextInput
											id="password_confirmation"
											type="password"
											class="mt-1 block w-full"
											v-model="form.password_confirmation"
											required
											autocomplete="new-password"
										/>

										<InputError
											class="mt-2"
											:message="form.errors.password_confirmation"
										/>
									</div>

									<div class="flex items-center justify-end mt-4">
										<PrimaryButton
											:class="{ 'opacity-25': form.processing }"
											:disabled="form.processing"
										>
											Reset Password
										</PrimaryButton>
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

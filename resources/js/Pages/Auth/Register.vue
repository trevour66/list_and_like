<script setup>
import GuestLayout from "@/Layouts/GuestLayout_new.vue";

import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
	name: "",
	email: "",
	password: "",
	password_confirmation: "",
});

const submit = () => {
	form.post(route("register"), {
		onFinish: () => form.reset("password", "password_confirmation"),
	});
};
</script>

<template>
	<GuestLayout>
		<Head title="Register" />

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
							<div class="p-6 pb-0 mb-0 text-center">
								<h4 class="font-bold">Register a new account</h4>
								<p class="mb-0">Complete the form below to continue</p>
							</div>
							<div class="flex-auto p-4">
								<form role="form" @submit.prevent="submit">
									<div class="mb-4">
										<TextInput
											id="name"
											type="text"
											placeholder="Name"
											class="mt-1 block w-full"
											v-model="form.name"
											required
											autofocus
											autocomplete="name"
										/>

										<InputError class="mt-2" :message="form.errors.name" />
									</div>
									<div class="mb-4">
										<TextInput
											id="email"
											type="email"
											placeholder="Email"
											class="mt-1 block w-full"
											v-model="form.email"
											required
											autofocus
											autocomplete="username"
										/>

										<InputError class="mt-2" :message="form.errors.email" />
									</div>
									<div class="mb-4">
										<TextInput
											id="password"
											type="password"
											placeholder="Password"
											class="mt-1 block w-full"
											v-model="form.password"
											required
											autocomplete="new-password"
										/>
										<label
											class="font-normal cursor-pointer select-none text-xs text-slate-700"
											for="password"
											>Ensure your account is using a password of atleast least
											8 characters</label
										>

										<InputError class="mt-2" :message="form.errors.password" />
									</div>
									<div class="mt-4">
										<TextInput
											id="password_confirmation"
											type="password"
											placeholder="Confirm Password"
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

									<p class="text-right my-2">
										<Link
											:href="route('login')"
											class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
										>
											Already registered?
										</Link>
									</p>

									<div class="text-center">
										<PrimaryButton
											class="w-full hover:-translate-y-px"
											:class="{ 'opacity-25': form.processing }"
											:disabled="form.processing"
										>
											Register
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

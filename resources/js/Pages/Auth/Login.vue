<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import Checkbox_Two from "@/Components/Checkbox_Two.vue";
import GuestLayout from "@/Layouts/GuestLayout_new.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
	canResetPassword: {
		type: Boolean,
	},
	status: {
		type: String,
	},
});

const form = useForm({
	email: "",
	password: "",
	remember: false,
});

const submit = () => {
	form.post(route("login"), {
		onFinish: () => form.reset("password"),
	});
};
</script>

<template>
	<GuestLayout>
		<Head title="Log in" />

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
								<h4 class="font-bold">Sign In</h4>
								<p class="mb-0">Enter your email and password to sign in</p>
								<p
									v-if="status"
									class="my-2 font-medium text-sm text-green-600"
								>
									{{ status }}
								</p>
							</div>
							<div class="flex-auto p-4">
								<form role="form" @submit.prevent="submit">
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
											autocomplete="current-password"
										/>

										<InputError class="mt-2" :message="form.errors.password" />
									</div>
									<div class="flex items-center pl-12 mb-0.5 text-left min-h-6">
										<Checkbox_Two
											name="remember"
											v-model:checked="form.remember"
										/>
										<label
											class="ml-2 font-normal cursor-pointer select-none text-sm text-slate-700"
											for="rememberMe"
											>Remember me</label
										>
									</div>

									<p class="text-right">
										<Link
											v-if="canResetPassword"
											:href="route('password.request')"
											class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
										>
											Forgot your password?
										</Link>
									</p>

									<div class="text-center">
										<PrimaryButton
											class="w-full hover:-translate-y-px"
											:class="{ 'opacity-25': form.processing }"
											:disabled="form.processing"
										>
											Sign in
										</PrimaryButton>
									</div>
								</form>
							</div>

							<div
								class="border-black/12.5 rounded-b-2xl border-t-0 border-solid p-6 text-center pt-0 px-1 sm:px-6"
							>
								<p class="mx-auto mb-6 leading-normal text-sm">
									Don't have an account?

									<Link
										:href="route('register')"
										class="font-semibold text-transparent bg-clip-text bg-gradient-to-tl from-blue-500 to-violet-500"
									>
										Sign up
									</Link>
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</GuestLayout>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout_new.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

defineProps({
	canLogin: {
		type: Boolean,
	},
	canRegister: {
		type: Boolean,
	},
	laravelVersion: {
		type: String,
		required: true,
	},
	phpVersion: {
		type: String,
		required: true,
	},
});
</script>

<template>
	<GuestLayout>
		<Head title="Welcome" />

		<div class="relative sm:flex sm:justify-center sm:items-center h-full">
			<div class="max-w-7xl mx-auto p-6 lg:p-8">
				<section class="relative py-12 lg:py-24">
					<div class="max-w-4xl mx-auto">
						<h1 class="text-3xl font-bold mb-6 text-center">
							Terms and Conditions
						</h1>
						<!-- <p class="mb-4">Last updated: <strong>[Date]</strong></p> -->

						<h2 class="text-2xl font-semibold mb-4">Introduction</h2>
						<p class="mb-4">
							These Terms and Conditions govern your use of our application. By
							accessing or using the app, you agree to be bound by these terms.
							If you do not agree with any part of the terms, you must not use
							our app.
						</p>

						<h2 class="text-2xl font-semibold mb-4">Use of the App</h2>
						<p class="mb-4">
							You must use the app only for lawful purposes and in accordance
							with these Terms. You agree not to:
						</p>
						<ul class="list-disc list-inside mb-4">
							<li>
								Use the app in any way that violates any applicable federal,
								state, local, or international law or regulation.
							</li>
							<li>
								Engage in any conduct that restricts or inhibits anyone's use or
								enjoyment of the app.
							</li>
							<li>
								Introduce any viruses, trojan horses, worms, or other material
								that is malicious or technologically harmful.
							</li>
						</ul>

						<h2 class="text-2xl font-semibold mb-4">Account Security</h2>
						<p class="mb-4">
							You are responsible for maintaining the confidentiality of your
							account and password, and for restricting access to your computer
							or device. You agree to accept responsibility for all activities
							that occur under your account.
						</p>

						<h2 class="text-2xl font-semibold mb-4">Termination</h2>
						<p class="mb-4">
							We reserve the right to terminate or suspend your access to the
							app without prior notice or liability, for any reason whatsoever,
							including without limitation if you breach the Terms.
						</p>

						<h2 class="text-2xl font-semibold mb-4">Intellectual Property</h2>
						<p class="mb-4">
							The app and its original content, features, and functionality are
							and will remain the exclusive property of [Your Company Name] and
							its licensors. The app is protected by copyright, trademark, and
							other laws of both the United States and foreign countries.
						</p>

						<h2 class="text-2xl font-semibold mb-4">Limitation of Liability</h2>
						<p class="mb-4">
							In no event shall [Your Company Name], its directors, employees,
							partners, agents, suppliers, or affiliates, be liable for any
							indirect, incidental, special, consequential, or punitive damages,
							including without limitation, loss of profits, data, use,
							goodwill, or other intangible losses, resulting from:
						</p>
						<ul class="list-disc list-inside mb-4">
							<li>
								Your access to or use of (or inability to access or use) the
								app;
							</li>
							<li>Any conduct or content of any third party on the app;</li>
							<li>Any content obtained from the app; and</li>
							<li>
								Unauthorized access, use, or alteration of your transmissions or
								content.
							</li>
						</ul>

						<h2 class="text-2xl font-semibold mb-4">Changes to These Terms</h2>
						<p class="mb-4">
							We may update our Terms and Conditions from time to time. We will
							notify you of any changes by posting the new Terms on this page.
							You are advised to review these Terms periodically for any
							changes. Changes to these Terms are effective when they are posted
							on this page.
						</p>

						<!-- <h2 class="text-2xl font-semibold mb-4">Contact Us</h2>
							<p>If you have any questions about these Terms and Conditions, please contact us:</p>
							<p>Email: <a href="mailto:support@example.com" class="text-blue-600">support@example.com</a></p>
						-->
					</div>
				</section>
			</div>
		</div>
	</GuestLayout>
</template>

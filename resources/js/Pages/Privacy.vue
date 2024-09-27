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
						<h1 class="text-3xl font-bold mb-6 text-center">Privacy Policy</h1>
						<!-- <p class="mb-4">Last updated: <strong>[Date]</strong></p> -->

						<h2 class="text-2xl font-semibold mb-4">Introduction</h2>
						<p class="mb-4">
							This Privacy Policy outlines how we collect, use, and protect your
							information when you use our app. By using the app, you agree to
							the collection and use of information in accordance with this
							policy.
						</p>

						<h2 class="text-2xl font-semibold mb-4">Information We Collect</h2>
						<p class="mb-2">
							We may collect the following types of information:
						</p>
						<ul class="list-disc list-inside mb-4">
							<li>
								Personal Identification Information (Name, email address, etc.)
							</li>
							<li>
								Usage Data (How you use the app, including time spent and
								features used)
							</li>
							<li>
								Device Information (Type of device, operating system, etc.)
							</li>
						</ul>

						<h2 class="text-2xl font-semibold mb-4">
							How We Use Your Information
						</h2>
						<p class="mb-4">
							We use the collected information for various purposes:
						</p>
						<ul class="list-disc list-inside mb-4">
							<li>To provide and maintain our app</li>
							<li>To notify you about changes to our app</li>
							<li>To allow you to participate in interactive features</li>
							<li>To provide customer support</li>
							<li>
								To gather analysis or valuable information so we can improve the
								app
							</li>
							<li>To monitor the usage of the app</li>
						</ul>

						<h2 class="text-2xl font-semibold mb-4">Data Protection</h2>
						<p class="mb-4">
							The security of your data is important to us. We strive to use
							commercially acceptable means to protect your personal
							information, but remember that no method of transmission over the
							internet or method of electronic storage is 100% secure.
						</p>

						<h2 class="text-2xl font-semibold mb-4">Your Rights</h2>
						<p class="mb-4">You have the right to:</p>
						<ul class="list-disc list-inside mb-4">
							<li>Access your personal data</li>
							<li>Request correction of your personal data</li>
							<li>Request deletion of your personal data</li>
							<li>Object to processing of your personal data</li>
							<li>Request transfer of your personal data</li>
						</ul>

						<h2 class="text-2xl font-semibold mb-4">
							Changes to This Privacy Policy
						</h2>
						<p class="mb-4">
							We may update our Privacy Policy from time to time. We will notify
							you of any changes by posting the new Privacy Policy on this page.
							You are advised to review this Privacy Policy periodically for any
							changes.
						</p>

						<h2 class="text-2xl font-semibold mb-4">Contact Us</h2>
						<p>
							If you have any questions about this Privacy Policy, please
							contact us:
						</p>
						<!-- <p>
							Email:
							<a href="mailto:support@example.com" class="text-blue-600"
								>support@example.com</a
							>
						</p> -->
					</div>
				</section>
			</div>
		</div>
	</GuestLayout>
</template>

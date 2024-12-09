<script setup>
import DangerButton from "@/Components/DangerButton.vue";

import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { ref } from "vue";
import { useCookies } from "@vueuse/integrations/useCookies";
import { watchEffect } from "vue";

const cookies = useCookies(["preferedIgBussinessAccount"]);
const confirmingCookieClearing = ref(false);
const CookieClearingSuccess = ref(null);

const confirmCookieClearing = () => {
	confirmingCookieClearing.value = true;
};

const clearCookie = async () => {
	try {
		cookies.remove("preferedIgBussinessAccount");
		CookieClearingSuccess.value = true;

		await new Promise((resolve, reject) => {
			setInterval(() => {
				closeModal();
				resolve();
			}, 3000);
		});

		window.location.reload();
	} catch (error) {}
};

watchEffect(() => {
	if (CookieClearingSuccess.value !== null) {
		setInterval(() => {
			CookieClearingSuccess.value = null;
		}, 2000);
	}
});

const closeModal = () => {
	confirmingCookieClearing.value = false;
};
</script>

<template>
	<section class="space-y-6">
		<header>
			<h2 class="text-lg font-medium text-gray-900">Clear cookie</h2>

			<p class="mt-1 text-sm text-gray-600">
				We use cookies to improve your experience and make our website work
				properly. Clearing your data will remove saved preferences, such as the
				prefered IG Bussiness Account. Please note that you can always reset
				this with fresh data.
			</p>
		</header>

		<DangerButton @click="confirmCookieClearing">Clear Cookie</DangerButton>

		<Modal :show="confirmingCookieClearing" @close="closeModal">
			<div class="p-6">
				<h2 class="text-lg font-medium text-gray-900">
					Are you sure you want to delete your account?
				</h2>

				<p class="mt-1 text-sm text-gray-600">
					What happens when you clear your data? Your Prefered IG Bussiness
					Account would be reset.
				</p>

				<p v-if="CookieClearingSuccess !== null" class="my-2 text-sm">
					<span
						class="font-semibold text-green-600"
						v-if="CookieClearingSuccess === true"
						>Cookie Cleared</span
					>
					<span
						class="font-semibold text-red-600"
						v-if="CookieClearingSuccess === false"
						>There was an issue while clearing your Cookie. Please contact
						support</span
					>
				</p>

				<div class="mt-6 flex justify-end">
					<SecondaryButton @click="closeModal"> Cancel </SecondaryButton>

					<DangerButton class="ms-3" @click="clearCookie">
						Clear Cookie
					</DangerButton>
				</div>
			</div>
		</Modal>
	</section>
</template>

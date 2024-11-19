<script setup>
import ModalStructure from "./ModalStructure.vue";
import useModalStore from "@/Store/ModalStore";
import InputError from "@/Components/InputError.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { watchEffect } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const modalStore = useModalStore();

const form = useForm({
	list_name: "",
	list_description: "",
});

const success_submission = ref(false);

const submitForm = () => {
	form.post("/my-lists", {
		onSuccess: (response) => {
			// console.log("Form submitted successfully:", response);
			modalStore.toggelNewListModal(false);
		},
	});
};

watchEffect(() => {
	if (success_submission.value) {
		router.reload();
		modalStore.toggelNewListModal(false);
	}
});
</script>

<template>
	<ModalStructure>
		<template #header_text>
			<h3 class="text-xl font-semibold text-gray-900 dark:text-white">
				Create New list
			</h3>

			<button
				@click="modalStore.toggelNewListModal(false)"
				type="button"
				class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
			>
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
				<span class="sr-only">Close modal</span>
			</button>
		</template>

		<template #body>
			<form class="space-y-4" action="#">
				<div>
					<label
						for="list_name"
						class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
						>Name (required)</label
					>
					<input
						type="text"
						id="list_name"
						v-model="form.list_name"
						class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
					/>

					<InputError class="mt-2" :message="form.errors.list_name" />
				</div>
				<div>
					<label
						for="list_description"
						class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
						>Description</label
					>
					<input
						type="text"
						v-model="form.list_description"
						id="list_description"
						class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
					/>

					<InputError class="mt-2" :message="form.errors.list_description" />
				</div>
			</form>
		</template>

		<template #footer>
			<div
				class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600"
			>
				<PrimaryButton
					:disabled="form.processing"
					@click="submitForm"
					type="button"
					class="focus:ring-2 focus:outline-none font-medium rounded-lg text-sm text-center me-2 inline-flex items-center"
					:class="{
						'hover:cursor-not-allowed': form.processing,
					}"
				>
					<svg
						v-if="form.processing"
						aria-hidden="true"
						role="status"
						class="inline w-4 h-4 me-3 text-white animate-spin"
						viewBox="0 0 100 101"
						fill="none"
						xmlns="http://www.w3.org/2000/svg"
					>
						<path
							d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
							fill="#E5E7EB"
						/>
						<path
							d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
							fill="currentColor"
						/>
					</svg>
					Create list
				</PrimaryButton>
			</div>
		</template>
	</ModalStructure>
</template>

<script setup>
import ModalStructure from "./ModalStructure.vue";
import useModalStore from "@/Store/ModalStore";
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { watchEffect } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

import usePreferedIgAccountStore from "@/Store/preferedIgAccountStore";
import { useInstagramAccounts } from "@/Composables/useInstagramAccounts";
import { onMounted } from "vue";

import Prism from "prismjs";
import "prismjs/themes/prism.css"; // you can change

import { initTooltips } from "flowbite";
import { watch } from "vue";

const preferedIgAccountStore = usePreferedIgAccountStore();
const modalStore = useModalStore();
const instagramAccounts = useInstagramAccounts();

const props = defineProps({
	list_name: {
		type: String,
		required: true,
	},
	list_webhookURL: {
		type: String,
		required: true,
	},
});

const tooltip_content = ref("click to copy");
const tooltip_button = ref(null);

const copyURL = () => {
	navigator.clipboard.writeText(props.list_webhookURL).then(() => {
		tooltip_content.value = "url copied";
	});
};

watch(tooltip_content, (newval) => {
	if (newval === "url copied") {
		setTimeout(() => {
			tooltip_content.value = "click to copy";
		}, 3000);
	}
});

onMounted(() => {
	Prism.highlightAll();
	initTooltips();
});
</script>

<template>
	<ModalStructure>
		<template #header_text>
			<h3 class="text-lg font-semibold text-gray-900 dark:text-white uppercase">
				{{ list_name }} Webhook
			</h3>

			<button
				@click="modalStore.toggel_UserListWebHookDetails_Modal(false)"
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
			<section class="space-y-10">
				<div>
					<p
						class="block text-sm mb-2 font-medium text-gray-700 dark:text-white"
					>
						URL
					</p>
					<div class="overflow-x-hidden flex">
						<div
							class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full overflow-x-hidden p-2.5"
						>
							{{ list_webhookURL }}
						</div>

						<div
							id="tooltip_copy_status"
							role="tooltip"
							class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700"
						>
							{{ tooltip_content }}
							<div class="tooltip-arrow" data-popper-arrow></div>
						</div>
						<div>
							<button
								ref="tooltip_button"
								data-tooltip-target="tooltip_copy_status"
								@click="copyURL"
								class="py-2 px-4 ms-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-[#f24b54] focus:z-10 focus:ring-2 focus:ring-gray-100 flex items-center justify-center gap-x-2"
							>
								<svg
									class="h-6 w-6"
									viewBox="0 0 24 24"
									fill="none"
									xmlns="http://www.w3.org/2000/svg"
								>
									<g id="SVGRepo_bgCarrier" stroke-width="0"></g>
									<g
										id="SVGRepo_tracerCarrier"
										stroke-linecap="round"
										stroke-linejoin="round"
									></g>
									<g id="SVGRepo_iconCarrier">
										<path
											d="M20.9983 10C20.9862 7.82497 20.8897 6.64706 20.1213 5.87868C19.2426 5 17.8284 5 15 5H12C9.17157 5 7.75736 5 6.87868 5.87868C6 6.75736 6 8.17157 6 11V16C6 18.8284 6 20.2426 6.87868 21.1213C7.75736 22 9.17157 22 12 22H15C17.8284 22 19.2426 22 20.1213 21.1213C21 20.2426 21 18.8284 21 16V15"
											stroke="#1C274C"
											stroke-width="1.5"
											stroke-linecap="round"
										></path>
										<path
											d="M3 10V16C3 17.6569 4.34315 19 6 19M18 5C18 3.34315 16.6569 2 15 2H11C7.22876 2 5.34315 2 4.17157 3.17157C3.51839 3.82475 3.22937 4.69989 3.10149 6"
											stroke="#1C274C"
											stroke-width="1.5"
											stroke-linecap="round"
										></path>
									</g>
								</svg>
							</button>
						</div>
					</div>
				</div>

				<div>
					<p
						class="block text-sm mb-2 font-medium text-gray-700 dark:text-white"
					>
						Your request should have the format below
					</p>
					<div
						class="text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
					>
						<pre class="language-bash">
<code>
curl -X POST "{{ list_webhookURL }}" \
-H "Content-Type: application/json" \
-d '{
	"ig_handle": "THE_IG_HANDLE_YOUR_WISH_TO_ADD"
	}'
</code>
</pre>
					</div>
				</div>
			</section>
		</template>
	</ModalStructure>
</template>

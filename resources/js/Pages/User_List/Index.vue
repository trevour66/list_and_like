<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { initModals } from "flowbite";
import { onMounted, ref } from "vue";

import useModalStore from "@/Store/ModalStore";
import NewListModal from "@/Components/modals/NewListModal.vue";

const modalStore = useModalStore();

defineProps({
	user_lists: {
		type: Array,
		default: [],
	},
});

onMounted(() => {
	initModals();
});
</script>

<template>
	<Head title="Dashboard" />

	<AuthenticatedLayout>
		<template #bits>
			<NewListModal v-if="modalStore.getNewListModalStatus" />
		</template>
		<template #header>
			<div>
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">
					My Lists
				</h2>
			</div>

			<div>
				<div class="flex flex-wrap -mx-3">
					<div class="flex items-center md:ml-auto md:pr-4">
						<div
							class="relative flex flex-wrap items-stretch w-full transition-all rounded-lg ease"
						>
							<button
								@click="modalStore.toggelNewListModal(true)"
								type="button"
								class="text-white bg-[#24292F] hover:bg-[#24292F]/90 focus:ring-4 focus:outline-none focus:ring-[#24292F]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2"
							>
								Add new List
							</button>
						</div>
					</div>
				</div>
			</div>
		</template>

		<template #content>
			<!-- {{ user_lists }} -->
			<div class="pt-6 mx-3">
				<!-- row 1 -->
				<div class="flex flex-wrap -mx-3">
					<!-- card1 -->
					<template v-for="(list, index) in user_lists" :key="index">
						<div
							class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4"
						>
							<div
								class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border mb-4"
							>
								<div class="flex-auto p-4">
									<div class="flex flex-row -mx-3">
										<div class="flex-none w-2/3 max-w-full px-3">
											<div>
												<p
													class="mb-4 font-sans text-sm font-semibold leading-normal uppercase text-gray-700"
												>
													{{ list.list_name }}
												</p>
												<p class="mb-0 dark:text-white dark:opacity-60">
													<span
														class="text-sm font-bold leading-normal text-gray-700"
														>{{ (list.ig_profiles_ids ?? []).length }}</span
													>
													<span class="text-gray-500 text-xs font-bold">
														IG Profiles
													</span>
												</p>
											</div>
										</div>
										<div class="px-3 text-right basis-1/3">
											<Link
												:href="route('user_lists.show', { userList: list._id })"
											>
												<div
													class="inline-flex justify-center items-center w-12 h-12 text-center rounded border-2 border-grey-300 hover:border-grey-500"
												>
													<i
														class="fa-solid fa-up-right-from-square text-lg leading-none relative text-gray-500"
													></i>
												</div>
											</Link>
										</div>
									</div>
								</div>
							</div>
						</div>
					</template>
				</div>
			</div>
		</template>
	</AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InfinityScrollLoader from "@/Components/InfinityScrollLoader.vue";
import IGBizPost from "@/Components/IGBizPost.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";
import IGBusinessPost from "@/Services/IGBusinessPost";
import ActiveIGAccountSelector from "@/Components/ActiveIGAccountSelector.vue";
import usePreferedIgAccountStore from "@/Store/preferedIgAccountStore";

import useModalStore from "@/Store/ModalStore";
import IGProfilePostsModal from "@/Components/modals/IGProfilePostsModal.vue";
import TopEngagers from "@/Pages/Engagement/TopEngagers.vue";
import OtherEngagers from "@/Pages/Engagement/OtherEngagers.vue";

const modalStore = useModalStore();
const preferedIgAccountStore = usePreferedIgAccountStore();

defineProps({
	ig_data_fetch_process: {
		type: Array,
		default: [],
	},
	user_lists: {
		type: Array,
		default: [],
	},
});

const associated_user_IG_Biz_posts = ref([]);
const next_page_url = ref("");

const ig_handle = ref("");

const goToIGProfilePosts = (ig_handle_passedThrough) => {
	// console.log(ig_handle_passedThrough);

	if (!(ig_handle_passedThrough ?? false)) return;

	ig_handle.value = ig_handle_passedThrough;
	modalStore.toggel_IGProfilePost_Modal(true);
};
</script>

<template>
	<Head title="Engagements" />

	<AuthenticatedLayout>
		<template #bits>
			<IGProfilePostsModal
				v-if="modalStore.get_IGProfilePost_ModalStatus"
				:ig_handle="ig_handle"
				:business_account_id="
					preferedIgAccountStore.get_preferedIgBussinessAccount?.IG_username ??
					''
				"
			/>
		</template>
		<template #header>
			<div>
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">
					Engagements
				</h2>
			</div>
			<div>
				<div class="flex flex-wrap -mx-3"></div>
			</div>
		</template>

		<template #content>
			<section class="w-full max-w-full my-6 relative px-4 mb-10">
				<div class="float-right">
					<ActiveIGAccountSelector
						:ig_data_fetch_process="ig_data_fetch_process"
						:loadingData="false"
					/>
				</div>
				<div class="clear-both"></div>
			</section>

			<section id="top_five" class="max-w-full w-full mb-20">
				<h2
					class="mb-4 px-4 text-lg font-extrabold leading-none tracking-tight text-gray-900 text-left"
				>
					Top Engagers
				</h2>

				<TopEngagers
					:user_lists="user_lists"
					@goToIGProfilePosts="goToIGProfilePosts"
				/>
			</section>

			<section class="max-w-full w-full">
				<h2
					class="mb-8 px-4 text-lg font-extrabold leading-none tracking-tight text-gray-900 text-left"
				>
					Other Engagers
				</h2>

				<OtherEngagers @goToIGProfilePosts="goToIGProfilePosts" />
			</section>
		</template>
	</AuthenticatedLayout>
</template>

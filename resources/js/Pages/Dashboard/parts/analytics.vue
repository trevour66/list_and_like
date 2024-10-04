<script setup>
import DashboardAnalyticsCard from "@/Components/DashboardAnalyticsCard.vue";
import StackedSlider from "./stackedSlider.vue";
import { usePage } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import post from "./icons/post.vue";
import comment from "./icons/comment.vue";
import profile from "./icons/profile.vue";
import list from "./icons/list.vue";

import DashboardData from "@/Services/DashboardData";

const props = defineProps({
	businessAccountUsed: {
		type: Object,
		required: true,
	},
});

const userAccessToken = usePage().props.auth.user.auth_token;
const miniVersionActive = usePage().props.mini_version ?? false;

const Loading = ref(true);
const posts_processed = ref(0);
const comments_processed = ref(0);
const posts_from_commenters_processed = ref(0);
const posts_from_commenters_processed_skipped = ref(0);
const posts_from_commenters_processed_reactedTo = ref(0);
const all_IG_profiles_linked_to_IG_business_account = ref(0);
const all_user_lists = ref(0);

const fetchAnalytics = async () => {
	console.log("response.data");
	let IG_username = props.businessAccountUsed?.IG_username ?? "";

	if (IG_username == "") return;

	Loading.value = true;

	await DashboardData.getAnalytics(userAccessToken, IG_username)
		.then(function (response) {
			console.log(response);
			const data = response.data.data ?? {};

			posts_processed.value = data?.posts_processed ?? 0;
			comments_processed.value = data?.comments_processed ?? 0;
			posts_from_commenters_processed.value =
				data?.posts_from_commenters_processed ?? 0;
			posts_from_commenters_processed_skipped.value =
				data?.posts_from_commenters_processed_skipped ?? 0;
			posts_from_commenters_processed_reactedTo.value =
				data?.posts_from_commenters_processed_reactedTo ?? 0;
			all_IG_profiles_linked_to_IG_business_account.value =
				data?.all_IG_profiles_linked_to_IG_business_account ?? 0;
			all_user_lists.value = data?.all_user_lists ?? 0;

			Loading.value = false;
		})
		.catch(function (error) {
			// handle error
			console.log(error);
			Loading.value = false;
		});
};

onMounted(() => {
	fetchAnalytics();
});
</script>

<template>
	<div class="w-full px-6 py-6 mx-auto">
		<!-- row 1 -->
		<div
			class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4"
		>
			<div>
				<DashboardAnalyticsCard title="Post Processed" :value="posts_processed">
					<template #icon><post /></template
				></DashboardAnalyticsCard>
			</div>
			<div>
				<DashboardAnalyticsCard
					title="Comments analyzed"
					:value="comments_processed"
					><template #icon><comment /></template
				></DashboardAnalyticsCard>
			</div>
		</div>

		<!-- cards row 2 -->
		<div class="flex flex-wrap mt-8 -mx-3" v-if="!miniVersionActive">
			<div class="w-full max-w-full my-4 px-3 mt-0 lg:w-8/12 lg:flex-none">
				<div
					class="dark:bg-slate-850 dark:shadow-dark-xl shadow-xl relative z-20 flex min-w-0 flex-col break-words rounded-2xl border-0 border-solid bg-white bg-clip-border"
				>
					<div
						class="mb-0 rounded-t-2xl border-b-0 border-solid p-6 pt-4 pb-0 inline-flex gap-x-1"
					>
						<p class="mb-0 text-sm leading-normal underline decoration-dotted">
							<span class="font-semibold">{{
								posts_from_commenters_processed
							}}</span>
						</p>
						<h6
							class="mb-0 text-gray-500 font-sans text-sm font-semibold leading-normal uppercase"
						>
							Posts from IG Profiles
						</h6>
					</div>
					<div class="flex-auto p-6">
						<div>
							<StackedSlider />
						</div>
					</div>
				</div>
			</div>

			<div class="w-full max-w-full my-4 px-3 lg:w-4/12 lg:flex-none">
				<div class="relative w-full h-full overflow-hidden rounded-2xl">
					<div class="grid grid-cols-1 gap-4">
						<div>
							<DashboardAnalyticsCard
								title="IG Profiles"
								:value="all_IG_profiles_linked_to_IG_business_account"
								><template #icon><profile /></template
							></DashboardAnalyticsCard>
						</div>
						<div>
							<DashboardAnalyticsCard title="Lists" :value="all_user_lists"
								><template #icon><list /></template
							></DashboardAnalyticsCard>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import IGProfilePost from "@/Services/IGProfilePost";
import InfinityScrollLoader from "@/Components/InfinityScrollLoader.vue";
import { Head, usePage } from "@inertiajs/vue3";
import IGPostRepresentation from "@/Components/IGPostRepresentation.vue";

import { onMounted, ref, watch } from "vue";

import DashboardData from "@/Services/DashboardData";

const props = defineProps({
	businessAccountUsed: {
		type: Object,
		required: true,
	},
});

const next_page_url = ref("");

const hasMounted = ref(false);

const Loading = ref(false);
const noMorePost = ref(false);
const userAccessToken = usePage().props.auth.user.auth_token;

const cards = ref([
	{ hide: true, dataSlide: 0, data: {}, isPlaceholder: true },
]);

const rotateCards = () => {
	const cardIndexes = cards.value.map((data, i) => {
		return data.dataSlide;
	});

	cardIndexes.unshift(cardIndexes.pop());
	setIndexes(cardIndexes);
};

const setIndexes = (indexes) => {
	cards.value.forEach((card, i) => {
		card.dataSlide = indexes[i];
	});
};

const userPostsFetch = async () => {
	// console.log("response.data");
	let IG_username = props.businessAccountUsed?.IG_username ?? "";

	if (IG_username == "") return;

	Loading.value = true;
	noMorePost.value = false;
	cards.value = [{ hide: true, dataSlide: 0, data: {}, isPlaceholder: true }];

	await DashboardData.getCommunityData(
		userAccessToken,
		IG_username,
		next_page_url.value
	)
		.then(function (response) {
			// console.log(response);
			const associated_user_posts_data =
				response?.data?.associated_user_posts ?? false;
			const status = response?.data?.status ?? false;

			const posts = associated_user_posts_data?.data ?? [];
			// const prev_cursor = associated_user_posts_data?.prev_cursor ?? null;

			next_page_url.value = associated_user_posts_data?.next_page_url ?? "";

			// console.log(posts);

			if (posts.length === 0) {
				console.log("no post");
				noMorePost.value = true;
			}

			const formatedList = posts.map((data, index) => {
				if (index == 0) {
					cards.value[0].data = data;
				}
				return {
					hide: false,
					dataSlide: index,
					data: data,
					isPlaceholder: false,
				};
			});

			cards.value = cards.value.concat(formatedList);

			Loading.value = false;
		})
		.catch(function (error) {
			// handle error
			console.log(error);
			Loading.value = false;
		});
};

const refreshCards = async () => {
	await userPostsFetch();
};

watch(props.businessAccountUsed, async (newValue) => {
	// console.log(newValue);

	// console.log("called");
	let IG_username = props.businessAccountUsed?.IG_username ?? "";

	if (IG_username !== "" && hasMounted.value) {
		// console.log("start userPostsFetch");
		next_page_url.value = "";

		await userPostsFetch();
		// console.log("finish userPostsFetch");
	}
});

onMounted(async () => {
	const initialIndexes = cards.value.map((data, i) => {
		return data.dataSlide;
	});

	userPostsFetch();

	setIndexes(initialIndexes);
	hasMounted.value = true;
});
</script>

<template>
	<div class="min-h-[200px]">
		<template v-if="Loading">
			<div class="flex items-center justify-center w-full h-80">
				<InfinityScrollLoader />
			</div>
		</template>
		<template v-else>
			<template v-if="noMorePost">
				<!-- component -->
				<div class="flex items-center justify-center min-h-32">
					<div class="rounded-lg bg-gray-50 px-16 py-14">
						<h3 class="my-4 text-center text-3xl font-semibold text-gray-700">
							Ops!!!
						</h3>
						<p class="text-center font-normal text-gray-600">No post found!</p>
					</div>
				</div>
			</template>
			<template v-else>
				<div class="cards-box relative min-h-full">
					<div
						v-for="(card, index) in cards"
						:class="['card', { hide: card.hide }]"
						:data-slide="card.dataSlide"
					>
						<template v-if="!card.isPlaceholder">
							<IGPostRepresentation
								:post="card.data"
								:index="card.dataSlide"
								:parentIsDashboard="true"
							/>
						</template>
						<template v-else>
							<IGPostRepresentation
								:post="card.data"
								:index="card.dataSlide"
								:parentIsDashboard="true"
							/>
						</template>
					</div>
				</div>

				<div class="m-8 flex items-center justify-center float-right">
					<div
						v-if="next_page_url !== ''"
						@click="refreshCards"
						class="mx-2 inline-block w-12 h-12 text-center rounded-full shadow-md bg-white p-1 flex items-center justify-center float-right hover:cursor-pointer hover:shadow-xl"
					>
						<svg
							fill="#333"
							class="h-8 w-8"
							viewBox="0 0 24 24"
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
									d="M23,12A11,11,0,1,1,12,1a10.9,10.9,0,0,1,5.882,1.7l1.411-1.411A1,1,0,0,1,21,2V6a1,1,0,0,1-1,1H16a1,1,0,0,1-.707-1.707L16.42,4.166A8.9,8.9,0,0,0,12,3a9,9,0,1,0,9,9,1,1,0,0,1,2,0Z"
								></path>
							</g>
						</svg>
					</div>
					<div
						@click="rotateCards"
						class="mx-2 inline-block w-12 h-12 text-center rounded-full shadow-md bg-white p-1 flex items-center justify-center float-right hover:cursor-pointer hover:shadow-xl"
					>
						<svg
							class="h-8 w-8"
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
									d="M4 12H20M20 12L16 8M20 12L16 16"
									stroke="#333"
									stroke-width="2"
									stroke-linecap="round"
									stroke-linejoin="round"
								></path>
							</g>
						</svg>
					</div>
				</div>

				<div class="clear-both"></div>
			</template>
		</template>
	</div>
</template>

<style scoped>
.cards-box .card {
	width: calc(18rem + 19vh);
	max-width: 80vw;
	background: #f7fffd;
	border-radius: 14px;
}

.cards-box .card.hide {
	visibility: hidden;
}

.cards-box .card:not(.hide) {
	position: absolute;
	top: 0;
	left: 0;
	transition: all 0.8s cubic-bezier(0.18, 0.98, 0.45, 1);
	box-shadow: 0 2px 10px 0 rgba(0, 0, 0, 0.07);
}

.cards-box .card:not(.hide)[data-slide="0"] {
	transform: translate(0px, 0px) scale(1);
	z-index: 6;
	opacity: 1;
}

.cards-box .card:not(.hide)[data-slide="1"] {
	transform: translate(10px, 0px) scale(0.98);
	z-index: 5;
	opacity: 0.9;
}

.cards-box .card:not(.hide)[data-slide="2"] {
	transform: translate(20px, 0px) scale(0.96);
	z-index: 4;
	opacity: 0.8;
}

.cards-box .card:not(.hide)[data-slide="3"] {
	transform: translate(30px, 0px) scale(0.94);
	z-index: 3;
	opacity: 0.7;
}

.cards-box .card:not(.hide)[data-slide="4"] {
	transform: translate(40px, 0px) scale(0.92);
	z-index: 2;
	opacity: 0.6;
}

.cards-box .card:not(.hide)[data-slide="5"] {
	transform: translate(50px, 0px) scale(0.9);
	z-index: 1;
	opacity: 0.5;
}

.cards-box .card:not(.hide)[data-slide="6"] {
	transform: translate(55px, 0px) scale(0.88);
	z-index: 1;
	opacity: 0.5;
}

.cards-box .card:not(.hide)[data-slide="7"] {
	transform: translate(60px, 0px) scale(0.86);
	z-index: 1;
	opacity: 0.5;
}

.cards-box .card:not(.hide)[data-slide="8"] {
	transform: translate(65px, 0px) scale(0.84);
	z-index: 1;
	opacity: 0.5;
}

.cards-box .card:not(.hide)[data-slide="9"] {
	transform: translate(70px, 0px) scale(0.82);
	z-index: 1;
	opacity: 0.5;
}

.cards-box .card:not(.hide)[data-slide="10"] {
	transform: translate(75px, 0px) scale(0.8);
	z-index: 1;
	opacity: 0.5;
}
</style>

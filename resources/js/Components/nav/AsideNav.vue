<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import NavLink_New from "@/Components/NavLink_New.vue";
import { usePage, Link } from "@inertiajs/vue3";

const props = defineProps({
	showingNavigationBar: {
		type: Boolean,
		required: true,
		default: true,
	},
});

const miniVersionActive = usePage().props.mini_version ?? false;

const isActive = (parent) => {
	return usePage().url.startsWith(parent);
};
</script>

<template>
	<aside
		v-show="showingNavigationBar"
		class="fixed inset-y-0 flex-wrap items-center justify-between block w-full p-0 my-4 overflow-y-auto antialiased transition-transform duration-200 bg-white border-0 shadow-xl max-w-64 ease-nav-brand z-[1000] ml-6 rounded-2xl xl:left-0 xl:translate-x-0 xl:block"
		aria-expanded="false"
	>
		<div class="h-19">
			<i
				@click="$emit('closeNavigationBar')"
				class="absolute top-0 right-0 p-4 opacity-50 cursor-pointer fas fa-times dark:text-white text-slate-400 xl:hidden"
			></i>
			<a
				class="block px-8 py-6 m-0 text-sm whitespace-nowrap text-slate-700"
				href="#"
			>
				<ApplicationLogo />
			</a>
		</div>

		<hr
			class="h-px mt-0 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent"
		/>

		<div class="items-center block w-auto max-h-screen grow basis-full">
			<ul class="flex flex-col pl-0 mb-0">
				<li class="w-full">
					<NavLink_New
						:href="route('dashboard')"
						:active="route().current('dashboard')"
					>
						<template #icon>
							<!-- <i
								class="leading-normal text-xl text-gray-500 fa-solid fa-table-columns"
							></i> -->
						</template>
						<template #text>Dashboard</template>
					</NavLink_New>
				</li>
				<li class="w-full" v-if="!miniVersionActive">
					<NavLink_New
						:href="route('community.index')"
						:active="route().current('community.index')"
					>
						<template #icon>
							<!-- <i
								class="leading-normal text-xl text-gray-500 fa-solid fa-table-columns"
							></i> -->
						</template>
						<template #text>Community</template>
					</NavLink_New>
				</li>
				<li class="w-full">
					<NavLink_New
						:href="route('user_lists.index')"
						:active="
							route().current('user_lists.index') || isActive('/my-lists')
						"
					>
						<template #icon>
							<!-- <i
								class="leading-normal text-xl text-gray-500 fa-solid fa-table-columns"
							></i> -->
						</template>
						<template #text>Lists</template>
					</NavLink_New>
				</li>
				<li class="w-full">
					<NavLink_New
						:href="route('added_ig_profile.index')"
						:active="route().current('added_ig_profile.index')"
					>
						<template #icon>
							<!-- <i
								class="leading-normal text-xl text-gray-500 fa-solid fa-table-columns"
							></i> -->
						</template>
						<template #text>Added IG Profiles</template>
					</NavLink_New>
				</li>

				<li class="w-full">
					<NavLink_New :href="route('logout')" method="post">
						<template #icon>
							<!-- <i
								class="leading-normal text-xl text-gray-500 fa-solid fa-table-columns"
							></i> -->
						</template>
						<template #text>Log Out</template>
					</NavLink_New>
				</li>

				<!-- 
				<li class="w-full">
					<NavLink_New :href="route('list')" :active="route().current('list')">
						<template #icon>
							<i
								class="leading-normal text-xl text-gray-500 fa-solid fa-table-columns"
							></i>
						</template>
						<template #text>List</template>
					</NavLink_New>
				</li>

				<li class="py-8 w-full">
					<NavLink_New
						:href="route('engaged')"
						:active="route().current('engaged')"
					>
						<template #icon>
							<i
								class="leading-normal text-xl text-gray-500 fa-solid fa-table-columns"
							></i>
						</template>
						<template #text>Engaged</template>
					</NavLink_New>
				</li>

				<li class="py-8 w-full">
					<NavLink_New
						:href="route('replies')"
						:active="route().current('replies')"
					>
						<template #icon>
							<i
								class="leading-normal text-xl text-gray-500 fa-solid fa-table-columns"
							></i>
						</template>
						<template #text>Replies</template>
					</NavLink_New>
				</li>

				<li class="py-8 w-full">
					<NavLink_New
						:href="route('leads')"
						:active="route().current('leads')"
					>
						<template #icon>
							<i
								class="leading-normal text-xl text-gray-500 fa-solid fa-table-columns"
							></i>
						</template>
						<template #text>Leads</template>
					</NavLink_New>
				</li>

				<li class="py-8 w-full">
					<NavLink_New
						:href="route('sign_out')"
						:active="route().current('sign_out')"
					>
						<template #icon>
							<i
								class="leading-normal text-xl text-gray-500 fa-solid fa-table-columns"
							></i>
						</template>
						<template #text>Sign Out</template>
					</NavLink_New>
				</li>
				 -->
			</ul>
		</div>
	</aside>
</template>

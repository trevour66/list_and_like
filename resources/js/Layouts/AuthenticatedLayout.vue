<script setup>
import { ref } from "vue";
import AsideNav from "@/Components/nav/AsideNav.vue";

import { Link, router } from "@inertiajs/vue3";
import axios from "axios";
import { onMounted } from "vue";
import { useCookies } from "@vueuse/integrations/useCookies";

const cookies = useCookies();

onMounted(() => {
	const XSRF_TOKEN = cookies.get("XSRF-TOKEN") ?? null;
	// console.log(XSRF_TOKEN);
	if (
		typeof XSRF_TOKEN === "undefined" ||
		XSRF_TOKEN == null ||
		XSRF_TOKEN == ""
	) {
		axios
			.get("/sanctum/csrf-cookie")
			.then((res) => {})
			.catch((err) => {
				console.log("Error");
				console.log(err);
			});
	}
});

const showingNavigationBar = ref(true);
</script>

<template>
	<div class="absolute w-full bg-gray-500 dark:hidden min-h-[18.75rem]"></div>

	<AsideNav
		:showingNavigationBar="showingNavigationBar"
		@closeNavigationBar="showingNavigationBar = false"
	/>

	<slot name="bits"></slot>

	<main
		id="main"
		class="relative h-full max-h-screen transition-all duration-200 ease-in-out xl:ml-[17rem] overflow-y-auto overflow-x-hidden"
	>
		<!-- Navbar -->
		<nav
			class="relative flex flex-wrap items-center justify-between px-0 py-4 mx-6 transition-all ease-in shadow-none duration-250 rounded-2xl lg:flex-nowrap"
			navbar-scroll="false"
		>
			<div
				class="flex items-center justify-between w-full py-1 mx-auto flex-wrap-inherit"
			>
				<div>
					<div
						class="flex items-center mt-2 grow sm:mt-0 sm:mr-6 md:mr-0 lg:flex lg:basis-auto"
					>
						<ul
							class="flex flex-row justify-end pl-0 mb-0 list-none md-max:w-full"
						>
							<li
								@click="showingNavigationBar = true"
								class="flex items-center xl:hidden mr-3"
							>
								<div
									class="block p-1 text-sm text-white transition-all ease-nav-brand border rounded"
								>
									<div class="w-6 overflow-hidden">
										<i
											class="ease mb-[5px] relative block h-[3px] rounded-sm bg-white transition-all"
										></i>
										<i
											class="ease mb-[5px] relative block h-[3px] rounded-sm bg-white transition-all"
										></i>
										<i
											class="ease relative block h-[3px] rounded-sm bg-white transition-all"
										></i>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div>
					<div
						class="flex items-center mt-2 grow sm:mt-0 sm:mr-6 md:mr-0 lg:flex lg:basis-auto"
					>
						<ul
							class="flex flex-row justify-end pl-0 mb-0 list-none md-max:w-full"
						>
							<li class="flex items-center mx-3">
								<Link
									class="block px-0 py-2 text-sm font-semibold text-white transition-all ease-nav-brand"
									:href="route('profile.edit')"
								>
									<i class="fa fa-user sm:mr-1 text-xl"></i>
								</Link>
							</li>

							<!-- notifications -->

							<!-- <li class="relative flex items-center pr-2">
								<p class="hidden transform-dropdown-show"></p>
								<a
									href="javascript:;"
									class="block p-0 text-sm text-white transition-all ease-nav-brand"
									dropdown-trigger
									aria-expanded="false"
								>
									<i class="cursor-pointer fa fa-bell text-xl"></i>
								</a>
							</li> -->
						</ul>
					</div>
				</div>
			</div>
		</nav>

		<div class="w-full px-6 mx-auto">
			<!-- content -->

			<div
				v-if="$slots.header"
				class="w-full max-w-full mb-3 relative flex flex-col md:flex-row min-w-0 mt-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border p-4 mb-0 justify-between items-center gap-4"
			>
				<slot name="header" />
			</div>

			<div class="flex flex-wrap -mx-3">
				<div class="w-full max-w-full px-1 md:px-3 mt-6 md:flex-none">
					<div
						class="relative flex flex-col min-w-0 break-words border-0 rounded-2xl bg-clip-border bg-gray-50 pb-8 min-h-[70vh]"
					>
						<slot name="content" />
					</div>
				</div>
			</div>

			<footer class="pt-4"></footer>
		</div>
	</main>
</template>

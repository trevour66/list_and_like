<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";

const toggleNavbar = ref(false);
</script>

<template class="selection:bg-red-500 selection:text-white">
	<header class="flex flex-wrap min-h-[10vh]">
		<div class="w-full max-w-full px-3 flex-0">
			<!-- Navbar -->
			<nav
				class="z-30 flex flex-wrap flex-col items-center px-4 py-2 m-6 mb-0 shadow-md rounded-xl bg-white/80 backdrop-blur-2xl backdrop-saturate-200 lg:flex-nowrap lg:justify-start gap-3"
			>
				<div
					class="flex items-center justify-between w-full py-2 px-6 mx-auto flex-wrap-inherit"
				>
					<Link
						:href="route('home')"
						class="py-1.75 text-sm mr-4 ml-4 whitespace-nowrap font-bold text-slate-700 lg:ml-0"
					>
						<ApplicationLogo />
					</Link>
					<div>
						<div
							class="hidden items-center flex-grow transition-all duration-500 ease lg-max:max-h-0 basis-full lg:flex lg:basis-auto"
						>
							<ul class="flex pl-0 mb-0 list-none gap-2">
								<li>
									<a
										class="block px-4 py-2 mr-2 font-normal transition-all ease-in-out lg-max:opacity-0 duration-250 text-sm text-slate-700 lg:px-2"
										:href="route('about_us')"
										:class="{
											'font-semibold underline decoration-dotted':
												route().current('about_us'),
										}"
									>
										About
									</a>
								</li>

								<!-- <li>
									<a
										class="flex items-center px-4 py-2 mr-2 font-normal transition-all ease-in-out lg-max:opacity-0 duration-250 text-sm text-slate-700 lg:px-2"
										aria-current="page"
										href="../pages/dashboard.html"
									>
										<i class="mr-1 fa fa-chart-pie opacity-60"></i>
										Dashboard
									</a>
								</li> -->

								<li>
									<a
										class="block px-4 py-2 mr-2 font-normal transition-all ease-in-out lg-max:opacity-0 duration-250 text-sm text-slate-700 lg:px-2"
										:href="route('login')"
										>Sign in</a
									>
								</li>

								<li>
									<a
										:href="route('register')"
										class="inline-block px-8 py-2 mb-0 mr-1 font-bold leading-normal text-center text-white align-middle transition-all ease-in bg-[#f24b54] border-0 rounded-lg shadow-md cursor-pointer hover:shadow-xs active:opacity-85 text-xs tracking-tight-rem"
										>Sign Up</a
									>
								</li>
							</ul>
						</div>
						<button
							@click="toggleNavbar = !toggleNavbar"
							:class="{
								'border-gray-500': toggleNavbar,
								'border-gray-200': !toggleNavbar,
							}"
							class="px-3 py-1 ml-2 leading-none transition-all ease-in-out bg-transparent rounded-lg border-2 shadow-none cursor-pointer text-lg lg:hidden"
							type="button"
							aria-controls="navigation"
							aria-expanded="false"
							aria-label="Toggle navigation"
						>
							<span
								class="inline-block mt-2 align-middle bg-center bg-no-repeat bg-cover w-6 h-6 bg-none"
							>
								<span
									bar1
									class="w-6 rounded-xs relative my-0 mx-auto block h-px bg-gray-600 transition-all duration-300"
								></span>
								<span
									bar2
									class="w-6 rounded-xs mt-2 relative my-0 mx-auto block h-px bg-gray-600 transition-all duration-300"
								></span>
								<span
									bar3
									class="w-6 rounded-xs mt-2 relative my-0 mx-auto block h-px bg-gray-600 transition-all duration-300"
								></span>
							</span>
						</button>
					</div>
				</div>
			</nav>
			<div class="relative" v-if="toggleNavbar">
				<nav
					class="z-30 block py-2 m-6 mb-0 shadow-md rounded-xl bg-white/80 backdrop-blur-2xl backdrop-saturate-200"
				>
					<div
						class="flex items-center justify-between w-full p-0 mx-auto flex-wrap-inherit"
					>
						<div
							class="items-center flex-grow transition-all duration-500 ease lg-max:max-h-0 basis-full w-full"
						>
							<ul class="pl-0 mb-0 list-none w-full">
								<li class="">
									<a
										class="flex items-center px-4 py-4 mr-2 font-normal transition-all ease-in-out lg-max:opacity-0 duration-250 text-sm text-slate-700 lg:px-2"
										aria-current="page"
										href="../pages/dashboard.html"
									>
										<i class="mr-1 fa fa-chart-pie opacity-60"></i>
										Dashboard
									</a>
								</li>
								<li class="border-t">
									<a
										class="block px-4 py-4 mr-2 font-normal transition-all ease-in-out lg-max:opacity-0 duration-250 text-sm text-slate-700 lg:px-2"
										href="../pages/profile.html"
									>
										<i class="mr-1 fa fa-user opacity-60"></i>
										Profile
									</a>
								</li>
								<li class="border-t">
									<a
										class="flex items-center px-4 py-4 mr-2 font-normal transition-all ease-in-out lg-max:opacity-0 duration-250 text-sm text-slate-700 lg:px-2"
										aria-current="page"
										:href="route('login')"
									>
										<i class="mr-1 fa fa-chart-pie opacity-60"></i>
										Sign in
									</a>
								</li>
								<li class="border-t">
									<a
										class="block px-4 py-4 mr-2 font-normal transition-all ease-in-out lg-max:opacity-0 duration-250 text-sm text-slate-700 lg:px-2"
										:href="route('register')"
									>
										<i class="mr-1 fa fa-user opacity-60"></i>
										Sign Up
									</a>
								</li>
							</ul>
						</div>
					</div>
				</nav>
			</div>
		</div>
	</header>

	<main class="mt-0 transition-all duration-200 ease-in-out min-h-[80vh] ">
		<slot />
	</main>

	<footer class="min-h-[10vh]">
		<hr class="text-white mx-5" />
		<div class="max-w-screen-xl px-4 pt-8 mx-auto sm:px-6 lg:px-8">
			<div class="sm:flex sm:items-center sm:justify-between">
				<div class="flex justify-center text-teal-300 sm:justify-start">
					<Link
						:href="route('home')"
						class="py-1.75 text-sm mr-4 ml-4 whitespace-nowrap font-bold text-slate-700 lg:ml-0"
					>
						<ApplicationLogo />
					</Link>
				</div>

				<p class="mt-4 text-sm text-center text-gray-400 lg:text-right lg:mt-0">
					<div class="inline-flex">
						<Link
						:href="route('terms_and_condition')"
						class="py-1.75 text-sm mr-4 ml-4 whitespace-nowrap font-bold text-slate-700 lg:ml-0"
					>
						<span> T&C</span>
					</Link>
					<Link
						:href="route('privacy')"
						class="py-1.75 text-sm mr-4 ml-4 whitespace-nowrap font-bold text-slate-700 lg:ml-0"
					>
						<span> Privacy & Policy</span>
					</Link>
					</div>
				</p>
			</div>
		</div>
	</footer>
</template>

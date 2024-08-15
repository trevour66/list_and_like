<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
</script>

<template>
	<Head title="Dashboard" />

	<AuthenticatedLayout>
		<template #header>
			<div>
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">
					Dashboard
				</h2>
			</div>
			<div>
				<div class="flex flex-wrap -mx-3">
					<div class="flex items-center md:ml-auto md:pr-4">
						<div
							class="relative flex flex-wrap items-stretch w-full transition-all rounded-lg ease"
						>
							<input
								type="text"
								class="pl-3 text-sm focus:shadow-primary-outline ease w-1/100 leading-5.6 relative -ml-px block min-w-0 flex-auto rounded-lg border border-solid border-gray-300 dark:bg-slate-850 dark:text-white bg-white bg-clip-padding py-2 pr-9 text-gray-700 transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none focus:transition-shadow"
								placeholder="Type here..."
							/>
							<span
								class="text-md ease leading-5.6 absolute right-0 z-50 -ml-px flex h-full items-center whitespace-nowrap rounded-lg rounded-tr-none rounded-br-none border border-r-0 border-transparent bg-transparent py-2 px-2.5 text-center font-normal text-slate-500 transition-all"
							>
								<i class="fas fa-search"></i>
							</span>
						</div>
					</div>
				</div>
			</div>
		</template>

		<template #content>
			<div class="grid lg:grid-cols-2 grid-cols-1 pt-6 gap-3">
				<div
					class="relative flex flex-col flex-auto min-w-0 p-4 mx-3 overflow-hidden break-words bg-white border-0 dark:bg-slate-850 dark:shadow-dark-xl shadow-3xl rounded-2xl bg-clip-border"
				>
					<div class="flex flex-wrap -mx-3">
						<div class="flex-none w-auto max-w-full px-3">
							<div
								class="relative inline-flex items-center justify-center text-white transition-all duration-200 ease-in-out text-base h-19 w-19 rounded-xl"
							>
								<!-- <img
												src="../assets/img/team-1.jpg"
												alt="profile_image"
												class="w-full shadow-2xl rounded-xl"
											/> -->
							</div>
						</div>
						<div class="flex-none w-auto max-w-full my-auto">
							<div class="h-full">
								<h5 class="mb-0 dark:text-white">Sayo Kravits</h5>
								<p
									class="mb-0 font-semibold leading-normal dark:text-white dark:opacity-60 text-sm"
								>
									Public Relations
								</p>
							</div>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0 md:flex">
						<div
							class="md:w-6/12 w-full bg-gray-50 relative inline-flex items-center justify-center text-white transition-all duration-200 ease-in-out text-base h-60 rounded-xl"
						></div>
						<div
							class="md:w-6/12 w-full flex justify-center items-center px-3 py-4"
						>
							<p>
								Lorem ipsum dolor sit amet consectetur adipisicing elit.
								Incidunt facere reiciendis aspernatur et quia.
							</p>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0">
						<div class="relative">
							<ul
								class="relative flex flex-wrap p-1 list-none bg-gray-50 rounded-xl"
								nav-pills
								role="tablist"
							>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-all ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										active
										href="javascript:;"
										role="tab"
										aria-selected="true"
									>
										<div>
											<i class="far fa-heart"></i>
										</div>
									</a>
								</li>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-all ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="fas fa-external-link-alt"></i>
										</div>
									</a>
								</li>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-colors ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="fas fa-walking"></i>
										</div>
									</a>
								</li>

								<li class="z-30 flex-auto text-center">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-colors ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="far fa-heart"></i>
											<i class="mx-2">+</i>
											<i class="fas fa-walking"></i>
										</div>
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0">
						<div
							class="relative flex flex-wrap p-1 bg-gray-50 rounded-xl w-full"
						>
							<form class="w-full">
								<label for="chat" class="sr-only">Your message</label>
								<div
									class="flex flex-col items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700"
								>
									<textarea
										id="chat"
										rows="1"
										class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
										placeholder="Your comment..."
									></textarea>
									<div class="w-full">
										<button
											type="submit"
											class="inline-flex justify-center p-2 text-slate-700 rounded-lg cursor-pointer hover:bg-blue-100 dark:text-blue-500 dark:hover:bg-gray-600 border-2 mt-2 float-right"
										>
											<div>
												<i class="far fa-heart"></i>
												<i class="mx-2">+</i>
												<i class="far fa-paper-plane"></i>
											</div>
											<span class="sr-only">comment and like</span>
										</button>
										<span class="clear-both"></span>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div
					class="relative flex flex-col flex-auto min-w-0 p-4 mx-3 overflow-hidden break-words bg-white border-0 dark:bg-slate-850 dark:shadow-dark-xl shadow-3xl rounded-2xl bg-clip-border"
				>
					<div class="flex flex-wrap -mx-3">
						<div class="flex-none w-auto max-w-full px-3">
							<div
								class="relative inline-flex items-center justify-center text-white transition-all duration-200 ease-in-out text-base h-19 w-19 rounded-xl"
							>
								<!-- <img
												src="../assets/img/team-1.jpg"
												alt="profile_image"
												class="w-full shadow-2xl rounded-xl"
											/> -->
							</div>
						</div>
						<div class="flex-none w-auto max-w-full my-auto">
							<div class="h-full">
								<h5 class="mb-0 dark:text-white">Sayo Kravits</h5>
								<p
									class="mb-0 font-semibold leading-normal dark:text-white dark:opacity-60 text-sm"
								>
									Public Relations
								</p>
							</div>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0 md:flex">
						<div
							class="md:w-6/12 w-full bg-gray-50 relative inline-flex items-center justify-center text-white transition-all duration-200 ease-in-out text-base h-60 rounded-xl"
						></div>
						<div
							class="md:w-6/12 w-full flex justify-center items-center px-3 py-4"
						>
							<p>
								Lorem ipsum dolor sit amet consectetur adipisicing elit.
								Incidunt facere reiciendis aspernatur et quia.
							</p>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0">
						<div class="relative">
							<ul
								class="relative flex flex-wrap p-1 list-none bg-gray-50 rounded-xl"
								nav-pills
								role="tablist"
							>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-all ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										active
										href="javascript:;"
										role="tab"
										aria-selected="true"
									>
										<div>
											<i class="far fa-heart"></i>
										</div>
									</a>
								</li>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-all ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="fas fa-external-link-alt"></i>
										</div>
									</a>
								</li>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-colors ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="fas fa-walking"></i>
										</div>
									</a>
								</li>

								<li class="z-30 flex-auto text-center">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-colors ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="far fa-heart"></i>
											<i class="mx-2">+</i>
											<i class="fas fa-walking"></i>
										</div>
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0">
						<div
							class="relative flex flex-wrap p-1 bg-gray-50 rounded-xl w-full"
						>
							<form class="w-full">
								<label for="chat" class="sr-only">Your message</label>
								<div
									class="flex flex-col items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700"
								>
									<textarea
										id="chat"
										rows="1"
										class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
										placeholder="Your comment..."
									></textarea>
									<div class="w-full">
										<button
											type="submit"
											class="inline-flex justify-center p-2 text-slate-700 rounded-lg cursor-pointer hover:bg-blue-100 dark:text-blue-500 dark:hover:bg-gray-600 border-2 mt-2 float-right"
										>
											<div>
												<i class="far fa-heart"></i>
												<i class="mx-2">+</i>
												<i class="far fa-paper-plane"></i>
											</div>
											<span class="sr-only">comment and like</span>
										</button>
										<span class="clear-both"></span>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="grid lg:grid-cols-2 grid-cols-1 pt-6 gap-3">
				<div
					class="relative flex flex-col flex-auto min-w-0 p-4 mx-3 overflow-hidden break-words bg-white border-0 dark:bg-slate-850 dark:shadow-dark-xl shadow-3xl rounded-2xl bg-clip-border"
				>
					<div class="flex flex-wrap -mx-3">
						<div class="flex-none w-auto max-w-full px-3">
							<div
								class="relative inline-flex items-center justify-center text-white transition-all duration-200 ease-in-out text-base h-19 w-19 rounded-xl"
							>
								<!-- <img
												src="../assets/img/team-1.jpg"
												alt="profile_image"
												class="w-full shadow-2xl rounded-xl"
											/> -->
							</div>
						</div>
						<div class="flex-none w-auto max-w-full my-auto">
							<div class="h-full">
								<h5 class="mb-0 dark:text-white">Sayo Kravits</h5>
								<p
									class="mb-0 font-semibold leading-normal dark:text-white dark:opacity-60 text-sm"
								>
									Public Relations
								</p>
							</div>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0 md:flex">
						<div
							class="md:w-6/12 w-full bg-gray-50 relative inline-flex items-center justify-center text-white transition-all duration-200 ease-in-out text-base h-60 rounded-xl"
						></div>
						<div
							class="md:w-6/12 w-full flex justify-center items-center px-3 py-4"
						>
							<p>
								Lorem ipsum dolor sit amet consectetur adipisicing elit.
								Incidunt facere reiciendis aspernatur et quia.
							</p>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0">
						<div class="relative">
							<ul
								class="relative flex flex-wrap p-1 list-none bg-gray-50 rounded-xl"
								nav-pills
								role="tablist"
							>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-all ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										active
										href="javascript:;"
										role="tab"
										aria-selected="true"
									>
										<div>
											<i class="far fa-heart"></i>
										</div>
									</a>
								</li>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-all ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="fas fa-external-link-alt"></i>
										</div>
									</a>
								</li>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-colors ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="fas fa-walking"></i>
										</div>
									</a>
								</li>

								<li class="z-30 flex-auto text-center">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-colors ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="far fa-heart"></i>
											<i class="mx-2">+</i>
											<i class="fas fa-walking"></i>
										</div>
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0">
						<div
							class="relative flex flex-wrap p-1 bg-gray-50 rounded-xl w-full"
						>
							<form class="w-full">
								<label for="chat" class="sr-only">Your message</label>
								<div
									class="flex flex-col items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700"
								>
									<textarea
										id="chat"
										rows="1"
										class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
										placeholder="Your comment..."
									></textarea>
									<div class="w-full">
										<button
											type="submit"
											class="inline-flex justify-center p-2 text-slate-700 rounded-lg cursor-pointer hover:bg-blue-100 dark:text-blue-500 dark:hover:bg-gray-600 border-2 mt-2 float-right"
										>
											<div>
												<i class="far fa-heart"></i>
												<i class="mx-2">+</i>
												<i class="far fa-paper-plane"></i>
											</div>
											<span class="sr-only">comment and like</span>
										</button>
										<span class="clear-both"></span>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div
					class="relative flex flex-col flex-auto min-w-0 p-4 mx-3 overflow-hidden break-words bg-white border-0 dark:bg-slate-850 dark:shadow-dark-xl shadow-3xl rounded-2xl bg-clip-border"
				>
					<div class="flex flex-wrap -mx-3">
						<div class="flex-none w-auto max-w-full px-3">
							<div
								class="relative inline-flex items-center justify-center text-white transition-all duration-200 ease-in-out text-base h-19 w-19 rounded-xl"
							>
								<!-- <img
												src="../assets/img/team-1.jpg"
												alt="profile_image"
												class="w-full shadow-2xl rounded-xl"
											/> -->
							</div>
						</div>
						<div class="flex-none w-auto max-w-full my-auto">
							<div class="h-full">
								<h5 class="mb-0 dark:text-white">Sayo Kravits</h5>
								<p
									class="mb-0 font-semibold leading-normal dark:text-white dark:opacity-60 text-sm"
								>
									Public Relations
								</p>
							</div>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0 md:flex">
						<div
							class="md:w-6/12 w-full bg-gray-50 relative inline-flex items-center justify-center text-white transition-all duration-200 ease-in-out text-base h-60 rounded-xl"
						></div>
						<div
							class="md:w-6/12 w-full flex justify-center items-center px-3 py-4"
						>
							<p>
								Lorem ipsum dolor sit amet consectetur adipisicing elit.
								Incidunt facere reiciendis aspernatur et quia.
							</p>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0">
						<div class="relative">
							<ul
								class="relative flex flex-wrap p-1 list-none bg-gray-50 rounded-xl"
								nav-pills
								role="tablist"
							>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-all ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										active
										href="javascript:;"
										role="tab"
										aria-selected="true"
									>
										<div>
											<i class="far fa-heart"></i>
										</div>
									</a>
								</li>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-all ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="fas fa-external-link-alt"></i>
										</div>
									</a>
								</li>
								<li class="z-30 flex-auto text-center border-r-2">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-colors ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="fas fa-walking"></i>
										</div>
									</a>
								</li>

								<li class="z-30 flex-auto text-center">
									<a
										class="z-30 flex items-center justify-center w-full px-0 py-1 mb-0 transition-colors ease-in-out border-0 rounded-lg bg-inherit text-slate-700"
										nav-link
										href="javascript:;"
										role="tab"
										aria-selected="false"
									>
										<div>
											<i class="far fa-heart"></i>
											<i class="mx-2">+</i>
											<i class="fas fa-walking"></i>
										</div>
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="w-full max-w-full mx-auto mt-4 sm:mr-0">
						<div
							class="relative flex flex-wrap p-1 bg-gray-50 rounded-xl w-full"
						>
							<form class="w-full">
								<label for="chat" class="sr-only">Your message</label>
								<div
									class="flex flex-col items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700"
								>
									<textarea
										id="chat"
										rows="1"
										class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
										placeholder="Your comment..."
									></textarea>
									<div class="w-full">
										<button
											type="submit"
											class="inline-flex justify-center p-2 text-slate-700 rounded-lg cursor-pointer hover:bg-blue-100 dark:text-blue-500 dark:hover:bg-gray-600 border-2 mt-2 float-right"
										>
											<div>
												<i class="far fa-heart"></i>
												<i class="mx-2">+</i>
												<i class="far fa-paper-plane"></i>
											</div>
											<span class="sr-only">comment and like</span>
										</button>
										<span class="clear-both"></span>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</template>
	</AuthenticatedLayout>
</template>

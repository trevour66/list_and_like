<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { initDropdowns } from "flowbite";
import { onMounted } from "vue";

defineProps({
	ig_profiles: {
		type: Array,
		default: [],
	},
	user_lists: {
		type: Array,
		default: [],
	},
});

const ig_profile_is_already_in_list = (user_list_ids, list_id) => {
	return (user_list_ids ?? []).find((item) => item == list_id) ?? false;
};

const addUserToList = (list_id, ig_handle, user_list_ids) => {
	if (ig_profile_is_already_in_list(user_list_ids, list_id)) return;

	const form = useForm({
		ig_handle: ig_handle,
	});

	form.post(`/my-lists/${list_id}/add-profile`, {
		onFinish: () => {
			window.alert("IG profile added to list");
			form.reset();
		},
	});
};

onMounted(() => {
	initDropdowns();
});
</script>

<template>
	<Head title="IG Profiles" />

	<AuthenticatedLayout>
		<template #header>
			<div>
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">
					IG_Profiles
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
			<!-- {{ ig_profiles }} -->
			<!-- {{ route("proxy") }} -->
			<div
				class="grid lg:grid-cols-3 xl:grid-cols-4 grid-cols-1 pt-6 gap-3 mx-3"
			>
				<div v-for="(profile, index) in ig_profiles" :key="index">
					<div
						class="w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 px-5"
					>
						<!-- {{ profile }} -->
						<div class="flex justify-end px-4 pt-4">
							<button
								:id="`${index}_dropdownButton`"
								:data-dropdown-toggle="`${index}dropdown`"
								data-dropdown-placement="bottom-end"
								class="inline-block text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-1.5"
								type="button"
							>
								<span class="sr-only">Open dropdown</span>
								<svg
									class="w-5 h-5"
									aria-hidden="true"
									xmlns="http://www.w3.org/2000/svg"
									fill="currentColor"
									viewBox="0 0 16 3"
								>
									<path
										d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"
									/>
								</svg>
							</button>
							<!-- Dropdown menu -->
							<div
								:id="`${index}dropdown`"
								class="z-10 hidden text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700"
							>
								<div class="px-4 py-3 text-sm text-gray-700">
									<div class="font-bold truncate">Add to list</div>
								</div>
								<ul
									class="text-sm text-gray-700 dark:text-gray-200 h-36 overflow-y-auto"
									:aria-labelledby="`${index}_dropdownButton`"
								>
									<li v-for="(list, index) in user_lists" :key="index">
										<div
											@click="
												addUserToList(
													list._id,
													profile.ig_handle,
													profile?.user_list_ids ?? []
												)
											"
											class="h-full w-full px-4 py-4 hover:bg-gray-100 hover:cursor-pointer focus:bg-gray-100"
											:class="{
												'bg-gray-100': ig_profile_is_already_in_list(
													profile?.user_list_ids ?? [],
													list._id
												),
											}"
										>
											<!-- {{ list }} -->
											<span class="block">{{ list.list_name }}</span>
										</div>
									</li>
								</ul>
								<a
									href="#"
									class="flex items-center p-3 text-sm font-medium text-red-600 border-t border-gray-200 rounded-b-lg bg-gray-50 dark:border-gray-600 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-red-500 hover:underline"
								>
									<svg
										class="w-4 h-4 me-2"
										aria-hidden="true"
										xmlns="http://www.w3.org/2000/svg"
										fill="currentColor"
										viewBox="0 0 20 18"
									>
										<path
											d="M6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Zm11-3h-6a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2Z"
										/>
									</svg>
									Delete
								</a>
							</div>
						</div>
						<div class="flex flex-col items-center pb-10">
							<!-- <img
								class="w-24 h-24 mb-3 rounded-full shadow-lg"
								:src="profile.profile_pic"
								alt="Bonnie image"
							/> -->
							<h5
								class="mb-1 text-xl font-medium text-gray-900 dark:text-white"
							>
								{{ profile.ig_handle }}
							</h5>
							<div
								class="text-sm text-gray-500 my-2 h-[100px] flex items-center"
							>
								<span>{{ profile.bio }}</span>
							</div>
							<div class="flex my-3 gap-2">
								<span class="py-2 text-sm font-medium text-gray-700"
									>{{ profile.followers }}
									<span class="text-gray-500 text-xs">followers</span>
								</span>
								<span class="py-2 text-sm font-medium text-gray-700"
									>{{ profile.following }}
									<span class="text-gray-500 text-xs">following</span>
								</span>
								<span class="py-2 text-sm font-medium text-gray-700"
									>{{ profile.post_count }}
									<span class="text-gray-500 text-xs">posts</span>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</template>
	</AuthenticatedLayout>
</template>

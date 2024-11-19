<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
	href: {
		type: String,
		required: true,
	},
	active: {
		type: Boolean,
	},
});

const classes = computed(() =>
	props.active
		? "py-4 text-md text-gray-800 font-bold ease-nav-brand m-0 flex items-center whitespace-nowrap px-4 transition-colors bg-gray-200"
		: "py-4 text-md text-gray-600 ease-nav-brand m-0 flex items-center whitespace-nowrap px-4 transition-colors hover:bg-gray-200 hover:text-gray-800"
);
</script>

<template>
	<Link :href="href" :class="classes">
		<div
			class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5"
		>
			<slot name="icon" />
		</div>
		<span class="ml-1 duration-300 opacity-100 pointer-events-none ease text-lg"
			><slot name="text"
		/></span>
	</Link>
</template>

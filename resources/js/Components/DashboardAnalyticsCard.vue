<script setup>
import { randomColor } from "@/config/chartPalette";

const props = defineProps({
	title: {
		type: String,
		required: true,
	},

	value: {
		type: Number,
		required: true,
	},
});

const randomColorPairs = randomColor();
</script>

<template>
	<div class="w-full max-w-full px-3 mb-6">
		<div
			class="relative flex flex-col min-w-0 break-words bg-white shadow-xl rounded-2xl bg-clip-border"
		>
			<div class="flex-auto p-4">
				<div class="flex justify-between flex-row -mx-3">
					<div class="flex-none max-w-full px-3">
						<div>
							<p
								class="mb-0 text-gray-500 font-sans text-sm font-semibold leading-normal uppercase"
							>
								{{ title }}
							</p>
							<h5 class="mt-4 text-gray-700 font-bold">{{ value }}</h5>
						</div>
					</div>
					<div class="px-3 text-right">
						<div
							class="inline-block w-12 h-12 text-center rounded-full bg-gradient-to-tl shadow-lg flex items-center justify-center"
							:class="[
								`from-${randomColorPairs[0]}-500`,
								`to-${randomColorPairs[1]}-500`,
							]"
						>
							<slot name="icon"></slot>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

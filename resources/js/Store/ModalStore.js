import { defineStore } from "pinia";
import { computed, ref } from "vue";

const useModalStore = defineStore("modalStore", () => {
	const showNewListModal = ref(false);

	const getNewListModalStatus = computed(() => {
		return showNewListModal.value;
	});

	const toggelNewListModal = (status) => {
		showNewListModal.value = status;
	};

	return {
		showNewListModal,
		getNewListModalStatus,
		toggelNewListModal,
	};
});

export default useModalStore;

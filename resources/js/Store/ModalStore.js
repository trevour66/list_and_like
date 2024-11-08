import { defineStore } from "pinia";
import { computed, ref } from "vue";

const useModalStore = defineStore("modalStore", () => {
	const showNewListModal = ref(false);
	const showCommentsModal = ref(false);

	const getNewListModalStatus = computed(() => {
		return showNewListModal.value;
	});

	const getCommentsModalStatus = computed(() => {
		return showCommentsModal.value;
	});

	const toggelNewListModal = (status) => {
		showNewListModal.value = status;
	};

	const toggelCommentsModal = (status) => {
		showCommentsModal.value = status;
	};

	return {
		showNewListModal,
		showCommentsModal,
		getNewListModalStatus,
		getCommentsModalStatus,
		toggelNewListModal,
		toggelCommentsModal,
	};
});

export default useModalStore;

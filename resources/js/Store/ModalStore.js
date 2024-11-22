import { defineStore } from "pinia";
import { computed, ref } from "vue";

const useModalStore = defineStore("modalStore", () => {
	const showNewListModal = ref(false);
	const showCommentsModal = ref(false);

	const endOfModalBodyReached = ref(false);

	const getNewListModalStatus = computed(() => {
		return showNewListModal.value;
	});

	const getCommentsModalStatus = computed(() => {
		return showCommentsModal.value;
	});

	const getEndOfModalBodyReached = computed(() => {
		return endOfModalBodyReached.value;
	});

	const toggelNewListModal = (status) => {
		showNewListModal.value = status;
	};

	const toggelCommentsModal = (status) => {
		showCommentsModal.value = status;
	};

	const setEndOfModalBodyReached = (status) => {
		endOfModalBodyReached.value = status;
	};

	return {
		showNewListModal,
		showCommentsModal,
		endOfModalBodyReached,
		getNewListModalStatus,
		getCommentsModalStatus,
		getEndOfModalBodyReached,
		toggelNewListModal,
		toggelCommentsModal,
		setEndOfModalBodyReached,
	};
});

export default useModalStore;

import { defineStore } from "pinia";
import { computed, ref } from "vue";

const useModalStore = defineStore("modalStore", () => {
	const showNewListModal = ref(false);
	const showEditListModal = ref(false);
	const showCommentsModal = ref(false);
	const show_IGProfilePost_Modal = ref(false);
	const show_UserListWebHookDetails_Modal = ref(false);

	const endOfModalBodyReached = ref(false);

	const getNewListModalStatus = computed(() => {
		return showNewListModal.value;
	});

	const getEditListModalStatus = computed(() => {
		return showEditListModal.value;
	});

	const getCommentsModalStatus = computed(() => {
		return showCommentsModal.value;
	});

	const get_IGProfilePost_ModalStatus = computed(() => {
		return show_IGProfilePost_Modal.value;
	});

	const get_UserListWebHookDetails_ModalStatus = computed(() => {
		return show_UserListWebHookDetails_Modal.value;
	});

	const getEndOfModalBodyReached = computed(() => {
		return endOfModalBodyReached.value;
	});

	const toggelNewListModal = (status) => {
		showNewListModal.value = status;
	};

	const toggelEditListModal = (status) => {
		showEditListModal.value = status;
	};

	const toggelCommentsModal = (status) => {
		showCommentsModal.value = status;
	};

	const toggel_IGProfilePost_Modal = (status) => {
		show_IGProfilePost_Modal.value = status;
	};

	const toggel_UserListWebHookDetails_Modal = (status) => {
		show_UserListWebHookDetails_Modal.value = status;
	};

	const setEndOfModalBodyReached = (status) => {
		endOfModalBodyReached.value = status;
	};

	return {
		showNewListModal,
		showEditListModal,
		showCommentsModal,
		show_IGProfilePost_Modal,
		show_UserListWebHookDetails_Modal,
		endOfModalBodyReached,
		getNewListModalStatus,
		getEditListModalStatus,
		getCommentsModalStatus,
		get_IGProfilePost_ModalStatus,
		get_UserListWebHookDetails_ModalStatus,
		getEndOfModalBodyReached,
		toggelNewListModal,
		toggelEditListModal,
		toggelCommentsModal,
		toggel_IGProfilePost_Modal,
		toggel_UserListWebHookDetails_Modal,
		setEndOfModalBodyReached,
	};
});

export default useModalStore;

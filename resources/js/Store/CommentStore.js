import { defineStore } from "pinia";
import { computed, ref, reactive } from "vue";
import { useCookies } from "@vueuse/integrations/useCookies";

const useCommentStore = defineStore("CommentStore", () => {
	const CommentData = reactive({
		from_IG_username: "",
		IG_username_replying_to: "",
		IG_comment_replying_to: "",
		IG_Post: "",
		text: "",
	});

	const get_CommentData = computed(() => {
		return CommentData;
	});

	const set_CommentData = (
		from_IG_username,
		IG_username_replying_to,
		IG_comment_replying_to,
		IG_Post,
		text
	) => {
		CommentData.from_IG_username = from_IG_username;
		CommentData.IG_username_replying_to = IG_username_replying_to;
		CommentData.IG_comment_replying_to = IG_comment_replying_to;
		CommentData.IG_Post = IG_Post;
		CommentData.text = text;
	};

	const addText_CommentData = (text) => {
		CommentData.text = text;
	};

	const reset_CommentData = () => {
		CommentData.from_IG_username = "";
		CommentData.IG_username_replying_to = "";
		CommentData.IG_comment_replying_to = "";
		CommentData.IG_Post = "";
		CommentData.text = "";
	};

	const cancel_ReplyToSpecificUser = () => {
		CommentData.IG_username_replying_to = "";
		CommentData.IG_comment_replying_to = "";
	};

	return {
		CommentData,
		get_CommentData,
		set_CommentData,
		addText_CommentData,
		reset_CommentData,
		cancel_ReplyToSpecificUser,
	};
});

export default useCommentStore;

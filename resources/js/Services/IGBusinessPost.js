import axios from "axios";

const IGBusinessPost = {
	async getIGBusinessPost(IG_username, nextPageURL = "") {
		// console.log(IG_username);
		let url = route("my_post.index_api");

		if (nextPageURL != "") {
			url = nextPageURL;
		}

		return axios.post(url, {
			IG_username: IG_username,
		});
	},

	async getIGBusinessPostComments(post_id, nextPageURL = "") {
		// console.log(post_id);
		let url = route("my_post.get_comments_api");

		if (nextPageURL != "") {
			url = nextPageURL;
		}

		return axios.post(url, {
			post_id: post_id,
		});
	},

	async replyComments(
		post_id,
		ig_comment_replying_to,
		message,
		from_IG_username
	) {
		post_id = post_id ?? false;
		ig_comment_replying_to = ig_comment_replying_to ?? false;
		message = message ?? false;

		if (!post_id || !ig_comment_replying_to || !message || !from_IG_username) {
			throw new Error("required data not supplied");
		}

		let url = route("my_post.reply_to_comment_api");

		return axios.post(url, {
			post_id: post_id,
			ig_comment_replying_to: ig_comment_replying_to,
			message: message,
			from: from_IG_username,
		});
	},
};

export default IGBusinessPost;

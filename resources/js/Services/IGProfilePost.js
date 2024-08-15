import axios from "axios";

const IGProfilePost = {
	async getCommunityPosts(userAccessToken, nextPageURL = "") {
		let url = route("community.index_api");

		if (nextPageURL != "") {
			url = nextPageURL;
		}

		return axios.get(url, {
			headers: {
				Authorization: `Bearer ${userAccessToken}`,
			},
		});
	},

	async skipPost(userAccessToken, post_id) {
		console.log("here");
		if (!(post_id ?? false)) return;

		let url = route("ig_profile_post.skip", {
			post_id: post_id,
		});

		return axios.post(url, {
			headers: {
				Authorization: `Bearer ${userAccessToken}`,
			},
		});
	},
};

export default IGProfilePost;

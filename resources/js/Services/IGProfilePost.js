import axios from "axios";

const IGProfilePost = {
	async getIGProfilePosts(
		ig_handle = "",
		business_account_id = "",
		nextPageURL = ""
	) {
		if (ig_handle === "" || business_account_id === "") {
			return;
		}

		let url = route("community.get_ig_profile_posts_api");

		if (nextPageURL != "") {
			url = nextPageURL;
		}

		return axios.post(url, {
			ig_handle: ig_handle,
			business_account_id: business_account_id,
		});
	},

	async getCommunityPosts(business_account_id = "", nextPageURL = "") {
		if (business_account_id === "") {
			return;
		}

		let url = route("community.index_api");

		if (nextPageURL != "") {
			url = nextPageURL;
		}

		// return axios.get(url, {
		// 	headers: {
		// 		Authorization: `Bearer ${userAccessToken}`,
		// 	},
		// });

		return axios.post(url, {
			business_account_id: business_account_id,
		});
	},

	async skipPost(userAccessToken, post_id) {
		// console.log("here");
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

	async reactedToPost(userAccessToken, post_id) {
		// console.log("here");
		if (!(post_id ?? false)) return;

		let url = route("ig_profile_post.react", {
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

import axios from "axios";

const UserList = {
	async getUserList(IG_username) {
		// console.log(IG_username);
		let url = route("user_lists.index_api");

		return axios.post(url, {
			IG_username: IG_username,
		});
	},

	async getUserList_posts(
		userList_id,
		business_account_id = "",
		nextPageURL = ""
	) {
		if (userList_id === "" || business_account_id === "") {
			return;
		}

		let url = route("user_lists.show_posts_api", {
			userList: userList_id,
		});

		if (nextPageURL != "") {
			url = nextPageURL;
		}

		return axios.post(url, {
			business_account_id: business_account_id,
		});
	},

	async getUserList_profiles(userList_id, nextPageURL = "") {
		if (userList_id === "") {
			return;
		}

		let url = route("user_lists.show_profiles_api", {
			userList: userList_id,
		});

		if (nextPageURL != "") {
			url = nextPageURL;
		}

		return axios.post(url);
	},

	async delete_IG_profile_from_list(userList_id, ig_profile_id) {
		if (userList_id === "" || ig_profile_id === "") {
			return;
		}

		let url = route("user_lists.delete_IG_profile_from_list", {
			userList: userList_id,
			ig_profile_id: ig_profile_id,
		});

		return axios.post(url);
	},
};

export default UserList;

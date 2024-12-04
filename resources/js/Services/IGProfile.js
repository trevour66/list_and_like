import axios from "axios";

const IGProfile = {
	async getProfiles(nextPageURL = "") {
		let url = route("added_ig_profile.index_api");

		if (nextPageURL != "") {
			url = nextPageURL;
		}

		if (nextPageURL == null) {
			// console.log("hhghg");
			return;
		}

		// console.log(url);

		return axios.get(url);
	},

	async searchProfiles(nextPageURL = "", ig_handle = "") {
		if (ig_handle == "") return;

		let url = route("added_ig_profile.search_api");

		if (nextPageURL != "") {
			url = nextPageURL;
		}

		if (nextPageURL == null) {
			// console.log("hhghg");
			return;
		}

		// console.log(url);

		return axios.post(url, {
			ig_handle,
		});
	},
};

export default IGProfile;

import axios from "axios";

const IGProfile = {
	async getProfiles(userAccessToken, nextPageURL = "") {
		let url = route("added_ig_profile.index_api");

		if (nextPageURL != "") {
			url = nextPageURL;
		}

		return axios.get(url, {
			headers: {
				Authorization: `Bearer ${userAccessToken}`,
			},
		});
	},
};

export default IGProfile;

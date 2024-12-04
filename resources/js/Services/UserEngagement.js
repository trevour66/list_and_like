import axios from "axios";

const UserEngagement = {
	async getTopFive(business_account_id = "") {
		if (business_account_id === "") {
			return;
		}

		let url = route("engagements.top_five");

		return axios.post(url, {
			business_account_id: business_account_id,
		});
	},

	async getOtherProfiles(business_account_id = "", nextPageURL = "") {
		if (business_account_id === "") {
			return;
		}

		let url = route("engagements.others");

		if (nextPageURL != "") {
			url = nextPageURL;
		}

		// console.log(url);

		return axios.post(url, {
			business_account_id: business_account_id,
		});
	},
};

export default UserEngagement;

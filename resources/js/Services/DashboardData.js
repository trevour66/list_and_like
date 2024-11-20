import axios from "axios";

const DashboardData = {
	async getAnalytics(IG_username) {
		let url = route("dashboard.fetch_account_analytics_data");

		return axios.post(url, {
			IG_username: IG_username,
		});

		// return axios.post(
		// 	url,
		// 	{
		// 		IG_username: IG_username,
		// 	},
		// 	{
		// 		headers: {
		// 			Authorization: `Bearer ${userAccessToken}`,
		// 		},
		// 	}
		// );
	},

	async getCommunityData(IG_username, next_page_url) {
		let url =
			next_page_url != ""
				? next_page_url
				: route("dashboard.fetch_community_data");

		return axios.post(url, {
			IG_username: IG_username,
		});

		// return axios.post(
		// 	url,
		// 	{
		// 		IG_username: IG_username,
		// 	},
		// 	{
		// 		headers: {
		// 			Authorization: `Bearer ${userAccessToken}`,
		// 		},
		// 	}
		// );
	},
};

export default DashboardData;

import axios from "axios";

const DashboardData = {
	async getAnalytics(userAccessToken, IG_username) {
		let url = route("dashboard.fetch_account_analytics_data");

		return axios.post(
			url,
			{
				IG_username: IG_username,
			},
			{
				headers: {
					Authorization: `Bearer ${userAccessToken}`,
				},
			}
		);
	},

	async getCommunityData(userAccessToken, IG_username) {
		let url = route("dashboard.fetch_community_data");

		return axios.post(
			url,
			{
				IG_username: IG_username,
			},
			{
				headers: {
					Authorization: `Bearer ${userAccessToken}`,
				},
			}
		);
	},
};

export default DashboardData;

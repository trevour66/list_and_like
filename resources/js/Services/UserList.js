import axios from "axios";

const UserList = {
	async getUserList(IG_username) {
		// console.log(IG_username);
		let url = route("user_lists.index_api");

		return axios.post(url, {
			IG_username: IG_username,
		});
	},
};

export default UserList;

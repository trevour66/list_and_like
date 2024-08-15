<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\user_mongodb_subprofile;
use App\Models\ig_profile_post;

class IgProfilePostController extends Controller
{
    public function skip(Request $request, string $post_id)
    {
        try {
            logger($post_id ?? '');

            if (!($post_id ?? '')) {
                return;
            }

            $user = auth()->user();

            $user_mongodb_subprofile_user = user_mongodb_subprofile::where("user_id", "=", $user->id)->first() ?? false;

            if (!$user_mongodb_subprofile_user) {
                return;
            }
            $associated_ig_bussiness_acc = $user_mongodb_subprofile_user->IG_bussiness_accounts ?? [];

            $associated_ig_bussiness_acc_collection = collect($associated_ig_bussiness_acc);
            $unique = $associated_ig_bussiness_acc_collection->unique();
            $unique_arr = $unique->values()->all();

            if (count($unique_arr ?? []) > 0) {
                ig_profile_post::where('post_id', $post_id)
                    ->push(
                        'skipped_by',
                        $unique_arr,
                        unique: true
                    );
            }
            $resData = response(json_encode(
                [
                    'status' => "success",
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("Community API Error" . $th->getMessage());
            $resData = response(json_encode(
                [
                    'status' => "error",
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }
}

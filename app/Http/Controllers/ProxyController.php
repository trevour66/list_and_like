<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProxyController extends Controller
{
    public function fetchImage(Request $request)
    {
        try {
            //code...
            $user = $request->user();

            $IGAccessCodes = $user->IGAccessCodes ?? [];

            if (count($IGAccessCodes) == 0) {
            }

            // Define the URL of the image to fetch
            $imageUrl = 'https://instagram.fuln1-2.fna.fbcdn.net/v/t39.30808-6/452511201_17872530594113140_4090643350859880610_n.jpg?stp=dst-jpg_e35_p1080x1080_sh0.08&_nc_ht=instagram.fuln1-2.fna.fbcdn.net&_nc_cat=104&_nc_ohc=wgiJ9VYwTIQQ7kNvgH_xwU5&edm=AOQ1c0wAAAAA&ccb=7-5&ig_cache_key=MzQxODA4MDk2NzQzMTYyNzEzMQ%3D%3D.2-ccb7-5&oh=00_AYBfSNh4kSO8WULiIWorAbhJM8F6INRzqmALndid7_6V1Q&oe=66C253BF&_nc_sid=8b3546';


            // Fetch the image from the external URL
            $response = Http::withQueryParameters([
                "access_token" => $IGAccessCodes[0]->short_lived_access_token ?? '',
            ])->get($imageUrl);

            logger(print_r($response->body(), true));
            // Check if the response is successful
            if ($response->successful()) {
                // Return the image content with the correct content type
                return response($response->body(), 200)
                    ->header('Content-Type', $response->header('Content-Type'));
            }

            // Handle the error case
        } catch (\Throwable $th) {
            return response('Unable to fetch image', 500);
            //throw $th;
        }
    }
}

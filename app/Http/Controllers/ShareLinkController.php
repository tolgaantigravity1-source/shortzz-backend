<?php

namespace App\Http\Controllers;

use App\Models\Constants;
use App\Models\GlobalFunction;
use App\Models\GlobalSettings;
use App\Models\Posts;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShareLinkController extends Controller
{
    //

    public function encryptedId(Request $request)
    {
        $encryptedId = $request->encryptedId;
        $decoded = base64_decode($encryptedId);

        $result = null;
        $thumbUrl = null;
        $type = null;

        if (preg_match('/^post_(\d+)$/', $decoded, $matches)) {
            // Case: post
            $itemId = (int) $matches[1];
            $result = Posts::find($itemId);
            $type = 'Post';
            $title = $result->description;
            if($result->description == null){
                $title = 'Post By '.$result->user->fullname;
            }
            $thumbUrl = GlobalFunction::generateFileUrl($result->thumbnail);
            if ($result->post_type == Constants::postTypeImage) {
                $thumbUrl = GlobalFunction::generateFileUrl($result->images[0]->image);
            }
        } elseif (preg_match('/^reel_(\d+)$/', $decoded, $matches)) {
            // Case: drama
            $itemId = (int) $matches[1];
            $result = Posts::find($itemId);
            $type = 'Reel';
            $title = $result->description;
            if($result->description == null){
                $title = 'Post By '.$result->user->fullname;
            }
            $thumbUrl = GlobalFunction::generateFileUrl($result->thumbnail);
        } elseif (preg_match('/^user_(\d+)$/', $decoded, $matches)) {
            // Case: drama
            $itemId = (int) $matches[1];
            $result = Users::find($itemId);
            $type = 'User';
            $title = $result->fullname;
            $thumbUrl = GlobalFunction::generateFileUrl($result->profile_photo);
        } else {
            abort(404, 'Invalid ID format');
        }

        if (!$result) {
            abort(404, ucfirst($type) . ' not found');
        }

        $setting = GlobalSettings::first();

        return view('shareLinkPage', [
            'encryptedId' => $encryptedId,
            'decoded' => $decoded,
            'type' => $type,
            'data' => $result,
            'title' => $title,
            'setting' => $setting,
            'thumbUrl' => $thumbUrl,
        ]);
    }
}

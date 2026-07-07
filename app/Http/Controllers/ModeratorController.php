<?php

namespace App\Http\Controllers;

use App\Models\GlobalFunction;
use App\Models\Posts;
use App\Models\Story;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModeratorController extends Controller
{
    //
    public function moderator_unFreezeUser(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'user_id' => 'required|exists:tbl_users,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        if($user->is_moderator != 1){
            return GlobalFunction::sendSimpleResponse(false, 'you are not allowed to make this action!');
        }
        $dataUser = Users::find($request->user_id);

        $dataUser->is_freez = 0;
        $dataUser->save();

        return GlobalFunction::sendSimpleResponse(true, 'user un-freezed successfully');
    }
    public function moderator_freezeUser(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'user_id' => 'required|exists:tbl_users,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        if($user->is_moderator != 1){
            return GlobalFunction::sendSimpleResponse(false, 'you are not allowed to make this action!');
        }
        $dataUser = Users::find($request->user_id);

        if($user->id == $dataUser->id){
            return GlobalFunction::sendSimpleResponse(false, 'you can not freeze yourself!');
        }

        $dataUser->is_freez = 1;
        $dataUser->save();

        return GlobalFunction::sendSimpleResponse(true, 'user freezed successfully');
    }

    public function moderator_deleteStory(Request $request)
    {
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $validator = Validator::make($request->all(), [
            'story_id' => 'required|exists:stories,id',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        if($user->is_moderator != 1){
            return GlobalFunction::sendSimpleResponse(false, 'you are not allowed to make this action!');
        }

        $story = Story::find($request->story_id);

            if($story->content != null){
                GlobalFunction::deleteFile($story->content);
            }
            if ($story->thumbnail != null) {
                GlobalFunction::deleteFile($story->thumbnail);
            }
            $story->delete();

            return response()->json([
                'status' => true,
                'message' => 'Story delete successfully',
            ]);

    }

    public function moderator_deletePost(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'post_id' => 'required|exists:tbl_post,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' =>  $validator->errors()->first()]);
        }

        if($user->is_moderator != 1){
            return GlobalFunction::sendSimpleResponse(false, 'you are not allowed to make this action!');
        }

        $post = Posts::find($request->post_id);
        $post->delete();

        GlobalFunction::deleteAllPostData($post);

        return GlobalFunction::sendSimpleResponse(true, 'post deleted successfully!');
    }
}

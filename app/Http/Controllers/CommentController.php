<?php

namespace App\Http\Controllers;

use App\Models\CommentLikes;
use App\Models\CommentReplies;
use App\Models\Constants;
use App\Models\GlobalFunction;
use App\Models\GlobalSettings;
use App\Models\PostComments;
use App\Models\Posts;
use App\Models\Users;
use Egulias\EmailValidator\Parser\Comment;
use Google\Service\Blogger\PostReplies;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    //

    public function deleteComment_Admin(Request $request){
        $item = PostComments::find($request->id);
        $commentId = $item->id;
        $postId = $item->post_id;
        $item->delete();

        CommentReplies::where('comment_id', $commentId)->delete();
        GlobalFunction::settlePostCommentCount($postId);

        return GlobalFunction::sendSimpleResponse(true, 'comment deleted successfully');
    }

    public function deleteCommentReply_Admin(Request $request){
        $item = CommentReplies::find($request->id);
        $commentId = $item->comment_id;
        $item->delete();

        GlobalFunction::settleCommentsRepliesCount($commentId);
        return GlobalFunction::sendSimpleResponse(true, 'comment reply deleted successfully');
    }

    public function listCommentReplies(Request $request)
    {
        $query = CommentReplies::query();
        $query->where('comment_id', $request->commentId);
        $totalData = $query->count();

        $columns = ['id'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('reply', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $data = $result->map(function ($item) {

            $delete = "<a href='#'
            rel='{$item->id}'
            class='action-btn delete d-flex align-items-center justify-content-center btn border rounded-2 text-danger ms-1'>
              <i class='uil-trash-alt'></i>
            </a>";

            $action = "<span class='d-flex justify-content-end align-items-center'>{$delete}</span>";

            $replyUser = GlobalFunction::createUserDetailsColumn($item->user_id);

            $formattedReply = GlobalFunction::formatDescription($item->reply);

            $reply = '<div class="itemDescription d-inline">'.$formattedReply.'</div>';

            return [
                $reply,
                $replyUser,
                GlobalFunction::formateDatabaseTime($item->created_at),
                $action
            ];
        });

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        ];

        return response()->json($json_data);
    }
    public function listPostComments(Request $request)
    {
        $query = PostComments::query();
        $query->where('post_id', $request->postId);
        $totalData = $query->count();

        $columns = ['id'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('comment', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $data = $result->map(function ($item) {

            $replies = "<a href='#'
            rel='{$item->id}'
            class='action-btn show-replies d-flex align-items-center justify-content-center btn border rounded-2 text-info ms-1'>
              <i class='uil-comments'></i>
            </a>";
            $delete = "<a href='#'
            rel='{$item->id}'
            class='action-btn delete d-flex align-items-center justify-content-center btn border rounded-2 text-danger ms-1'>
              <i class='uil-trash-alt'></i>
            </a>";

            $action = "<span class='d-flex justify-content-end align-items-center'>{$replies}{$delete}</span>";

            $commentUser = GlobalFunction::createUserDetailsColumn($item->user_id);

            $states = GlobalFunction::createCommentStatesView($item->id);

            if($item->type == Constants::commentTypeImage){
                $formattedComment = '<img class="rounded" width="80" height="80" src='.$item->comment.' alt="">';
            }else{
                $formattedComment = GlobalFunction::formatDescription($item->comment);
            }

            $commentAndStats = '<div class="itemDescription d-inline">'.$formattedComment.'</div><div class="mt-1">'.$states.'</div>';

            return [
                $commentAndStats,
                $commentUser,
                GlobalFunction::formateDatabaseTime($item->created_at),
                $action
            ];
        });

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        ];

        return response()->json($json_data);
    }

    public function deleteCommentReply(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'reply_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $reply = CommentReplies::find($request->reply_id);
        $commentId = $reply->comment_id;

        if($reply == null){
            return GlobalFunction::sendSimpleResponse(false, 'reply does not exists!');
        }
        $reply->delete();

        GlobalFunction::settleCommentsRepliesCount($commentId);
        GlobalFunction::deleteNotifications(Constants::notify_reply_comment, $reply->id, $user->id);

        return GlobalFunction::sendSimpleResponse(true, 'reply deleted successfully');

    }
    public function deleteComment(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'comment_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $comment = PostComments::find($request->comment_id);
        $postId = $comment->post_id;

        if($comment == null){
            return GlobalFunction::sendSimpleResponse(false, 'comment does not exists!');
        }
        CommentReplies::where('comment_id', $comment->id)->delete();
        $comment->delete();

        GlobalFunction::settlePostCommentCount($postId);
        GlobalFunction::deleteNotifications(Constants::notify_comment_post,$comment->id,$user->id);

        return GlobalFunction::sendSimpleResponse(true, 'comment deleted successfully');

    }
    public function fetchPostCommentReplies(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'comment_id' => 'required',
            'limit' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $comment = PostComments::find($request->comment_id);

        if($comment == null){
            return GlobalFunction::sendSimpleResponse(false, 'comment does not exists!');
        }
        $query = CommentReplies::where('comment_id', $comment->id)
        ->orderBy('id', 'DESC')
        ->limit($request->limit)
        ->with(['user:'.Constants::userPublicFields]);
        if($request->has('last_item_id')){
            $query->where('id','<',$request->last_item_id);
        }
       $replies = $query ->get();

        if($replies->count() > 0){
            foreach($replies as $reply){
                $reply->mentionedUsers = Users::whereIn('id', explode(',', $reply->mentioned_user_ids))
                                        ->select(explode(',',Constants::userPublicFields))
                                        ->get();
            }
        }

        return GlobalFunction::sendDataResponse(true,'comment replies fetched successfully', $replies);

    }

    public function fetchPostComments(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'post_id' => 'required',
            'limit' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $post = GlobalFunction::preparePostFullData($request->post_id);

        if($post == null){
            return GlobalFunction::sendSimpleResponse(false, 'post does not exists!');
        }
        $pinnedComments = PostComments::where([
            'post_id'=> $post->id,
            'is_pinned'=> 1,
        ])
        ->with(['user:'.Constants::userPublicFields])
        ->get();

        $query = PostComments::where('post_id', $post->id)
                    ->orderBy('id', 'DESC')
                    ->limit($request->limit)
                    ->with(['user:'.Constants::userPublicFields]);
                    if($request->has('last_item_id')){
                        $query->where('id','<',$request->last_item_id);
                    }
                   $comments = $query ->get();

        // Like or not
        foreach($comments as $comment){
            $comment->is_liked = false;
            $like = CommentLikes::where('comment_id', $comment->id)->where('user_id', $user->id)->first();
            if($like){
                $comment->is_liked = true;
            }
            $comment->mentionedUsers = Users::whereIn('id', explode(',', $comment->mentioned_user_ids))
            ->select(explode(',',Constants::userPublicFields))
            ->get();

        }

        if($pinnedComments->count() > 0){
            foreach($pinnedComments as $comment){
                $comment->is_liked = false;
                $like = CommentLikes::where('comment_id', $comment->id)->where('user_id', $user->id)->first();
                if($like){
                    $comment->is_liked = true;
                }
            $comment->mentionedUsers = Users::whereIn('id', explode(',', $comment->mentioned_user_ids))
            ->select(explode(',',Constants::userPublicFields))
            ->get();
            }
        }
        // End : Like or not

        $data['comments'] = $comments;
        $data['pinnedComments'] = $pinnedComments;

        return GlobalFunction::sendDataResponse(true,'comments data fetched successfully', $data);

    }

    public function unPinComment(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'comment_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $comment = PostComments::find($request->comment_id);

        if($comment == null){
            return GlobalFunction::sendSimpleResponse(false, 'comment does not exists!');
        }
        $comment->is_pinned = 0; // 1=pinned 0=not
        $comment->save();

        return GlobalFunction::sendSimpleResponse(true, 'comment un-pinned successfull');

    }
    public function pinComment(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'comment_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $comment = PostComments::find($request->comment_id);
        if($comment == null){
            return GlobalFunction::sendSimpleResponse(false, 'comment does not exists!');
        }

        $settings = GlobalSettings::first();
        $pinnedCommentsCounts = PostComments::where([
            'post_id'=> $comment->post_id,
            'is_pinned'=> 1,
        ])->count();
        if($pinnedCommentsCounts >= $settings->max_comment_pins){
            return GlobalFunction::sendSimpleResponse(false, 'you can only pin only '.$settings->max_comment_pins.' comments for each post!');
        }


        $comment->is_pinned = 1; // 1=pinned 0=not
        $comment->save();

        return GlobalFunction::sendSimpleResponse(true, 'comment pinned successfull');

    }

    public function replyToComment(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'comment_id' => 'required',
            'reply' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $comment = PostComments::find($request->comment_id);

        if($comment == null){
            return GlobalFunction::sendSimpleResponse(false, 'comment does not exists!');
        }
         // Checking Daily Limit of reply
         $globalSettings = GlobalSettings::first();
         $commentCount = CommentReplies::where('user_id', $user->id)->whereDate('created_at', Carbon::today())->count();
         if ($commentCount >= $globalSettings->max_comment_reply_daily) {
             return ['status' => false, 'message' => "daily comment-reply limit exhausted!"];
         }
         $reply = new CommentReplies();
         $reply->user_id = $user->id;
         $reply->comment_id = $comment->id;
         $reply->reply = $request->reply;
         if ($request->has('mentioned_user_ids')) {
            $reply->mentioned_user_ids = GlobalFunction::cleanString($request->mentioned_user_ids);
        }
         $reply->save();
         // Insert Notification Data : Mention In Reply Of Comment
        if ($request->has('mentioned_user_ids')) {
            $mentionedUsers = Users::whereIn('id', explode(',',$reply->mentioned_user_ids))->get();
            foreach($mentionedUsers as $mUser){
                $notificationData = GlobalFunction::insertUserNotification(Constants::notify_mention_reply,$user->id, $mUser->id, $reply->id);
            }
        }

         $reply = CommentReplies::where('id', $reply->id)->with(['user:'.Constants::userPublicFields])->first();

         GlobalFunction::settleCommentsRepliesCount($comment->id);

         // Insert Notification Data : Reply Added
        $notificationData = GlobalFunction::insertUserNotification(Constants::notify_reply_comment,$user->id, $comment->user_id, $reply->id);

         return GlobalFunction::sendDataResponse(true, 'reply added successfully', $reply);


    }

    public function disLikeComment(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'comment_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $comment = PostComments::find($request->comment_id);

        if($comment == null){
            return GlobalFunction::sendSimpleResponse(false, 'comment does not exists!');
        }
        $like = CommentLikes::where([
            'user_id'=> $user->id,
            'comment_id'=> $comment->id
        ])->first();

        if($like == null){
            return GlobalFunction::sendSimpleResponse(false, 'comment like does not exists!');
        }
        $like->delete();

        GlobalFunction::settleCommentsLikesCount($comment->id);

        return GlobalFunction::sendSimpleResponse(true, 'comment disliked successfully');
    }
    public function fetchCommentByReplyId(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'reply_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $reply = CommentReplies::find($request->reply_id);

        if($reply == null){
            return GlobalFunction::sendSimpleResponse(false, 'reply does not exists!');
        }

        $comment = PostComments::where('id',$reply->comment_id)
                    ->with(['user:'.Constants::userPublicFields])
                    ->first();

        if($comment == null){
            return GlobalFunction::sendSimpleResponse(false, 'comment does not exists!');
        }

        $comment->is_liked = false;
        $like = CommentLikes::where('comment_id', $comment->id)->where('user_id', $user->id)->first();
        if($like){
            $comment->is_liked = true;
        }

        $comment->mentionedUsers = Users::whereIn('id', explode(',', $comment->mentioned_user_ids))
        ->select(explode(',',Constants::userPublicFields))
        ->get();

        return GlobalFunction::sendDataResponse(true, 'comment fetched successfully', $comment);
    }
    public function fetchCommentById(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'comment_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $comment = PostComments::where('id',$request->comment_id)
                    ->with(['user:'.Constants::userPublicFields])
                    ->first();

        if($comment == null){
            return GlobalFunction::sendSimpleResponse(false, 'comment does not exists!');
        }

        $comment->is_liked = false;
        $like = CommentLikes::where('comment_id', $comment->id)->where('user_id', $user->id)->first();
        if($like){
            $comment->is_liked = true;
        }

        $comment->mentionedUsers = Users::whereIn('id', explode(',', $comment->mentioned_user_ids))
        ->select(explode(',',Constants::userPublicFields))
        ->get();

        return GlobalFunction::sendDataResponse(true, 'comment fetched successfully', $comment);
    }
    public function likeComment(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'comment_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $comment = PostComments::find($request->comment_id);

        if($comment == null){
            return GlobalFunction::sendSimpleResponse(false, 'comment does not exists!');
        }
        $like = CommentLikes::where([
            'user_id'=> $user->id,
            'comment_id'=> $comment->id
        ])->first();
        if($like != null){
            return GlobalFunction::sendSimpleResponse(false, 'comment is liked already!');
        }
        $like = new CommentLikes();
        $like->comment_id = $comment->id;
        $like->user_id = $user->id;
        $like->save();

        GlobalFunction::settleCommentsLikesCount($comment->id);

        return GlobalFunction::sendSimpleResponse(true, 'comment liked successfully');
    }

    public function addPostComment(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'post_id' => 'required',
            'comment' => 'required',
            'type' => Rule::in([
                Constants::commentTypeText,
                Constants::commentTypeImage
            ]),
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $post = Posts::find($request->post_id);
        if($post == null){
            return GlobalFunction::sendSimpleResponse(false, 'post does not exists!');
        }
        // Checking Daily Limit
        $globalSettings = GlobalSettings::first();
        $commentCount = PostComments::where('user_id', $user->id)->whereDate('created_at', Carbon::today())->count();
        if ($commentCount >= $globalSettings->max_comment_daily) {
            return ['status' => false, 'message' => "daily comment limit exhausted!"];
        }
        // Add Comment
        $commentItem = new PostComments();
        $commentItem->user_id = $user->id;
        $commentItem->post_id = $post->id;
        $commentItem->type = $request->type;
        $commentItem->comment = $request->comment;
        if ($request->has('mentioned_user_ids')) {
            $commentItem->mentioned_user_ids = GlobalFunction::cleanString($request->mentioned_user_ids);
        }
        $commentItem->save();
        GlobalFunction::settlePostCommentCount($post->id);

        // Insert Notification Data : Mention In Comment
        if ($request->has('mentioned_user_ids')) {
            $mentionedUsers = Users::whereIn('id', explode(',',$commentItem->mentioned_user_ids))->get();
            foreach($mentionedUsers as $mUser){
                $notificationData = GlobalFunction::insertUserNotification(Constants::notify_mention_comment,$user->id, $mUser->id, $commentItem->id);
            }
        }

        // Insert Notification Data : Comment
        $notificationData = GlobalFunction::insertUserNotification(Constants::notify_comment_post,$user->id, $post->user_id, $commentItem->id);

        $comment = PostComments::where('id', $commentItem->id)->with(['user:'.Constants::userPublicFields])->first();
        return GlobalFunction::sendDataResponse(true, 'comment added successfully', $comment);
    }

}

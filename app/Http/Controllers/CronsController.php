<?php

namespace App\Http\Controllers;

use App\Models\CommentReplies;
use App\Models\Constants;
use App\Models\DailyActiveUsers;
use App\Models\GlobalFunction;
use App\Models\PostComments;
use App\Models\Posts;
use App\Models\Story;
use App\Models\UserNotification;
use App\Models\Users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CronsController extends Controller
{
    //
    public function reGeneratePlaceApiToken(){
        GlobalFunction::regenerateGoogleAPIToken();
    }

    function deleteExpiredStories(){
        $stories = Story::where('created_at', '<=', Carbon::now()->subDay())->get();
        foreach($stories as $story){
            if($story->content != null){
                GlobalFunction::deleteFile($story->content);
            }
            if($story->thumbnail != null){
                GlobalFunction::deleteFile($story->thumbnail);
            }
            $story->delete();
        }
    }
    function deleteOldNotifications(){
        UserNotification::where('created_at', '<', Carbon::now()->subDays(7))->delete();
    }
    function countDailyActiveUsers(){
        $yesterday = Carbon::yesterday();
        $start = $yesterday->startOfDay();
        $end = $yesterday->endOfDay();

        // Log::debug($yesterday.'---'. $start.'---'.$end);

        // Check if entry already exists
        $existing = DailyActiveUsers::where('date', $yesterday->toDateString())->first();

        if ($existing) {
            return;
        }

        $count = Users::whereDate('app_last_used_at', Carbon::yesterday()->toDateString())->count();

        $item = new DailyActiveUsers();
        $item->date =$yesterday->toDateString();
        $item->user_count = $count;
        $item->save();
    }

      function cleanDemoAppData(){
        $userIds = [8,1,20,25,28,27,35,39,41,42,45];
        // Posts
       Posts::whereNotIn('user_id', $userIds)
            ->chunk(100, function($posts) {
                foreach ($posts as $post) {
                    GlobalFunction::deleteAllPostData($post);
                    $post->delete();
                }
            });

        PostComments::whereNotIn('user_id', $userIds)
            ->chunk(100, function($comments) {
                foreach ($comments as $comment) {
                    $postId = $comment->post_id;
                    $userId = $comment->user_id;

                    CommentReplies::where('comment_id', $comment->id)->delete();
                    $comment->delete();

                    GlobalFunction::settlePostCommentCount($postId);
                    GlobalFunction::deleteNotifications(Constants::notify_comment_post, $comment->id, $userId);
                }
            });

    }
}

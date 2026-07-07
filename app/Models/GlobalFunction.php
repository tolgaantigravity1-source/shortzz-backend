<?php

namespace App\Models;

use Google\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use PhpParser\Node\Stmt\Return_;

class GlobalFunction extends Model
{
    use HasFactory;

    public static function fetchUserFollowingIds($userId)
    {
        return Followers::where('from_user_id', $userId)->pluck('to_user_id');
    }

    public static function deleteUserAccount($user)
    {
        // Minusing followers count of other users
        $followers = Followers::where('from_user_id', $user->id)->get();
        foreach ($followers as $follower) {
            $follower->delete();
            GlobalFunction::settleFollowCount($follower->to_user_id);
        }
        $followings = Followers::where('to_user_id', $user->id)->get();
        foreach ($followings as $following) {
            $following->delete();
            GlobalFunction::settleFollowCount($following->from_user_id);
        }
        // Comments
        $comments = PostComments::where('user_id', $user->id)->get();
        foreach ($comments as $comment) {
            $comment->delete();
            GlobalFunction::settlePostCommentCount($comment->post_id);
        }
        // Comment Likes
        $commentLikes = CommentLikes::where('user_id', $user->id)->get();
        foreach ($commentLikes as $commentLike) {
            $commentLike->delete();
        }
        // Comment Replies
        $commentReplies = CommentReplies::where('user_id', $user->id)->get();
        foreach ($commentReplies as $commentReply) {
            $commentReply->delete();
        }
        // post likes
        $postLikes = PostLikes::where('user_id', $user->id)->get();
        foreach ($postLikes as $postLike) {
            $postLike->delete();
            GlobalFunction::settlePostLikesCount($postLike->post_id);
        }
        // Self Posts
        $posts = Posts::where('user_id', $user->id)->get();
        foreach ($posts as $post) {
            $post->delete();
            GlobalFunction::deleteAllPostData($post);
        }

        // Other Data
        UserNotification::where('from_user_id', $user->id)->orWhere('to_user_id', $user->id)->delete();
        RedeemRequests::where('user_id', $user->id)->delete();
        ReportUsers::where('user_id', $user->id)->orWhere('by_user_id', $user->id)->delete();
        ReportPosts::where('by_user_id', $user->id)->delete();

        Story::where('user_id', $user->id)->delete();
        UserLinks::where('user_id', $user->id)->delete();
    }

    public static function getNotificationItemData($notifyItem, $user)
    {
        $data = null;
        switch ($notifyItem->type) {
            case Constants::notify_like_post:
                $post = GlobalFunction::preparePostFullData($notifyItem->data_id);
                $data['post'] = $post;
                break;
            case Constants::notify_comment_post:
                $comment = PostComments::find($notifyItem->data_id);
                $comment->mentionedUsers = Users::whereIn('id', explode(',', $comment->mentioned_user_ids))
                    ->select(explode(',', Constants::userPublicFields))
                    ->get();
                $data['comment'] = $comment;
                $data['post'] = GlobalFunction::preparePostFullData($comment->post_id);
                break;
            case Constants::notify_mention_post:
                $post = GlobalFunction::preparePostFullData($notifyItem->data_id);
                $data['post'] = $post;
                break;
            case Constants::notify_mention_comment:
                $comment = PostComments::find($notifyItem->data_id);
                $comment->mentionedUsers = Users::whereIn('id', explode(',', $comment->mentioned_user_ids))
                    ->select(explode(',', Constants::userPublicFields))
                    ->get();
                $data['comment'] = $comment;
                $data['post'] = GlobalFunction::preparePostFullData($comment->post_id);
                break;
            case Constants::notify_gift_user:
                $gift = Gifts::find($notifyItem->data_id);
                $data['gift'] = $gift;
                break;
            case Constants::notify_reply_comment:
                $reply = CommentReplies::find($notifyItem->data_id);
                $comment = PostComments::find($reply->comment_id);
                $data['reply'] = $reply;
                $data['comment'] = $comment;
                $data['post'] = GlobalFunction::preparePostFullData($comment->post_id);
                break;
            case Constants::notify_mention_reply:
                $reply = CommentReplies::find($notifyItem->data_id);
                $comment = PostComments::find($reply->comment_id);
                $data['reply'] = $reply;
                $data['comment'] = $comment;
                $data['post'] = GlobalFunction::preparePostFullData($comment->post_id);
                break;
        }

        if (Arr::has($data, 'post') && $data['post'] != null) {
            $post = $data['post'];

            $post->is_liked = PostLikes::where('post_id', $post->id)->where('user_id', $user->id)->exists();
            $post->is_saved = PostSaves::where('post_id', $post->id)->where('user_id', $user->id)->exists();
            $post->user->is_following = Followers::where('from_user_id', $user->id)->where('to_user_id', $post->user_id)->exists();
            $post->mentionedUsers = Users::whereIn('id', explode(',', $post->mentioned_user_ids))
                ->select(explode(',', Constants::userPublicFields))
                ->get();

            $data['post'] = $post;
        }

        return $data;
    }

    public static function deleteNotifications($notifyType, $dataId, $fromUserId = null)
    {
        switch ($notifyType) {
            case Constants::notify_like_post:
                UserNotification::where([
                    'type' => $notifyType,
                    'from_user_id' => $fromUserId,
                    'data_id' => $dataId,
                ])->delete();
                break;
            case Constants::notify_comment_post:
                UserNotification::whereIn('type', [Constants::notify_comment_post, Constants::notify_mention_comment])
                    ->where('data_id', $dataId)
                    ->where('from_user_id', $fromUserId)
                    ->delete();
                break;
            case Constants::notify_reply_comment:
                UserNotification::whereIn('type', [Constants::notify_reply_comment, Constants::notify_mention_reply])
                    ->where('data_id', $dataId)
                    ->where('from_user_id', $fromUserId)
                    ->delete();
                break;
            case Constants::notify_follow_user:
                UserNotification::whereIn('type', [Constants::notify_follow_user])
                    ->where('data_id', $dataId)
                    ->where('from_user_id', $fromUserId)
                    ->delete();
                break;
        }
    }

    public static function regenerateGoogleAPIToken()
    {
        $client = new Client();
        $client->setAuthConfig('googleCredentials.json');
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->addScope('https://www.googleapis.com/auth/cloud-platform');
        $client->fetchAccessTokenWithAssertion();
        $accessToken = $client->getAccessToken();
        $accessToken = $accessToken['access_token'];

        $setting = GlobalSettings::first();
        $setting->place_api_access_token = $accessToken;
        $setting->save();
    }

    public static function insertUserNotification($type, $fromUserId, $toUserId, $dataId)
    {
        // Store Notification
        if ($fromUserId != $toUserId) {
            $item = new UserNotification();
            $item->type = $type;
            $item->from_user_id = $fromUserId;
            $item->to_user_id = $toUserId;
            $item->data_id = $dataId;
            $item->save();
            return $item;
        }
        return null;
    }

    public static function initiatePushNotification($notifyON, $notSelfPost, $toUser, $title, $description, $notificationData)
    {
        if ($notifyON && $notSelfPost) {
            $notifyPayload = GlobalFunction::generatePushNotificationPayload($toUser->device, Constants::pushTypeToken, $toUser->device_token, $title, $description, null, $notificationData);
            GlobalFunction::sendPushNotification($notifyPayload);
        }
    }

    public static function generatePushNotificationPayload($deviceType, $topicOrToken, $pushToken, $title, $description, $image = null, $data = null)
    {
        $notificationPayload = [
            'title' => $title,
            'body' => $description,
        ];

        if ($image) {
            $notificationPayload['image'] = $image;
        }

        $messagePayload = [
            $topicOrToken => $pushToken,
        ];

        if ($data) {
            $data = array_map(fn($value) => (string) $value, $data);
        }

        switch ($deviceType) {
            case Constants::android:
                $messagePayload['data'] = $data ? array_merge($notificationPayload, $data) : $notificationPayload;
                $messagePayload['android'] = ['priority' => 'high'];
                break;

            case Constants::iOS:
                $messagePayload['notification'] = $notificationPayload;
                if ($data) {
                    $messagePayload['data'] = $data;
                }
                $messagePayload['apns'] = [
                    'payload' => ['aps' => ['sound' => 'default']],
                ];
                break;
        }

        return ['message' => $messagePayload];
    }

    public static function sendPushNotification($payload)
    {
        // Load Google credentials and fetch the access token
        $googleCredentialsPath = base_path('googleCredentials.json');
        $googleCredentials = json_decode(File::get($googleCredentialsPath), true);

        $settings = GlobalSettings::first();
        $accessToken = $settings->place_api_access_token;

        // FCM endpoint
        $url = 'https://fcm.googleapis.com/v1/projects/' . $googleCredentials['project_id'] . '/messages:send';

        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $payload);

        // Check if token is expired or not
        // Log::debug('Notification Response:', [$response->body()]);
        $responseBody = json_decode($response->body(), true);
        if (isset($responseBody['error']['code'])) {
            $errorCode = $responseBody['error']['code'];
            // Log::debug('iOS Notification Error Code:', $errorCode);
            if ($errorCode == 401) {
                // Regenerate Token
                GlobalFunction::regenerateGoogleAPIToken();
            }
            return;
        }
        return $response->successful();
    }

    public static function determineUserLevel($userId)
    {
        $user = Users::find($userId);

        // Get the highest level the user qualifies for
        $level = UserLevels::where('coins_collection', '<=', $user->coin_collected_lifetime)->orderBy('coins_collection', 'desc')->first();

        return $level ? $level->level : 1; // Default to level 1 if no match
    }

    public static function createUserModeratorSwitch($user, $type = 'all')
    {
        $checked = $user->is_moderator == Constants::isFreeze ? 'checked' : '';

        $moderatorSwitch = "<input type='checkbox' id='moderrator-status-{$user->id}{$type}' rel='{$user->id}' class='moderatorUser' {$checked} data-switch='none'/>
                    <label for='moderrator-status-{$user->id}{$type}'></label>";

        return $moderatorSwitch;
    }
    public static function createUserFreezeSwitch($user, $type = 'all')
    {
        $checked = $user->is_freez == Constants::isFreeze ? 'checked' : '';

        $freezeSwitch = "<input type='checkbox' id='block-all-user-{$user->id}{$type}' rel='{$user->id}' class='freezeUser' {$checked} data-switch='none'/>
                    <label for='block-all-user-{$user->id}{$type}'></label>";

        return $freezeSwitch;
    }
    public static function createPostDetailsButton($postId)
    {
        $view =
            "<a href=\"" .
            route('postDetails', $postId) .
            "\"
                      rel=\"{$postId}\"
                      class=\"action-btn delete d-flex align-items-center justify-content-center btn border rounded-2 text-info ms-1\">
                        <i class=\"ri-eye-line\"></i>
                    </a>";

        return $view;
    }
    public static function createPostDeleteButton($postId)
    {
        $delete = "<a href='#'
                          rel='{$postId}'
                          class='action-btn delete-post d-flex align-items-center justify-content-center btn border rounded-2 text-danger ms-1'>
                            <i class='uil-trash-alt'></i>
                        </a>";

        return $delete;
    }
    public static function createCommentStatesView($commentId)
    {
        $item = PostComments::find($commentId);

        $likes = "<span class='rounded-pill bg-light px-2 py-1 me-1 border'><i class='uil-heart fs-4'></i> : {$item->likes}</span>";
        $replies = "<span class='rounded-pill bg-light px-2 py-1  me-1 border'><i class='uil-comments fs-4'></i> : {$item->replies_count}</span>";

        $states = '<div class="">' . $likes . $replies . '</div>';

        return $states;
    }
    public static function createPostStatesView($postId)
    {
        $post = Posts::find($postId);
        $contentType = $post->post_type;

        $formattedLikes = GlobalFunction::formatNumber($post->likes);
        $formattedComments = GlobalFunction::formatNumber($post->comments);
        $formattedSaves = GlobalFunction::formatNumber($post->saves);
        $formattedShares = GlobalFunction::formatNumber($post->shares);
        $formattedViews = GlobalFunction::formatNumber($post->views);

        $likes = "<span class='d-inline-flex align-items-center rounded-pill bg-light px-2 py-1 me-1 my-1 border'><i class='uil-heart fs-4'></i> : {$formattedLikes}</span>";
        $comments = "<span class='d-inline-flex align-items-center rounded-pill bg-light px-2 py-1  me-1 my-1 border'><i class='uil-comment fs-4'></i> : {$formattedComments}</span>";
        $saves = "<span class='d-inline-flex align-items-center rounded-pill bg-light px-2 py-1  me-1 my-1 border'><i class='uil-bookmark fs-4'></i> : {$formattedSaves}</span>";
        $shares = "<span class='d-inline-flex align-items-center rounded-pill bg-light px-2 py-1  me-1 my-1 border'><i class='uil-share-alt fs-4'></i> : {$formattedShares}</span>";
        $postViews = '';
        if ($contentType == Constants::postTypeReel || $contentType == Constants::postTypeVideo) {
            $postViews = "<span class='d-inline-flex align-items-center rounded-pill bg-light px-2 py-1  me-1 my-1 border'><i class='uil-eye fs-4'></i> : {$formattedViews} </span>";
        }
        $states = '<div class="">' . $likes . $comments . $saves . $shares . $postViews . '</div>';

        return $states;
    }

    public static function createUserDetailsColumn($userId)
    {
        $user = Users::find($userId);
        if ($user == null) {
            return '';
        }

        $profileImageUrl = $user->profile_photo != null ? GlobalFunction::generateFileUrl($user->profile_photo) : url('assets/img/placeholder.png');

        $verifyIcon = '';
        if ($user->is_verify == 1) {
            $verifyIcon = '<img src=' . asset('assets/img/ic_verify.png') . ' alt="profile" class="rounded-circle object-fit-cover custom-width-18px custom-height-18px">';
        }
        $postUser =
            '
                     <div class="card rounded-pill w-auto d-inline-block pe-3 shadow-none bg-light m-0 border">
                        <a target="_blank" href="' .
            route('viewUserDetails', $user->id) .
            '" class="table-user d-flex p-1">
                        <img id="imgUserProfile-userDetails" src="' .
            $profileImageUrl .
            '" class="me-2 avatar-sm rounded-circle object-fit-cover bg-primary-lighten text-info rounded border">
                        <div class="d-grid text-start">
                            <div class="text-body text-dark fw-semibold">' .
            $user->username .
            $verifyIcon .
            '</div>
                            <div class="text-secondary fs-6">' .
            $user->fullname .
            '</div>
                        </div>
                        </a>
                    </div>';
        return $postUser;
    }

    public static function formatNumber($number, $precision = 1)
    {
        if ($number >= 1_000_000_000) {
            return number_format($number / 1_000_000_000, $precision) . 'B';
        } elseif ($number >= 1_000_000) {
            return number_format($number / 1_000_000, $precision) . 'M';
        } elseif ($number >= 1_000) {
            return number_format($number / 1_000, $precision) . 'K';
        } else {
            return $number;
        }
    }

    public static function createStoryTypeBadge($storyId)
    {
        $story = Story::find($storyId);

        switch ($story->type) {
            case Constants::storyTypeImage:
                $type = 'Image';
                break;
            case Constants::storyTypeVideo:
                $type = 'Video';
                break;
        }
        $storyType = "<span class='badge badge-info-lighten fs-5'>{$type}</span>";

        return $storyType;
    }
    public static function createUserTypeBadge($userId)
    {
        $user = Users::find($userId);

        switch ($user->is_dummy) {
            case Constants::userDummy:
                $userType = 'Dummy';
                break;
            case Constants::userReal:
                $userType = 'Real';
                break;
        }
        $userType = "<span class='badge badge-info-lighten fs-5'>{$userType}</span>";

        return $userType;
    }
    public static function createPostTypeBadge($postId)
    {
        $post = Posts::find($postId);

        switch ($post->post_type) {
            case Constants::postTypeImage:
                $postType = 'Image';
                break;
            case Constants::postTypeVideo:
                $postType = 'Video';
                break;
            case Constants::postTypeText:
                $postType = 'Text';
                break;
            case Constants::postTypeReel:
                $postType = 'Reel';
                break;
        }
        $postType = "<span class='badge badge-info-lighten fs-5'>{$postType}</span>";

        return $postType;
    }

    public static function createViewStoryButton($story)
    {
        $storyType = $story->type;

        $viewStory = '';
        $story->content = GlobalFunction::getItemBaseUrl() . $story->content;

        if ($storyType == Constants::storyTypeImage) {
            $viewStory =
                '<button type="button" data-contenttype=' .
                $storyType .
                ' class="btn btn-primary viewImageStory rounded-pill d-flex align-items-center commonViewBtn" data-bs-toggle="modal" data-bs-target="#imageStoryModal"  data-username=' .
                $story->user->username .
                ' data-content=' .
                $story->content .
                ' data-userid=' .
                $story->user->id .
                ' data-storyid="' .
                $story->id .
                '" rel="' .
                $story->id .
                '">
            <i class="uil-image-v fs-4 me-1" style="line-height: 18px;"></i> View Content</button>';
        } elseif ($storyType == Constants::storyTypeVideo) {
            $viewStory =
                '<button type="button" data-contenttype=' .
                $storyType .
                ' class="btn btn-primary viewVideoStory rounded-pill d-flex align-items-center commonViewBtn" data-bs-toggle="modal" data-bs-target="#videoStoryModal"  data-username=' .
                $story->user->username .
                ' data-content=' .
                $story->content .
                ' data-userid=' .
                $story->user->id .
                ' data-storyid="' .
                $story->id .
                '" rel="' .
                $story->id .
                '">
            <i class="uil-video fs-3 me-1" style="line-height: 18px;"></i> View Content</button>';
        }

        return $viewStory;
    }
    public static function createViewContentButton($post)
    {
        $contentType = $post->post_type;
        if ($contentType == Constants::postTypeImage) {
            $images = $post->images;
            foreach ($images as $image) {
                $image->image = GlobalFunction::getItemBaseUrl() . $image->image;
            }

            $viewContent =
                '<button type="button" data-contenttype=' .
                $contentType .
                ' class="btn btn-primary viewImagePost rounded-pill d-flex align-items-center commonViewBtn" data-bs-toggle="modal" data-bs-target="#imagePostModal"  data-username=' .
                $post->user->username .
                ' data-images=' .
                $images .
                ' data-userid=' .
                $post->user->id .
                '  data-desc="' .
                $post->description .
                '" data-postid="' .
                $post->id .
                '" rel="' .
                $post->id .
                '">
            <i class="uil-image-v fs-4 me-1" style="line-height: 18px;"></i> View Content</button>';
        } elseif ($contentType == Constants::postTypeVideo) {
            $videoUrl = GlobalFunction::generateFileUrl($post->video);
            $viewContent =
                '<button type="button" data-contenttype=' .
                $contentType .
                ' data-videourl=' .
                $videoUrl .
                ' class="btn btn-primary viewVideoPost rounded-pill d-flex align-items-center commonViewBtn" data-bs-toggle="modal" data-bs-target="#videoPostModal"  data-username=' .
                $post->user->username .
                ' data-userid=' .
                $post->user->id .
                '  data-desc="' .
                $post->description .
                '" data-postid="' .
                $post->id .
                '" rel="' .
                $post->id .
                '">
            <i class="uil-video fs-3 me-1" style="line-height: 18px;"></i> View Content</button>';
        } elseif ($contentType == Constants::postTypeText) {
            $viewContent =
                '<button type="button" data-contenttype=' .
                $contentType .
                ' class="btn btn-primary viewTextPost rounded-pill d-flex align-items-center commonViewBtn" data-bs-toggle="modal" data-bs-target="#textPostModal" data-username=' .
                $post->user->username .
                ' data-userid=' .
                $post->user->id .
                ' data-desc="' .
                $post->description .
                '" data-postid="' .
                $post->id .
                '" rel="' .
                $post->id .
                '">
            <i class="uil-text fs-3 me-1" style="line-height: 18px;"></i> View Content</button>';
        } elseif ($contentType == Constants::postTypeReel) {
            $videoUrl = GlobalFunction::generateFileUrl($post->video);
            $viewContent =
                '<button type="button" data-contenttype=' .
                $contentType .
                ' data-videourl=' .
                $videoUrl .
                ' class="btn btn-primary viewReelPost rounded-pill d-flex align-items-center commonViewBtn" data-bs-toggle="modal" data-bs-target="#videoPostModal" data-username=' .
                $post->user->username .
                ' data-desc="' .
                $post->description .
                '" data-userid=' .
                $post->user->id .
                ' data-postid="' .
                $post->id .
                '" rel="' .
                $post->id .
                '">
            <i class="uil-play fs-4 me-1" style="line-height: 18px;"></i> View Content</button>';
        }

        return $viewContent;
    }

    public static function generateRedeemRequestNumber($userId)
    {
        $number = $userId . '-' . rand(1000, 9999);
        $count = Users::where('username', $number)->count();

        while ($count >= 1) {
            $number = $userId . '-' . rand(1000, 9999);
            $count = Users::where('username', $number)->count();
        }
        return $number;
    }

    public static function deleteAllPostData($post)
    {
        PostLikes::where('post_id', $post->id)->delete();
        PostSaves::where('post_id', $post->id)->delete();
        ReportPosts::where('post_id', $post->id)->delete();

        // ***** Start : Delete notifications attached with that post

        UserNotification::whereIn('type', [Constants::notify_like_post, Constants::notify_mention_post])
            ->where('data_id', $post->id)
            ->delete();

        $commentIds = PostComments::where('post_id', $post->id)->pluck('id');
        $replyIds = CommentReplies::whereIn('comment_id', $commentIds)->pluck('id');

        //  Replies Notification Delete
        UserNotification::whereIn('type', [Constants::notify_reply_comment, Constants::notify_mention_reply])
            ->whereIn('data_id', $replyIds)
            ->delete();
        //  Comment Notification Delete
        UserNotification::whereIn('type', [Constants::notify_comment_post, Constants::notify_mention_comment])
            ->whereIn('data_id', $commentIds)
            ->delete();

        // ***** End : Delete notifications attached with that post

        $comments = PostComments::where('post_id', $post->id)->get();
        foreach ($comments as $comment) {
            CommentReplies::where('comment_id', $comment->id)->delete();
            CommentLikes::where('comment_id', $comment->id)->delete();
            $comment->delete();
        }

        // Deleting Images
        if ($post->images) {
            foreach ($post->images as $image) {
                GlobalFunction::deleteFile($image->image);
                $image->delete();
            }
        }

        // Minusing video count on hashtag
        if ($post->hashtags != null) {
            $hash_tag_array = explode(',', $post->hashtags);
            foreach ($hash_tag_array as $tag) {
                $hashtag = Hashtags::where('hashtag', $tag)->first();
                if ($hashtag != null) {
                    GlobalFunction::settleHashtagPostCounts($hashtag->id);
                }
            }
        }
        // Settle post count on sound
        if ($post->sound_id != null) {
            $sound = Musics::find($post->sound_id);
            if ($sound != null) {
                GlobalFunction::settleSoundPostCounts($sound->id);
            }
        }

        $user = Users::find($post->user_id);
        GlobalFunction::settleUserTotalPostLikesCount($user->id);
    }

    public static function processPostsListData($posts, $user)
    {
        $new_post_list = [];

        foreach ($posts as $post) {
            $post->is_liked = PostLikes::where('post_id', $post->id)->where('user_id', $user->id)->exists();
            $post->is_saved = PostSaves::where('post_id', $post->id)->where('user_id', $user->id)->exists();
            $post->user->is_following = Followers::where('from_user_id', $user->id)->where('to_user_id', $post->user_id)->exists();
            $post->mentioned_users = Users::whereIn('id', explode(',', $post->mentioned_user_ids))
                ->select(explode(',', Constants::userPublicFields))
                ->get();
            // Filter of who can view post
            $postUser = Users::find($post->user_id);
            if ($postUser->who_can_view_post == 1) {
                $follow = Followers::where('from_user_id', $user->id)->where('to_user_id', $postUser->id)->first();
                if ($follow != null || $post->user_id == $user->id) {
                    array_push($new_post_list, $post);
                }
            } else {
                array_push($new_post_list, $post);
            }
        }

        return $new_post_list;
    }

    public static function checkUserBlock($firstUserId, $secondUserId)
    {
        return UserBlocks::where(function ($query) use ($firstUserId, $secondUserId) {
            $query->where('from_user_id', $firstUserId)->where('to_user_id', $secondUserId);
        })
            ->orWhere(function ($query) use ($firstUserId, $secondUserId) {
                $query->where('from_user_id', $secondUserId)->where('to_user_id', $firstUserId);
            })
            ->exists();
    }

    public static function getUsersBlockedUsersIdsArray($userId)
    {
        $blockedUsers = UserBlocks::where('from_user_id', $userId)->pluck('to_user_id');
        $userBlockedMeIds = UserBlocks::where('to_user_id', $userId)->pluck('from_user_id');

        return array_merge($blockedUsers->toArray(), $userBlockedMeIds->toArray());
    }

    public static function settleHashtagPostCounts($hashtagId)
    {
        $hashtag = Hashtags::find($hashtagId);
        $hashtagText = $hashtag->hashtag;
        $hashtag->post_count = Posts::whereRaw("FIND_IN_SET('$hashtagText',hashtags)")->count();
        $hashtag->save();
    }
    public static function settleSoundPostCounts($soundId)
    {
        $sound = Musics::find($soundId);
        $sound->post_count = Posts::where('sound_id', $soundId)->count();
        $sound->save();
    }
    public static function settleFollowCount($userId)
    {
        $user = Users::find($userId);
        $user->following_count = Followers::where('from_user_id', $userId)->count();
        $user->follower_count = Followers::where('to_user_id', $userId)->count();
        $user->save();
    }
    public static function settlePostCommentCount($postId)
    {
        $post = Posts::find($postId);
        $post->comments = PostComments::where('post_id', $postId)->count();
        $post->save();
    }
    public static function settlePostSaveCount($postId)
    {
        $post = Posts::find($postId);
        $post->saves = PostSaves::where('post_id', $postId)->count();
        $post->save();
    }
    public static function settleUserTotalPostLikesCount($userId)
    {
        $user = Users::find($userId);
        $user->total_post_likes_count = Posts::where('user_id', $userId)->sum('likes');
        $user->save();
    }
    public static function settlePostLikesCount($postId)
    {
        $post = Posts::find($postId);
        $post->likes = PostLikes::where('post_id', $postId)->count();
        $post->save();
    }
    public static function settleCommentsLikesCount($id)
    {
        $item = PostComments::find($id);
        $item->likes = CommentLikes::where('comment_id', $id)->count();
        $item->save();
    }
    public static function settleCommentsRepliesCount($id)
    {
        $item = PostComments::find($id);
        $item->replies_count = CommentReplies::where('comment_id', $id)->count();
        $item->save();
    }

    public static function generatePost($request, $postType, $user, $sound = null)
    {
        $post = new Posts();
        $post->user_id = $user->id;
        $post->post_type = $postType;
        $post->can_comment = $request->can_comment;
        if ($request->has('description')) {
            $post->description = $request->description;
        }
        if ($request->has('mentioned_user_ids')) {
            $post->mentioned_user_ids = GlobalFunction::cleanString($request->mentioned_user_ids);
        }
        if ($request->has('metadata')) {
            $post->metadata = $request->metadata;
        }
        // Location
        if ($request->has('place_title')) {
            $post->place_title = $request->place_title;
        }
        if ($request->has('place_lat')) {
            $post->place_lat = $request->place_lat;
        }
        if ($request->has('place_lon')) {
            $post->place_lon = $request->place_lon;
        }
        if ($request->has('state')) {
            $post->state = $request->state;
        }
        if ($request->has('country')) {
            $post->country = $request->country;
        }
        // Hashtags
        if ($request->has('hashtags')) {
            $post->hashtags = GlobalFunction::cleanString($request->hashtags);

            $hash_tag_array = explode(',', $request->hashtags);
            foreach ($hash_tag_array as $tag) {
                if ($tag != '') {
                    $hashtag = Hashtags::where('hashtag', $tag)->first();
                    if ($hashtag == null) {
                        $newHashtag = new Hashtags();
                        $newHashtag->hashtag = $tag;
                        $newHashtag->post_count = 1;
                        $newHashtag->save();
                    } else {
                        $hashtag->post_count = $hashtag->post_count + 1;
                        $hashtag->save();
                    }
                }
            }
        }

        // Reel & Feed Video : Video & Thumb
        if ($postType == Constants::postTypeReel || $postType == Constants::postTypeVideo) {
            $post->video = $request->video;
            $post->thumbnail = $request->thumbnail;
        }

        // sound check (Reels post only for now)
        if ($sound != null) {
            $sound->post_count += 1;
            $sound->save();
            $post->sound_id = $request->sound_id;
        }
        $post->save();

        // Image Post
        if ($postType == Constants::postTypeImage) {
            foreach ($request->post_images as $image) {
                $postImage = new PostImages();
                $postImage->post_id = $post->id;
                $postImage->image = $image;
                $postImage->save();
            }
        }

        // Insert Notification Data : Post Mention
        if ($request->has('mentioned_user_ids')) {
            $mentionedUsers = Users::whereIn('id', explode(',', $post->mentioned_user_ids))->get();
            foreach ($mentionedUsers as $mUser) {
                $notificationData = GlobalFunction::insertUserNotification(Constants::notify_mention_post, $user->id, $mUser->id, $post->id);
            }
        }

        $post = GlobalFunction::preparePostFullData($post->id);

        return $post;
    }

    public static function checkIfUserCanPost($user)
    {
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => 'this user is freezed!'];
        }

        $globalSettings = GlobalSettings::first();
        $postsCount = Posts::where('user_id', $user->id)->whereDate('created_at', Carbon::today())->count();
        if ($postsCount >= $globalSettings->max_upload_daily) {
            return ['status' => false, 'message' => 'daily post upload limit exhausted!'];
        }
        return ['status' => true];
    }

    public static function saveFileInLocal($file, $filename)
    {
        if ($file != null) {
            $filePath = public_path('assets/img/' . $filename);
            $file->move(public_path('assets/img'), $filename);
            return $filePath;
        } else {
            return null;
        }
    }

    public static function prepareStoryFullData($id)
    {
        $item = Story::where('id', $id)
            ->with(['user:' . Constants::userPublicFields])
            ->with(['music'])
            ->first();

        return $item;
    }
    public static function preparePostFullData($id)
    {
        $item = Posts::where('id', $id)->with(Constants::postsWithArray)->first();

        if ($item != null) {
            $item->mentioned_users = Users::whereIn('id', explode(',', $item->mentioned_user_ids))->get();
        }

        return $item;
    }

    public static function prepareUserFullData($id)
    {
        return Users::with([
            'links',
            'stories' => function ($query) {
                $query->where('created_at', '>=', now()->subDay());
            },
            'stories.music',
        ])
            ->where('id', $id)
            ->first();
    }

    public static function cleanString($string)
    {
        return str_replace(['<', '>', '{', '}', '[', ']', '`'], '', $string);
    }
    public static function generateDummyUserIdentity()
    {
        $token = rand(100000, 999999);

        $first = GlobalFunction::generateRandomString(3);
        $first .= $token;
        $first .= GlobalFunction::generateRandomString(3);
        $count = Users::where('username', $first)->count();

        while ($count >= 1) {
            $token = rand(100000, 999999);
            $first = GlobalFunction::generateRandomString(3);
            $first .= $token;
            $first .= GlobalFunction::generateRandomString(3);
            $count = Users::where('username', $first)->count();
        }

        return $first;
    }
    public static function generateUsername($fullname)
    {
        // Remove spaces and prepare the base username
        $fullname = str_replace(' ', '', $fullname);

        do {
            // Generate a random token and append it to the base username
            $username = $fullname . rand(1, 9999);

            // Check if the generated username already exists
            $exists = Users::where('username', $username)->exists();
        } while ($exists); // Repeat until a unique username is found

        return $username;
    }
    public static function getUserFromAuthToken($token)
    {
        $userToken = UserAuthTokens::where('auth_token', $token)->first();

        if ($userToken == null) {
            return null;
        }
        $user = Users::find($userToken->user_id);
        return $user;
    }

    public static function generateRandomString($length)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function saveFileAndGivePath($file)
    {
        $storageType = env('FILES_STORAGE_LOCATION');

        // Clean APP_NAME (remove spaces and special characters)
        $rawAppName = env('APP_NAME');
        $cleanAppName = $rawAppName ? preg_replace('/[^A-Za-z0-9_\-]/', '_', $rawAppName) : '';

        // Create file name without spaces
        $fileName = time() . '_' . $cleanAppName . '_' . str_replace(' ', '_', $file->getClientOriginalName());

        // Set base path with cleaned app name
        $appNamePath = $cleanAppName ? $cleanAppName . '/' : '';

        $filePath = $storageType === 'PUBLIC' ? 'uploads/' . $fileName : $appNamePath . 'uploads/' . $fileName;

        // Store file in the appropriate disk
        switch ($storageType) {
            case 'AWSS3':
                Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                break;
            case 'DOSPACE':
                Storage::disk('digitalocean')->put($filePath, file_get_contents($file), 'public');
                break;
            case 'PUBLIC':
                Storage::disk('public')->put($filePath, file_get_contents($file), 'public');
                break;
        }

        return $filePath;
    }

    public static function deleteFile($filePath)
    {
        $storageType = env('FILES_STORAGE_LOCATION');

        switch ($storageType) {
            case 'AWSS3':
                // Check and delete the file from AWS S3
                if (Storage::disk('s3')->exists($filePath)) {
                    Storage::disk('s3')->delete($filePath);
                }
                break;
            case 'DOSPACE':
                // Check and delete the file from DigitalOcean Spaces
                if (Storage::disk('digitalocean')->exists($filePath)) {
                    Storage::disk('digitalocean')->delete($filePath);
                }
                break;
            case 'PUBLIC':
                // Check and delete the file from local storage
                if (Storage::disk('local')->exists('public/' . $filePath)) {
                    Storage::disk('local')->delete('public/' . $filePath);
                }
                break;
        }
    }

    public static function getItemBaseUrl()
    {
        $storageType = env('FILES_STORAGE_LOCATION');
        switch ($storageType) {
            case 'AWSS3':
                return env('AWS_ITEM_BASE_URL');
            case 'DOSPACE':
                return env('DO_SPACE_URL');
            case 'PUBLIC':
                return env('APP_URL') . 'public/storage/';
        }
    }
    public static function generateFileUrl($filePath)
    {
        if ($filePath != null) {
            $storageType = env('FILES_STORAGE_LOCATION');
            switch ($storageType) {
                case 'AWSS3':
                    return env('AWS_ITEM_BASE_URL') . $filePath;
                case 'DOSPACE':
                    return env('DO_SPACE_URL') . $filePath;
                case 'PUBLIC':
                    return env('APP_URL') . 'public/storage/' . $filePath;
            }
        }else{
            return null;
        }
    }

    public static function sendSimpleResponse($status, $msg)
    {
        return response()->json(['status' => $status, 'message' => $msg]);
    }

    public static function sendDataResponse($status, $msg, $data)
    {
        return response()->json(['status' => $status, 'message' => $msg, 'data' => $data]);
    }

    public static function formateDatabaseTime($time)
    {
        return $time->format('d M Y');
    }

    public static function generateUserAuthToken($user)
    {
        UserAuthTokens::where('user_id', $user->id)->delete();
        $token = new UserAuthTokens();
        $token->user_id = $user->id;
        $token->auth_token = Crypt::encryptString($user->identity . Carbon::now());
        $token->save();

        return $token;
    }

    public static function formatDescription($description)
    {
        // Use regex to find all mentions of @<id>
        $pattern = '/@(\d+)/';

        // Callback function to replace user IDs with usernames and add a URL
        $description = preg_replace_callback(
            $pattern,
            function ($matches) {
                $userId = $matches[1]; // Extract the user ID from the match
                // Fetch the username corresponding to the user ID
                $username = Users::where('id', $userId)->value('username');

                if ($username) {
                    // Generate a URL for the user and wrap the username in an anchor tag
                    $url = route('viewUserDetails', $userId);
                    return '<a target="_blank" href="' . $url . '" class="username">@' . $username . '</a>';
                }

                // If the user is not found, return the original mention
                return $matches[0];
            },
            $description,
        );

        // Make hashtags clickable with a URL
        $description = preg_replace_callback(
            '/#(\w+)/',
            function ($matches) {
                $hashtag = $matches[1]; // Extract the hashtag
                // Generate a URL for the hashtag and wrap it in an anchor tag
                $url = route('hashtagDetails', $hashtag);
                return '<a target="_blank" href="' . $url . '" class="hashtag">#' . $hashtag . '</a>';
            },
            $description,
        );

        return $description;
    }

    public static function getPhoneCountryCodes()
    {
        $filePath = public_path('assets/csv/phone_country_codes.csv'); // Update with your file name
        $csvData = [];

        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = fgetcsv($handle); // Read the first row as headers

            while (($row = fgetcsv($handle)) !== false) {
                $csvData[] = array_combine($headers, $row);
            }
            fclose($handle);
        }

        return $csvData;
    }
}

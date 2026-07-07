<?php

namespace App\Models;

final class Constants
{

    const isDeletedNo = 0;
    const isDeletedYes = 1;

    const userTypeAdmin = 1;
    const userTypeUser = 0;

    const isFreeze = 1;
    const isFreezeNot = 0;

    const android = 0;
    const iOS = 1;

    const pushTypeTopic = 'topic';
    const pushTypeToken = 'token';

    const isNotifyYes = 1;

    const credit = 1;
    const debit = 0;

    const withdrawalPending = 0;
    const withdrawalCompleted = 1;
    const withdrawalRejected = 2;

    const postTypeReel = 1;
    const postTypeImage = 2;
    const postTypeVideo = 3;
    const postTypeText = 4;

    const storyTypeImage = 0;
    const storyTypeVideo = 1;

    const commentTypeText = 0;
    const commentTypeImage = 1;

    const notify_like_post = 1;
    const notify_comment_post = 2;
    const notify_mention_post = 3;
    const notify_mention_comment = 4;
    const notify_follow_user = 5;
    const notify_gift_user = 6;
    const notify_reply_comment = 7;
    const notify_mention_reply = 8;

    const userDummy = 1;
    const userReal = 0;

    const userPublicFields= 'id,username,fullname,bio,profile_photo,is_verify,device,device_token,app_language,notify_post_like,notify_post_comment,notify_follow,notify_mention,notify_gift_received,notify_chat,coin_collected_lifetime,total_post_likes_count,following_count,follower_count,receive_message';

    const postsWithArray = ['images','music','user.links','user.stories','user:'.Constants::userPublicFields];

}

# Date: 18 Nov 2025

## Summary
- Fixed Repeat Notification issue

#### Updated Files
- README.md
- app/Http/Controllers/NotificationController.php

#### Added Files
None

#### Deleted Files
None

### Database
None

******************************
# Date: 26 Sept 2025

## Summary
- Forget password option added in admin log in page with database user & password validation
- Developed own deeplinking function & removed branch.io
- Some other changes

#### Updated Files
- [README.md](README.md)
- [LoginController.php](app/Http/Controllers/LoginController.php)
- [SettingsController.php](app/Http/Controllers/SettingsController.php)
- [GlobalFunction.php](app/Models/GlobalFunction.php)
- [min.css](public/assets/css/app-saas.min.css)
- [login.js](public/assets/script/login.js)
- [settings.js](public/assets/script/settings.js)
- [blade.php](resources/views/login.blade.php)
- [blade.php](resources/views/settings.blade.php)
- [api.php](routes/api.php)
- [web.php](routes/web.php)

#### Added Files
- [ShareLinkController.php](app/Http/Controllers/ShareLinkController.php)
- [apple-app-site-association](public/assets/apple-app-site-association)
- [assetlinks.json](public/assets/assetlinks.json)
- [appstore.png](public/assets/img/appstore.png)
- [download.png](public/assets/img/download.png)
- [playstore.png](public/assets/img/playstore.png)
- [shareLinkPage.blade.php](resources/views/shareLinkPage.blade.php)

#### Deleted Files
None

### Database
- tbl_settings : fields added : uri_scheme, app_store_download_link, play_store_download_link

******************************
# Date: 6 Sept 2025

## Summary 
- Fake user log in issue fixed

#### Updated Files
- [README.md](README.md)
- [UserController.php](app/Http/Controllers/UserController.php)
- [api.php](routes/api.php)

#### Added Files
None

#### Deleted Files
None

### Database
None

******************************
# Date: 4 Sept 2025

## Summary 
- Wallet Coin deduct issue while withdrawals fix

#### Updated Files
- [README.md](README.md)
- [WalletController.php](app/Http/Controllers/WalletController.php)

#### Added Files
None

#### Deleted Files
None

### Database
None

******************************

# Date: 24 July 2025

## Summary 
- Some minor fixes

#### Updated Files
- [web.php](routes/web.php)
- [README.md](README.md)


#### Added Files
None

#### Deleted Files
None

### Database
None

******************************

# Date: 15 July 2025

## Summary 
- Some minor fixes

#### Updated Files
- [SettingsController.php](app/Http/Controllers/SettingsController.php)

#### Added Files
None

#### Deleted Files
None

### Database
None

******************************

# Date: 3 July 2025

## Summary 
- Some minor fixes

#### Updated Files
- [CommentController.php](app/Http/Controllers/CommentController.php)
- [DashboardController.php](app/Http/Controllers/DashboardController.php)
- [UserController.php](app/Http/Controllers/UserController.php)
- [WalletController.php](app/Http/Controllers/WalletController.php)
- [GlobalFunction.php](app/Models/GlobalFunction.php)
- [api.php](routes/api.php)

#### Added Files
None

#### Deleted Files
None

### Database
None

******************************

# Date: 20 June 2025

## Summary 
- Some minor fixes

#### Updated Files
- [login.blade.php](resources/views/login.blade.php)
- [app.blade.php](resources/views/include/app.blade.php)

#### Added Files
None

#### Deleted Files
None

### Database
None

*****************************
# Date: 17 June 2025

## Summary 
- Some fixes

#### Updated Files
- [CronsController.php](app/Http/Controllers/CronsController.php)
- [LanguageController.php](app/Http/Controllers/LanguageController.php)
- [LoginController.php](app/Http/Controllers/LoginController.php)
- [MusicController.php](app/Http/Controllers/MusicController.php)
- [PostsController.php](app/Http/Controllers/PostsController.php)
- [SettingsController.php](app/Http/Controllers/SettingsController.php)

- [CheckLogin.php](app/Http/Middleware/CheckLogin.php)
- [app-saas.min.css](public/assets/css/app-saas.min.css)

- [hashtagDetails.blade.php](resources/views/hashtagDetails.blade.php)
- [posts.blade.php](resources/views/posts.blade.php)
- [settings.blade.php](resources/views/settings.blade.php)
- [viewUserDetails.blade.php](resources/views/viewUserDetails.blade.php)

- [api.php](routes/api.php)
- [web.php](routes/web.php)

#### Added Files


#### Deleted Files

### Database
tbl_admin : admin_password (Field type changed to text)


*****************************

# Date: 16 June 2025

## Summary 
---- Release of Shortzz 2.0 packed with amazing features ----
### For new buyers (Buying Shortzz 2.0)
You don’t have to take care of this note, follow documentation and configure your project.

### Existing Buyers (Already bought Shortzz before 2.0)
We’ve rolled out a major update to the project, which includes a wide range of advanced features, critical security patches, and significant changes to the database structure. Implementing these improvements required a complete rewrite of both the backend and Flutter codebases from scratch.
We would like to take a moment to clarify a few important points, and we kindly request all existing users to please take note.

Summary of Key Changes
Rebuilt the entire backend from scratch.
Rebuilt the entire Flutter app from scratch.
Made extensive database modifications, including renaming, adding, and deleting fields and tables.

There are two ways to proceed with the updated version 2.0 of the Shortzz app for existing customers before 2.0 update.
*********************
1. Update your project from scratch : RECOMMENDED
We recommend that you remove your existing setup entirely and configure the 2.0 version from scratch by following the updated documentation. While this may seem like a significant step, it is the most reliable approach to avoid potential conflicts during the update process.
If your user base is still new or relatively small, this method is especially beneficial. It will ensure a smoother experience moving forward and make it easier to adopt future updates and features.

2. Try updating the existing project : NOT RECOMMENDED
We don’t recommend this way at all. But still we can suggest what can be done if you want to try keeping all your data.
However, you have to use the updated backend & Flutter project for sure, to use the features of this update. The only thing which can be updated is Database.

In the backend project, there is a readme.md file containing the changes in the database. Please read that carefully and make the changes in your database. (Consider duplicate database and make changes in it).
Configure your new backend project with this database
Configure the flutter project with this new backend.

*Be careful while considering this step as we will be no responsible for any data loss or any other issues face while making updates.
*The help of this way is not included in the support, so choose this way only if you are truly having enough technical  knowledge.

****************************************

We recommend setting up the 2.0 version from scratch to prevent any potential conflicts and to ensure your users enjoy the latest features seamlessly.

We sincerely apologize for any inconvenience this may have caused. These changes were essential to provide enhanced functionality and improved security for our valued buyers.

Thank you for the incredible support and love you’ve shown for us and this project. We remain committed to delivering the best products—always.
Thank you once again.🙏🙏🙏

#### Updated Files
- Whole Backend Project
- Whole Flutter Project

#### Added Files
- Whole Backend Project
- Whole Flutter Project

#### Deleted Files

### Database

******** ADDED
*****************************************
- tbl_settings : app_name, terms_of_uses, privacy_policy, admob_iOS_status, admob_android_status, max_comment_daily, max_comment_reply_daily, is_deepAR, live_battle, live_dummy_show, max_comment_pins, max_post_pins, max_story_daily, max_user_links, max_images_per_post, sight_engine_image_workflow_id, sight_engine_video_workflow_id, gif_support, giphy_key, watermark_status, watermark_image, zego_app_id, zego_app_sign, is_withdrawal_on, deepar_android_key, deepar_iOS_key, registration_bonus_status, registration_bonus_amount
- tbl_admin : user_type
- tbl_users : country, countryCode, region, regionName, city, lat, lon, notify_post_like, notify_post_comment, notify_follow, notify_mention, notify_gift_received, notify_chat, app_last_used_at, who_can_view_post, show_my_following, receive_message, saved_music_ids, following_count, follower_count, total_post_likes_count, coin_collected_lifetime, coin_gifted_lifetime, coin_purchased_lifetime, mobile_country_code, is_moderator, app_language, is_dummy, password
- languages : New Table added
- user_auth_tokens : New Table added
- user_links : New Table added
- tbl_sound : user_id, post_count
- tbl_post : post_type, mentioned_user_ids, place_title, place_lat, place_lon, state, country, likes, comments, saves, shares, is_pinned, metadata
- post_saves : New Table added
- onboarding_screens : New Table added
- tbl_comments : type, mentioned_user_ids, likes, is_pinned, replies_count
- comment_replies : New Table added
- comment_likes : New Table added
- stories : New Table added
- user_levels : New Table added
- tbl_redeem_gateways : New Table added
- tbl_redeem_request : coins, coin_value, request_number
- report_posts : New Table added
- notification_admin : New Table added
- report_reasons : New Table added
- tbl_coin_plan : image, status
- notification_users : New Table added
- dummy_live_videos : New Table added
- daily_active_users : New Table added
- deepar_filters : New Table added

******** FIELD NAME CHANGES : (old, new)
*****************************************
- tbl_admin : (admin_id, id), (admin_email,admin_username)
- tbl_users : (user_id, id), (full_name, fullname), (user_name, username), (user_profile, profile_photo), (my_wallet, coin_wallet), (login_type, login_method), (platform, device), (freez_or_not, is_freez)
- tbl_sound_category : (sound_category_id , id), (sound_category_name, name), (sound_category_profile, image)
- tbl_sound : (sound_id, id), (sound_category_id, category_id), (sound_title, title), (singer, artist), ('sound_image, image)
- tbl_post : (post_id, id), (post_description, description), (post_hash_tag, hashtags), (video_view_count, views), (post_image, thumbnail), (post_video, video)
- tbl_hash_tags : (hash_tag_id , id), (hash_tag_name, hashtag), (move_explore, on_explore), (video_count, post_count)
- tbl_likes : (like_id, id)
- tbl_user_block : (block_user_id, to_user_id)
- tbl_comments : (comments_id, id)
- tbl_followers : (follower_id, id)
- tbl_settings : (min_fans_for_live, min_followers_for_live)
- tbl_redeem_request : (redeem_request_id, id),(redeem_request_type, gateway)
- tbl_redeem_gateways : (gateway, title)
- tbl_coin_plan : (coin_plan_id, id)


******** REMOVED FIELDS
*****************************************
- tbl_admin : unique_key
- tbl_users : profile_category, youtube_url, insta_url, fb_url, is_notification, status,
- tbl_post : can_save, can_duet, country_code
- tbl_hash_tags : move_to_banner, hash_tag_profile
- tbl_followers : status
- tbl_settings : min_fans_verification, reward_video_upload
- tbl_bookmark : Table Removed
- tbl_coin_rate : Table Removed
- tbl_pages : Table Removed
- tbl_notification : Table Removed
- tbl_profile_category : Table Removed
- tbl_report : Table Removed
- tbl_rewarding_action : Table Removed
- tbl_verification_request : Table Removed
- tbl_coin_plan : coin_plan_name, coin_plan_description


******** MISC CHANGES
*****************************************
- tbl_sound : added_by (Field type change)



--------------------------------------------------

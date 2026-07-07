<?php

namespace App\Http\Controllers;

use App\Models\CommentLikes;
use App\Models\CommentReplies;
use App\Models\Constants;
use App\Models\Followers;
use App\Models\GlobalFunction;
use App\Models\GlobalSettings;
use App\Models\Hashtags;
use App\Models\Musics;
use App\Models\PostComments;
use App\Models\PostLikes;
use App\Models\Posts;
use App\Models\PostSaves;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller
{
    //
    public function postDetails($postId){
        $post = GlobalFunction::preparePostFullData($postId);

        $music = null;
        if($post->sound_id != null){
            $music = $post->music;
        }
        $formattedDesc = GlobalFunction::formatDescription($post->description);
        $states = GlobalFunction::createPostStatesView($postId);
        $postType = GlobalFunction::createPostTypeBadge($postId);
        $postUser = GlobalFunction::prepareUserFullData($post->user_id);
        $baseUrl = GlobalFunction::getItemBaseUrl();

        return view('postDetails',[
            'post'=> $post,
            'music'=> $music,
            'formattedDesc'=> $formattedDesc,
            'states'=> $states,
            'postType'=> $postType,
            'baseUrl'=> $baseUrl,
            'postUser'=> $postUser,
        ]);
    }
    public function fetchFormattedPostDesc(Request $request){
        $post = Posts::find($request->postId);
        $formattedDesc = GlobalFunction::formatDescription($post->description);

        return GlobalFunction::sendDataResponse(true,'Description fetched', $formattedDesc);

    }
    public function listHashtagPosts(Request $request)
    {
        $hashtag = $request->hashtag;
        $query = Posts::query();
        $query->whereRaw("FIND_IN_SET('$hashtag',hashtags)");
        $totalData = $query->count();

        $columns = ['id'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('description', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $data = $result->map(function ($post) {

            $view = GlobalFunction::createPostDetailsButton($post->id);
            $delete = GlobalFunction::createPostDeleteButton($post->id);
            $action = "<span class='d-flex justify-content-end align-items-center'>{$view}{$delete}</span>";

            $postUser = GlobalFunction::createUserDetailsColumn($post->user_id);

            $states = GlobalFunction::createPostStatesView($post->id);

            $postType = GlobalFunction::createPostTypeBadge($post->id);

            $formattedDesc = GlobalFunction::formatDescription($post->description);

            $descAndStates = '<div class="itemDescription">'.$states.$formattedDesc.'</div>';

            // View Content Button
            $viewContent = GlobalFunction::createViewContentButton($post);

            return [
                $viewContent,
                $postType,
                $postUser,
                $descAndStates,
                GlobalFunction::formateDatabaseTime($post->created_at),
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
    public function listUserPosts(Request $request)
    {
        $query = Posts::query();
        $query->where('user_id', $request->user_id);
        $totalData = $query->count();

        $columns = ['id'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('description', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $data = $result->map(function ($post) {

            $view = GlobalFunction::createPostDetailsButton($post->id);
            $delete = GlobalFunction::createPostDeleteButton($post->id);
            $action = "<span class='d-flex justify-content-end align-items-center'>{$view}{$delete}</span>";

            $states = GlobalFunction::createPostStatesView($post->id);

            $postType = GlobalFunction::createPostTypeBadge($post->id);

            $formattedDesc = GlobalFunction::formatDescription($post->description);

            $descAndStates = '<div class="itemDescription">'.$states.$formattedDesc.'</div>';

            // View Content Button
            $viewContent = GlobalFunction::createViewContentButton($post);

            return [
                $viewContent,
                $postType,
                $descAndStates,
                GlobalFunction::formateDatabaseTime($post->created_at),
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
    public function listAllPosts(Request $request)
    {
        $query = Posts::query();
        $totalData = $query->count();

        $columns = ['id'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('description', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $data = $result->map(function ($post) {

            $view = GlobalFunction::createPostDetailsButton($post->id);
            $delete = GlobalFunction::createPostDeleteButton($post->id);
            $action = "<span class='d-flex justify-content-end align-items-center'>{$view}{$delete}</span>";

            $postUser = GlobalFunction::createUserDetailsColumn($post->user_id);

            $states = GlobalFunction::createPostStatesView($post->id);

            $postType = GlobalFunction::createPostTypeBadge($post->id);

            $formattedDesc = GlobalFunction::formatDescription($post->description);

            $descAndStates = '<div class="itemDescription">'.$states.$formattedDesc.'</div>';

            // View Content Button
            $viewContent = GlobalFunction::createViewContentButton($post);

            return [
                $viewContent,
                $postType,
                $postUser,
                $descAndStates,
                GlobalFunction::formateDatabaseTime($post->created_at),
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
    public function listImagePosts(Request $request)
    {
        $query = Posts::query();
        $query->where('post_type', Constants::postTypeImage);
        $totalData = $query->count();

        $columns = ['id'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('description', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $data = $result->map(function ($post) {

            $view = GlobalFunction::createPostDetailsButton($post->id);
            $delete = GlobalFunction::createPostDeleteButton($post->id);
            $action = "<span class='d-flex justify-content-end align-items-center'>{$view}{$delete}</span>";

            $postUser = GlobalFunction::createUserDetailsColumn($post->user_id);

            $states = GlobalFunction::createPostStatesView($post->id);

            $postType = GlobalFunction::createPostTypeBadge($post->id);

            $formattedDesc = GlobalFunction::formatDescription($post->description);

            $descAndStates = '<div class="itemDescription">'.$states.$formattedDesc.'</div>';

            // View Content Button
            $viewContent = GlobalFunction::createViewContentButton($post);

            return [
                $viewContent,
                $postType,
                $postUser,
                $descAndStates,
                GlobalFunction::formateDatabaseTime($post->created_at),
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
    public function listTextPosts(Request $request)
    {
        $query = Posts::query();
        $query->where('post_type', Constants::postTypeText);
        $totalData = $query->count();

        $columns = ['id'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('description', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $data = $result->map(function ($post) {

            $view = GlobalFunction::createPostDetailsButton($post->id);
            $delete = GlobalFunction::createPostDeleteButton($post->id);
            $action = "<span class='d-flex justify-content-end align-items-center'>{$view}{$delete}</span>";

            $postUser = GlobalFunction::createUserDetailsColumn($post->user_id);

            $states = GlobalFunction::createPostStatesView($post->id);

            $postType = GlobalFunction::createPostTypeBadge($post->id);

            $formattedDesc = GlobalFunction::formatDescription($post->description);

            $descAndStates = '<div class="itemDescription">'.$states.$formattedDesc.'</div>';

            // View Content Button
            $viewContent = GlobalFunction::createViewContentButton($post);

            return [
                $viewContent,
                $postType,
                $postUser,
                $descAndStates,
                GlobalFunction::formateDatabaseTime($post->created_at),
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
    public function listVideoPosts(Request $request)
    {
        $query = Posts::query();
        $query->where('post_type', Constants::postTypeVideo);
        $totalData = $query->count();

        $columns = ['id'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('description', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $data = $result->map(function ($post) {

            $view = GlobalFunction::createPostDetailsButton($post->id);
            $delete = GlobalFunction::createPostDeleteButton($post->id);
            $action = "<span class='d-flex justify-content-end align-items-center'>{$view}{$delete}</span>";

            $postUser = GlobalFunction::createUserDetailsColumn($post->user_id);

            $states = GlobalFunction::createPostStatesView($post->id);

            $postType = GlobalFunction::createPostTypeBadge($post->id);

            $formattedDesc = GlobalFunction::formatDescription($post->description);

            $descAndStates = '<div class="itemDescription">'.$states.$formattedDesc.'</div>';

            // View Content Button
            $viewContent = GlobalFunction::createViewContentButton($post);

            return [
                $viewContent,
                $postType,
                $postUser,
                $descAndStates,
                GlobalFunction::formateDatabaseTime($post->created_at),
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
    public function listReelPosts(Request $request)
    {
        $query = Posts::query();
        $query->where('post_type', Constants::postTypeReel);
        $totalData = $query->count();

        $columns = ['id'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('description', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $data = $result->map(function ($post) {

            $view = GlobalFunction::createPostDetailsButton($post->id);
            $delete = GlobalFunction::createPostDeleteButton($post->id);
            $action = "<span class='d-flex justify-content-end align-items-center'>{$view}{$delete}</span>";

            $postUser = GlobalFunction::createUserDetailsColumn($post->user_id);

            $states = GlobalFunction::createPostStatesView($post->id);

            $postType = GlobalFunction::createPostTypeBadge($post->id);

            $formattedDesc = GlobalFunction::formatDescription($post->description);

            $descAndStates = '<div class="itemDescription">'.$states.$formattedDesc.'</div>';

            // View Content Button
            $viewContent = GlobalFunction::createViewContentButton($post);

            return [
                $viewContent,
                $postType,
                $postUser,
                $descAndStates,
                GlobalFunction::formateDatabaseTime($post->created_at),
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

    public function posts(){
        return view('posts');
    }
    public function deletePost_Admin(Request $request){

        $post = Posts::find($request->id);
        $post->delete();

        GlobalFunction::deleteAllPostData($post);

        return GlobalFunction::sendSimpleResponse(true, 'post deleted successfully!');

    }
    public function deletePost(Request $request){
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
        $post = Posts::find($request->post_id);
        $post->delete();

        GlobalFunction::deleteAllPostData($post);

        return GlobalFunction::sendSimpleResponse(true, 'post deleted successfully!');

    }

    public function searchPosts(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }
        $rules = [
            'types' => 'required',
            'limit' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

       $blockedUserIds = GlobalFunction::getUsersBlockedUsersIdsArray($user->id);

       $search = GlobalFunction::cleanString($request->keyword);

       $query = Posts::whereHas('user', function ($query) {
               $query->Where('is_freez', 0);
           })
            ->with(Constants::postsWithArray)
           ->whereNotIn('user_id', $blockedUserIds)
           ->whereIn('post_type', explode(',',$request->types))
           ->where('description', 'LIKE', "%{$search}%")
           ->orderBy('id', 'DESC')
           ->limit($request->limit);

           if($request->has('last_item_id')){
               $query->where('id','<',$request->last_item_id);
           }

       $posts = $query->get();

       $postList = GlobalFunction::processPostsListData($posts, $user);

       return GlobalFunction::sendDataResponse(true, 'search posts fetched successfully', $postList);

    }
    public function fetchSavedPosts(Request $request){

        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'types' => 'required',
            'limit' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        // $blockedUserIds = GlobalFunction::getUsersBlockedUsersIdsArray($user->id);

       $query = PostSaves::whereHas('user', function ($query) {
               $query->Where('is_freez', 0);
           })->whereHas('post', function ($query) use ($request) {
            $query->whereIn('post_type', explode(',',$request->types));
        })
           ->with(['post.images','post.music','post.user:'.Constants::userPublicFields])
           ->orderBy('id', 'DESC')
           ->where('user_id', $user->id)
           ->limit($request->limit);

           if($request->has('last_item_id')){
               $query->where('id','<',$request->last_item_id);
           }

       $postSaves = $query->get();

       $post_list = [];
       foreach($postSaves as $save){

        $post = $save['post'];
        $post->post_save_id = $save->id;
        array_push($post_list, $post);
       }

       $postList = GlobalFunction::processPostsListData($post_list, $user);

       return GlobalFunction::sendDataResponse(true, 'saved posts fetched successfully', $postList);

    }
    public function fetchUserPosts(Request $request){

        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'limit' => 'required',
            'user_id'=>'required|exists:tbl_users,id',
            'types' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        $dataUser = Users::find($request->user_id);

        $pinnedPosts = Posts::with(Constants::postsWithArray)
        ->whereIn('post_type', explode(',',$request->types))
        ->where('user_id', $dataUser->id)
        ->where('is_pinned', 1)
        ->get();

        $pinnedPostList = GlobalFunction::processPostsListData($pinnedPosts, $user);


       $query = Posts::with(Constants::postsWithArray)
           ->whereIn('post_type', explode(',',$request->types))
           ->where('user_id', $dataUser->id)
           ->orderBy('id', 'DESC')
           ->limit($request->limit);

           if($request->has('last_item_id')){
               $query->where('id','<',$request->last_item_id);
           }

       $posts = $query->get();

       $postList = GlobalFunction::processPostsListData($posts, $user);

       $data['posts'] = $postList;
       $data['pinnedPostList'] = $pinnedPostList;

       return GlobalFunction::sendDataResponse(true, 'data fetched successfully', $data);

    }
    public function fetchPostsByHashtag(Request $request){

        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'limit' => 'required',
            'hashtag' => 'required',
            'types' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

       $blockedUserIds = GlobalFunction::getUsersBlockedUsersIdsArray($user->id);

       $hashtag = $request->hashtag;
        $query = Posts::whereHas('user', function ($query) {
                $query->Where('is_freez', 0);
            })
            ->whereNotIn('user_id', $blockedUserIds)
             ->with(Constants::postsWithArray)
            ->whereIn('post_type', explode(',',$request->types))
            ->whereRaw("FIND_IN_SET('$hashtag',hashtags)")
            ->orderBy('id', 'DESC')
            ->limit($request->limit);

            if($request->has('last_item_id')){
                $query->where('id','<',$request->last_item_id);
            }

        $posts = $query->get();

        $postList = GlobalFunction::processPostsListData($posts, $user);

        $hashtag = Hashtags::where('hashtag', $request->hashtag)->first();
        $hashtagText = $hashtag->hashtag;
        $hashtag->post_count = Posts::whereRaw("FIND_IN_SET('$hashtagText',hashtags)")->count();
        $hashtag->save();

        $data['hashtag'] = $hashtag;
        $data['posts'] = $postList;

        return GlobalFunction::sendDataResponse(true,'posts by hashtag fetched successfully', $data);
    }

    public function fetchReelPostsByMusic(Request $request){

        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'limit' => 'required',
            'music_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

       $blockedUserIds = GlobalFunction::getUsersBlockedUsersIdsArray($user->id);

        $query = Posts::whereHas('user', function ($query) {
                $query->Where('is_freez', 0);
            })
             ->with(Constants::postsWithArray)
            ->whereNotIn('user_id', $blockedUserIds)
            ->whereIn('post_type', [Constants::postTypeReel])
            ->where('sound_id', $request->music_id)
            ->orderBy('id', 'DESC')
            ->limit($request->limit);

            if($request->has('last_item_id')){
                $query->where('id','<',$request->last_item_id);
            }


        $posts = $query->get();

        $postList = GlobalFunction::processPostsListData($posts, $user);

        return GlobalFunction::sendDataResponse(true,'posts by music fetched successfully', $postList);

    }
    public function fetchExplorePageData(Request $request){

        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

       $blockedUserIds = GlobalFunction::getUsersBlockedUsersIdsArray($user->id);

       $hashtags = Hashtags::where('post_count','>=',1)->orderBy('post_count','DESC')->get();

       $highPostHashtags = Hashtags::where('post_count','>=',4)->inRandomOrder()->get();

       foreach($highPostHashtags as $singleHashtag){

            $hashtag = $singleHashtag->hashtag;
            $posts = Posts::whereHas('user', function ($query) {
                $query->Where('is_freez', 0);
            })
            ->whereNotIn('user_id', $blockedUserIds)
             ->with(Constants::postsWithArray)
            ->whereIn('post_type', [Constants::postTypeImage,Constants::postTypeReel,Constants::postTypeVideo])
            ->whereRaw("FIND_IN_SET('$hashtag',hashtags)")
            ->inRandomOrder()
            ->limit(6)
            ->get();

            $postList = GlobalFunction::processPostsListData($posts, $user);
            $singleHashtag->postList = $postList;
       }

       $data['hashtags'] = $hashtags;
       $data['highPostHashtags'] = $highPostHashtags;

        return GlobalFunction::sendDataResponse(true,'explore data fetched successfully', $data);

    }
    public function fetchPostsDiscover(Request $request){

        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'limit' => 'required',
            'types' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

       $blockedUserIds = GlobalFunction::getUsersBlockedUsersIdsArray($user->id);

        $posts = Posts::inRandomOrder()
            ->whereHas('user', function ($query) {
                $query->Where('is_freez', 0);
            })
            ->with(Constants::postsWithArray)
            ->whereNotIn('user_id', $blockedUserIds)
            ->whereIn('post_type', explode(',',$request->types))
            ->limit($request->limit)
            ->get();

            $postList = GlobalFunction::processPostsListData($posts, $user);

        return GlobalFunction::sendDataResponse(true,'discover posts fetched successfully', $postList);

    }
    public function fetchPostsNearBy(Request $request)
        {
            $token = $request->header('authtoken');
            $user = GlobalFunction::getUserFromAuthToken($token);

            if ($user->is_freez == 1) {
                return ['status' => false, 'message' => "this user is freezed!"];
            }

            $rules = [
                'place_lat' => 'required',
                'place_lon' => 'required',
                'types' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            }

            $latitude = $request->place_lat;
            $longitude = $request->place_lon;
            $radius = 5; // range in KM

            $blockedUserIds = GlobalFunction::getUsersBlockedUsersIdsArray($user->id);

            $query = Posts::whereHas('user', function ($query) {
                    $query->where('is_freez', 0);
                })
                ->whereNotIn('user_id', $blockedUserIds)
                 ->with(Constants::postsWithArray)
                ->whereIn('post_type', explode(',', $request->types))
                ->whereRaw("(6371 * acos(cos(radians(?))
                    * cos(radians(place_lat))
                    * cos(radians(place_lon) - radians(?))
                    + sin(radians(?))
                    * sin(radians(place_lat)))) <= ?",
                    [$latitude, $longitude, $latitude, $radius]
                )
                ->inRandomOrder()
                ->limit(50);

            $posts = $query->get();

            $postList = GlobalFunction::processPostsListData($posts, $user);

            return GlobalFunction::sendDataResponse(true, 'Nearby posts fetched successfully', $postList);
        }
    public function fetchPostsByLocation(Request $request)
        {
            $token = $request->header('authtoken');
            $user = GlobalFunction::getUserFromAuthToken($token);

            if ($user->is_freez == 1) {
                return ['status' => false, 'message' => "this user is freezed!"];
            }

            $rules = [
                'place_lat' => 'required',
                'place_lon' => 'required',
                'limit' => 'required|integer',
                'types' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            }

            $latitude = $request->place_lat;
            $longitude = $request->place_lon;
            $radius = 1; // range in KM

            $blockedUserIds = GlobalFunction::getUsersBlockedUsersIdsArray($user->id);

            $query = Posts::whereHas('user', function ($query) {
                    $query->where('is_freez', 0);
                })
                ->whereNotIn('user_id', $blockedUserIds)
                 ->with(Constants::postsWithArray)
                ->whereIn('post_type', explode(',', $request->types))
                ->whereRaw("(6371 * acos(cos(radians(?))
                    * cos(radians(place_lat))
                    * cos(radians(place_lon) - radians(?))
                    + sin(radians(?))
                    * sin(radians(place_lat)))) <= ?",
                    [$latitude, $longitude, $latitude, $radius]
                )
                ->orderBy('id', 'DESC')
                ->limit($request->limit);

            if ($request->has('last_item_id')) {
                $query->where('id', '<', $request->last_item_id);
            }

            $posts = $query->get();

            $postList = GlobalFunction::processPostsListData($posts, $user);

            return GlobalFunction::sendDataResponse(true, 'posts by location fetched successfully', $postList);
        }

    public function fetchPostsFollowing(Request $request){

        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'limit' => 'required',
            'types' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

       $blockedUserIds = GlobalFunction::getUsersBlockedUsersIdsArray($user->id);
       $followingUserIds = Followers::where('from_user_id', $user->id)->pluck('to_user_id');

        $posts = Posts::inRandomOrder()
            ->whereHas('user', function ($query) {
                $query->Where('is_freez', 0);
            })
            ->with(Constants::postsWithArray)
            ->whereNotIn('user_id', $blockedUserIds)
            ->whereIn('user_id', $followingUserIds)
            ->whereIn('post_type', explode(',',$request->types))
            ->limit($request->limit)
            ->get();

            $postList = GlobalFunction::processPostsListData($posts, $user);

        return GlobalFunction::sendDataResponse(true,'following posts fetched successfully', $postList);

    }


    public function unSavePost(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'post_id' => 'required',
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
        $item = PostSaves::where([
            'user_id'=> $user->id,
            'post_id'=> $post->id
        ])->first();
        if($item == null){
            return GlobalFunction::sendSimpleResponse(false, 'post is not saved yet!');
        }
        $item->delete();

        GlobalFunction::settlePostSaveCount($post->id);

        return GlobalFunction::sendSimpleResponse(true, 'post unsaved successfully');

    }
    public function disLikePost(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'post_id' => 'required',
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
        $like = PostLikes::where([
            'user_id'=> $user->id,
            'post_id'=> $post->id
        ])->first();
        if($like == null){
            return GlobalFunction::sendSimpleResponse(false, 'post is not liked yet!');
        }
        $like->delete();

        GlobalFunction::settlePostLikesCount($post->id);
        GlobalFunction::deleteNotifications(Constants::notify_like_post,$post->id,$user->id);

        return GlobalFunction::sendSimpleResponse(true, 'post disLiked successfully');

    }
    public function savePost(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'post_id' => 'required',
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
        $item = PostSaves::where([
            'user_id'=> $user->id,
            'post_id'=> $post->id
        ])->first();
        if($item != null){
            return GlobalFunction::sendSimpleResponse(false, 'post is saved already!');
        }
        $item = new PostSaves();
        $item->post_id = $post->id;
        $item->user_id = $user->id;
        $item->save();

        GlobalFunction::settlePostSaveCount($post->id);

        return GlobalFunction::sendSimpleResponse(true, 'post saved successfully');

    }
    public function likePost(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'post_id' => 'required',
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
        $like = PostLikes::where([
            'user_id'=> $user->id,
            'post_id'=> $post->id
        ])->first();
        if($like != null){
            return GlobalFunction::sendSimpleResponse(false, 'post is liked already!');
        }
        $like = new PostLikes();
        $like->post_id = $post->id;
        $like->user_id = $user->id;
        $like->save();

        GlobalFunction::settlePostLikesCount($post->id);
        GlobalFunction::settleUserTotalPostLikesCount($post->user_id);

        // Insert Notification Data : Like Post
        $notificationData = GlobalFunction::insertUserNotification(Constants::notify_like_post,$user->id, $post->user_id, $post->id);

        return GlobalFunction::sendSimpleResponse(true, 'post liked successfully');

    }
    public function unpinPost(Request $request){
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
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        $post = Posts::find($request->post_id);
        if($post->user_id != $user->id){
            return GlobalFunction::sendSimpleResponse(false, 'this post is not owned by you!');
        }
        if($post->is_pinned == 0){
            return GlobalFunction::sendSimpleResponse(false, 'this post is un-pinned already!');
        }
        $post->is_pinned = 0;
        $post->save();

        return GlobalFunction::sendSimpleResponse(true, 'post un-pinned successfully');

    }
    public function pinPost(Request $request){
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
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $post = Posts::find($request->post_id);
        if($post->user_id != $user->id){
            return GlobalFunction::sendSimpleResponse(false, 'this post is not owned by you!');
        }

        $settings = GlobalSettings::first();
        $pinnedPostsCounts = Posts::where([
            'user_id'=> $user->id,
            'is_pinned'=> 1,
        ])->count();
        if($pinnedPostsCounts >= $settings->max_post_pins){
            return GlobalFunction::sendSimpleResponse(false, 'you can pin '.$settings->max_post_pins.' posts only!');
        }

        if($post->is_pinned == 1){
            return GlobalFunction::sendSimpleResponse(false, 'this post is pinned already!');
        }
        $post->is_pinned = 1;
        $post->save();

        return GlobalFunction::sendSimpleResponse(true, 'post pinned successfully');

    }
    public function increaseShareCount(Request $request){
        $rules = [
            'post_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $post =  Posts::find($request->post_id);

        if($post == null){
            return GlobalFunction::sendSimpleResponse(false, 'post does not exists!');
        }

        $post->shares += 1;
        $post->save();

        return GlobalFunction::sendSimpleResponse(true, 'post share count increased successfully');

    }
    public function increaseViewsCount(Request $request){
        $rules = [
            'post_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $post =  Posts::find($request->post_id);

        if($post == null){
            return GlobalFunction::sendSimpleResponse(false, 'post does not exists!');
        }

        $post->views += 1;
        $post->save();

        return GlobalFunction::sendSimpleResponse(true, 'post view increased successfully');

    }

    public function addPost_Feed_Text(Request $request){
        // Validate user token and fetch user
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        $canPost = GlobalFunction::checkIfUserCanPost($user);
        if (!$canPost['status']) {
            return response()->json($canPost);
        }

        $rules = [
            'can_comment' => 'required',
            'description' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $post = GlobalFunction::generatePost($request, Constants::postTypeText, $user, null);

        return GlobalFunction::sendDataResponse(true, 'feed text : post uploaded successfully', $post);

    }
    public function fetchPostById(Request $request){

        // Validate user token and fetch user
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'post_id' => 'required',
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
        $post->is_liked = PostLikes::where('post_id', $post->id)->where('user_id', $user->id)->exists();
        $post->is_saved = PostSaves::where('post_id', $post->id)->where('user_id', $user->id)->exists();
        $post->user->is_following = Followers::where('from_user_id', $user->id)->where('to_user_id', $post->user_id)->exists();
        $post->mentioned_users = Users::whereIn('id', explode(',', $post->mentioned_user_ids))->select(explode(',',Constants::userPublicFields))->get();

        $data['post'] = $post;

        if($request->has('comment_id')){
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
            $data['comment'] = $comment;
        }
        // Try to fetch comment
        if($request->has('comment_id')){
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
            $data['comment'] = $comment;
        }
        if($request->has('reply_id')){
            $reply = CommentReplies::where('id',$request->reply_id)
                    ->with(['user:'.Constants::userPublicFields])
                    ->first();

            if($reply == null){
                return GlobalFunction::sendSimpleResponse(false, 'reply does not exists!');
            }
            $reply->mentionedUsers = Users::whereIn('id', explode(',', $reply->mentioned_user_ids))
                                        ->select(explode(',',Constants::userPublicFields))
                                        ->get();

            $data['reply'] = $reply;
        }

        return GlobalFunction::sendDataResponse(true, 'post fetched successfuly', $data);

    }
    public function addPost_Feed_Image(Request $request){
        // Validate user token and fetch user
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        $canPost = GlobalFunction::checkIfUserCanPost($user);
        if (!$canPost['status']) {
            return response()->json($canPost);
        }

        $rules = [
            'can_comment' => 'required',
            'post_images' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $post = GlobalFunction::generatePost($request, Constants::postTypeImage, $user, null);

        return GlobalFunction::sendDataResponse(true, 'feed images : post uploaded successfully', $post);

    }
    public function addPost_Feed_Video(Request $request){
        // Validate user token and fetch user
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        $canPost = GlobalFunction::checkIfUserCanPost($user);
        if (!$canPost['status']) {
            return response()->json($canPost);
        }

        $rules = [
            'can_comment' => 'required',
            'video' => 'required',
            'thumbnail' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $post = GlobalFunction::generatePost($request, Constants::postTypeVideo, $user, null);

         return GlobalFunction::sendDataResponse(true, 'feed video : post uploaded successfully', $post);

    }
    public function addPost_Reel(Request $request){
        // Validate user token and fetch user
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        $canPost = GlobalFunction::checkIfUserCanPost($user);
        if (!$canPost['status']) {
            return response()->json($canPost);
        }

        $rules = [
            'can_comment' => 'required',
            'video' => 'required',
            'thumbnail' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }


        $sound = null;
        if($request->has('sound_id')){
            $sound = Musics::find($request->sound_id);
            if ($sound == null) {
                return response()->json(['status' => false, 'message' => "Sound doesn't exists !"]);
            }
        }
        $post = GlobalFunction::generatePost($request, Constants::postTypeReel, $user, $sound);

        return GlobalFunction::sendDataResponse(true, 'reel : post uploaded successfully', $post);

    }

    public function addUserMusic(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'sound' => 'required',
            'duration' => 'required',
            'artist' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        // Validate user token and fetch user
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if (!$user) {
            return GlobalFunction::sendSimpleResponse(false, 'User not found!');
        }
        if ($user->is_freez == 1) {
            return response()->json(['status' => false, 'message' => "this user is freezed!"]);
        }

        $music = new Musics();
        $music->title = $request->title;
        $music->sound = GlobalFunction::saveFileAndGivePath($request->sound);
        if($request->has('image')){
            $music->image = GlobalFunction::saveFileAndGivePath($request->image);
        }
        $music->duration = $request->duration;
        $music->artist = $request->artist;
        $music->added_by = Constants::userTypeUser;
        $music->user_id = $user->id;
        $music->save();

        $music = Musics::where('id', $music->id)->with(['user:'.Constants::userPublicFields])->first();
        return GlobalFunction::sendDataResponse(true, 'music added successfully', $music);

    }
}

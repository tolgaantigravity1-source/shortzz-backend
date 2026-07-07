<?php

namespace App\Http\Controllers;

use App\Models\Constants;
use App\Models\Followers;
use App\Models\GlobalFunction;
use App\Models\GlobalSettings;
use App\Models\Musics;
use App\Models\Story;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use PHPUnit\TextUI\XmlConfiguration\Constant;

class StoryController extends Controller
{
    //
    public function createStory(Request $request)
    {
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

         // Checking Daily Limit
         $globalSettings = GlobalSettings::first();
         $storyCount = Story::where('user_id', $user->id)->whereDate('created_at', Carbon::today())->count();
         if ($storyCount >= $globalSettings->max_story_daily) {
             return ['status' => false, 'message' => "daily story limit exhausted!"];
         }

        $story = new Story();
        $story->user_id = $user->id;
        $story->duration = $request->duration;
        $story->type = $request->type;

        if($request->has('sound_id')){
            $sound = Musics::find($request->sound_id);
            if ($sound == null) {
                return response()->json(['status' => false, 'message' => "Sound doesn't exists !"]);
            }
            $story->sound_id = $sound->id;
        }

        if ($request->hasFile('content')) {
            $file = $request->file('content');
            $path = GlobalFunction::saveFileAndGivePath($file);
            $story->content = $path;
        }

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $path = GlobalFunction::saveFileAndGivePath($file);
            $story->thumbnail = $path;
        }

        $story->save();

        $story = GlobalFunction::prepareStoryFullData($story->id);

        return GlobalFunction::sendDataResponse(true, 'story created successfully', $story);
    }

    public function viewStory(Request $request)
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

        $story = Story::where('id', $request->story_id)->first();

        // Check if user has already viewed the story
        $viewedByUserIds = array_filter(explode(',', $story->view_by_user_ids));

        if (!in_array($user->id, $viewedByUserIds)) {
            $viewedByUserIds[] = $user->id; // Add the current user to the viewed list
            $story->view_by_user_ids = implode(',', $viewedByUserIds); // Rebuild the string
            $story->save();
        }

        return GlobalFunction::sendDataResponse(true, 'Story viewed!', $story);

    }
    public function fetchStory(Request $request)
    {
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);

        if ($user->is_freez) {
            return ['status' => false, 'message' => "This user is frozen!"];
        }

        $blockedUserIds = GlobalFunction::getUsersBlockedUsersIdsArray($user->id);

        $followingUsers = Followers::where('from_user_id', $user->id)
            ->whereNotIn('to_user_id', $blockedUserIds) // Ensure blocked users are excluded
            ->whereHas('to_user', function ($query) {
                $query->where('is_freez', 0) // Exclude frozen users
                      ->whereHas('stories', function ($storyQuery) {
                          $storyQuery->where('created_at', '>=', now()->subDay()); // Fetch only recent stories
                      });
            })
            ->with(['to_user' => function ($query) {
                $query->select(explode(',', Constants::userPublicFields)) // Select only specified fields
                      ->with(['stories' => function ($storyQuery) {
                          $storyQuery->where('created_at', '>=', now()->subDay())->with('music');
                      }]);
            }])
            ->get()
            ->pluck('to_user');

        return GlobalFunction::sendDataResponse(true, 'Stories fetched successfully', $followingUsers);
    }


    public function listUserStories(Request $request)
    {
        $query = Story::query();
        $query->where('user_id', $request->user_id);
        $totalData = $query->count();

        $columns = ['id'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $data = $result->map(function ($story) {

            $delete = "<a href='#'
                          rel='{$story->id}'
                          class='action-btn delete d-flex align-items-center justify-content-center btn border rounded-2 text-danger ms-1'>
                            <i class='uil-trash-alt'></i>
                        </a>";
            $action = "<span class='d-flex justify-content-end align-items-center'>{$delete}</span>";

            $storyType = GlobalFunction::createStoryTypeBadge($story->id);

            // View Content Button
            $viewStory = GlobalFunction::createViewStoryButton($story);

            return [
                $viewStory,
                $storyType,
                $story->created_at->diffForHumans(),
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

    public function fetchStoryByID(Request $request){
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
        $story = Story::where('id',$request->story_id)->with('music')->with('user:'.Constants::userPublicFields)->first();

        return GlobalFunction::sendDataResponse(true,'story fetched successfully', $story);
    }
    public function deleteStory(Request $request)
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

        $story = Story::find($request->story_id);

        if($story->user_id != $user->id){
            return GlobalFunction::sendSimpleResponse(false,'story is not owned by this user!');
        }

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
    public function deleteStory_Admin(Request $request)
    {

        $story = Story::find($request->id);

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


}

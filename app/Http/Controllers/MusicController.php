<?php

namespace App\Http\Controllers;

use App\Models\Constants;
use App\Models\GlobalFunction;
use App\Models\MusicCategories;
use App\Models\Musics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MusicController extends Controller
{
    //
    public function editMusicCategory(Request $request){
        $musicCat = MusicCategories::find($request->id);
        $musicCat->name = $request->name;
        $musicCat->save();

        return GlobalFunction::sendSimpleResponse(true, 'category edited successfully');
    }

    public function deleteMusicCategory(Request $request){
        $musicCat = MusicCategories::find($request->id);
        $musicCat->is_deleted = 1;
        $musicCat->save();

        return GlobalFunction::sendSimpleResponse(true, 'category deleted successfully');
    }

    public function deleteMusic(Request $request){
        $music = Musics::find($request->id);
        GlobalFunction::deleteFile($music->image);
        GlobalFunction::deleteFile($music->sound);
        $music->delete();

        return GlobalFunction::sendSimpleResponse(true,'music deleted successfully');
    }
    public function editMusic(Request $request){
        $music = Musics::find($request->id);
        $music->title = $request->title;
        $music->duration = $request->duration;
        $music->artist = $request->artist;
        $music->category_id = $request->category_id;
        if($request->has('image')){
            GlobalFunction::deleteFile($music->image);
            $music->image = GlobalFunction::saveFileAndGivePath($request->image);
        }
        if($request->has('sound')){
            GlobalFunction::deleteFile($music->sound);
            $music->sound = GlobalFunction::saveFileAndGivePath($request->sound);
        }
        $music->save();


        return GlobalFunction::sendSimpleResponse(true,'music edited successfully');
    }
    public function fetchMusicByCategories(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'limit' => 'required',
            'category_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $category = MusicCategories::find($request->category_id);

        if($category == null){
            return GlobalFunction::sendSimpleResponse(false, 'category does not exists!');
        }

        $query = Musics::orderBy('id', 'DESC')
                    ->limit($request->limit)
                    ->where('added_by', Constants::userTypeAdmin)
                    ->where('is_deleted', Constants::isDeletedNo)
                    ->where('category_id', $category->id);
                    if($request->has('last_item_id')){
                        $query->where('id','<',$request->last_item_id);
                    }
        $musics = $query ->get();

        return GlobalFunction::sendDataResponse(true, 'data fetched successfully', $musics);
    }

    public function fetchSavedMusics(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $query = Musics::whereIn('id', explode(',',$user->saved_music_ids))
                    ->where('is_deleted', Constants::isDeletedNo);
        $musics = $query ->get();

        return GlobalFunction::sendDataResponse(true, 'data fetched successfully', $musics);
    }
    public function fetchMusicExplore(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'limit' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $query = Musics::orderBy('id', 'DESC')
                    ->limit($request->limit)
                    ->where('added_by', Constants::userTypeAdmin)
                    ->where('is_deleted', Constants::isDeletedNo);
                    if($request->has('last_item_id')){
                        $query->where('id','<',$request->last_item_id);
                    }
        $musics = $query ->get();

        return GlobalFunction::sendDataResponse(true, 'data fetched successfully', $musics);
    }


    public function serchMusic(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'limit' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $search = GlobalFunction::cleanString($request->keyword);

        $query = Musics::orderBy('id', 'DESC')
                    ->limit($request->limit)
                    ->where('added_by', Constants::userTypeAdmin)
                    ->where('is_deleted', Constants::isDeletedNo)
                    ->where('title', 'LIKE', "%{$search}%");
                    if($request->has('last_item_id')){
                        $query->where('id','<',$request->last_item_id);
                    }
        $musics = $query ->get();

        return GlobalFunction::sendDataResponse(true, 'search music fetched successfully', $musics);
    }
    public function listMusics(Request $request)
    {
        $query = Musics::where(['is_deleted'=> Constants::isDeletedNo,'added_by'=> Constants::userTypeAdmin]);
        $totalData = $query->count();

        $columns = ['id'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('title', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $data = $result->map(function ($item) {


            $imgUrl = GlobalFunction::generateFileUrl($item->image);
            $image = "<img class='rounded' width='80' height='80' src='{$imgUrl}' alt=''>";

            $musicUrl = GlobalFunction::generateFileUrl($item->sound);
            $music = '<audio controls><source src="' . $musicUrl . '" type="audio/mp3"></audio>';

            $edit = "<a href='#'
                        rel='{$item->id}'
                        data-title='{$item->title}'
                        data-image='{$imgUrl}'
                        data-music='{$musicUrl}'
                        data-category='{$item->category_id}'
                        data-duration='{$item->duration}'
                        data-artist='{$item->artist}'
                        class='action-btn edit d-flex align-items-center justify-content-center btn border rounded-2 text-success ms-1'>
                        <i class='uil-pen'></i>
                        </a>";

            $delete = "<a href='#'
                          rel='{$item->id}'
                          class='action-btn delete d-flex align-items-center justify-content-center btn border rounded-2 text-danger ms-1'>
                            <i class='uil-trash-alt'></i>
                        </a>";
            $action = "<span class='d-flex justify-content-end align-items-center'>{$edit}{$delete}</span>";

            return [
                $image,
                $music,
                $item->title,
                $item->category->name ?? '',
                $item->duration,
                $item->artist,
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
    public function listMusicCategories(Request $request)
    {
        $query = MusicCategories::where('is_deleted', Constants::isDeletedNo);
        $totalData = $query->count();

        $columns = ['id'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $data = $result->map(function ($item) {

            $musicCount = Musics::where('category_id', $item->id)->count();


            $edit = "<a href='#'
                        rel='{$item->id}'
                        data-name='{$item->name}'
                        class='action-btn edit d-flex align-items-center justify-content-center btn border rounded-2 text-success ms-1'>
                        <i class='uil-pen'></i>
                        </a>";

            $delete = "<a href='#'
                          rel='{$item->id}'
                          class='action-btn delete d-flex align-items-center justify-content-center btn border rounded-2 text-danger ms-1'>
                            <i class='uil-trash-alt'></i>
                        </a>";
            $action = "<span class='d-flex justify-content-end align-items-center'>{$edit}{$delete}</span>";

            return [
                $item->name,
                $musicCount,
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

    function addMusicCategory(Request $request) {
        $category = MusicCategories::where('name', $request->name)->first();
        if($category){
            return GlobalFunction::sendSimpleResponse(false, 'category exists already!');
        }
        $category = new MusicCategories();
        $category->name = $request->name;
        $category->save();

        return  GlobalFunction::sendSimpleResponse(true, 'category added successfully');
    }

    function addMusic(Request $request){
        $music = new Musics();
        $music->category_id = $request->category_id;
        $music->title = $request->title;
        $music->duration = $request->duration;
        $music->artist = $request->artist;
        $music->sound = GlobalFunction::saveFileAndGivePath($request->sound);
        $music->image = GlobalFunction::saveFileAndGivePath($request->image);
        $music->added_by = Constants::userTypeAdmin;
        $music->save();

        return GlobalFunction::sendSimpleResponse(true, 'Music added successfully');
    }

    function music(){

        $categories = MusicCategories::where([
            'is_deleted'=> 0,
        ])->get();

        return view('music',['categories' => $categories]);
    }
}

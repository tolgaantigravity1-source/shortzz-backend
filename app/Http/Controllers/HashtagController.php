<?php

namespace App\Http\Controllers;

use App\Models\GlobalFunction;
use App\Models\Hashtags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HashtagController extends Controller
{
    //
    public function deleteHashtag(Request $request){
        $item = Hashtags::find($request->id);
        $item->delete();

        return GlobalFunction::sendSimpleResponse(true, 'hashtag deleted successfully');
    }

    public function addHashtag_Admin(Request $request){
        $item = Hashtags::where('hashtag', $request->hashtag)->first();
        if($item != null){
            return GlobalFunction::sendSimpleResponse(false, 'hashtag exists already');
        }
        $item = new Hashtags();
        $item->hashtag = $request->hashtag;
        $item->save();

        return GlobalFunction::sendSimpleResponse(true, 'hashtag added successfully');
    }
    public function hashtags(){
        return view('hashtags');
    }

    public function listAllHashtags(Request $request)
    {
        $query = Hashtags::query();
        $totalData = $query->count();

        $columns = ['id'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('hashtag', 'LIKE', "%{$searchValue}%");
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
                        data-postcount='{$item->post_count}'
                        class='action-btn delete d-flex align-items-center justify-content-center btn border rounded-2 text-danger ms-1'>
                        <i class='uil-trash-alt'></i>
                    </a>";

            $hashtagUrl =  route('hashtagDetails', $item->hashtag);

            $view = "<a href='$hashtagUrl' target='_blank'
                          class='action-btn d-flex align-items-center justify-content-center btn border rounded-2 text-info ms-1'>
                            <i class='ri-eye-line'></i>
                        </a>";
            $action = "<span class='d-flex justify-content-end align-items-center'>{$view}{$delete}</span>";

            $hashtag = '<a href='.$hashtagUrl.' target="_blank"
            <h5 class="m-0 hashtag">#'.$item->hashtag.'</h5>
             </a>';

            return [
                $hashtag,
                $item->post_count,
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

    public function hashtagDetails($hashtag){
        $hashtag = Hashtags::where('hashtag', $hashtag)->first();

        return view('hashtagDetails',[
            'hashtag'=> $hashtag
        ]);
    }

    public function searchHashtags(Request $request){

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

        $query = Hashtags::where('hashtag', 'LIKE', "%{$search}%")
                    ->orderBy('id', 'DESC')
                    ->limit($request->limit);
                    if($request->has('last_item_id')){
                        $query->where('id','<',$request->last_item_id);
                    }

        $hashtags =  $query->get();

        return GlobalFunction::sendDataResponse(true, 'hashtags fetched successfully', $hashtags);

    }
}

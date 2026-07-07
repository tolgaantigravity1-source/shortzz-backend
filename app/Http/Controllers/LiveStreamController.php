<?php

namespace App\Http\Controllers;

use App\Models\DummyLiveVideos;
use App\Models\GlobalFunction;
use App\Models\GlobalSettings;
use App\Models\Users;
use Illuminate\Http\Request;

class LiveStreamController extends Controller
{
    //
    function dummyLives(){
        $dummyUsers = Users::where('is_dummy', 1)->get();
        return view('dummyLives',[
            'dummyUsers' => $dummyUsers
        ]);
    }

    function addDummyLive(Request $request){
        $item = new DummyLiveVideos();
        $item->user_id = $request->user_id;
        $item->title = $request->title;
        $item->link = $request->link;
        $item->save();

        return GlobalFunction::sendSimpleResponse(true, 'Dummy live added successfully');
    }

    function deleteDummyLive(Request $request){
        $item = DummyLiveVideos::find($request->id);
        $item->delete();

        return GlobalFunction::sendSimpleResponse(true, 'Dummy live deleted successfully');
    }

    function changeDummyLiveStatus(Request $request){
        $coinPackage = DummyLiveVideos::find($request->id);
        $coinPackage->status = $request->status;
        $coinPackage->save();

        return GlobalFunction::sendSimpleResponse(true, 'Status changed successfully!');
    }

    public function listDummyLives(Request $request)
    {
        $query = DummyLiveVideos::query();
        $totalData = $query->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('title', 'LIKE', "%{$searchValue}%")
                ->orwhere('link', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $data = $result->map(function ($item){

            $user = GlobalFunction::createUserDetailsColumn($item->user_id);

            $edit = "<a href='#'
                        rel='{$item->id}'
                        data-userid='{$item->user_id}'
                        data-title='{$item->title}'
                        data-link='{$item->link}'
                        class='action-btn edit d-flex align-items-center justify-content-center btn border rounded-2 text-success ms-1'>
                        <i class='uil-pen'></i>
                        </a>";

            $delete = "<a href='#'
                          rel='{$item->id}'
                          class='action-btn delete d-flex align-items-center justify-content-center btn border rounded-2 text-danger ms-1'>
                            <i class='uil-trash-alt'></i>
                        </a>";
            $action = "<span class='d-flex justify-content-end align-items-center'>{$edit}{$delete}</span>";

            $checked = $item->status == 1 ? 'checked' : '';
            $status = "<input type='checkbox' id='dummyLiveStatus-{$item->id}' rel='{$item->id}' class='onOffDummyLive' {$checked} data-switch='none'/>
                    <label for='dummyLiveStatus-{$item->id}'></label>";

            return [
                $user,
                $item->title,
                $item->link,
                $status,
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

    function editDummyLive(Request $request){
        $dummyLive = DummyLiveVideos::find($request->id);
        $dummyLive->title = $request->title;
        $dummyLive->link = $request->link;
        $dummyLive->save();

        return GlobalFunction::sendSimpleResponse(true, 'Dummy live updated successfully!');
    }
}

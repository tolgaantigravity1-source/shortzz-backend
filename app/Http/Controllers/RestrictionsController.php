<?php

namespace App\Http\Controllers;

use App\Models\GlobalFunction;
use App\Models\UsernameRestrictions;
use Illuminate\Http\Request;

class RestrictionsController extends Controller
{
    //
    public function deleteUsernameRestriction(Request $request){

        $item = UsernameRestrictions::where('id', $request->id)->first();
        $item->delete();

        return GlobalFunction::sendSimpleResponse(true,'restriction deleted successfully');
    }
    public function editUsernameRestriction(Request $request){

        $item = UsernameRestrictions::where('username', $request->username)->first();
        if($item != null){
            return GlobalFunction::sendSimpleResponse(false,'username already restricted!');
        }
        $item = UsernameRestrictions::where('id', $request->id)->first();
        $item->username = $request->username;
        $item->save();

        return GlobalFunction::sendSimpleResponse(true,'username edited successfully');
    }
    public function addUsernameRestriction(Request $request){
        $usernamesArray = explode(',', $request->usernames);
        foreach($usernamesArray as $username){
            $item = UsernameRestrictions::where('username', $username)->first();
            if($item == null){
                $item = new UsernameRestrictions();
                $item->username = $username;
                $item->save();
            }
        }
        return GlobalFunction::sendSimpleResponse(true,'usernames added successfully');
    }
    public function restrictions(){
        return view('restrictions');
    }
    public function listUsernameRestrictions(Request $request)
    {
        $query = UsernameRestrictions::query();
        $totalData = $query->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('username', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $data = $result->map(function ($item){

            $username = '<h5 class="m-0 username">'.$item->username.'</h5>';

            $edit = "<a href='#'
                        rel='{$item->id}'
                        data-username='{$item->username}'
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
               $username,
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

}

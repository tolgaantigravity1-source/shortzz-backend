<?php

namespace App\Http\Controllers;

use App\Models\GlobalFunction;
use App\Models\Posts;
use App\Models\ReportPosts;
use App\Models\ReportUsers;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    //
    public function reports(){
        return view('reports');
    }
    public function reportUser(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'user_id' =>'required|exists:tbl_users,id',
            'reason' =>'required',
            'description' =>'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()]);
        }

       $report = ReportUsers::where([
        'user_id'=> $request->user_id,
        'by_user_id'=> $user->id,
       ])->first();
       if($report != null){
        return GlobalFunction::sendSimpleResponse(false,'you have reported this user already!');
       }
       $report = new ReportUsers();
       $report->user_id = $request->user_id;
       $report->by_user_id = $user->id;
       $report->reason = $request->reason;
       $report->description = $request->description;
       $report->save();

    return GlobalFunction::sendSimpleResponse(true, 'user report submitted successfully');

    }
    public function rejectUserReport(Request $request){

        $report = ReportUsers::find($request->id);
        $report->delete();

        return GlobalFunction::sendSimpleResponse(true,'Report rejected successfully!');
    }
    public function acceptUserReport(Request $request){

        $report = ReportUsers::find($request->id);
        $user = Users::find($report->user_id);
        $user->is_freez = 1;
        $user->save();

        $report->delete();

        return GlobalFunction::sendSimpleResponse(true,'User freezed successfully!');
    }
    public function acceptPostReport(Request $request){

        $report = ReportPosts::find($request->id);

        $post = Posts::find($report->post_id);
        $post->delete();
        GlobalFunction::deleteAllPostData($post);

        return GlobalFunction::sendSimpleResponse(true,'Post deleted successfully!');
    }
    public function rejectPostReport(Request $request){
        $report = ReportPosts::find($request->id);
        $report->delete();

        return GlobalFunction::sendSimpleResponse(true,'Report rejected successfully!');
    }
    public function listUserReports(Request $request)
    {
        $query = ReportUsers::query();
        $totalData = $query->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('reason', 'LIKE', "%{$searchValue}%")
                ->orWhere('description', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $data = $result->map(function ($item) {

            $acceptReport = "<a href='#'
                          rel='{$item->id}'
                          style='width:110px'
                          class='mb-1 action-btn-text accept-report d-flex align-items-center justify-content-center border rounded-2 text-success ms-1'>
                            Accept Report
                        </a>";
            $rejectReport = "<a href='#'
                          rel='{$item->id}'
                          style='width:110px'
                          class='action-btn-text reject-report d-flex align-items-center justify-content-center border rounded-2 text-danger ms-1'>
                            Reject Report
                        </a>";

            $action = "<div class='float-end'>{$acceptReport}{$rejectReport}</div>";

            $user = GlobalFunction::createUserDetailsColumn($item->user_id);
            $reportedBy = GlobalFunction::createUserDetailsColumn($item->by_user_id);

            $reason = "<h5>{$item->reason}</h5>";
            $description = "<p>{$item->description}</p>";

            $details = '<div class="reportDescription">'.$reason.$description.'</div>';

            return [
                $user,
                $details,
                $reportedBy,
                GlobalFunction::formateDatabaseTime($item->created_at),
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
    public function listPostReports(Request $request)
    {
        $query = ReportPosts::query();
        $totalData = $query->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('reason', 'LIKE', "%{$searchValue}%")
                ->orWhere('description', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $data = $result->map(function ($item) {

            $post = $item->post;

            $acceptReport = "<a href='#'
                          rel='{$item->id}'
                          style='width:110px'
                          class='mb-1 action-btn-text accept-report d-flex align-items-center justify-content-center border rounded-2 text-success ms-1'>
                            Accept Report
                        </a>";
            $rejectReport = "<a href='#'
                          rel='{$item->id}'
                          style='width:110px'
                          class='action-btn-text reject-report d-flex align-items-center justify-content-center border rounded-2 text-danger ms-1'>
                            Reject Report
                        </a>";

            $action = "<div class='float-end'>{$acceptReport}{$rejectReport}</div>";

            $postUser = GlobalFunction::createUserDetailsColumn($post->user_id);

            $reportedBy = GlobalFunction::createUserDetailsColumn($item->by_user_id);

            // View Content Button
            $viewContent = GlobalFunction::createViewContentButton($post);

            $reason = "<h5>{$item->reason}</h5>";
            $description = "<p>{$item->description}</p>";

            $details = '<div class="reportDescription">'.$reason.$description.'</div>';

            return [
                $viewContent,
                $postUser,
                $details,
                $reportedBy,
                GlobalFunction::formateDatabaseTime($item->created_at),
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
    //
    public function reportPost(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }

        $rules = [
            'post_id' =>'required|exists:tbl_post,id',
            'reason' =>'required',
            'description' =>'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()]);
        }

       $report = ReportPosts::where([
        'post_id'=> $request->post_id,
        'by_user_id'=> $user->id,
       ])->first();
       if($report != null){
        return GlobalFunction::sendSimpleResponse(false,'you have reported this post already!');
       }
       $report = new ReportPosts();
       $report->post_id = $request->post_id;
       $report->by_user_id = $user->id;
       $report->reason = $request->reason;
       $report->description = $request->description;
       $report->save();

    return GlobalFunction::sendSimpleResponse(true, 'post report submitted successfully');

    }
}

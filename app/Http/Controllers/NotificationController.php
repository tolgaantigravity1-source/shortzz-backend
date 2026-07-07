<?php

namespace App\Http\Controllers;

use App\Models\AdminNotifications;
use App\Models\Constants;
use App\Models\GlobalFunction;
use App\Models\GlobalSettings;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Google\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    //

    function pushNotificationToSingleUser(Request $request)
    {
        $payload = $request->json()->all();
        $result = GlobalFunction::sendPushNotification($payload);
        // return $response;
        return response()->json(['result' => $result, 'fields' => $payload]);
    }

    public function fetchActivityNotifications(Request $request)
    {
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => 'this user is freezed!'];
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

        $query = UserNotification::orderBy('id', 'DESC')
            ->where('to_user_id', $user->id)
            ->with(['from_user:' . Constants::userPublicFields])
            ->limit($request->limit);

        if ($request->has('last_item_id')) {
            $query->where('id', '<', $request->last_item_id);
        }

        $items = $query->get();

        foreach ($items as $item) {
            $item->data = GlobalFunction::getNotificationItemData($item, $user);
        }

        return GlobalFunction::sendDataResponse(true, 'activity notifications fetched successfully', $items);
    }
    public function fetchAdminNotifications(Request $request)
    {
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => 'this user is freezed!'];
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

        $query = AdminNotifications::orderBy('id', 'DESC')->limit($request->limit);

        if ($request->has('last_item_id')) {
            $query->where('id', '<', $request->last_item_id);
        }
        $items = $query->get();

        return GlobalFunction::sendDataResponse(true, 'admin notifications fetched successfully', $items);
    }

    public function editAdminNotification(Request $request)
    {
        $item = AdminNotifications::find($request->id);
        if ($request->has('image')) {
            if ($item->image != null) {
                GlobalFunction::deleteFile($item->image);
            }
            $item->image = GlobalFunction::saveFileAndGivePath($request->image);
        }
        $item->title = $request->title;
        $item->description = $request->description;

        $item->save();

        return GlobalFunction::sendSimpleResponse(true, 'Notification edited successfully');
    }
    public function addAdminNotification(Request $request)
    {
        $item = new AdminNotifications();
        $item->title = $request->title;
        $item->description = $request->description;
        if ($request->has('image')) {
            $item->image = GlobalFunction::saveFileAndGivePath($request->image);
        }
        $item->save();

        // Generate Payloads
        $pushTokenAndroid = env('NOTIFICATION_TOPIC') . '_android';
        $pushTokenIOS = env('NOTIFICATION_TOPIC') . '_ios';
        $androidPayload = GlobalFunction::generatePushNotificationPayload(Constants::android, Constants::pushTypeTopic, $pushTokenAndroid, $item->title, $item->description, $item->image);
        $iosPayLoad = GlobalFunction::generatePushNotificationPayload(Constants::iOS, Constants::pushTypeTopic, $pushTokenIOS, $item->title, $item->description, $item->image);
        // Sending Push
        GlobalFunction::sendPushNotification($androidPayload);
        GlobalFunction::sendPushNotification($iosPayLoad);

        return GlobalFunction::sendSimpleResponse(true, 'Notification sent successfully');
    }

    public function listAdminNotifications(Request $request)
    {
        $query = AdminNotifications::query();
        $totalData = $query->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('title', 'LIKE', "%{$searchValue}%")->orWhere('description', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)->limit($limit)->orderBy('id', 'DESC')->get();

        $data = $result->map(function ($item) {
            $image = '';
            if ($item->image) {
                $imgUrl = GlobalFunction::generateFileUrl($item->image);
                $image = "<img class='rounded' width='130' height='80' src='{$imgUrl}' alt=''>";
            }
            $title = "<h5>{$item->title}</h5>";
            $description = "<p class='m-0'>{$item->description}</p>";
            $notification = '<div class="reportDescription">' . $image . $title . $description . '</div>';

            $repeat = "<a href='#'
                        rel='{$item->id}'
                        class='action-btn repeat d-flex align-items-center justify-content-center btn border rounded-2 text-info ms-1'>
                        <i class='uil-repeat'></i>
                        </a>";

            $edit = "<a href='#'
                        rel='{$item->id}'
                        data-title='{$item->title}'
                        data-description='{$item->description}'
                        class='action-btn edit d-flex align-items-center justify-content-center btn border rounded-2 text-success ms-1'>
                        <i class='uil-pen'></i>
                        </a>";

            $delete = "<a href='#'
                          rel='{$item->id}'
                          class='action-btn delete d-flex align-items-center justify-content-center btn border rounded-2 text-danger ms-1'>
                            <i class='uil-trash-alt'></i>
                        </a>";
            $action = "<span class='d-flex justify-content-end align-items-center'>{$repeat}{$edit}{$delete}</span>";

            return [$notification, GlobalFunction::formateDatabaseTime($item->created_at), $action];
        });

        $json_data = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ];

        return response()->json($json_data);
    }

    public function repeatAdminNotification(Request $request)
    {
        $item = AdminNotifications::find($request->id);
        if ($item != null) {
            // Generate Payloads
            $pushTokenAndroid = env('NOTIFICATION_TOPIC') . '_android';
            $pushTokenIOS = env('NOTIFICATION_TOPIC') . '_ios';
            $androidPayload = GlobalFunction::generatePushNotificationPayload(Constants::android, Constants::pushTypeTopic, $pushTokenAndroid, $item->title, $item->description, $item->image);
            $iosPayLoad = GlobalFunction::generatePushNotificationPayload(Constants::iOS, Constants::pushTypeTopic, $pushTokenIOS, $item->title, $item->description, $item->image);
            // Sending Push
            GlobalFunction::sendPushNotification($androidPayload);
            GlobalFunction::sendPushNotification($iosPayLoad);

            return GlobalFunction::sendSimpleResponse(true, 'Notification repeated successfully');
        }

        return GlobalFunction::sendSimpleResponse(true, 'Notification repeated successfully');
    }
    public function deleteAdminNotification(Request $request)
    {
        $item = AdminNotifications::find($request->id);
        if ($item->image != null) {
            GlobalFunction::deleteFile($item->image);
        }
        $item->delete();

        return GlobalFunction::sendSimpleResponse(true, 'Notification deleted successfully');
    }

    public function notifications()
    {
        return view('notifications');
    }
}

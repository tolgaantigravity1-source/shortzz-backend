<?php

namespace App\Http\Controllers;

use App\Models\CoinPackages;
use App\Models\Constants;
use App\Models\Gifts;
use App\Models\GlobalFunction;
use App\Models\GlobalSettings;
use App\Models\RedeemRequests;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use function Psy\debug;

class WalletController extends Controller
{
    //
    public function buyCoins(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }
        $rules = [
            'coin_package_id' => 'required|exists:tbl_coin_plan,id',
            'purchased_at' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $date = Carbon::now();
        $timeInMilliseconds = $date->valueOf();
        $bufferTimeInMs = 30000;

        if($timeInMilliseconds - $request->purchased_at > $bufferTimeInMs){
            return GlobalFunction::sendSimpleResponse(false, 'something went wrong!-0');
        }

        $coinPackage = CoinPackages::find($request->coin_package_id);

        $userId = $user->id;
        $rcProjectId = env('RC_PROJECT_ID');
        $rcApiKey = env('RC_KIT_API_KEY');

         // Define the external API URL you want to call
         $apiUrl = 'https://api.revenuecat.com/v2/projects/'.$rcProjectId.'/customers/'.$userId.'/purchases?limit=10000';
        //  Log::debug($apiUrl);

         try {
             $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $rcApiKey
            ])->get($apiUrl);

             // Check if the request was successful
             if ($response->successful()) {

                // Log::debug($response->json());

                $jsonResponse = $response->json();
                $items = $jsonResponse['items'];
                // Log::debug($items);

              $matchedItem = collect($items)
                            ->filter(function ($item) {
                                return isset($item['store_purchase_identifier'], $item['purchased_at']);
                            })
                            ->sortByDesc('purchased_at')
                            ->first();


                if ($matchedItem) {

                    $date = Carbon::now();
                    $timeInMilliseconds = $date->valueOf();
                    if(($timeInMilliseconds - $matchedItem['purchased_at']) > $bufferTimeInMs){
                        return GlobalFunction::sendSimpleResponse(false, 'something went wrong!-1');
                    }

                    $productVerifyUrl = 'https://api.revenuecat.com/v2/projects/'.$rcProjectId.'/products/'.$matchedItem['product_id'];
                    $productResponse = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $rcApiKey
                    ])->get($productVerifyUrl);

                    // Log::debug($matchedItem['product_id']);
                    // Log::debug($productResponse);

                    if ($productResponse->successful()) {
                         $productResponse = $productResponse->json();
                        //  Log::debug($productResponse);
                         if($matchedItem['store'] == 'play_store'){
                            $productId = $coinPackage->playstore_product_id;
                        }else{
                             $productId = $coinPackage->appstore_product_id;
                         }

                         if($productResponse['store_identifier'] != $productId){
                            return GlobalFunction::sendSimpleResponse(false,'somehting went wrong!-2');
                         }

                        $user->coin_wallet += $coinPackage->coin_amount;
                        $user->coin_purchased_lifetime += $coinPackage->coin_amount;
                        $user->save();

                        $user = GlobalFunction::prepareUserFullData($user->id);
                        return GlobalFunction::sendDataResponse(true, 'coins purchased successfully', $user);

                    }else{
                         return GlobalFunction::sendSimpleResponse(false,'somehting went wrong!-3');
                    }

                } else {
                    return GlobalFunction::sendSimpleResponse(false, 'no purchased item found!');
                }

            } else {
                // Log::debug($response->body());
               return GlobalFunction::sendSimpleResponse(false, 'something went wrong!-4');
             }
         } catch (\Exception $e) {
            //  Log::debug( $e->getMessage());
             return GlobalFunction::sendSimpleResponse(false, 'something went wrong!-5');

         }

    }

    public function addCoinsToUserWallet_FromAdmin(Request $request){
        $user = Users::find($request->user_id);
        $user->coin_wallet += $request->coins;
        $user->coin_collected_lifetime += $request->coins;
        $user->save();

         return GlobalFunction::sendSimpleResponse(true, 'Coins added to user wallet successfully!');
    }

    public function listCoinPackages(Request $request)
    {
        $query = CoinPackages::query();
        $totalData = $query->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('coin_amount', 'LIKE', "%{$searchValue}%")
                ->orwhere('coin_plan_price', 'LIKE', "%{$searchValue}%")
                ->orwhere('playstore_product_id', 'LIKE', "%{$searchValue}%")
                ->orwhere('appstore_product_id', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $settings = GlobalSettings::first();
        $data = $result->map(function ($item) use($settings) {

            $imgUrl = GlobalFunction::generateFileUrl($item->image);
            $image = "<img class='rounded' width='80' height='80' src='{$imgUrl}' alt=''>";

            $edit = "<a href='#'
                        rel='{$item->id}'
                        data-coinamount='{$item->coin_amount}'
                        data-coinprice='{$item->coin_plan_price}'
                        data-playstoreid='{$item->playstore_product_id}'
                        data-appstoreid='{$item->appstore_product_id}'
                        data-image='{$imgUrl}'
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
            $status = "<input type='checkbox' id='coinPackageStatus-{$item->id}' rel='{$item->id}' class='onOffCoinPackage' {$checked} data-switch='none'/>
                    <label for='coinPackageStatus-{$item->id}'></label>";

            return [
                $image,
                $item->coin_amount,
                $settings->currency.$item->coin_plan_price,
                $status,
                $item->playstore_product_id,
                $item->appstore_product_id,
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

    function changeCoinPackageStatus(Request $request){
        $coinPackage = CoinPackages::find($request->id);
        $coinPackage->status = $request->status;
        $coinPackage->save();

        return GlobalFunction::sendSimpleResponse(true, 'Status changed successfully!');
    }

    function editCoinPackage(Request $request){
        $item = CoinPackages::find($request->id);
        if($request->has('image')){
            if($item->image != null){
                GlobalFunction::deleteFile($item->image);
            }
            $item->image = GlobalFunction::saveFileAndGivePath($request->image);
        }
        $item->coin_amount = $request->coin_amount;
        $item->coin_plan_price = $request->coin_plan_price;
        $item->appstore_product_id = $request->appstore_product_id;
        $item->playstore_product_id = $request->playstore_product_id;
        $item->save();

        return GlobalFunction::sendSimpleResponse(true, 'Coin package edited successfully');
    }
    function addCoinPackage(Request $request){
        $item = new CoinPackages();
        $item->image = GlobalFunction::saveFileAndGivePath($request->image);
        $item->coin_amount = $request->coin_amount;
        $item->coin_plan_price = $request->coin_plan_price;
        $item->appstore_product_id = $request->appstore_product_id;
        $item->playstore_product_id = $request->playstore_product_id;
        $item->save();

        return GlobalFunction::sendSimpleResponse(true, 'Coin package added successfully');
    }
    function deleteCoinPackage(Request $request){
        $item = CoinPackages::find($request->id);
        GlobalFunction::deleteFile($item->image);
        $item->delete();

        return GlobalFunction::sendSimpleResponse(true, 'Coin package deleted successfully');
    }

    public function coinPackages(){
        return view('coinPackages');
    }
    public function rejectWithdrawal(Request $request){
        $item = RedeemRequests::find($request->id);
        $item->status = Constants::withdrawalRejected;
        $item->save();

        $user = $item->user;
        $user->coin_wallet += $item->coins;
        $user->save();

        return GlobalFunction::sendSimpleResponse(true,'withdrawal rejected successfully');
    }
    public function completeWithdrawal(Request $request){
        $item = RedeemRequests::find($request->id);
        $item->status = Constants::withdrawalCompleted;
        $item->save();

        return GlobalFunction::sendSimpleResponse(true,'withdrawal complete successfully');
    }
    public function deleteGift(Request $request){
        $item = Gifts::find($request->id);
        GlobalFunction::deleteFile($item->image);
        $item->delete();

        return GlobalFunction::sendSimpleResponse(true, 'gift deleted successfully');
    }
    public function listCompletedWithdrawals(Request $request)
    {
        $query = RedeemRequests::where('status', Constants::withdrawalCompleted);
        $totalData = $query->count();

        $columns = ['id'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('request_number', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $settings = GlobalSettings::first();
        $data = $result->map(function ($item) use($settings) {

            $user = GlobalFunction::createUserDetailsColumn($item->user_id);

            $amount = "<h4 class='text-primary m-0'>{$settings->currency} {$item->amount}</h4>";
            $withdrawal = $amount."<span class='fs-6'>Coins: {$item->coins}</span><br><span class='fs-6'>Coin Value: {$item->coin_value}</span>";

            $gateway = "<span class='badge badge-info-lighten fs-6'>{$item->gateway}</span>";
            $paymentDetails = $gateway."<br><span class='fs-6'>{$item->account}</span>";

            $requestNumber = "<h5 class='text-success'>#{$item->request_number}</h5>";


            return [
                $requestNumber,
                $user,
                $withdrawal,
                $paymentDetails,
                GlobalFunction::formateDatabaseTime($item->created_at),
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
    public function listRejectedWithdrawals(Request $request)
    {
        $query = RedeemRequests::where('status', Constants::withdrawalRejected);
        $totalData = $query->count();

        $columns = ['id'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('request_number', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $settings = GlobalSettings::first();
        $data = $result->map(function ($item) use($settings) {

            $user = GlobalFunction::createUserDetailsColumn($item->user_id);

            $amount = "<h4 class='text-primary m-0'>{$settings->currency} {$item->amount}</h4>";
            $withdrawal = $amount."<span class='fs-6'>Coins: {$item->coins}</span><br><span class='fs-6'>Coin Value: {$item->coin_value}</span>";

            $gateway = "<span class='badge badge-info-lighten fs-6'>{$item->gateway}</span>";
            $paymentDetails = $gateway."<br><span class='fs-6'>{$item->account}</span>";

            $requestNumber = "<h5 class='text-danger'>#{$item->request_number}</h5>";


            return [
                $requestNumber,
                $user,
                $withdrawal,
                $paymentDetails,
                GlobalFunction::formateDatabaseTime($item->created_at),
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
    public function listPendingWithdrawals(Request $request)
    {
        $query = RedeemRequests::where('status', Constants::withdrawalPending);
        $totalData = $query->count();

        $columns = ['id'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('request_number', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->offset($start)
                        ->limit($limit)
                        ->orderBy('id', 'DESC')
                        ->get();

        $settings = GlobalSettings::first();
        $data = $result->map(function ($item) use($settings) {

            $user = GlobalFunction::createUserDetailsColumn($item->user_id);

            $amount = "<h4 class='text-primary m-0'>{$settings->currency} {$item->amount}</h4>";
            $withdrawal = $amount."<span class='fs-6'>Coins: {$item->coins}</span><br><span class='fs-6'>Coin Value: {$item->coin_value}</span>";

            $gateway = "<span class='badge badge-info-lighten fs-6'>{$item->gateway}</span>";
            $paymentDetails = $gateway."<br><span class='fs-6'>{$item->account}</span>";

            $requestNumber = "<h5 class='text-primary'>#{$item->request_number}</h5>";

            $complete = "<a href='#'
                        rel='{$item->id}'
                        class='action-btn complete d-flex align-items-center justify-content-center btn border rounded-2 text-success ms-1'>
                        <i class='uil-check'></i>
                        </a>";

            $reject = "<a href='#'
                          rel='{$item->id}'
                          class='action-btn reject d-flex align-items-center justify-content-center btn border rounded-2 text-danger ms-1'>
                            <i class='uil-times'></i>
                        </a>";
            $action = "<span class='d-flex justify-content-end align-items-center'>{$complete}{$reject}</span>";

            return [
                $requestNumber,
                $user,
                $withdrawal,
                $paymentDetails,
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
    public function withdrawals(){
        return view('withdrawals');
    }
    public function gifts(){
        $gifts = Gifts::all();
        $baseUrl = GlobalFunction::getItemBaseUrl();
        return view('gifts' ,compact('gifts','baseUrl'));
    }

    public function editGift(Request $request)
    {
        $item = Gifts::find($request->id);
        $item->coin_price = $request->coin_price;
        if($request->has('image')){
            GlobalFunction::deleteFile($item->image);
            $item->image = GlobalFunction::saveFileAndGivePath($request->image);
        }
        $item->save();

        return response()->json([
            'status' => true,
            'message' => 'Gift Added Successfully',
        ]);
    }
    public function addGift(Request $request)
    {
        $item = new Gifts();
        $item->coin_price = $request->coin_price;
        $item->image = GlobalFunction::saveFileAndGivePath($request->image);
        $item->save();

        return response()->json([
            'status' => true,
            'message' => 'Gift Added Successfully',
        ]);
    }
    public function fetchMyWithdrawalRequest(Request $request){
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

        $query = RedeemRequests::where('user_id', $user->id)
        ->orderBy('id', 'DESC')
        ->limit($request->limit);
        if($request->has('last_item_id')){
            $query->where('id','<',$request->last_item_id);
        }

        $redeemRequests =  $query->get();

        return GlobalFunction::sendDataResponse(true, 'Withdrawal requests fetched successfully', $redeemRequests);

    }
    //
    public function submitWithdrawalRequest(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }
        $rules = [
            'coins' => 'required',
            'gateway' => 'required',
            'account' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        $settings = GlobalSettings::first();
        if($request->coins < $settings->min_redeem_coins){
            return GlobalFunction::sendSimpleResponse(false, 'min. amount to Withdrawal is '. $settings->min_redeem_coins);
        }
        if($request->coins > $user->coin_wallet){
            return GlobalFunction::sendSimpleResponse(false, 'insufficient coins to redeem');
        }
        $redeem = new RedeemRequests();
        $redeem->request_number = GlobalFunction::generateRedeemRequestNumber($user->id);
        $redeem->user_id = $user->id;
        $redeem->gateway = $request->gateway;
        $redeem->account = $request->account;
        $redeem->coins = $request->coins;
        $redeem->coin_value = $settings->coin_value;
        $redeem->amount = $settings->coin_value * $request->coins;
        $redeem->save();

        $user->coin_wallet -= $request->coins;
        $user->save();

        return GlobalFunction::sendSimpleResponse(true, 'Withdrawal submitted successfully');

    }

    public function sendGift(Request $request){
        $token = $request->header('authtoken');
        $user = GlobalFunction::getUserFromAuthToken($token);
        if ($user->is_freez == 1) {
            return ['status' => false, 'message' => "this user is freezed!"];
        }
        $rules = [
            'user_id' => 'required|exists:tbl_users,id',
            'gift_id' => 'required|exists:tbl_gifts,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        $dataUser = GlobalFunction::prepareUserFullData($request->user_id);
        $gift = Gifts::find($request->gift_id);
        // Self check
         if($user->id == $dataUser->id){
            return GlobalFunction::sendSimpleResponse(false, 'you can not gift yourself!');
        }
        if($user->coin_wallet < $gift->coin_price){
            return GlobalFunction::sendSimpleResponse(false, 'no enough coins in your wallet!');
        }
        $user->coin_wallet -= $gift->coin_price;
        $user->coin_gifted_lifetime += $gift->coin_price;
        $user->save();

        $dataUser->coin_wallet += $gift->coin_price;
        $dataUser->coin_collected_lifetime += $gift->coin_price;
        $dataUser->save();

         // Insert Notification Data : Gift Sent
         $notificationData = GlobalFunction::insertUserNotification(Constants::notify_gift_user,$user->id, $dataUser->id, $gift->id);

        return GlobalFunction::sendSimpleResponse(true, 'gift sent successfully!');
    }
}

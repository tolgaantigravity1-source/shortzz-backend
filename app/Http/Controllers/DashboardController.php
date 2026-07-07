<?php

namespace App\Http\Controllers;

use App\Models\Constants;
use App\Models\DailyActiveUsers;
use App\Models\GlobalFunction;
use App\Models\Posts;
use App\Models\RedeemRequests;
use App\Models\ReportPosts;
use App\Models\ReportUsers;
use App\Models\Users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    //
    function dashboard()
    {
        // Validate googleCredentials.json file
        try {
            $filePath = base_path('googleCredentials.json');
            if (!File::exists($filePath)) {
                return response()->json(['message' => 'The googleCredentials.json file does not exist.'], 404);
            }

            $contents = File::get($filePath);
            if (empty(trim($contents))) {
                return response()->view('google_credentials_empty');
            }

            // Validate JSON format
            $json = json_decode($contents, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['message' => 'The googleCredentials.json file contains invalid JSON.'], 400);
            }

        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }

        $userTotal = Users::count();
        $userFreezed = Users::where('is_freez', 1)->count();
        $userModerator = Users::where('is_moderator', 1)->count();
        $userDummy = Users::where('is_dummy', 1)->count();

        $postsTotal = Posts::count();
        $postsReels = Posts::where('post_type', Constants::postTypeReel)->count();
        $postsVideos = Posts::where('post_type', Constants::postTypeVideo)->count();
        $postsImages = Posts::where('post_type', Constants::postTypeImage)->count();
        $postsText = Posts::where('post_type', Constants::postTypeText)->count();

        $reportsPost = ReportPosts::count();
        $reportsUser = ReportUsers::count();

        $withdrawalPending = RedeemRequests::where('status', Constants::withdrawalPending)->count();
        $withdrawalCompleted = RedeemRequests::where('status', Constants::withdrawalCompleted)->count();
        $withdrawalRejected = RedeemRequests::where('status', Constants::withdrawalRejected)->count();

        return view('dashboard')->with([
            'userTotal'=> $userTotal,
            'userFreezed'=> $userFreezed,
            'userModerator'=> $userModerator,
            'userDummy'=> $userDummy,

            'postsTotal'=> $postsTotal,
            'postsReels'=> $postsReels,
            'postsVideos'=> $postsVideos,
            'postsImages'=> $postsImages,
            'postsText'=> $postsText,

            'reportsPost'=> $reportsPost,
            'reportsUser'=> $reportsUser,

            'withdrawalPending'=> $withdrawalPending,
            'withdrawalCompleted'=> $withdrawalCompleted,
            'withdrawalRejected'=> $withdrawalRejected,
        ]);


    }

    function fetchChartData(Request $request){

            $month = $request->month;
            $year = $request->year;

            // Log::debug($month . $year);

            $startDate = Carbon::create($year, $month, 1);
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();
            $dates = collect();

            $datesWithCount = [];
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                $formattedDate = $date->format('Y-m-d');
                $dates->push($formattedDate);

                $usersCount = Users::whereDate('created_at', $date)->count();
                $postsCount = Posts::whereDate('created_at', $date)->count();

                $dauCount = 0;

                if ($date->isToday()) {
                    // If it's today, get users active today from app_last_used_at
                    $dauCount = Users::whereDate('app_last_used_at', $date)->count();
                } else {
                    // Otherwise, get from daily_active_users table
                    $dau = DailyActiveUsers::whereDate('date', $date)->first();
                    $dauCount = $dau?->user_count ?? 0;
                }

                $datesWithCount[] = [
                    'date' => $formattedDate,
                    'usersCount' => $usersCount,
                    'postsCount' => $postsCount,
                    'dauCount' => $dauCount,
                ];
            }
            return GlobalFunction::sendDataResponse(true, 'Users fetched successfully.', $datesWithCount);
    }
}

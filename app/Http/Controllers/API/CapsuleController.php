<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\sendMessage;
use App\Models\Capsule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CapsuleController extends Controller
{
    use sendMessage;

    // THIS METHOD IS FOR INCREASE THE CAPSULES
    public function capsulesPlus(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'capsules_count' => 'required|numeric'
        ]);

        // Check for any errors in the request
        if($validator->fails())
            return $this->sendResponseError('Bad Request', $validator->errors(), 400);

        // Get current time
        $date = Carbon::now()->format('Y/m/d');

        // Read the data from DB - To check if the user has already a row for current day
        $capsules = Capsule::where('user_id', Auth::guard('api')->id())->where('date', $date)->first();

        // Update or create the capsules count
        if($capsules)
            return $this->update($capsules, $request->capsules_count);
        else return $this->create($request->capsules_count, $date, Auth::guard('api')->id());
    }

    // Update capsules count
    protected function update($capsules, $capsules_count)
    {
        // Increase
        $capsules->capsules_count += $capsules_count;
        $capsules->save();

        // Send response
        return $this->sendResponseMessage('Increase has been added', 200);
    }

    // Create capsules count
    protected function create($capsules_count, $date, $user_id)
    {
        // Create row for current day
        $value = [
            'capsules_count' => $capsules_count,
            'user_id' => $user_id,
            'date' => $date
        ];
        Capsule::create($value);

        // Send response
        return $this->sendResponseMessage('Increase has been added', 200);
    }

    // THE REPORT
    public function capsulesReport()
    {
        // Get the current date as 'dd'
        $today = Carbon::now()->isoFormat('dd');

        // Create a week map
        $weekMap = [
            'Sa' => 0,
            'Su' => -1,
            'Mo' => -2,
            'Tu' => -3,
            'We' => -4,
            'Th' => -5,
            'Fr' => -6,
        ];

        // Get number of this day ad negative
        $dayNumber = $weekMap[$today];

        // Get report of the days, weeks and months
        $reportDays = $this->reportDays(Auth::guard('api')->id());
        $reportWeeks = $this->reportWeeks(Auth::guard('api')->id(), $dayNumber);
        $reportMonths = $this->reportMonths(Auth::guard('api')->id());

        // Return the response
        return response()->json([
            'today' => $reportDays['sumOfThisDay'],
            'yesterday' => $reportDays['sumOfLastDay'],
            'this week' => $reportWeeks['sumOfThisWeek'],
            'last week' => $reportWeeks['sumOfLastWeek'],
            'this month' => $reportMonths['sumOfThisMonth'],
            'last month' => $reportMonths['sumOfLastMonth'],
        ], 200);
    }

    // REPORT OF TODAY AND YESTERDAY
    protected function reportDays($user_id)
    {
        // Get this day and yesterday
        $thisDay = Carbon::now();
        $lastDay = Carbon::yesterday();

        // Read the data from DB - get the capsules count of this day
        $countThisDay = Capsule::select('capsules_count')->where('user_id', $user_id)
            ->where('date', $thisDay->format('Y/m/d'))->first();

        // Read the data from DB - get the capsules count of yesterday
        $countLastDay = Capsule::select('capsules_count')->where('user_id', $user_id)
            ->where('date', $lastDay->format('Y/m/d'))->first();

        if($countThisDay)
            $sumOfThisDay = $countThisDay->capsules_count;
        else $sumOfThisDay = 0;

        if($countLastDay)
            $sumOfLastDay = $countLastDay->capsules_count;
        else $sumOfLastDay = 0;

        // Return the report
        $reportDays = [
            'sumOfThisDay' => $sumOfThisDay,
            'sumOfLastDay' => $sumOfLastDay,
        ];
        return $reportDays;
    }

    // REPORT OF CURRENT AND LAST WEEK
    protected function reportWeeks($user_id, $dayNumber)
    {
        // Get first and current day of this week
        $fisrtDayOfThisWeek = Carbon::now()->addDays($dayNumber);
        $CurrentDayOfThisWeek = Carbon::now();

        // Get first and last day of last week
        $lastDayOfLastWeek = Carbon::now()->addDays($dayNumber)->add(-1, 'day');
        $firstDayOfLastWeek = Carbon::now()->addDays($dayNumber)->add(-7, 'day');

        // Read the data from DB - get the capsules count of this week
        $countThisWeek = Capsule::where('user_id', $user_id)
            ->whereBetween('date', [$fisrtDayOfThisWeek->format('Y/m/d'), $CurrentDayOfThisWeek->format('Y/m/d')])->get();

        // Read the data from DB - get the capsules count of last week
        $countLastWeek = Capsule::where('user_id', $user_id)
            ->whereBetween('date', [$firstDayOfLastWeek->format('Y/m/d'), $lastDayOfLastWeek->format('Y/m/d')])->get();

        // To count the capsules of weeks
        $sumThisWeek = 0;
        $sumLastWeek = 0;

        // Counting the capsules of this week
        foreach($countThisWeek as $item) {
            $sumThisWeek += $item->capsules_count;
        }

        // Counting the capsules of last week
        foreach($countLastWeek as $item) {
            $sumLastWeek += $item->capsules_count;
        }

        // Return the report
        $reportWeeks = [
            'sumOfThisWeek' => $sumThisWeek,
            'sumOfLastWeek' => $sumLastWeek
        ];
        return $reportWeeks;
    }

    // REPORT OF CURRENT AND LAST MONTH
    protected function reportMonths($user_id)
    {
        // Get current and last month
        $thisMonth = Carbon::now()->format('m');
        $lastMonth = Carbon::now()->addMonth(-1)->format('m');

        $startOfThisMonth = Carbon::now()->startOfMonth();
        $endOfThisMonth = Carbon::now()->endOfMonth();

        $startOfLastMonth = Carbon::now()->addMonth(-1)->startOfMonth();
        $endOfLastMonth = Carbon::now()->addMonth(-1)->endOfMonth();

        // Read the data from DB - get the capsules count of this month
        $countThisMonth = Capsule::where('user_id', $user_id)
            ->whereBetween('date', [$startOfThisMonth->format('Y/m/d'), $endOfThisMonth->format('Y/m/d')])->get();

        // Read the data from DB - get the capsules count of last month
        $countLastMonth = Capsule::where('user_id', $user_id)
            ->whereBetween('date', [$startOfLastMonth->format('Y/m/d'), $endOfLastMonth->format('Y/m/d')])->get();

        // To count the capsules of weeks
        $sumThisMonth = 0;
        $sumLastMonth = 0;

        // Counting the capsules of current and last month
        foreach($countThisMonth as $item)
            $sumThisMonth += $item->capsules_count;

        foreach($countLastMonth as $item)
            $sumLastMonth += $item->capsules_count;

        // Return the response
        $reportMonths = [
            'sumOfThisMonth' => $sumThisMonth,
            'sumOfLastMonth' => $sumLastMonth
        ];
        return $reportMonths;
    }
}

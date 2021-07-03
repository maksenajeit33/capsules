<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\SendResponse;
use App\Models\Capsule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CapsuleController extends Controller
{
    use SendResponse;

    // THIS METHOD IS FOR INCREASE THE CAPSULES
    public function capsulesPlus(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'capsules_count' => 'required|numeric'
        ]);

        // Check for any errors in the request
        if($validator->fails())
            return $this->sendResponseError('Please validate the errors', $validator->errors(), 400);

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
        return $this->sendResponseMessage('Increase added', 200);
    }

    // Create capsules count
    protected function create($capsules_count, $date, $user_id)
    {
        // Create
        $value = [
            'capsules_count' => $capsules_count,
            'user_id' => $user_id,
            'date' => $date
        ];
        Capsule::create($value);

        // Send response
        return $this->sendResponseMessage('Increase added', 200);
    }
}

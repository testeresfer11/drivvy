<?php

namespace App\Http\Controllers\api;

use App\Models\SearchHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class SearchHistoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'departure_city' => 'required|string',
            'departure_lat' => 'required|string|max:100',
            'departure_long' => 'required|string|max:100',
            'arrival_city' => 'required|string',
            'arrival_lat' => 'required|string|max:100',
            'arrival_long' => 'required|string|max:100',
           
        ]);

        // Create search history
       $searchHistory = SearchHistory::updateOrCreate(
    [
        'user_id' => Auth::id(),  // condition to check for existing records
        'departure_city' => $request->departure_city,  // optional condition for matching
        'arrival_city' => $request->arrival_city,  // optional condition for matching
    ],
    [
        'departure_lat' => $request->departure_lat,
        'departure_long' => $request->departure_long,
        'arrival_lat' => $request->arrival_lat,
        'arrival_long' => $request->arrival_long,
    ]
);


        return response()->json([
            'message' => 'Search history saved successfully',
            'data' => $searchHistory
        ], 201);
    }


   public function getSearchHistories()
{
    // Get the authenticated user's ID
    $user = Auth::user();
    $userId = $user->user_id;

    // Fetch the latest 5 search histories for the current user, ordered by descending created_at
    $searchHistories = SearchHistory::where('user_id', $userId)
                                    ->orderBy('created_at', 'desc')
                                    ->take(5)
                                    ->get();

    return response()->json([
        'message' => 'Search histories retrieved successfully',
        'data' => $searchHistories
    ], 200);
}


}

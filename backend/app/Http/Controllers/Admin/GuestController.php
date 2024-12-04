<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Guest;

class GuestController extends Controller
{
    public function index()
    {
        $guests = Guest::all();
        return response()->json($guests);
    }

    public function addGuest(Request $request)
    {
        $guest = Guest::create([
            'guest_count' => $request->guestCount,
            'guest_price' => $request->price,
        ]);

        if($guest){
            return response()->json([
                'message' => 'Guest added successfully',
                'guest' => $guest,
                'status' => 200,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Guest not added',
                'status' => 400
            ], 400);
        }
    }
}

<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::where('flag', '0')->get();
        return response()->json($packages);
    }

    public function addPackage(Request $request)
    {
        // Handle image upload
        $image = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = Str::random(32) . '.' . $extension;
            $file->storeAs('public/packages', $filename);
            $image = 'packages/' . $filename;
        }

        $package = Package::create([
            'guest_id' => $request->guest_id,
            'package_name' => $request->name,
            'package_image' => $image,
            'package_description' => $request->description,
            'package_type' => $request->eventType,
            'package_price' => $request->price,
            'package_inclusion' => $request->inclusions,
        ]);

        return response()->json([
            'message' => 'Package added successfully',
            'status' => 200,
            'data' => $package
        ], 200);
    }

    public function getPackageById($id)
    {
        $package = Package::find($id);
        return response()->json($package);
    }

    public function deletePackage(Request $request, $id)
    {
        $package = Package::find($id);
        $package->update([
            'flag' => '1'
        ]);
        return response()->json([
            'message' => 'Package updated successfully',
            'status' => 200
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{

    // Post
    public function store(Request $request)
    {
        $service = Service::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'subcategory' => $request->subcategory,
            'price' => $request->price
        ]);

        return response()->json([
            'message' => 'Service created successfully',
            'data' => $service
        ]);
    }

    // View
    public function index()
    {
        $services = Service::all();

        return response()->json([
            'data' => $services
        ]);
    }

}
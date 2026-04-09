<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function create()
    {
        if (Auth::user()->role !== 'provider') {
            abort(403, 'Unauthorized action.');
        }
        return view('services.create');
    }

    // Post
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string'
        ]);
        
        Service::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'subcategory' => $request->subcategory,
            'price' => $request->price
        ]);

        return redirect()->route('services.index')->with('success', 'Service posted successfully!');
    }

    // View
    public function index()
    {
        $services = Service::latest()->get();
        return view('services.index', compact('services'));
    }

}
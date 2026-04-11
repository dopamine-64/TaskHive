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

    // Post - Store a new service
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
            'price' => $request->price,
            'location' => $request->location,
        ]);

        return redirect()->route('services.index')->with('success', 'Service posted successfully!');
    }

    // View - Search and filter services
    public function index(Request $request)
    {
        $query = Service::query();

        // Filter by location
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter by category/service type (search in category, subcategory, and title)
        if ($request->filled('category')) {
            $query->where(function($q) use ($request) {
                $q->where('category', 'like', '%' . $request->category . '%')
                  ->orWhere('subcategory', 'like', '%' . $request->category . '%')
                  ->orWhere('title', 'like', '%' . $request->category . '%');
            });
        }

        // Parse price range (e.g., "50-200" or "50 - 200")
        if ($request->filled('price_range')) {
            $priceRange = explode('-', str_replace(' ', '', $request->price_range));
            if (count($priceRange) == 2) {
                $query->where('price', '>=', (float)$priceRange[0]);
                $query->where('price', '<=', (float)$priceRange[1]);
            }
        }

        // Filter by min price (individual)
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        // Filter by max price (individual)
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $services = $query->latest()->paginate(12);

        return view('services.index', compact('services'));
    }
}
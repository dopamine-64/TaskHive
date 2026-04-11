<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProviderProfile;

class ProviderSearchController extends Controller
{
    public function search(Request $request)
    {
        // Get the coordinates sent from the hidden form via JavaScript
        $customerLat = $request->input('lat');
        $customerLng = $request->input('lng');

        if ($customerLat && $customerLng) {
            // Eager load the 'user' relationship to get the names efficiently
            // and apply the Haversine math formula to filter by distance
            $providers = ProviderProfile::with('user')
                            ->availableInArea($customerLat, $customerLng)
                            ->get();
        } else {
            // If no location was provided, return an empty list
            $providers = collect(); 
        }

        // Return the view and pass the providers list AND the coordinates.
        // (Note: if you named your view file search-results.blade.php instead of index.blade.php, 
        // just change 'providers.index' to 'providers.search-results')
        return view('providers.search-results', compact('providers', 'customerLat', 'customerLng'));
    }

    public function show($id)
    {
        $provider = ProviderProfile::with('user')->findOrFail($id);
        return view('providers.show', compact('provider'));
    }
}
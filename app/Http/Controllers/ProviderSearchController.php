<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProviderProfile;
use Illuminate\Pagination\LengthAwarePaginator;

class ProviderSearchController extends Controller
{
    public function search(Request $request)
    {
        // 1. If user clicks "Not Now" (no lat/lng in URL), return an empty paginator safely.
        if (!$request->has('lat') || !$request->has('lng')) {
            $emptyPaginator = new LengthAwarePaginator([], 0, 12, 1, ['path' => $request->url()]);
            return view('providers.index', ['providers' => $emptyPaginator]);
        }

        $userLat = (float) $request->input('lat');
        $userLng = (float) $request->input('lng');

        // 2. Fetch all providers that have set up their location
        $allProviders = ProviderProfile::with('user')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        // 3. Filter the providers using pure PHP (Bypasses SQLite math errors)
        $filteredProviders = $allProviders->filter(function ($provider) use ($userLat, $userLng) {
            $providerLat = (float) $provider->latitude;
            $providerLng = (float) $provider->longitude;

            // Calculate the exact distance
            $distance = $this->calculateDistance($userLat, $userLng, $providerLat, $providerLng);
            
            // Attach the distance so your Blade view can print it: {{ $provider->distance }}
            $provider->distance = $distance; 

            // Get the provider's radius. If they haven't set one, default to 50km
            $radius = (float) ($provider->service_radius_km ?? 50.0);
            
            // Keep this provider only if the user is within their radius
            return $distance <= $radius;

        })->sortBy('distance'); // Sort from closest to furthest

        // 4. Manually paginate the filtered list so your Blade UI stays intact
        $page = (int) $request->input('page', 1);
        $perPage = 12;
        
        $paginatedProviders = new LengthAwarePaginator(
            $filteredProviders->forPage($page, $perPage)->values(),
            $filteredProviders->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                // Keep the lat/lng in the URL when they click page 2, page 3, etc.
                'query' => $request->query() 
            ]
        );

        return view('providers.index', ['providers' => $paginatedProviders]);
    }

    /**
     * Standard Haversine distance calculation in pure PHP
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Kilometers
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + 
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
             
        $c = 2 * asin(sqrt($a));
        
        return $earthRadius * $c;
    }
}
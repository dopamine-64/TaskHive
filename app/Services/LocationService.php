<?php

namespace App\Services;

class LocationService
{
    public function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $lat1 = (float) $lat1;
        $lon1 = (float) $lon1;
        $lat2 = (float) $lat2;
        $lon2 = (float) $lon2;

        $earthRadius = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * asin(min(1, sqrt($a)));

        return $earthRadius * $c;
    }

    public function haversineSql(string $latitudeColumn, string $longitudeColumn): string
    {
        return "(6371 * acos(cos(radians(?)) * cos(radians({$latitudeColumn})) * cos(radians({$longitudeColumn}) - radians(?)) + sin(radians(?)) * sin(radians({$latitudeColumn}))))";
    }
}

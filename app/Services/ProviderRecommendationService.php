<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class ProviderRecommendationService
{
    private const LOCATION_WEIGHT = 0.45;
    private const RATING_WEIGHT = 0.35;
    private const PRICE_WEIGHT = 0.10;
    private const SKILLS_WEIGHT = 0.10;

    public function __construct(private LocationService $locationService)
    {
    }

    public function recommendForCustomer(User $customer, Collection $providers, int $limit = 5, array $requiredSkills = []): Collection
    {
        if ($providers->isEmpty()) {
            return collect();
        }

        $requiredSkills = $this->normalizeSkills($requiredSkills);
        $priceStats = $this->resolvePriceRange($providers);

        $customerLat = $customer->latitude !== null ? (float) $customer->latitude : null;
        $customerLng = $customer->longitude !== null ? (float) $customer->longitude : null;
        $customerHasLocation = $customerLat !== null && $customerLng !== null;

        $scored = $providers->map(function ($provider) use ($customer, $requiredSkills, $priceStats, $customerLat, $customerLng, $customerHasLocation) {
                $distance = $this->distanceFromCustomer($customerLat, $customerLng, $provider);
                $ratingScore = $this->calculateRatingScore($provider);
                $priceScore = $this->calculatePriceScore($provider, $priceStats['min'], $priceStats['max']);
                $skillsScore = $this->calculateSkillsScore($provider, $requiredSkills);
                $locationScore = $customerHasLocation
                    ? $this->calculateLocationScore($provider, $distance)
                    : $ratingScore;

                $match = round(
                    ($locationScore * self::LOCATION_WEIGHT) +
                    ($ratingScore * self::RATING_WEIGHT) +
                    ($priceScore * self::PRICE_WEIGHT) +
                    ($skillsScore * self::SKILLS_WEIGHT),
                    2
                );

                $provider->match_percentage = $match;
                $provider->match_breakdown = [
                    'location' => round($locationScore, 2),
                    'rating' => round($ratingScore, 2),
                    'price' => round($priceScore, 2),
                    'skills' => round($skillsScore, 2),
                ];

                $provider->distance = $distance;
                $provider->distance_label = $distance !== null ? number_format($distance, 1) . ' km away' : 'Unknown';

                return $provider;
            });

        if (!$customerHasLocation) {
            return $scored
                ->sortByDesc('average_rating')
                ->take($limit)
                ->values();
        }

        return $scored
            ->sort(function ($a, $b) {
                $scoreCompare = ($b->match_percentage <=> $a->match_percentage);
                if ($scoreCompare !== 0) {
                    return $scoreCompare;
                }

                $aDistance = $a->distance ?? PHP_FLOAT_MAX;
                $bDistance = $b->distance ?? PHP_FLOAT_MAX;
                return $aDistance <=> $bDistance;
            })
            ->take($limit)
            ->values();
    }

    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        return $this->locationService->calculateDistance($lat1, $lng1, $lat2, $lng2);
    }

    public function distanceFromCustomer(?float $customerLat, ?float $customerLng, $provider): ?float
    {
        if ($customerLat === null || $customerLng === null) {
            return null;
        }

        [$providerLat, $providerLng] = $this->resolveProviderCoordinates($provider);
        if ($providerLat === null || $providerLng === null) {
            return null;
        }

        return $this->locationService->calculateDistance(
            $customerLat,
            $customerLng,
            $providerLat,
            $providerLng
        );
    }

    private function calculateLocationScore($provider, ?float $distance): float
    {
        if ($distance === null) {
            return 30.0;
        }

        $maxRadius = (float) ($provider->service_radius_km ?? 50.0);
        if ($maxRadius <= 0) {
            $maxRadius = 50.0;
        }

        if ($distance > $maxRadius) {
            return 0.0;
        }

        return $this->clamp((1 - ($distance / $maxRadius)) * 100);
    }

    private function calculateRatingScore($provider): float
    {
        $rating = (float) ($provider->average_rating ?? 0);
        return $this->clamp(($rating / 5) * 100);
    }

    private function calculatePriceScore($provider, ?float $minPrice, ?float $maxPrice): float
    {
        $price = $this->extractProviderPrice($provider);
        if ($price === null || $price <= 0) {
            return 0.0;
        }

        if ($minPrice === null || $maxPrice === null || $maxPrice <= $minPrice) {
            return 100.0;
        }

        return $this->clamp((($maxPrice - $price) / ($maxPrice - $minPrice)) * 100);
    }

    private function calculateSkillsScore($provider, array $requiredSkills): float
    {
        if (empty($requiredSkills)) {
            return 100.0;
        }

        $providerSkills = $this->normalizeSkills($provider->skills ?? []);
        if (empty($providerSkills)) {
            return 0.0;
        }

        $matches = array_intersect($requiredSkills, $providerSkills);
        return $this->clamp((count($matches) / count($requiredSkills)) * 100);
    }

    private function resolveProviderCoordinates($provider): array
    {
        if ($provider->latitude !== null && $provider->longitude !== null) {
            return [(float) $provider->latitude, (float) $provider->longitude];
        }

        $profileLocation = $provider->service_area ?? null;
        $serviceLocation = $provider->latest_service_location ?? null;

        $fromProfile = $this->resolveKnownAreaCoordinates($profileLocation);
        if ($fromProfile !== null) {
            return $fromProfile;
        }

        $fromService = $this->resolveKnownAreaCoordinates($serviceLocation);
        if ($fromService !== null) {
            return $fromService;
        }

        return [null, null];
    }

    private function resolvePriceRange(Collection $providers): array
    {
        $prices = $providers
            ->map(fn ($provider) => $this->extractProviderPrice($provider))
            ->filter(fn ($price) => $price !== null && $price > 0)
            ->values();

        if ($prices->isEmpty()) {
            return ['min' => null, 'max' => null];
        }

        return [
            'min' => (float) $prices->min(),
            'max' => (float) $prices->max(),
        ];
    }

    private function extractProviderPrice($provider): ?float
    {
        $price = $provider->fixed_rate ?? $provider->hourly_rate ?? null;
        if ($price === null || $price === '') {
            return null;
        }

        return (float) $price;
    }

    private function normalizeSkills(mixed $skills): array
    {
        if (is_string($skills)) {
            $decoded = json_decode($skills, true);
            $skills = json_last_error() === JSON_ERROR_NONE ? $decoded : explode(',', $skills);
        }

        if (!is_array($skills)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($item) => mb_strtolower(trim((string) $item)),
            $skills
        )));
    }

    private function clamp(float $value): float
    {
        return max(0, min(100, $value));
    }

    private function resolveKnownAreaCoordinates(?string $location): ?array
    {
        if (!$location) {
            return null;
        }

        $normalized = mb_strtolower(trim($location));
        if ($normalized === '') {
            return null;
        }

        $knownAreas = [
            'dhanmondi' => [23.7465, 90.3760],
            'gulshan' => [23.7925, 90.4078],
            'banani' => [23.7937, 90.4066],
            'mirpur' => [23.8223, 90.3654],
            'uttara' => [23.8759, 90.3795],
            'mohammadpur' => [23.7639, 90.3589],
            'badda' => [23.7808, 90.4251],
            'motijheel' => [23.7338, 90.4173],
            'farmgate' => [23.7570, 90.3897],
            'bashundhara' => [23.8227, 90.4337],
            'dhaka' => [23.8103, 90.4125],
        ];

        foreach ($knownAreas as $area => $coordinates) {
            if (str_contains($normalized, $area)) {
                return $coordinates;
            }
        }

        return null;
    }
}

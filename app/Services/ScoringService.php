<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ScoringService
{
    public function __construct(private LocationService $locationService)
    {
    }

    public function calculateForProvider($provider, array $context): array
    {
        $distance = $this->resolveDistance($provider, $context);
        $maxRadius = (float) ($context['max_radius_km'] ?? config('matching.default_radius_km', 10));
        $locationScore = $this->getLocationScore($distance, $maxRadius);

        $rating = (float) ($provider->average_rating ?? 0);
        $ratingScore = $this->clamp(($rating / 5) * 100);

        $providerRate = (float) ($provider->effective_rate ?? $provider->fixed_rate ?? $provider->hourly_rate ?? 0);
        $priceScore = $this->getPriceScore(
            $providerRate,
            $context['price_min'] ?? null,
            $context['price_max'] ?? null
        );

        $skillsScore = $this->getSkillsMatchScore(
            $provider->skills ?? [],
            $context['required_skills'] ?? []
        );

        $historyScore = $this->getHistoryScore(
            $context['user_id'] ?? null,
            $provider->user_id ?? null
        );

        $totalScore = $this->calculateWeightedTotal([
            'location' => $locationScore,
            'rating' => $ratingScore,
            'price' => $priceScore,
            'skills' => $skillsScore,
            'history' => $historyScore,
        ]);

        return [
            'distance_km' => $distance,
            'location_score' => $locationScore,
            'rating_score' => $ratingScore,
            'price_score' => $priceScore,
            'skills_score' => $skillsScore,
            'history_score' => $historyScore,
            'total_score' => $totalScore,
        ];
    }

    public function getPriceScore($providerRate, $userMin, $userMax): float
    {
        if ($userMin === null && $userMax === null) {
            return 100.0;
        }

        $providerRate = (float) $providerRate;
        $userMin = $userMin !== null ? (float) $userMin : null;
        $userMax = $userMax !== null ? (float) $userMax : null;

        if ($providerRate <= 0) {
            return 40.0;
        }

        if ($userMin !== null && $userMax !== null && $providerRate >= $userMin && $providerRate <= $userMax) {
            return 100.0;
        }

        if ($userMin !== null && $providerRate < $userMin) {
            $distance = $userMin - $providerRate;
            return $this->clamp(100 - (($distance / max($userMin, 1)) * 40));
        }

        if ($userMax !== null && $providerRate > $userMax) {
            $distance = $providerRate - $userMax;
            return $this->clamp(100 - (($distance / max($userMax, 1)) * 100));
        }

        return 70.0;
    }

    public function getSkillsMatchScore($providerSkills, $requiredSkills): float
    {
        $providerSkills = $this->normalizeSkills($providerSkills);
        $requiredSkills = $this->normalizeSkills($requiredSkills);

        if (empty($requiredSkills)) {
            return 100.0;
        }

        if (empty($providerSkills)) {
            return 0.0;
        }

        $matches = array_intersect($requiredSkills, $providerSkills);
        $ratio = count($matches) / count($requiredSkills);

        return $this->clamp($ratio * 100);
    }

    public function getHistoryScore($userId, $providerId): float
    {
        if (!$userId || !$providerId) {
            return 0.0;
        }

        $completedCount = DB::table('trackings')
            ->where('customer_id', $userId)
            ->where('provider_id', $providerId)
            ->where('status', 'completed')
            ->count();

        if ($completedCount <= 0) {
            return 0.0;
        }

        return $this->clamp(min(100, 35 * $completedCount));
    }

    private function resolveDistance($provider, array $context): ?float
    {
        if (isset($provider->distance_km) && $provider->distance_km !== null) {
            return (float) $provider->distance_km;
        }

        if (!isset($context['user_lat'], $context['user_lng'], $provider->latitude, $provider->longitude)) {
            return null;
        }

        return $this->locationService->calculateDistance(
            $context['user_lat'],
            $context['user_lng'],
            $provider->latitude,
            $provider->longitude
        );
    }

    private function getLocationScore(?float $distanceKm, float $maxRadius): float
    {
        if ($distanceKm === null) {
            return 50.0;
        }

        if ($distanceKm <= 0) {
            return 100.0;
        }

        if ($distanceKm > $maxRadius) {
            return 0.0;
        }

        $score = (1 - ($distanceKm / max($maxRadius, 0.01))) * 100;
        return $this->clamp($score);
    }

    private function calculateWeightedTotal(array $scores): float
    {
        $weights = config('matching.weights');
        $total = 0.0;

        foreach ($scores as $key => $score) {
            $weight = (float) ($weights[$key] ?? 0);
            $total += ((float) $score) * ($weight / 100);
        }

        return round($this->clamp($total), 2);
    }

    private function normalizeSkills($skills): array
    {
        if (is_string($skills)) {
            $decoded = json_decode($skills, true);
            $skills = json_last_error() === JSON_ERROR_NONE ? $decoded : explode(',', $skills);
        }

        if (!is_array($skills)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($value) => mb_strtolower(trim((string) $value)),
            $skills
        )));
    }

    private function clamp(float $value): float
    {
        return max(0, min(100, $value));
    }
}

<?php

namespace App\Services;

use App\Models\MatchingScore;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MatchingEngine
{
    public function __construct(
        private LocationService $locationService,
        private ScoringService $scoringService
    ) {
    }

    public function match(array $input): Collection
    {
        $limit = (int) ($input['limit'] ?? config('matching.top_results', 5));
        $cacheKey = $this->cacheKey($input, 'match');
        $ttl = now()->addSeconds((int) config('matching.cache_ttl_seconds', 3600));

        return Cache::remember($cacheKey, $ttl, function () use ($input, $limit) {
            $candidates = $this->candidateQuery($input)->limit((int) config('matching.candidate_limit', 200))->get();

            $scored = $candidates->map(function ($provider) use ($input) {
                $score = $this->scoringService->calculateForProvider($provider, $input);
                $payload = array_merge((array) $provider, $score, [
                    'why_recommended' => $this->buildReason($provider, $score),
                ]);

                $this->persistScore((int) ($input['user_id'] ?? 0), (int) $provider->id, $score, $input);

                Log::info('matching_engine.provider_scored', [
                    'user_id' => $input['user_id'] ?? null,
                    'provider_profile_id' => $provider->id,
                    'total_score' => $score['total_score'],
                    'distance_km' => $score['distance_km'],
                ]);

                return $payload;
            })->sort(function (array $a, array $b) {
                $scoreCompare = ($b['total_score'] <=> $a['total_score']);
                if ($scoreCompare !== 0) {
                    return $scoreCompare;
                }

                $aDistance = $a['distance_km'] ?? PHP_FLOAT_MAX;
                $bDistance = $b['distance_km'] ?? PHP_FLOAT_MAX;
                return $aDistance <=> $bDistance;
            })->take($limit)->values();

            Log::info('matching_engine.completed', [
                'user_id' => $input['user_id'] ?? null,
                'matches_count' => $scored->count(),
                'service_type' => $input['service_type'] ?? null,
            ]);

            return $scored;
        });
    }

    private function candidateQuery(array $input)
    {
        $query = DB::table('provider_profiles as pp')
            ->join('users as u', 'u.id', '=', 'pp.user_id')
            ->select([
                'pp.id',
                'pp.user_id',
                'pp.skills',
                'pp.hourly_rate',
                'pp.fixed_rate',
                'pp.average_rating',
                'pp.total_ratings',
                'pp.latitude',
                'pp.longitude',
                'pp.service_radius_km',
                'pp.is_available',
                'pp.response_time',
                'pp.completion_rate',
                'pp.total_completed_jobs',
                'u.name as provider_name',
            ])
            ->selectRaw("CAST(COALESCE(NULLIF(pp.fixed_rate, ''), NULLIF(pp.hourly_rate, ''), '0') AS DECIMAL(10,2)) as effective_rate")
            ->where('u.role', 'provider')
            ->where('pp.is_available', true)
            ->whereNotNull('pp.latitude')
            ->whereNotNull('pp.longitude');

        if (isset($input['user_lat'], $input['user_lng'])) {
            $distanceSql = $this->locationService->haversineSql('pp.latitude', 'pp.longitude');
            $query->selectRaw("{$distanceSql} as distance_km", [(float) $input['user_lat'], (float) $input['user_lng'], (float) $input['user_lat']]);

            $radius = (float) ($input['max_radius_km'] ?? config('matching.default_radius_km', 10));
            $query->whereRaw("{$distanceSql} <= ?", [(float) $input['user_lat'], (float) $input['user_lng'], (float) $input['user_lat'], $radius]);
        }

        if (isset($input['min_rating'])) {
            $query->where('pp.average_rating', '>=', (float) $input['min_rating']);
        }

        if (isset($input['price_min'])) {
            $query->whereRaw("CAST(COALESCE(NULLIF(pp.fixed_rate, ''), NULLIF(pp.hourly_rate, ''), '0') AS DECIMAL(10,2)) >= ?", [(float) $input['price_min']]);
        }

        if (isset($input['price_max'])) {
            $query->whereRaw("CAST(COALESCE(NULLIF(pp.fixed_rate, ''), NULLIF(pp.hourly_rate, ''), '0') AS DECIMAL(10,2)) <= ?", [(float) $input['price_max']]);
        }

        if (!empty($input['service_type'])) {
            $serviceType = (string) $input['service_type'];
            $query->where(function ($nested) use ($serviceType) {
                $nested->where('pp.skills', 'like', '%' . $serviceType . '%')
                    ->orWhereExists(function ($serviceSub) use ($serviceType) {
                        $serviceSub->selectRaw('1')
                            ->from('services as s')
                            ->whereColumn('s.provider_profile_id', 'pp.id')
                            ->where('s.is_active', true)
                            ->where('s.category', 'like', '%' . $serviceType . '%');
                    });
            });
        }

        return $query->orderByDesc('pp.average_rating');
    }

    private function persistScore(int $userId, int $providerProfileId, array $score, array $input): void
    {
        MatchingScore::create([
            'user_id' => $userId ?: null,
            'provider_profile_id' => $providerProfileId,
            'distance_km' => $score['distance_km'],
            'location_score' => $score['location_score'],
            'rating_score' => $score['rating_score'],
            'price_score' => $score['price_score'],
            'skills_score' => $score['skills_score'],
            'history_score' => $score['history_score'],
            'total_score' => $score['total_score'],
            'context' => $input,
            'calculated_at' => now(),
        ]);
    }

    private function buildReason(object $provider, array $score): array
    {
        return [
            'distance_km' => round((float) ($score['distance_km'] ?? 0), 2),
            'rating' => (float) ($provider->average_rating ?? 0),
            'price' => (float) ($provider->effective_rate ?? 0),
            'repeat_customer_bonus' => (float) $score['history_score'],
            'component_scores' => [
                'location' => $score['location_score'],
                'rating' => $score['rating_score'],
                'price' => $score['price_score'],
                'skills' => $score['skills_score'],
                'history' => $score['history_score'],
            ],
        ];
    }

    private function cacheKey(array $input, string $prefix): string
    {
        ksort($input);
        return 'matching:' . $prefix . ':' . md5(json_encode($input));
    }
}

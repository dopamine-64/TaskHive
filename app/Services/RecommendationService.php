<?php

namespace App\Services;

use App\Models\ProviderRecommendation;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RecommendationService
{
    public function __construct(private MatchingEngine $matchingEngine)
    {
    }

    public function recommendForUser(int $userId, array $input = []): Collection
    {
        $user = User::findOrFail($userId);
        $preference = UserPreference::firstOrCreate(['user_id' => $user->id], [
            'auto_match_enabled' => true,
        ]);

        $context = $this->buildContext($user, $preference, $input);
        $cacheKey = 'matching:recommend:user:' . $user->id . ':' . md5(json_encode($context));
        $ttlSeconds = (int) config('matching.cache_ttl_seconds', 3600);

        return Cache::remember($cacheKey, now()->addSeconds($ttlSeconds), function () use ($context, $user, $cacheKey, $ttlSeconds) {
            $results = $this->matchingEngine->match($context);

            ProviderRecommendation::where('user_id', $user->id)->delete();
            $expiresAt = now()->addSeconds($ttlSeconds);

            foreach ($results as $result) {
                ProviderRecommendation::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'provider_profile_id' => $result['id'],
                        'cache_key' => $cacheKey,
                    ],
                    [
                        'match_score' => $result['total_score'],
                        'metadata' => $result,
                        'recommended_at' => now(),
                        'expires_at' => $expiresAt,
                    ]
                );
            }

            Log::info('recommendation_service.generated', [
                'user_id' => $user->id,
                'count' => $results->count(),
            ]);

            return $results;
        });
    }

    private function buildContext(User $user, UserPreference $preference, array $input): array
    {
        return [
            'user_id' => $user->id,
            'user_lat' => $input['user_lat'] ?? $input['lat'] ?? null,
            'user_lng' => $input['user_lng'] ?? $input['lng'] ?? null,
            'price_min' => $input['price_min'] ?? $preference->preferred_price_min,
            'price_max' => $input['price_max'] ?? $preference->preferred_price_max,
            'max_radius_km' => $input['max_radius_km'] ?? $preference->preferred_radius_km,
            'service_type' => $input['service_type'] ?? null,
            'required_skills' => $input['required_skills'] ?? ($preference->preferred_categories ?? []),
            'min_rating' => $input['min_rating'] ?? null,
            'limit' => (int) ($input['limit'] ?? config('matching.top_results', 5)),
        ];
    }
}

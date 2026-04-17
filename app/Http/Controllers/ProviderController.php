<?php

namespace App\Http\Controllers;

use App\Models\ProviderProfile;
use App\Models\Service;
use App\Models\UserPreference;
use App\Services\MatchingEngine;
use App\Services\ProviderRecommendationService;
use App\Services\RecommendationService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProviderController extends Controller
{
    public function __construct(
        private MatchingEngine $matchingEngine,
        private RecommendationService $recommendationService,
        private ProviderRecommendationService $providerRecommendationService
    ) {
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'skills' => 'nullable',
        ]);

        $user = $request->user();
        $userLat = isset($validated['lat']) ? (float) $validated['lat'] : ($user->latitude !== null ? (float) $user->latitude : null);
        $userLng = isset($validated['lng']) ? (float) $validated['lng'] : ($user->longitude !== null ? (float) $user->longitude : null);

        if (isset($validated['lat'], $validated['lng'])) {
            $user->update([
                'latitude' => $userLat,
                'longitude' => $userLng,
            ]);
        }

        $allProviders = ProviderProfile::with('user')
            ->whereHas('user', fn ($query) => $query->where('role', 'provider'))
            ->addSelect([
                'latest_service_location' => Service::query()
                    ->select('location')
                    ->whereColumn('services.user_id', 'provider_profiles.user_id')
                    ->latest('id')
                    ->limit(1),
            ])
            ->get()
            ->map(function ($provider) use ($userLat, $userLng) {
                $provider->distance = $this->providerRecommendationService->distanceFromCustomer(
                    $userLat,
                    $userLng,
                    $provider
                );

                return $provider;
            });

        $allProviders = $allProviders->sortBy(fn ($provider) => $provider->distance ?? PHP_FLOAT_MAX)->values();

        $skills = $this->normalizeSkills($validated['skills'] ?? null);
        if (empty($skills)) {
            $skills = $this->normalizeSkills(optional($user->userPreference)->preferred_categories);
        }

        $isCustomer = $user->role === 'user';
        $recommendedProviders = collect();
        $otherProviders = $allProviders;

        if ($isCustomer) {
            $recommendedProviders = $this->providerRecommendationService->recommendForCustomer(
                $user,
                $allProviders,
                5,
                $skills
            );

            $recommendedIds = $recommendedProviders->pluck('id')->all();
            $otherProviders = $allProviders->reject(fn ($provider) => in_array($provider->id, $recommendedIds, true))->values();
        }

        $page = (int) $request->input('page', 1);
        $perPage = 12;
        $paginatedProviders = new LengthAwarePaginator(
            $otherProviders->forPage($page, $perPage)->values(),
            $otherProviders->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('providers.index', [
            'providers' => $paginatedProviders,
            'recommendedProviders' => $recommendedProviders,
            'totalProviders' => $allProviders->count(),
            'isCustomer' => $isCustomer,
        ]);
    }

    public function match(Request $request)
    {
        $validated = $request->validate([
            'user_lat' => 'required|numeric|between:-90,90',
            'user_lng' => 'required|numeric|between:-180,180',
            'service_type' => 'nullable|string|max:100',
            'required_skills' => 'nullable|array',
            'required_skills.*' => 'string|max:100',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
            'max_radius_km' => 'nullable|numeric|min:0.1',
            'min_rating' => 'nullable|numeric|min:0|max:5',
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        if (isset($validated['price_min'], $validated['price_max']) && $validated['price_min'] > $validated['price_max']) {
            return response()->json([
                'message' => 'price_min must be less than or equal to price_max',
            ], 422);
        }

        if (empty($validated['required_skills']) && !empty($validated['service_type'])) {
            $validated['required_skills'] = [$validated['service_type']];
        }

        $matches = $this->matchingEngine->match(array_merge($validated, [
            'user_id' => Auth::id(),
            'limit' => min((int) ($validated['limit'] ?? config('matching.top_results', 5)), 5),
        ]));

        Log::info('provider_controller.match', [
            'user_id' => Auth::id(),
            'count' => $matches->count(),
        ]);

        return response()->json([
            'data' => $matches,
            'count' => $matches->count(),
        ]);
    }

    public function recommend(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'user_lat' => 'nullable|numeric|between:-90,90',
            'user_lng' => 'nullable|numeric|between:-180,180',
            'service_type' => 'nullable|string|max:100',
            'required_skills' => 'nullable|array',
            'required_skills.*' => 'string|max:100',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
            'max_radius_km' => 'nullable|numeric|min:0.1',
            'min_rating' => 'nullable|numeric|min:0|max:5',
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        if (empty($validated['required_skills']) && !empty($validated['service_type'])) {
            $validated['required_skills'] = [$validated['service_type']];
        }

        $recommendations = $this->recommendationService->recommendForUser($user->id, $validated);

        Log::info('provider_controller.recommend', [
            'user_id' => $user->id,
            'count' => $recommendations->count(),
        ]);

        return response()->json([
            'data' => $recommendations,
            'count' => $recommendations->count(),
        ]);
    }

    public function preferences(Request $request)
    {
        $validated = $request->validate([
            'preferred_price_min' => 'nullable|numeric|min:0',
            'preferred_price_max' => 'nullable|numeric|min:0',
            'preferred_radius_km' => 'nullable|numeric|min:0.1|max:200',
            'preferred_categories' => 'nullable|array',
            'preferred_categories.*' => 'string|max:100',
            'preferred_categories_text' => 'nullable|string',
            'auto_match_enabled' => 'nullable|boolean',
        ]);

        if (isset($validated['preferred_price_min'], $validated['preferred_price_max']) &&
            $validated['preferred_price_min'] > $validated['preferred_price_max']) {
            return response()->json([
                'message' => 'preferred_price_min must be less than or equal to preferred_price_max',
            ], 422);
        }

        if (!empty($validated['preferred_categories_text']) && empty($validated['preferred_categories'])) {
            $validated['preferred_categories'] = collect(explode(',', $validated['preferred_categories_text']))
                ->map(fn ($item) => trim($item))
                ->filter()
                ->values()
                ->all();
        }

        unset($validated['preferred_categories_text']);

        $preferences = UserPreference::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        Log::info('provider_controller.preferences_updated', [
            'user_id' => $request->user()->id,
            'auto_match_enabled' => $preferences->auto_match_enabled,
        ]);

        return response()->json([
            'message' => 'Preferences updated successfully.',
            'data' => $preferences,
        ]);
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

        return collect($skills)
            ->map(fn ($value) => mb_strtolower(trim((string) $value)))
            ->filter()
            ->values()
            ->all();
    }
}

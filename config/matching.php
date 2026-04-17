<?php

return [
    'cache_ttl_seconds' => (int) env('MATCH_CACHE_TTL_SECONDS', 3600),
    'top_results' => (int) env('MATCH_TOP_RESULTS', 5),
    'candidate_limit' => (int) env('MATCH_CANDIDATE_LIMIT', 200),
    'default_radius_km' => (float) env('MATCH_DEFAULT_RADIUS_KM', 10),
    'weights' => [
        'location' => (float) env('MATCH_WEIGHT_LOCATION', 30),
        'rating' => (float) env('MATCH_WEIGHT_RATING', 25),
        'price' => (float) env('MATCH_WEIGHT_PRICE', 20),
        'skills' => (float) env('MATCH_WEIGHT_SKILLS', 15),
        'history' => (float) env('MATCH_WEIGHT_HISTORY', 10),
    ],
];

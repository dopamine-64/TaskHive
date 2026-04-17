<?php

namespace App\Jobs;

use App\Services\RecommendationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateRecommendationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $userId)
    {
    }

    public function handle(RecommendationService $recommendationService): void
    {
        $results = $recommendationService->recommendForUser($this->userId);

        Log::info('jobs.generate_recommendations.completed', [
            'user_id' => $this->userId,
            'count' => $results->count(),
        ]);
    }
}

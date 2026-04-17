<?php

namespace App\Console\Commands;

use App\Jobs\GenerateRecommendationsJob;
use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateRecommendations extends Command
{
    protected $signature = 'recommendations:generate {--user= : User ID} {--queue : Dispatch as queued jobs}';

    protected $description = 'Generate smart provider recommendations for users';

    public function handle(RecommendationService $recommendationService): int
    {
        $userId = $this->option('user');
        $queue = (bool) $this->option('queue');

        $query = User::query();

        if ($userId) {
            $query->where('id', $userId);
        } else {
            $query->whereExists(function ($sub) {
                $sub->selectRaw('1')
                    ->from('user_preferences as up')
                    ->whereColumn('up.user_id', 'users.id')
                    ->where('up.auto_match_enabled', true);
            });
        }

        $users = $query->get(['id']);

        if ($users->isEmpty()) {
            $this->warn('No users found for recommendation generation.');
            return self::SUCCESS;
        }

        DB::table('provider_recommendations')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->delete();

        foreach ($users as $user) {
            if ($queue) {
                GenerateRecommendationsJob::dispatch($user->id);
                $this->line("Queued recommendations for user {$user->id}");
                continue;
            }

            $recommendationService->recommendForUser($user->id);
            $this->line("Generated recommendations for user {$user->id}");
        }

        $this->info('Recommendation generation finished.');
        return self::SUCCESS;
    }
}

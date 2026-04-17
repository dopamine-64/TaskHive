<?php

namespace Tests\Unit;

use App\Services\LocationService;
use App\Services\ScoringService;
use PHPUnit\Framework\TestCase;

class MatchingServicesTest extends TestCase
{
    public function test_calculate_distance_returns_small_value_for_nearby_points(): void
    {
        $service = new LocationService();
        $distance = $service->calculateDistance(23.8103, 90.4125, 23.8150, 90.4200);

        $this->assertGreaterThan(0, $distance);
        $this->assertLessThan(2, $distance);
    }

    public function test_price_score_is_100_when_provider_inside_budget(): void
    {
        $scoring = new ScoringService(new LocationService());
        $score = $scoring->getPriceScore(700, 500, 1000);

        $this->assertSame(100.0, $score);
    }

    public function test_skills_match_score_calculates_percentage(): void
    {
        $scoring = new ScoringService(new LocationService());
        $score = $scoring->getSkillsMatchScore(['plumbing', 'cleaning'], ['plumbing', 'electrical']);

        $this->assertSame(50.0, $score);
    }
}

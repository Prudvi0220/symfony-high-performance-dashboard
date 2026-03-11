<?php

use App\Domain\Dashboard\Model\Dashboard;
use PHPUnit\Framework\TestCase;

class DashboardTest extends TestCase
{
    public function testDashboardCreation()
    {
        $dashboard = new Dashboard();
        $dashboard->setTitle('Test');
        $dashboard->setVisits(123);
        $dashboard->setRevenue(45.67);
        $createdAt = new \DateTimeImmutable('2026-03-11 12:00:00');
        $dashboard->setCreatedAt($createdAt);

        $this->assertEquals('Test', $dashboard->getTitle());
        $this->assertEquals(123, $dashboard->getVisits());
        $this->assertEquals(45.67, $dashboard->getRevenue());
        $this->assertSame($createdAt, $dashboard->getCreatedAt());
    }
}

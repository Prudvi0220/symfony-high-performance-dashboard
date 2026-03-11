<?php

namespace App\Tests\ReadModel;

use App\ReadModel\Dashboard\DashboardReadModel;
use PHPUnit\Framework\TestCase;

class DashboardReadModelTest extends TestCase
{
    public function testReadModelCreation(): void
    {
        $createdAt = new \DateTimeImmutable('2026-03-11 12:00:00');
        $model = new DashboardReadModel(1, 'Site 1', 100, 12.5, $createdAt);

        $this->assertSame(1, $model->getId());
        $this->assertSame('Site 1', $model->getTitle());
        $this->assertSame(100, $model->getVisits());
        $this->assertSame(12.5, $model->getRevenue());
        $this->assertSame($createdAt, $model->getCreatedAt());
    }
}

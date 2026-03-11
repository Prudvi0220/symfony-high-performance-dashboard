<?php

namespace App\Application\ReadModel;

interface DashboardReadModelRepositoryInterface
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getPaginated(int $page, int $perPage = 50): array;

    public function getTotalCount(): int;
}

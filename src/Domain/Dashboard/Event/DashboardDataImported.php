<?php

namespace App\Domain\Dashboard\Event;

final class DashboardDataImported
{
    private int $count;
    private \DateTimeImmutable $occurredAt;

    public function __construct(int $count, ?\DateTimeImmutable $occurredAt = null)
    {
        $this->count = $count;
        $this->occurredAt = $occurredAt ?? new \DateTimeImmutable();
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}

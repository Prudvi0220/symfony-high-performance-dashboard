<?php

namespace App\Application\EventHandler;

use App\Domain\Dashboard\Event\DashboardDataImported;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RebuildDashboardReadModelHandler
{
    private Connection $connection;
    private LoggerInterface $logger;

    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public function __invoke(DashboardDataImported $event): void
    {
        $start = microtime(true);

        $this->connection->executeStatement('TRUNCATE TABLE dashboard_read_model');
        $this->connection->executeStatement(
            'INSERT INTO dashboard_read_model (id, title, visits, revenue, created_at)
             SELECT id, title, visits, revenue, created_at FROM dashboard'
        );

        $durationMs = (microtime(true) - $start) * 1000;
        $this->logger->info('Rebuilt dashboard read model', [
            'count' => $event->getCount(),
            'duration_ms' => (int) $durationMs
        ]);
    }
}

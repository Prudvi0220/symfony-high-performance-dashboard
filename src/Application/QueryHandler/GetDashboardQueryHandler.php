<?php

namespace App\Application\QueryHandler;

use App\Application\Query\GetDashboardQuery;
use App\Application\ReadModel\DashboardReadModelRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class GetDashboardQueryHandler
{
    private DashboardReadModelRepositoryInterface $repository;
    private CacheInterface $cache;
    private LoggerInterface $logger;

    public function __construct(
        DashboardReadModelRepositoryInterface $repository,
        CacheInterface $cache,
        LoggerInterface $logger
    )
    {
        $this->repository = $repository;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function __invoke(GetDashboardQuery $query)
    {
        $page = $query->getPage();
        $perPage = $query->getPerPage();
        $cacheKey = 'dashboard.read.page.' . $page . '.' . $perPage;

        $start = microtime(true);
        $cacheMiss = false;
        $items = $this->cache->get($cacheKey, function (ItemInterface $item) use ($page, $perPage, &$cacheMiss) {
            $cacheMiss = true;
            $item->expiresAfter(60);
            return $this->repository->getPaginated($page, $perPage);
        });
        $total = $this->repository->getTotalCount();

        $durationMs = (microtime(true) - $start) * 1000;
        $this->logger->info('Dashboard read model query', [
            'page' => $page,
            'per_page' => $perPage,
            'duration_ms' => (int) $durationMs,
            'cache_hit' => !$cacheMiss
        ]);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage
        ];
    }
}

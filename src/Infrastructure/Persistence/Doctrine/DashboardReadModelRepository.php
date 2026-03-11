<?php

namespace App\Infrastructure\Persistence\Doctrine;

use App\Application\ReadModel\DashboardReadModelRepositoryInterface;
use App\ReadModel\Dashboard\DashboardReadModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DashboardReadModel>
 */
class DashboardReadModelRepository extends ServiceEntityRepository implements DashboardReadModelRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DashboardReadModel::class);
    }

    public function getPaginated(int $page, int $perPage = 50): array
    {
        return $this->createQueryBuilder('d')
            ->select('d.id, d.title, d.visits, d.revenue, d.createdAt')
            ->setMaxResults($perPage)
            ->setFirstResult(($page - 1) * $perPage)
            ->getQuery()
            ->getArrayResult();
    }

    public function getTotalCount(): int
    {
        return (int) $this->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}

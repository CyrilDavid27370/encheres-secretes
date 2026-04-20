<?php

namespace App\Repository;

use App\Entity\Items;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Items>
 */
class ItemsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Items::class);
    }

    public function findPublished(): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.status IN (:statuses)')
            ->setParameter('statuses', ['published', 'closed'])
            ->getQuery()
            ->getResult();
    }

    public function findByCategory(int $categoryId): array
    {
        return $this->createQueryBuilder('i')
            ->join('i.categories', 'c')
            ->where('c.id = :categoryId')
            ->andWhere('i.status IN (:statuses)')
            ->setParameter('categoryId', $categoryId)
            ->setParameter('statuses', ['published', 'closed'])
            ->getQuery()
            ->getResult();
    }

    public function findAllWithOfferCount(): array
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.offers', 'o')
            ->addSelect('COUNT(o.id) AS offerCount')
            ->groupBy('i.id')
            ->getQuery()
            ->getResult();
    }

    public function findBySearch(string $search) : array
    {
        return $this->createQueryBuilder('i')
            ->where('i.title LIKE :search')
            ->orWhere('i.description LIKE :search')
            ->andWhere('i.status IN (:statuses)')
            ->setParameter('search', '%' . $search . '%')
            ->setParameter('statuses', ['published', 'closed'])
            ->getQuery()
            ->getResult();
    }

    public function save(Items $item, bool $flush = true): void
    {
        $this->getEntityManager()->persist($item);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Items $item, bool $flush = true):void
    {
        $this->getEntityManager()->remove($item);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}

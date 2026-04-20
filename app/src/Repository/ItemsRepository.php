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
            ->leftJoin('i.offers', 'o')
            ->addSelect('COUNT(o.id) AS offerCount')
            ->where('i.status IN (:statuses)')
            ->setParameter('statuses', ['published', 'closed'])
            ->groupBy('i.id')
            ->getQuery()
            ->getResult();
    }

    public function findByCategory(int $categoryId): array
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.offers', 'o')
            ->addSelect('COUNT(o.id) AS offerCount')
            ->join('i.categories', 'c')
            ->where('c.id = :categoryId')
            ->andWhere('i.status IN (:statuses)')
            ->setParameter('categoryId', $categoryId)
            ->setParameter('statuses', ['published', 'closed'])
            ->groupBy('i.id')
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
            ->leftJoin('i.offers', 'o')
            ->addSelect('COUNT(o.id) AS offerCount')
            ->where('i.title LIKE :search')
            ->orWhere('i.description LIKE :search')
            ->andWhere('i.status IN (:statuses)')
            ->setParameter('search', '%' . $search . '%')
            ->setParameter('statuses', ['published', 'closed'])
            ->groupBy('i.id')
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

    public function findWonByUser($user): array
    {

    return $this->createQueryBuilder('i')
        ->select('i.id', 'i.title', 'i.finalPrice')
        ->where('i.winner = :user')
        ->andWhere('i.status = :status')
        ->setParameter('user', $user)
        ->setParameter('status', 'closed')
        ->getQuery()
        ->getArrayResult();
    }

    public function countByStatus(string $status): int
    {
        
    return (int) $this->createQueryBuilder('i')
        ->select('COUNT(i.id)')
        ->where('i.status = :status')
        ->setParameter('status', $status)
        ->getQuery()
        ->getSingleScalarResult();
    }

}

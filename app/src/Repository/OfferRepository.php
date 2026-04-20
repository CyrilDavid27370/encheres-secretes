<?php

namespace App\Repository;

use App\Entity\Offer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offer>
 */
class OfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offer::class);
    }

    public function findUserOfferForItem($user, $item): ?Offer
    {
        return $this->createQueryBuilder('o')
            ->where('o.user = :userId')
            ->andWhere('o.item = :itemId')
            ->setParameter('userId', $user)
            ->setParameter('itemId', $item)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByItem(int $itemId): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.item = :itemId')
            ->setParameter('itemId', $itemId)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findBestOferForItem(int $itemId): ?Offer
    {
        return $this->createQueryBuilder('o')
            ->where('o.item = :itemId')
            ->setParameter('itemId', $itemId)
            ->orderBy('o.amount', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByUser($user): array
    {
    return $this->createQueryBuilder('o')
        ->select('o.id', 'o.amount', 'i.id as itemId', 'i.title', 'i.status')
        ->join('o.item', 'i')
        ->where('o.user = :user')
        ->setParameter('user', $user)
        ->getQuery()
        ->getArrayResult();
    }

    public function countTotal(): int
    {

    return (int) $this->createQueryBuilder('o')
        ->select('COUNT(o.id)')
        ->getQuery()
        ->getSingleScalarResult();
    }

    public function sumTotal(): string
    {

    return (string) $this->createQueryBuilder('o')
        ->select('SUM(o.amount)')
        ->getQuery()
        ->getSingleScalarResult();
    }
}

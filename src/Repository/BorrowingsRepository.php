<?php

namespace App\Repository;

use App\Entity\Borrowing;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Borrowing>
 */
class BorrowingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Borrowing::class);
    }

    public function findUserBorrowings(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.bookCopy', 'bc')
            ->leftJoin('bc.book', 'book')
            ->leftJoin('book.authors', 'authors')
            ->where('b.borrowedBy = :user')
            ->setParameter('user', $user)
            ->orderBy('b.borrowDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findCurrentUserBorrowings(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.borrowedBy = :user')
            ->andWhere('b.returnDate IS NULL')
            ->setParameter('user', $user)
            ->orderBy('b.borrowDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

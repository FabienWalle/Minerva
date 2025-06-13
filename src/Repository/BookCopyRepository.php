<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\BookCopy;
use App\Entity\Enums\BookStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookCopy>
 */
class BookCopyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookCopy::class);
    }

    public function findFirstAvailableCopy(Book $book): ?BookCopy
    {
        return $this->createQueryBuilder('bc')
            ->andWhere('bc.book = :book')
            ->andWhere('bc.status = :status')
            ->setParameter('book', $book)
            ->setParameter('status', BookStatus::AVAILABLE)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

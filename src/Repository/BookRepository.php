<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function findThemesWithMinBooks(int $min = 10): array
    {
        $entityManager = $this->getEntityManager();

        $themeIds = $entityManager->createQueryBuilder()
            ->select('t.id')
            ->from(Theme::class, 't')
            ->join('t.books', 'b')
            ->groupBy('t.id')
            ->having('COUNT(b.id) >= :min')
            ->setParameter('min', $min)
            ->getQuery()
            ->getSingleColumnResult();

        if (empty($themeIds)) {
            return [];
        }

        return $entityManager->createQueryBuilder()
            ->select('t', 'b', 'a')
            ->from(Theme::class, 't')
            ->join('t.books', 'b')
            ->leftJoin('b.authors', 'a')
            ->where('t.id IN (:ids)')
            ->setParameter('ids', $themeIds)
            ->orderBy('t.name', 'ASC')
            ->addOrderBy('b.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

}

<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function findThemesWithMinBooks(int $minBooks = 10): array
    {
        $themeIds = $this->getThemeIdsWithMinBooks($minBooks);

        if (empty($themeIds)) {
            return [];
        }

        return $this->createThemesWithBooksAndAuthorsQuery($themeIds)
            ->getQuery()
            ->getResult();
    }

    private function getThemeIdsWithMinBooks(int $minBooks): array
    {
        return $this->createBaseQueryBuilder()
            ->select('t.id')
            ->from(Theme::class, 't')
            ->join('t.books', 'b')
            ->groupBy('t.id')
            ->having('COUNT(b.id) >= :min')
            ->setParameter('min', $minBooks)
            ->getQuery()
            ->getSingleColumnResult();
    }

    private function createThemesWithBooksAndAuthorsQuery(array $themeIds): QueryBuilder
    {
        return $this->createBaseQueryBuilder()
            ->select('t', 'b', 'a')
            ->from(Theme::class, 't')
            ->join('t.books', 'b')
            ->leftJoin('b.authors', 'a')
            ->where('t.id IN (:ids)')
            ->setParameter('ids', $themeIds)
            ->orderBy('t.name', 'ASC')
            ->addOrderBy('b.title', 'ASC');
    }

    private function createBaseQueryBuilder(): QueryBuilder
    {
        return $this->getEntityManager()->createQueryBuilder();
    }
}
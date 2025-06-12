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

    public function search(string $query): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.authors', 'a')
            ->leftJoin('b.themes', 't')
            ->where('LOWER(b.title) LIKE LOWER(:query)')
            ->orWhere('LOWER(a.name) LIKE LOWER(:query)')
            ->orWhere('LOWER(t.name) LIKE LOWER(:query)')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('b.title', 'ASC')
            ->getQuery()
            ->getResult();
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

    public function findAuthorsWithMinBooks(int $minBooks = 5): array
    {
        $authorIds = $this->getAuthorIdsWithMinBooks($minBooks);

        if (empty($authorIds)) {
            return [];
        }

        return $this->createAuthorsWithBooksQuery($authorIds)
            ->getQuery()
            ->getResult();
    }

    private function getAuthorIdsWithMinBooks(int $minBooks): array
    {
        return $this->createBaseQueryBuilder()
            ->select('a.id')
            ->from('App\Entity\Author', 'a')
            ->join('a.books', 'b')
            ->groupBy('a.id')
            ->having('COUNT(b.id) >= :min')
            ->setParameter('min', $minBooks)
            ->getQuery()
            ->getSingleColumnResult();
    }

    private function createAuthorsWithBooksQuery(array $authorIds): QueryBuilder
    {
        return $this->createBaseQueryBuilder()
            ->select('a', 'b', 'ba')
            ->from('App\Entity\Author', 'a')
            ->join('a.books', 'b')
            ->leftJoin('b.authors', 'ba')
            ->where('a.id IN (:ids)')
            ->setParameter('ids', $authorIds)
            ->orderBy('a.name', 'ASC')
            ->addOrderBy('b.title', 'ASC');
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
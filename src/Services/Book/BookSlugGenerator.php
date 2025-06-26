<?php

namespace App\Services\Book;

use App\Entity\Book;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class BookSlugGenerator
{
    public function __construct(
        private SluggerInterface $slugger
    ) {}

    public function generateBookSlug(Book $book): string
    {
        if (!$book->getId()) {
            throw new \RuntimeException('Cannot generate slug for non-persisted book');
        }

        $authorName = 'auteur-inconnu';
        if (!$book->getAuthors()->isEmpty()) {
            $firstAuthor = $book->getAuthors()->first();
            $authorName = mb_substr($firstAuthor->getName(), 0, 50);
            $authorName = $this->slugger->slug($authorName)->lower();
        }

        $title = mb_substr($book->getTitle(), 0, 100);
        $titleSlug = $this->slugger->slug($title)->lower();

        return $book->getId() . '-' . $authorName . '-' . $titleSlug;
    }
}
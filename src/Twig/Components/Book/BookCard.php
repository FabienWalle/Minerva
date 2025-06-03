<?php

namespace App\Twig\Components\Book;

use App\Entity\Book;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class BookCard
{
    public Book $book;
}

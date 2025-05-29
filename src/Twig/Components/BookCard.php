<?php

namespace App\Twig\Components;

use App\Entity\Book;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('book_card')]
final class BookCard
{
    public Book $book;
}

<?php

namespace App\Twig\Components;

use App\Entity\Book;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

use Doctrine\Common\Collections\Collection;

#[AsTwigComponent('book_slider')]
final class BookSlider
{
    public string $sliderId;
    public string $cardTitle;

    /** @var iterable<Book>|Collection<Book>|array */
    public iterable $books = [];
}

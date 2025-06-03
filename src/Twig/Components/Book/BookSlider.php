<?php

namespace App\Twig\Components\Book;

use App\Entity\Book;
use Doctrine\Common\Collections\Collection;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class BookSlider
{
    public string $sliderId;
    public string $cardTitle;

    /** @var iterable<Book>|Collection<Book>|array */
    public iterable $books = [];
}

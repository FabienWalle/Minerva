<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('book-slider')]
final class BookSlider
{
    public string $sliderId;
    public string $cardTitle;
    public array $books = [];
}
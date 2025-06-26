<?php

namespace App\Twig\Components\Borrowing;

use App\Entity\Borrowing;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Borrowing:BorrowingListItem')]
final class BorrowingListItem
{
    public Borrowing $borrowing;
}

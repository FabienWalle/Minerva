<?php

namespace App\Twig\Components\Borrowing;

use App\Entity\User;
use App\Repository\BorrowingsRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class BorrowingList
{
    public array $borrowings = [];

    public function __construct(
        private readonly BorrowingsRepository $borrowingsRepository,
        private readonly Security $security
    ) {}

    public function mount(): void
    {
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $this->borrowings = $this->borrowingsRepository->findUserBorrowings($user);
        }
    }
}
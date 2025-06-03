<?php

namespace App\Twig\Components;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('nav_bar')]
final readonly class Navbar
{
    public function __construct(private Security $security) {}

    public function isAuthenticated(): bool
    {
        return $this->security->isGranted('IS_AUTHENTICATED_FULLY');
    }

    public function getUserIdentifier(): ?string
    {
        return $this->security->getUser()?->getUserIdentifier();
    }
}
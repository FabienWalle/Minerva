<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
class Dropdown
{
    #[ExposeInTemplate]
    public ?string $buttonText = null;

    #[ExposeInTemplate]
    public ?string $buttonIcon = null;

    /** @var array<array{label: string, path: string, icon?: string}> */
    #[ExposeInTemplate]
    public array $items = [];

    #[ExposeInTemplate]
    public string $position = 'left';
}
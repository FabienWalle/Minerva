<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent('Dropdown')]
class Dropdown
{
    #[ExposeInTemplate]
    public ?string $buttonText = null;

    #[ExposeInTemplate]
    public ?string $buttonIcon = null;

    #[ExposeInTemplate]
    public ?string $buttonIconClass = null;

    #[ExposeInTemplate]
    public ?string $buttonIconSize = null;

    /** @var array<array{label: string, path: string, icon?: string, iconClass?: string, iconSize?: string}> */
    #[ExposeInTemplate]
    public array $items = [];

    #[ExposeInTemplate]
    public string $position = 'left';
}
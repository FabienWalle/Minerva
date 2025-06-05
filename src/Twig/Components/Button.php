<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Button')]
class Button
{
    public ?string $text = null;
    public ?string $icon = null;
    public ?string $iconClass = null;
    public ?string $iconSize = null;
}

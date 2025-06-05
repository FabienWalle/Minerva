<?php

namespace App\Twig\Components\UI;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Button
{
    public ?string $text = null;
    public ?string $icon = null;
    public ?string $iconClass = null;
    public ?string $iconSize = null;
}

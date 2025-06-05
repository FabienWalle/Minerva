<?php

namespace App\Twig\Components\UI;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Icon
{
    public string $name;
    public ?string $class = null;
    public ?string $width = null;
    public ?string $height = null;
}

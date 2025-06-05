<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Icon')]
class Icon
{
    public string $name;
    public ?string $class = null;
    public ?string $width = null;
    public ?string $height = null;
}

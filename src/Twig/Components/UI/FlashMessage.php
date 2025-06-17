<?php

namespace App\Twig\Components\UI;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class FlashMessage
{
    public string $type = 'info';
    public string $message;

    public function getColorClasses(): array
    {
        return match ($this->type) {
            'success' => [
                'bg' => 'bg-green-100',
                'border' => 'border-green-400',
                'text' => 'text-green-700',
                'icon_color' => 'text-green-500 hover:text-green-700'
            ],
            'error' => [
                'bg' => 'bg-red-100',
                'border' => 'border-red-400',
                'text' => 'text-red-700',
                'icon_color' => 'text-red-500 hover:text-red-700'
            ],
            'warning' => [
                'bg' => 'bg-yellow-100',
                'border' => 'border-yellow-400',
                'text' => 'text-yellow-700',
                'icon_color' => 'text-yellow-500 hover:text-yellow-700'
            ],
            default => [
                'bg' => 'bg-blue-100',
                'border' => 'border-blue-400',
                'text' => 'text-blue-700',
                'icon_color' => 'text-blue-500 hover:text-blue-700'
            ],
        };
    }

    public function getIconName(): string
    {
        return match ($this->type) {
            'success' => 'material-symbols:check-circle-outline',
            'error' => 'nonicons:error-16',
            'warning' => 'zondicons:exclamation-outline',
            default => 'material-symbols:info-outline'
        };
    }
}
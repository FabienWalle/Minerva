<?php

namespace App\Twig\Components\UI;

use App\Repository\BookRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\LiveAction;

#[AsLiveComponent]
class Search
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    public function __construct(private readonly BookRepository $bookRepository)
    {
    }

    public function getBooks(): array
    {
        if (empty($this->query)) {
            return [];
        }

        return $this->bookRepository->search($this->query);
    }

    #[LiveAction]
    public function clear(): void
    {
        $this->query = '';
    }
}
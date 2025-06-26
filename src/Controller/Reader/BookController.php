<?php

namespace App\Controller\Reader;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/book', name: 'app_book_')]
final class BookController extends AbstractController
{
    #[Route('/{slug}', name: 'show')]
    public function index(BookRepository $bookRepository, string $slug): Response
    {
        $book = $bookRepository->findOneBy(['slug' => $slug]);

        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé.');
        }

        return $this->render('book/index.html.twig', [
            'book' => $book,
        ]);
    }
}

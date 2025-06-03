<?php

namespace App\Controller\Reader;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookController extends AbstractController
{
    #[Route('/book/{id}', name: 'app_book')]
    public function index(BookRepository $bookRepository, Int $id): Response
    {
        $book = $bookRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé.');
        }

        return $this->render('book/index.html.twig', [
            'book' => $book,
        ]);
    }
}

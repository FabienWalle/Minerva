<?php

namespace App\Controller\Reader;

use App\Entity\Borrowing;
use App\Entity\Enums\BookStatus;
use App\Repository\BookCopyRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/loans', name: 'app_loans_')]
final class LoansController extends AbstractController
{
    #[Route('/{id}', name: 'borrow', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(BookRepository $bookRepository, BookCopyRepository $bookCopyRepository, int $id, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            $this->redirectToRoute('app_login');
        }

        $book = $bookRepository->find($id);

        if (!$book->isAvailable()) {
            $this->addFlash('error', 'Aucune copie disponible pour ce livre.');
            return $this->redirectToRoute('app_book_show', ['id' => $id]);
        }

        $bookCopy = $bookCopyRepository->findFirstAvailableCopy($book);
        $bookCopy->setStatus(BookStatus::BORROWED);

        $borrowing = new Borrowing();
        $borrowing->setBorrowedBy($this->getUser());
        $borrowing->setBookCopy($bookCopy);

        $entityManager->persist($borrowing);
        $entityManager->flush();

        $this->addFlash('success', 'Livre '.$book->getTitle().' emprunté avec succès !');
        return $this->redirectToRoute('app_home');
    }
}

<?php

namespace App\Controller\Reader;

use App\Entity\Borrowing;
use App\Entity\Enums\BookStatus;
use App\Entity\User;
use App\Message\SendBorrowingConfirmationEmail;
use App\Repository\BookCopyRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/loans', name: 'app_loans_')]
final class LoansController extends AbstractController
{
    /**
     * @throws ExceptionInterface
     */
    #[Route('/{id}', name: 'borrow', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function borrowBook(
        BookRepository $bookRepository,
        BookCopyRepository $bookCopyRepository,
        int $id,
        EntityManagerInterface $entityManager,
        MessageBusInterface $messageBus
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $book = $bookRepository->find($id);
        if (!$book || !$book->isAvailable()) {
            $this->addFlash('error', 'Aucune copie disponible pour ce livre.');
            return $this->redirectToRoute('app_book_show', ['id' => $id]);
        }

        $bookCopy = $bookCopyRepository->findFirstAvailableCopy($book);
        $bookCopy->setStatus(BookStatus::BORROWED);

        $borrowing = new Borrowing();
        $borrowing->setBorrowedBy($user);
        $borrowing->setBookCopy($bookCopy);

        $entityManager->persist($borrowing);
        $entityManager->flush();

        $messageBus->dispatch(new SendBorrowingConfirmationEmail(
            userEmail: $user->getEmail(),
            bookTitle: $book->getTitle(),
            borrowingDate: \DateTimeImmutable::createFromMutable($borrowing->getBorrowDate()),
            dueDate: \DateTimeImmutable::createFromMutable($borrowing->getDueDate())
        ));

        $this->addFlash('success', sprintf('Livre "%s" emprunté avec succès !', $book->getTitle()));
        return $this->redirectToRoute('app_home');
    }
}

<?php

namespace App\Controller\Reader;

use App\Entity\User;
use App\Repository\BorrowingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/borrowings', name: 'app_borrowings_')]
final class BorrowingsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(BorrowingsRepository $repository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $borrowings = $repository->findUserBorrowings($user);

        return $this->render('borrowings/index.html.twig', [
            'borrowings' => $borrowings
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function show(int $id, BorrowingsRepository $repository): Response
    {
        $borrowing = $repository->findOneBy(['id' => $id, 'borrowedBy' => $this->getUser()]);

        if (!$borrowing) {
            throw $this->createNotFoundException('Emprunt non trouvé');
        }

        return $this->render('borrowings/show.html.twig', [
            'borrowing' => $borrowing,
        ]);
    }
}

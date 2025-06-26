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
        return $this->render('borrowings/index.html.twig');
    }
}

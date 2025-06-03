<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(BookRepository $bookRepository, Request $request): Response
    {
        $sliderType = $request->query->get('sliders', 'themes');

        $data = match ($sliderType) {
            'authors' => ['authors' => $bookRepository->findAuthorsWithMinBooks(5)],
            default => ['themes' => $bookRepository->findThemesWithMinBooks(10)],
        };

        return $this->render('home.html.twig', $data);
    }
}
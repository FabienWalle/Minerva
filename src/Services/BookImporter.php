<?php

namespace App\Services;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\BookCopy;
use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class BookImporter
{
    private const API_BASE_URL = 'https://www.googleapis.com/books/v1/volumes';
    private const MAX_RESULTS = 10;
    private const REQUEST_DELAY = 1;

    private array $themes = [
        'science-fiction',
        'jeunesse',
        'poésie',
        'enquête',
        'thriller',
        'fantasy',
        'amour',
        'théâtre',
        'cuisine',
        'voyage',
        'biographie',
        'fantastique'
    ];

    private array $authors = [
        'Jules Verne',
        'Denis Diderot',
        'Albert Camus',
        'Simone de Beauvoir',
        'François Rabelais',
        'George Sand',
        'Stephen King',
        'Chrétien de Troyes',
        'Toni Morrison',
        'Fiodor Dostoïevski',
    ];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly string $googleBooksApiKey
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function importByThemes(OutputInterface $output): void
    {
        foreach ($this->themes as $themeName) {
            $output->writeln(sprintf('Importing books for theme: %s', $themeName));

            $theme = $this->getOrCreateTheme($themeName);
            $booksData = $this->fetchBooksData('subject', $themeName);

            $this->processBooksData($booksData, $output, $theme);

            sleep(self::REQUEST_DELAY);
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function importByAuthors(OutputInterface $output): void
    {
        foreach ($this->authors as $authorName) {
            $output->writeln(sprintf('Importing books for author: %s', $authorName));

            $booksData = $this->fetchBooksData('inauthor', $authorName);

            $this->processBooksData($booksData, $output, null, $authorName);

            sleep(self::REQUEST_DELAY);
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function fetchBooksData(string $searchType, string $searchTerm): array
    {
        $client = HttpClient::create();
        $url = sprintf(
            '%s?q=%s:"%s"&maxResults=%d&printType=books&orderBy=relevance&langRestrict=fr&key=%s',
            self::API_BASE_URL,
            $searchType,
            urlencode($searchTerm),
            self::MAX_RESULTS,
            $this->googleBooksApiKey
        );

        $response = $client->request('GET', $url);
        return $response->toArray();
    }

    /**
     * @throws Exception
     */
    private function processBooksData(array $data, OutputInterface $output, ?Theme $theme = null, ?string $authorName = null): void
    {
        if (!isset($data['items'])) {
            $output->writeln(sprintf('No books found for %s', $theme ? 'theme: '.$theme->getName() : 'author: '.$authorName));
            return;
        }

        foreach ($data['items'] as $item) {
            $this->processBookItem($item, $output, $theme, $authorName);
        }
    }

    /**
     * @throws Exception
     */
    private function processBookItem(array $item, OutputInterface $output, ?Theme $theme, ?string $searchAuthor): void
    {
        $volumeInfo = $item['volumeInfo'] ?? [];
        if (empty($volumeInfo)) {
            return;
        }

        $bookTitle = $volumeInfo['title'] ?? 'Unknown Title';
        $book = $this->entityManager->getRepository(Book::class)->findOneBy(['title' => $bookTitle]);

        if ($book) {
            $this->handleExistingBook($book, $theme, $output);
            return;
        }

        $this->createNewBook($volumeInfo, $bookTitle, $theme, $searchAuthor, $output);
    }

    /**
     * @throws Exception
     */
    private function createNewBook(array $volumeInfo, string $bookTitle, ?Theme $theme, ?string $searchAuthor, OutputInterface $output): void
    {
        $book = new Book();
        $book->setTitle($bookTitle);
        $book->setYear(substr($volumeInfo['publishedDate'] ?? '', 0, 4));
        $book->setDescription($volumeInfo['description'] ?? null);

        if (isset($volumeInfo['imageLinks']['thumbnail'])) {
            $book->setCover($volumeInfo['imageLinks']['thumbnail']);
        }

        $author = $this->getOrCreateAuthor($searchAuthor ?? ($volumeInfo['authors'][0] ?? null));
        if ($author) {
            $book->addAuthor($author);
        }

        if ($theme) {
            $book->addTheme($theme);
        }

        $this->entityManager->persist($book);
        $this->createBookCopies($book);

        $output->writeln(sprintf('Imported: %s (%s)', $bookTitle, $theme ? $theme->getName() : $searchAuthor));
    }

    private function handleExistingBook(Book $book, ?Theme $theme, OutputInterface $output): void
    {
        if ($theme && !$book->getTheme()->contains($theme)) {
            $book->addTheme($theme);
            $this->entityManager->flush();
            $output->writeln(sprintf('Added theme %s to existing book: %s', $theme->getName(), $book->getTitle()));
        }
    }

    private function getOrCreateTheme(string $themeName): Theme
    {
        $theme = $this->entityManager->getRepository(Theme::class)->findOneBy(['name' => $themeName]);

        if (!$theme) {
            $theme = new Theme();
            $theme->setName($themeName);
            $this->entityManager->persist($theme);
            $this->entityManager->flush();
        }

        return $theme;
    }

    private function getOrCreateAuthor(?string $authorName): ?Author
    {
        if (!$authorName) {
            return null;
        }

        $author = $this->entityManager->getRepository(Author::class)->findOneBy(['name' => $authorName]);

        if (!$author) {
            $author = new Author();
            $author->setName($authorName);
            $this->entityManager->persist($author);
        }

        return $author;
    }

    /**
     * @throws Exception
     */
    private function createBookCopies(Book $book): void
    {
        $copyCount = random_int(1, 3);
        for ($i = 0; $i < $copyCount; $i++) {
            $bookCopy = new BookCopy();
            $bookCopy->setBook($book);
            $this->entityManager->persist($bookCopy);
        }

        $this->entityManager->flush();
    }
}
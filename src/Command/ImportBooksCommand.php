<?php

namespace App\Command;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\BookCopy;
use App\Entity\Enums\BookStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsCommand(
    name: 'app:import-books',
    description: 'Import books from Google Books API'
)]
class ImportBooksCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private string $googleBooksApiKey;

    public function __construct(
        EntityManagerInterface $entityManager,
        #[\SensitiveParameter] string $googleBooksApiKey
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->googleBooksApiKey = $googleBooksApiKey;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $themes = [
            'science-fiction',
            'jeunesse',
            'poésie',
            'enquête',
            'thriller',
            'amour',
            'théâtre',
            'cuisine',
            'voyage',
            'biographie',
            'fantastique'
        ];

        $client = HttpClient::create();

        foreach ($themes as $theme) {
            $output->writeln(sprintf('Importing books for theme: %s', $theme));

            try {
                $response = $client->request(
                    'GET',
                    sprintf(
                        'https://www.googleapis.com/books/v1/volumes?q=subject:%s&maxResults=10&printType=books&orderBy=relevance&langRestrict=fr&key=%s',
                        urlencode($theme),
                        $this->googleBooksApiKey
                    )
                );

                $data = $response->toArray();

                if (!isset($data['items'])) {
                    $output->writeln(sprintf('No books found for theme: %s', $theme));
                    continue;
                }

                foreach ($data['items'] as $item) {
                    $volumeInfo = $item['volumeInfo'] ?? null;
                    if (!$volumeInfo) {
                        continue;
                    }

                    $authors = $volumeInfo['authors'] ?? [];
                    $authorEntities = [];

                    foreach ($authors as $authorName) {
                        $author = $this->entityManager->getRepository(Author::class)->findOneBy(['name' => $authorName]);

                        if (!$author) {
                            $author = new Author();
                            $author->setName($authorName);
                            $this->entityManager->persist($author);
                            $this->entityManager->flush();
                        }

                        $authorEntities[] = $author;
                    }

                    $bookTitle = $volumeInfo['title'] ?? 'Unknown Title';
                    $book = $this->entityManager->getRepository(Book::class)->findOneBy(['title' => $bookTitle]);

                    if (!$book) {
                        $book = new Book();
                        $book->setTitle($bookTitle);
                        $book->setYear(substr($volumeInfo['publishedDate'] ?? '0000', 0, 4));
                        $book->setDescription($volumeInfo['description'] ?? null);
                        $book->setCover($volumeInfo['imageLinks']['thumbnail'] ?? null);

                        foreach ($authorEntities as $author) {
                            $book->addAuthor($author);
                        }

                        $this->entityManager->persist($book);
                        $this->entityManager->flush();

                        $copyCount = random_int(1, 3);
                        for ($i = 0; $i < $copyCount; $i++) {
                            $bookCopy = new BookCopy();
                            $bookCopy->setBook($book);
                            $this->entityManager->persist($bookCopy);
                        }

                        $this->entityManager->flush();
                    }

                    $output->writeln(sprintf('Processed book: %s', $bookTitle));
                }

                sleep(1);

            } catch (TransportExceptionInterface|ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
                $output->writeln(sprintf('Error fetching books for theme %s: %s', $theme, $e->getMessage()));
                continue;
            } catch (\Exception $e) {
            }
        }

        $output->writeln('Book import completed!');
        return Command::SUCCESS;
    }
}
<?php

namespace App\Command;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\BookCopy;
use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsCommand(
    name: 'app:import-books',
    description: 'Import books from Google Books API by themes or authors'
)]
class ImportBooksCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private string $googleBooksApiKey;

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
        EntityManagerInterface $entityManager,
        #[\SensitiveParameter] string $googleBooksApiKey
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->googleBooksApiKey = $googleBooksApiKey;
    }

    protected function configure(): void
    {
        $this
            ->addOption('themes', null, InputOption::VALUE_NONE, 'Import by themes')
            ->addOption('authors', null, InputOption::VALUE_NONE, 'Import by authors');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = HttpClient::create();

        if ($input->getOption('themes')) {
            $this->importByThemes($client, $output);
        } elseif ($input->getOption('authors')) {
            $this->importByAuthors($client, $output);
        } else {
            $output->writeln('Please specify either --themes or --authors option');
            return Command::FAILURE;
        }

        $output->writeln('Book import completed!');
        return Command::SUCCESS;
    }

    private function importByThemes($client, OutputInterface $output): void
    {
        foreach ($this->themes as $themeName) {
            $output->writeln(sprintf('Importing books for theme: %s', $themeName));

            $theme = $this->entityManager->getRepository(Theme::class)->findOneBy(['name' => $themeName]);
            if (!$theme) {
                $theme = new Theme();
                $theme->setName($themeName);
                $this->entityManager->persist($theme);
                $this->entityManager->flush();
            }

            try {
                $response = $client->request(
                    'GET',
                    sprintf(
                        'https://www.googleapis.com/books/v1/volumes?q=subject:%s&maxResults=10&printType=books&orderBy=relevance&langRestrict=fr&key=%s',
                        urlencode($themeName),
                        $this->googleBooksApiKey
                    )
                );

                $this->processResponse($response, $theme, $output);

            } catch (Exception $e) {
                $output->writeln(sprintf('Error for theme %s: %s', $themeName, $e->getMessage()));
            }

            sleep(1);
        }
    }

    private function importByAuthors($client, OutputInterface $output): void
    {
        foreach ($this->authors as $authorName) {
            $output->writeln(sprintf('Importing books for author: %s', $authorName));

            try {
                $response = $client->request(
                    'GET',
                    sprintf(
                        'https://www.googleapis.com/books/v1/volumes?q=inauthor:"%s"&maxResults=10&printType=books&orderBy=relevance&langRestrict=fr&key=%s',
                        urlencode($authorName),
                        $this->googleBooksApiKey
                    )
                );

                $this->processResponse($response, null, $output, $authorName);

            } catch (Exception $e) {
                $output->writeln(sprintf('Error for author %s: %s', $authorName, $e->getMessage()));
            }

            sleep(1);
        }
    }

    /**
     * @throws Exception
     */
    private function processResponse($response, ?Theme $theme, OutputInterface $output, ?string $authorName = null): void
    {
        $data = $response->toArray();

        if (!isset($data['items'])) {
            $output->writeln(sprintf('No books found for %s', $theme ? 'theme: '.$theme->getName() : 'author: '.$authorName));
            return;
        }

        foreach ($data['items'] as $item) {
            $this->processBookItem($item, $theme, $output, $authorName);
        }
    }

    /**
     * @throws Exception
     */
    private function processBookItem(array $item, ?Theme $theme, OutputInterface $output, ?string $searchAuthor = null): void
    {
        $volumeInfo = $item['volumeInfo'] ?? [];
        if (empty($volumeInfo)) {
            return;
        }

        $authorName = $searchAuthor ?? ($volumeInfo['authors'][0] ?? null);
        $author = null;

        if ($authorName) {
            $author = $this->entityManager->getRepository(Author::class)->findOneBy(['name' => $authorName]);
            if (!$author) {
                $author = new Author();
                $author->setName($authorName);
                $this->entityManager->persist($author);
            }
        }

        $bookTitle = $volumeInfo['title'] ?? 'Unknown Title';
        $book = $this->entityManager->getRepository(Book::class)->findOneBy(['title' => $bookTitle]);

        if (!$book) {
            $book = new Book();
            $book->setTitle($bookTitle);

            $publishedDate = $volumeInfo['publishedDate'] ?? '';
            $book->setYear(substr($publishedDate, 0, 4));

            $book->setDescription($volumeInfo['description'] ?? null);

            if (isset($volumeInfo['imageLinks']['thumbnail'])) {
                $book->setCover($volumeInfo['imageLinks']['thumbnail']);
            }

            if ($author) {
                $book->addAuthor($author);
            }

            if ($theme) {
                $book->addTheme($theme);
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
            $output->writeln(sprintf('Imported: %s (%s)', $bookTitle, $theme ? $theme->getName() : $authorName));
        } else {
            if ($theme && !$book->getTheme()->contains($theme)) {
                $book->addTheme($theme);
                $this->entityManager->flush();
                $output->writeln(sprintf('Added theme %s to existing book: %s', $theme->getName(), $bookTitle));
            }
        }
    }
}
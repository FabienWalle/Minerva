<?php

namespace App\Command;

use App\Repository\BookRepository;
use App\Services\Book\BookSlugGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:update-book-slugs')]
class UpdateBookSlugsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private BookSlugGenerator $slugGenerator,
        private BookRepository $bookRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $batchSize = 20;
        $count = 0;
        $total = $this->bookRepository->count([]);

        $output->writeln(sprintf('Updating slugs for %d books...', $total));

        $query = $this->em->createQuery('SELECT b FROM App\Entity\Book b');
        $iterableResult = $query->toIterable();

        foreach ($iterableResult as $book) {
            try {
                $slug = $this->slugGenerator->generateBookSlug($book);
                $book->setSlug($slug);

                if (($count % $batchSize) === 0) {
                    $this->em->flush();
                    $this->em->clear();
                    $output->writeln(sprintf('Processed %d/%d books', $count, $total));
                }
                $count++;
            } catch (\Exception $e) {
                $output->writeln(sprintf(
                    'Error processing book %d: %s',
                    $book->getId(),
                    $e->getMessage()
                ));
            }
        }

        $this->em->flush();
        $output->writeln(sprintf('Successfully updated %d/%d books', $count, $total));

        return Command::SUCCESS;
    }
}
<?php

namespace App\Command;

use App\Services\BookImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsCommand(
    name: 'app:import-books',
    description: 'Import books from Google Books API by themes or authors'
)]
class ImportBooksCommand extends Command
{
    public function __construct(
        private readonly BookImporter $bookImporter
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('themes', null, InputOption::VALUE_NONE, 'Import by themes')
            ->addOption('authors', null, InputOption::VALUE_NONE, 'Import by authors');
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('themes')) {
            $this->bookImporter->importByThemes($output);
        } elseif ($input->getOption('authors')) {
            $this->bookImporter->importByAuthors($output);
        } else {
            $output->writeln('Please specify either --themes or --authors option');
            return Command::FAILURE;
        }

        $output->writeln('Book import completed!');
        return Command::SUCCESS;
    }
}
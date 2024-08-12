<?php

namespace App\Command;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:list-books',
    description: 'Add a short description for your command',
)]
class ListBooksCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $bookRepository = $this->entityManager->getRepository(Book::class);

        $books = $bookRepository->findAll();

        if (empty($books)) {
            $io->error('No books found.');
            return Command::SUCCESS;
        }

        $io->title('List of available books');
        $io->table(
            ['ID', 'ISBN', 'Title', 'Author'],
            array_map(fn(Book $book) => [
                $book->getId(),
                $book->getIsbn(),
                $book->getTitle(),
                $book->getAuthor()
            ], $books)
        );

        return Command::SUCCESS;
    }
}

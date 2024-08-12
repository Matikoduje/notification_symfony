<?php

namespace App\Command;

use App\Entity\Book;
use App\Validator\Isbn;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:update-book',
    description: 'Add a short description for your command',
)]
class UpdateBookCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'The ID of the book to update')
            ->addOption('isbn', null, InputOption::VALUE_OPTIONAL, 'The new ISBN of the book')
            ->addOption('title', null, InputOption::VALUE_OPTIONAL, 'The new title of the book')
            ->addOption('author', null, InputOption::VALUE_OPTIONAL, 'The new author of the book');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $id = $input->getOption('id');
        $isbn = $input->getOption('isbn');
        $title = $input->getOption('title');
        $author = $input->getOption('author');

        $book = $this->entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            $io->error(sprintf('No book found for ID %d', $id));
            return Command::FAILURE;
        }

        if ($isbn !== null) {
            $errors = $this->validator->validate($isbn, new Isbn());

            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $io->error($error->getMessage());
                }
                return Command::FAILURE;
            }

            $existingBook = $this->entityManager->getRepository(Book::class)->findOneBy(['isbn' => $isbn]);

            if ($existingBook && $existingBook->getId() !== $book->getId()) {
                $io->error('A book with this ISBN already exists. Cannot update.');
                return Command::FAILURE;
            }

            $book->setIsbn($isbn);
        }

        if ($title !== null) {
            $book->setTitle($title);
        }

        if ($author !== null) {
            $book->setAuthor($author);
        }

        $this->entityManager->flush();

        $io->success('The book has been updated successfully!');

        return Command::SUCCESS;
    }
}

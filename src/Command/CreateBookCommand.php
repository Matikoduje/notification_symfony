<?php

namespace App\Command;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Validator\Isbn;

#[AsCommand(
    name: 'app:create-book',
    description: 'Creates a new book',
)]
class CreateBookCommand extends Command
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
            ->addArgument('isbn', InputArgument::REQUIRED, 'The ISBN of the book')
            ->addArgument('title', InputArgument::REQUIRED, 'The title of the book')
            ->addArgument('author', InputArgument::REQUIRED, 'The author of the book');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $isbn = $input->getArgument('isbn');
        $title = $input->getArgument('title');
        $author = $input->getArgument('author');

        $errors = $this->validator->validate($isbn, new Isbn());

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $io->error($error->getMessage());
            }
            return Command::FAILURE;
        }

        $book = new Book();
        $book->setIsbn($isbn);
        $book->setTitle($title);
        $book->setAuthor($author);

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $io->success('The book has been created successfully!');

        return Command::SUCCESS;
    }
}

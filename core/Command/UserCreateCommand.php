<?php

namespace App\Core\Command;

use App\Core\Factory\UserFactory;
use App\Core\Manager\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserCreateCommand extends Command
{
    protected static $defaultName = 'murph:user:create';
    protected static $defaultDescription = 'Creates a user';
    protected UserFactory $userFactory;
    protected EntityManager $entityManager;

    public function __construct(UserFactory $userFactory, EntityManager $entityManager)
    {
        $this->userFactory = $userFactory;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('email', InputArgument::OPTIONAL, 'E-mail')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        $emailQuestion = new Question('E-mail: ');
        $emailQuestion->setValidator(function ($value) {
            if (empty($value)) {
                throw new \RuntimeException(
                    'The email must not be empty.'
                );
            }

            return $value;
        });

        $passwordQuestion = new Question('Password (leave empty to generate a random password): ');
        $passwordQuestion->setHidden(true);
        $isAdminQuestion = new ConfirmationQuestion('Is admin? [y/n] ', false);
        $isWriterQuestion = new ConfirmationQuestion('Is writer? [y/n] ', false);

        $email = $input->getArgument('email');

        if (empty($email)) {
            $email = $helper->ask($input, $output, $emailQuestion);
        }

        $password = $helper->ask($input, $output, $passwordQuestion);
        $isAdmin = $helper->ask($input, $output, $isAdminQuestion);
        $isWriter = $helper->ask($input, $output, $isWriterQuestion);

        $user = $this->userFactory->create($email, $password);
        $user->setIsAdmin($isAdmin);
        $user->setIsWriter($isWriter);

        $this->entityManager->create($user);

        $io->success('User created!');

        return Command::SUCCESS;
    }
}

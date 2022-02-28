<?php

namespace App\Core\Command;

use App\Core\Factory\UserFactory;
use App\Core\Manager\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserCreateCommand extends Command
{
    protected static $defaultName = 'murph:user:create';
    protected static $defaultDescription = 'Creates a user';
    protected UserFactory $userFactory;
    protected EntityManager $entityManager;
    protected TokenGeneratorInterface $tokenGenerator;

    public function __construct(
        UserFactory $userFactory,
        EntityManager $entityManager,
        TokenGeneratorInterface $tokenGenerator
    ) {
        $this->userFactory = $userFactory;
        $this->entityManager = $entityManager;
        $this->tokenGenerator = $tokenGenerator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('email', InputArgument::OPTIONAL, 'E-mail')
            ->addOption('is-admin', null, InputOption::VALUE_NONE, 'Add the admin role')
            ->addOption('is-writer', null, InputOption::VALUE_NONE, 'Add the write role')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        $emailQuestion = new Question('E-mail: ');
        $emailQuestion->setValidator(function ($value) {
            if (empty($value)) {
                throw new \RuntimeException('The email must not be empty.');
            }

            return $value;
        });

        $passwordQuestion = new Question('Password (leave empty to generate a random password): ');
        $passwordQuestion->setHidden(true);

        $isAdminDefault = $input->getOption('is-admin');
        $isWriterDefault = $input->getOption('is-writer');

        $isAdminQuestionLabel = sprintf('Administrator [%s] ', $isAdminDefault ? 'Y/n' : 'y/N');
        $isWriterQuestionLabel = sprintf('Writer [%s] ', $isWriterDefault ? 'Y/n' : 'y/N');

        $isAdminQuestion = new ConfirmationQuestion($isAdminQuestionLabel, $isAdminDefault);
        $isWriterQuestion = new ConfirmationQuestion($isWriterQuestionLabel, $isWriterDefault);

        $io->section('Authentication');

        $email = $input->getArgument('email');

        if (empty($email)) {
            $email = $helper->ask($input, $output, $emailQuestion);
        }

        $password = $helper->ask($input, $output, $passwordQuestion);

        $showPassword = empty($password);

        if ($showPassword) {
            $password = mb_substr($this->tokenGenerator->generateToken(), 0, 18);
            $io->info(sprintf('Password: %s', $password));
        } else {
            $io->newLine();
        }

        $io->section('Roles');

        $isAdmin = $helper->ask($input, $output, $isAdminQuestion);
        $isWriter = $helper->ask($input, $output, $isWriterQuestion);

        $user = $this->userFactory->create($email, $password);
        $user->setIsAdmin($isAdmin);
        $user->setIsWriter($isWriter);

        $this->entityManager->create($user);

        $io->newLine();
        $io->success('User created!');

        return Command::SUCCESS;
    }
}

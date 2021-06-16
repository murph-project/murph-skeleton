<?php

namespace App\Core\Maker;

use Doctrine\Common\Annotations\Annotation;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use function Symfony\Component\String\u;

class MakeRepositoryQuery extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:repository-query';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a repository query';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConf)
    {
        $command
            ->addArgument(
                'repository-class',
                InputArgument::OPTIONAL,
                'Define the repository (e.g. <fg=yellow>MyEntityRepository</>)'
            )
            ->setHelp('')
        ;
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $repositoryClass = $input->getArgument('repository-class');

        $repositoryDetails = $generator->createClassNameDetails(
            $repositoryClass,
            'Repository\\',
            ''
        );

        $queryDetails = $generator->createClassNameDetails(
            $repositoryClass.'Query',
            'Repository\\',
            ''
        );

        $id = u($queryDetails->getShortName())
            ->truncate(1)
            ->lower()
        ;

        $options = [
            'repository' => $repositoryDetails->getFullName(),
            'id' => $id,
        ];

        $factoryPath = $generator->generateController(
            $queryDetails->getFullName(),
            __DIR__.'/../Resources/maker/repository/RepositoryQuery.tpl.php',
            $options
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
        $io->text('Next: Open your new repository query class and configure it!');
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(
            Annotation::class,
            'doctrine/annotations'
        );
    }
}

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

class MakeCrudController extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:crud-controller';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new CRUD controller class';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConf)
    {
        $command
            ->addArgument(
                'controller-class',
                InputArgument::OPTIONAL,
                'Choose a name for your CRUD controller class (e.g. <fg=yellow>FooAdminController</>)'
            )
            ->addArgument(
                'entity-class',
                InputArgument::OPTIONAL,
                'Define the entity (e.g. <fg=yellow>App\Entity\Foo</>)'
            )
            ->addArgument(
                'repository-query-class',
                InputArgument::OPTIONAL,
                'Define the repository query  (e.g. <fg=yellow>App\Repository\FooRepositoryQuery</>)'
            )
            ->addArgument(
                'factory-class',
                InputArgument::OPTIONAL,
                'Define the factory (e.g. <fg=yellow>App\Factory\FooFactory</>)'
            )
            ->addArgument(
                'form-class',
                InputArgument::OPTIONAL,
                'Define the form  (e.g. <fg=yellow>App\Form\FooType</>)'
            )
            ->setHelp('')
        ;
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $controllerClassNameDetails = $generator->createClassNameDetails(
            $input->getArgument('controller-class'),
            'Controller\\',
            'Controller'
        );

        $entity = u($input->getArgument('entity-class'));
        $lastBackSlashIndex = $entity->indexOfLast('\\');
        $route = u($entity->slice($lastBackSlashIndex))->snake();

        $options = [
            'entity' => (string) $entity,
            'route' => (string) $route,
            'repository_query' => $input->getArgument('repository-query-class'),
            'form' => $input->getArgument('form-class'),
            'factory' => $input->getArgument('factory-class'),
        ];

        $controllerPath = $generator->generateController(
            $controllerClassNameDetails->getFullName(),
            __DIR__.'/../Resources/maker/crud-controller/CrudController.tpl.php',
            $options
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
        $io->text('Next: Open your new controller class and configure it!');
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(
            Annotation::class,
            'doctrine/annotations'
        );
    }
}

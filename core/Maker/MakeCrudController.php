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
                'Choose a name for your CRUD controller class (e.g. <fg=yellow>MyEntityAdminController</>)'
            )
            ->addArgument(
                'entity-class',
                InputArgument::OPTIONAL,
                'Define the entity (e.g. <fg=yellow>MyEntity</>)'
            )
            ->addArgument(
                'repository-query-class',
                InputArgument::OPTIONAL,
                'Define the repository query (e.g. <fg=yellow>MyEntityRepositoryQuery</>)'
            )
            ->addArgument(
                'factory-class',
                InputArgument::OPTIONAL,
                'Define the factory (e.g. <fg=yellow>MyEntityFactory</>)'
            )
            ->addArgument(
                'form-class',
                InputArgument::OPTIONAL,
                'Define the form  (e.g. <fg=yellow>MyEntityType</>)'
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

        $entityDetails = $generator->createClassNameDetails(
            $input->getArgument('entity-class'),
            'Entity\\',
            ''
        );

        $repoDetails = $generator->createClassNameDetails(
            $input->getArgument('repository-query-class'),
            'Repository\\',
            ''
        );

        $formDetails = $generator->createClassNameDetails(
            $input->getArgument('form-class'),
            'Form\\',
            ''
        );

        $factoryDetails = $generator->createClassNameDetails(
            $input->getArgument('factory-class'),
            'Factory\\',
            ''
        );

        $options = [
            'entity' => $entityDetails->getFullName(),
            'route' => (string) u($entityDetails->getShortName())->snake(),
            'repository_query' => $repoDetails->getFullName(),
            'form' => $formDetails->getFullName(),
            'factory' => $factoryDetails->getFullName(),
        ];

        $controllerPath = $generator->generateController(
            $controllerClassNameDetails->getFullName(),
            __DIR__.'/../Resources/maker/controller/CrudController.tpl.php',
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

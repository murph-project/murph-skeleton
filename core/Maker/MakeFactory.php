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

class MakeFactory extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:factory';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a factory';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConf)
    {
        $command
            ->addArgument(
                'factory-class',
                InputArgument::OPTIONAL,
                'Choose a name for your factory (e.g. <fg=yellow>MyEntityFactory</>)'
            )
            ->addArgument(
                'entity-class',
                InputArgument::OPTIONAL,
                'Define the entity (e.g. <fg=yellow>MyEntity</>)'
            )
            ->setHelp('')
        ;
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $entityDetails = $generator->createClassNameDetails(
            $input->getArgument('entity-class'),
            'Entity\\',
            ''
        );

        $factoryDetails = $generator->createClassNameDetails(
            $input->getArgument('factory-class'),
            'Factory\\',
            ''
        );

        $options = [
            'entity' => $entityDetails->getFullName(),
        ];

        $factoryPath = $generator->generateController(
            $factoryDetails->getFullName(),
            __DIR__.'/../Resources/maker/factory/Factory.tpl.php',
            $options
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
        $io->text('Next: Open your new factory class and configure it!');
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(
            Annotation::class,
            'doctrine/annotations'
        );
    }
}

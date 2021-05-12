<?php

namespace App\Core\Twig\Extension;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CrudExtension extends AbstractExtension
{
    protected PropertyAccessor $propertyAccessor;
    protected Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->getPropertyAccessor()
        ;

        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('render_field', [$this, 'renderField'], ['is_safe' => ['html']]),
        ];
    }

    public function renderField($entity, array $config): string
    {
        $field = $config['field'];
        $instance = new $field;
        $resolver = $instance->configureOptions(new OptionsResolver());

        return $instance->buildView($this->twig, $entity, $resolver->resolve($config['options']));
    }
}

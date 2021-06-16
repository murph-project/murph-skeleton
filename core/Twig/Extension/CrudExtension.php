<?php

namespace App\Core\Twig\Extension;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

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

    public function renderField($entity, array $config, ?string $locale = null): string
    {
        $field = $config['field'];
        $instance = new $field();
        $resolver = $instance->configureOptions(new OptionsResolver());
        $flags = ENT_HTML5 | ENT_QUOTES;

        $render = $instance->buildView($this->twig, $entity, $resolver->resolve($config['options']), $locale);

        if (isset($config['options']['href'])) {
            $hrefAttrConfig = $config['options']['href_attr'] ?? [];
            $hrefConfig = $config['options']['href'] ?? null;
            $attributes = '';

            if (is_callable($hrefAttrConfig)) {
                $attrs = (array) call_user_func($hrefAttrConfig, $entity, $config['options']);
            } else {
                $attrs = $hrefAttrConfig;
            }

            if (is_callable($hrefConfig)) {
                $attrs['href'] = call_user_func($hrefConfig, $entity, $config['options']);
            } else {
                $attrs['href'] = $hrefConfig;
            }

            foreach ($attrs as $k => $v) {
                $attributes .= sprintf(' %s="%s" ', htmlspecialchars($k, $flags), htmlspecialchars($v, $flags));
            }

            $render = sprintf('<a%s>%s</a>', $attributes, $render);
        }

        return $render;
    }
}

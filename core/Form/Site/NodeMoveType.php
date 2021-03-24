<?php

namespace App\Core\Form\Site;

use App\Core\Entity\Site\Node;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class NodeMoveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'position',
            ChoiceType::class,
            [
                'label' => 'Position',
                'required' => true,
                'choices' => [
                    'After' => 'after',
                    'Before' => 'before',
                    'Above' => 'above',
                ],
                'attr' => [
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'node',
            EntityType::class,
            [
                'label' => 'Element',
                'class' => Node::class,
                'choices' => call_user_func(function () use ($options) {
                    return $options['menu']->getRootNode()->getAllChildren();
                }),
                'choice_label' => 'treeLabel',
                'attr' => [
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'menu' => null,
        ]);
    }
}

<?php

namespace App\Core\Form\Site\Page\Filter;

use App\Core\Entity\Site\Page\Page;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Core\Entity\Site\Navigation;
use Doctrine\ORM\EntityRepository;

class PageFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'Name',
                'required' => false,
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'navigation',
            EntityType::class,
            [
                'label' => 'Naviation',
                'class' => Navigation::class,
                'choice_label' => 'label',
                'choice_value' => 'id',
                'required' => false,
                'attr' => [
                ],
                'query_builder' => function (EntityRepository $repo) {
                    return $repo->createQueryBuilder('n')
                        ->orderBy('n.label, n.domain', 'ASC')
                    ;
                },
                'constraints' => [
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => false,
        ]);
    }
}

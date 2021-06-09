<?php

namespace App\Entity\Page;

use App\Core\Entity\Site\Page\Block;
use App\Core\Entity\Site\Page\FileBlock;
use App\Core\Entity\Site\Page\Page;
use App\Core\Form\Site\Page\ImageBlockType;
use App\Core\Form\Site\Page\TextareaBlockType;
use App\Core\Form\Site\Page\TextBlockType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @ORM\Entity
 */
class SimplePage extends Page
{
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder->add(
            'title',
            TextBlockType::class,
            [
                'label' => 'Titre'
                'row_attr' => [
                ],
                'options' => [
                    'required' => true,
                    'attr' => [
                    ],
                    'constraints' => [
                    ],
                ],
            ]
        );

        $builder->add(
            'content',
            TextareaBlockType::class,
            [
                'label' => 'Content',
                'row_attr' => [
                ],
                'options' => [
                    'attr' => [
                        'data-tinymce' => '',
                        'rows' => '18',
                    ],
                    'constraints' => [
                    ],
                ],
            ]
        );

        $builder->add(
            'image',
            ImageBlockType::class,
            [
                'label' => 'Image',
                'row_attr' => [
                ],
                'options' => [
                    'attr' => [
                    ],
                    'constraints' => [
                    ],
                ],
            ]
        );
    }

    public function setTitle(Block $block)
    {
        return $this->setBlock($block);
    }

    public function getTitle()
    {
        return $this->getBlock('title');
    }

    public function setContent(Block $block)
    {
        return $this->setBlock($block);
    }

    public function getContent()
    {
        return $this->getBlock('content');
    }

    public function setImage(Block $block)
    {
        return $this->setBlock($block);
    }

    public function getImage()
    {
        return $this->getBlock('image', FileBlock::class);
    }
}

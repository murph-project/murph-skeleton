<?php

namespace App\Core\EventSubscriber\Site\Page;

use App\Core\Entity\EntityInterface;
use App\Core\Entity\Site\Page\Page;
use App\Core\Event\EntityManager\EntityManagerEvent;
use App\Core\EventSubscriber\EntityManagerEventSubscriber;
use App\Core\Form\FileUploadHandler;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * class PageEventSubscriber.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class PageEventSubscriber extends EntityManagerEventSubscriber
{
    protected FileUploadHandler $fileUpload;

    public function __construct(FileUploadHandler $fileUpload)
    {
        $this->fileUpload = $fileUpload;
    }

    public function support(EntityInterface $entity)
    {
        return $entity instanceof Page;
    }

    public function onPreUpdate(EntityManagerEvent $event)
    {
        if (!$this->support($event->getEntity())) {
            return;
        }

        $page = $event->getEntity();

        if ($page->getOgImage() instanceof UploadedFile) {
            $directory = 'uploads/page/ogImage';

            $this->fileUpload->handleForm(
                $page->getOgImage(),
                $directory,
                function ($filename) use ($page, $directory) {
                    $page->setOgImage($directory.'/'.$filename);
                }
            );
        }
    }

    public function onPreCreate(EntityManagerEvent $event)
    {
        return $this->onPreUpdate($event);
    }
}

<?php

namespace WJB\UploaderBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use WJB\UploaderBundle\Model\FileInterface;
use WJB\UploaderBundle\Service\FileUploader;

class FileUpdateEventSubscriber implements EventSubscriber
{
    /** @var FileUploader */
    private $fileUploader;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'postRemove',
        ];
    }

    public function prePersist(LifecycleEventArgs $event): void
    {
        $file = $event->getObject();
        if (!$file instanceof FileInterface) {
            return;
        }

        $this->fileUploader->moveToPermanentPosition($file);
    }

    public function postRemove(LifecycleEventArgs $event): void
    {
        $file = $event->getObject();
        if (!$file instanceof FileInterface) {
            return;
        }
        $this->fileUploader->removeFile($file);
    }
}

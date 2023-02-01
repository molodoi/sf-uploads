<?php

namespace App\Listener;

use Vich\UploaderBundle\Event\Event;
use Doctrine\ORM\EntityManagerInterface;
use Vich\UploaderBundle\Handler\UploadHandler;

class RemovedImageListener
{
    public function __construct(private EntityManagerInterface $em,
    private UploadHandler $uploadHandler)
    {}

    public function onVichUploaderPostRemove(Event $event)
    {
        /** App\Entity\Image $image */
        $image = $event->getObject();
        // $mapping = $event->getMapping();
        // $this->em->remove($image);
        // $this->em->flush();
        // $this->uploadHandler->remove($image, 'imageFile');
        // dd($image,$mapping,$post);
        // do your stuff with $object and/or $mapping...
    }

}
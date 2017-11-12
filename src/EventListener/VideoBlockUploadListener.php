<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\CmsPlugin\EventListener;

use BitBag\CmsPlugin\Entity\BlockInterface;
use BitBag\CmsPlugin\Entity\BlockTranslationInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
//@TODO video uploader
final class VideoBlockUploadListener
{
    /**
     * @var ImageUploaderInterface
     */
    private $uploader;

    /**
     * @param ImageUploaderInterface $uploader
     */
    public function __construct(ImageUploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function uploadVideo(ResourceControllerEvent $event): void
    {
        $block = $event->getSubject();

        if (false === $block instanceof BlockInterface) {
            return;
        }

        if (BlockInterface::VIDEO_BLOCK_TYPE !== $block->getType()) {
            return;
        }

        /** @var BlockTranslationInterface $translation */
        foreach ($block->getTranslations() as $translation) {
            $video = $translation->getVideo();

            if (null !== $video && true === $video->hasFile()) {
                $this->uploader->upload($video);
            }
        }
    }
}

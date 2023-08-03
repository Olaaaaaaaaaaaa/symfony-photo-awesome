<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use App\Repository\MediaRepository;

class MediaExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private MediaRepository $mediaRepository
    ) {
    }

    public function getLastFiveMedia()
    {
        $medias = $this->mediaRepository->findLastFive();
        return $medias;
    }
}

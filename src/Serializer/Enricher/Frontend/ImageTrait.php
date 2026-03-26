<?php

declare(strict_types=1);

namespace Revierstil\ContaoFeed\Serializer\Enricher\Frontend;

use Contao\CoreBundle\Filesystem\VirtualFilesystem;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\Validator;
use Symfony\Component\Uid\Uuid;

trait ImageTrait {

    protected VirtualFilesystem $filesystem;

    protected Studio $studio;

    protected function buildFigure(string|null $uuid, int|null $imageSize = null): array|null
    {
        if (Validator::isUuid($uuid) === false) {
            return null;
        }

        $item = $this->filesystem->get(Uuid::fromBinary($uuid));

        if ($item === null) {
            return null;
        }

        $figure = $this->studio
            ->createFigureBuilder()
            ->fromStorage($this->filesystem, $item->getUuid())
            ->setSize($imageSize)
            ->enableLightbox()
            ->buildIfResourceExists();

        return $figure?->getLegacyTemplateData();
    }
}
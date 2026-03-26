<?php

declare(strict_types=1);

namespace Revierstil\ContaoFeed\Serializer\Enricher\Frontend;

use BoelterIO\EnrichingSerializer\DependencyInjection\Attribute\AsEnricher;
use BoelterIO\EnrichingSerializer\Serializer\Enricher;
use BoelterIO\EnrichingSerializer\Serializer\Serializer;
use Carbon\Carbon;
use Contao\CoreBundle\Filesystem\VirtualFilesystem;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Image\Studio\Studio;
use Revierstil\ContaoFeed\Model\FeedModel;
use Revierstil\ContaoFeed\Serializer\Enricher\Base\AbstractFeedEnricher;

/** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
#[AsEnricher]
final class FeedEnricher extends AbstractFeedEnricher implements Enricher
{
    use ImageTrait;

    public function __construct(
        Serializer $serializer,
        protected Studio $studio,
        protected VirtualFilesystem $filesystem,
        ContaoFramework $framework
    ) {
        parent::__construct($serializer, $framework);
    }

    public function supports(mixed $data, array $normalized, string $format, array $context = []): bool
    {
        return $data instanceof FeedModel && in_array('frontend', $context['groups'], strict: true);
    }

    public function enrich(mixed $data, array $normalized, string $format, array $context = []): array
    {
        $normalized = parent::enrich($data, $normalized, $format, $context);

        if (array_key_exists('dateCreated', $normalized)) {
            $normalized['dateCreated'] = $data->dateCreated === 0 ? null : Carbon::createFromTimestamp($data->dateCreated,
                $this->config->get('timeZone'))->toRfc822String();
        }

        if (array_key_exists('image', $normalized)) {
            $this->enrichImage($data, $normalized, $context);
        }

        return $normalized;
    }

    private function enrichImage(FeedModel $data, array &$normalized, array $context): void
    {
        $normalized['image'] = $this->buildFigure($data->image, $context['imageSizes']['imageSize'] ?? null);
    }
}
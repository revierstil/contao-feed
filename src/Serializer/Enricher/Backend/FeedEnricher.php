<?php

declare(strict_types=1);

namespace Revierstil\ContaoFeed\Serializer\Enricher\Backend;

use BoelterIO\EnrichingSerializer\DependencyInjection\Attribute\AsEnricher;
use BoelterIO\EnrichingSerializer\Serializer\Enricher;
use Carbon\Carbon;
use Revierstil\ContaoFeed\Model\FeedModel;
use Revierstil\ContaoFeed\Serializer\Enricher\Base\AbstractFeedEnricher;

/** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
#[AsEnricher]
final class FeedEnricher extends AbstractFeedEnricher implements Enricher
{
    public function supports(mixed $data, array $normalized, string $format, array $context = []): bool
    {
        return $data instanceof FeedModel && in_array('backend', $context['groups'], strict: true);
    }

    public function enrich(mixed $data, array $normalized, string $format, array $context = []): array
    {
        $normalized = parent::enrich($data, $normalized, $format, $context);

        if (array_key_exists('dateCreated', $normalized)) {
            $normalized['dateCreated'] = $data->dateCreated === 0 ? null : Carbon::createFromTimestamp($data->dateCreated,
                $this->config->get('timeZone'));
        }

        return $normalized;
    }
}
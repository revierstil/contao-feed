<?php

declare(strict_types=1);

namespace Revierstil\ContaoFeed\Serializer\Enricher\Base;

use BoelterIO\EnrichingSerializer\DependencyInjection\Attribute\AsEnricher;
use BoelterIO\EnrichingSerializer\Serializer\Enricher;
use BoelterIO\EnrichingSerializer\Serializer\Serializer;
use Contao\Config;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Revierstil\ContaoFeed\Model\FeedModel;

/** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
abstract class AbstractFeedEnricher implements Enricher
{
    protected readonly Adapter|Config $config;

    public function __construct(protected readonly Serializer $serializer, ContaoFramework $framework)
    {
        $this->config = $framework->getAdapter(Config::class);
    }

    public function enrich(mixed $data, array $normalized, string $format, array $context = []): array
    {
        if (array_key_exists('author', $normalized)) {
            $this->enrichRelation($data, $normalized, $context, 'author');
        }

        if (array_key_exists('location', $normalized)) {
            $this->enrichRelation($data, $normalized, $context, 'location');
        }

        return $normalized;
    }

    private function enrichRelation(FeedModel $data, array &$normalized, array $context, string $relation): void
    {
        $related               = $data->getRelated($relation);
        $normalized[$relation] = $related !== null
            ? $this->serializer->serialize($related, 'array', $context)
            : [];
    }
}
<?php

declare(strict_types=1);

namespace Revierstil\ContaoFeed\Serializer\Enricher\Frontend;

use BoelterIO\EnrichingSerializer\DependencyInjection\Attribute\AsEnricher;
use BoelterIO\EnrichingSerializer\Serializer\Enricher;
use Contao\CoreBundle\Filesystem\VirtualFilesystem;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\MemberModel;

/** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
#[AsEnricher]
final class AuthorEnricher implements Enricher
{
    use ImageTrait;

    public function __construct(
        protected Studio $studio,
        protected VirtualFilesystem $filesystem,
    ) {
    }

    public function supports(mixed $data, array $normalized, string $format, array $context = []): bool
    {
        return $data instanceof MemberModel && in_array('frontend', $context['groups'], strict: true);
    }

    public function enrich(mixed $data, array $normalized, string $format, array $context = []): array
    {
        if (array_key_exists('avatar', $normalized)) {
            $this->enrichAvatar($data, $normalized, $context);
        }

        return $normalized;
    }

    private function enrichAvatar(MemberModel $data, array &$normalized, array $context): void
    {
        $normalized['avatar'] = $this->buildFigure($data->avatar, $context['imageSizes']['avatarSize'] ?? null);
    }
}
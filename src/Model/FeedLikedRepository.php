<?php

declare(strict_types=1);

namespace Revierstil\ContaoFeed\Model;

use Netzmacht\Contao\Toolkit\Data\Model\ContaoRepository;

class FeedLikedRepository extends ContaoRepository
{
    public function __construct()
    {
        parent::__construct(FeedLikedModel::class);
    }

    public function findLikeByFeedIdAndMemberId(int $feedId, int $memberId): ?FeedLikedModel
    {
        return $this->findOneBy(['pid=?', 'member=?'], [$feedId, $memberId]);
    }
}

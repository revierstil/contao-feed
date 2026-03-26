<?php

declare(strict_types=1);

namespace Revierstil\ContaoFeed\Model;

use BoelterIO\DoctrineDBALQueryFilter\Query\QueryBuilderFactory;
use Contao\Database\Result;
use Contao\Model\Collection;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Toolkit\Data\Model\ContaoRepository;

class FeedRepository extends ContaoRepository
{
    public function __construct(
        private Connection $connection,
        private readonly QueryBuilderFactory $queryBuilderFactory
    ) {
        parent::__construct(FeedModel::class);
    }

    public function countByFilter(array $filters = []): int
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('count(trf.id) as rowCount')
            ->from(FeedModel::getTable(), 'trf')
            ->where($queryBuilder->expr()->eq('trf.published', true));

        $wrappedQueryBuilder = $this->queryBuilderFactory->wrapQueryBuilder($queryBuilder);
        $queryBuilder        = $wrappedQueryBuilder->getFilteredQueryBuilder($filters);

        return $queryBuilder->executeQuery()->fetchOne() ?? 0;
    }

    public function findByFilter(array $filters = [], int $limit = 0, int $offset = 0): Collection|null
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('trf.*')
            ->from(FeedModel::getTable(), 'trf')
            ->where($queryBuilder->expr()->eq('trf.published', true));

        $wrappedQueryBuilder = $this->queryBuilderFactory->wrapQueryBuilder($queryBuilder);
        $queryBuilder        = $wrappedQueryBuilder->getFilteredQueryBuilder($filters);

        if ($limit > 0) {
            $queryBuilder->setMaxResults($limit)
                ->setFirstResult($offset);
        }

        return Collection::createFromDbResult(new Result($queryBuilder->executeQuery(), ''), FeedModel::getTable());
    }

    public function updateLikesForFeed(int $feedId): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->update(FeedModel::getTable(), 'trf')
            ->set('likes', 'likes + 1')
            ->where($queryBuilder->expr()->eq('id', $feedId))
            ->executeQuery();
    }
}

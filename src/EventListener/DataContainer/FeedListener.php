<?php

declare(strict_types=1);

namespace Revierstil\ContaoFeed\EventListener\DataContainer;

use BoelterIO\EnrichingSerializer\Serializer\Serializer;
use BoelterIO\Options\Model\OptionRepository;
use Contao\CoreBundle\DataContainer\DcaUrlAnalyzer;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Filesystem\VirtualFilesystem;
use Contao\MemberModel;
use Netzmacht\Contao\Toolkit\Data\Model\Repository;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;
use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;
use Revierstil\ContaoFeed\Model\FeedModel;
use Revierstil\ContaoFeed\Model\FeedRepository;
use Symfony\Component\Filesystem\Path;
use Twig\Environment;

final class FeedListener
{
    private readonly Repository $members;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        private readonly DcaManager $dcaManager,
        private readonly FeedRepository $feeds,
        private readonly OptionRepository $options,
        private readonly VirtualFilesystem $filesystem,
        private readonly DcaUrlAnalyzer $dcaUrlAnalyzer,
        private readonly Environment $twig,
        private readonly Serializer $serializer,
        private readonly AssetsManager $assetsManager,
        private readonly string $uploadPath,
        RepositoryManager $repositoryManager
    ) {
        $this->members = $repositoryManager->getRepository(MemberModel::class);
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    #[AsCallback(table: 'tl_rs_feed', target: 'config.oncreate')]
    public function onCreate(string $table, int $insertId): void
    {
        $feed = $this->feeds->find($insertId);
        $feed->refresh();

        $feed->dateCreated = time();
        $feed->save();
    }

    #[AsCallback(table: 'tl_rs_feed', target: 'config.ondelete')]
    public function onDelete(int $source): void
    {
        if ($this->filesystem->directoryExists(Path::join($this->uploadPath, 'feed-' . $source)) === true) {
            return;
        }

        $this->filesystem->createDirectory(Path::join($this->uploadPath, 'feed-' . $source));
    }

    #[AsCallback(table: 'tl_rs_feed', target: 'config.onload')]
    public function styleLoad(): void
    {
       $this->assetsManager->addStylesheet('bundles/revierstilcontaofeed/backend/feed.css');
    }

    /** @SuppressWarnings(PHPMD.UnusedLocalVariable) */
    #[AsCallback(table: 'tl_rs_feed', target: 'config.onload')]
    public function imagePathLoad(): void
    {
        [$table, $recordId] = $this->dcaUrlAnalyzer->getCurrentTableId();

        if ($recordId === null) {
            return;
        }

        if ($this->filesystem->directoryExists(Path::join($this->uploadPath, 'feed-' . $recordId)) === false) {
            $this->filesystem->createDirectory(Path::join($this->uploadPath, 'feed-' . $recordId));
        }

        $definition = $this->dcaManager->getDefinition(FeedModel::getTable());

        $definition->set(
            ['fields', 'image', 'eval', 'path'],
            Path::join(
                $this->filesystem->getPrefix(),
                $this->uploadPath,
                'feed-' . $recordId)
        );
    }

    #[AsCallback(table: 'tl_rs_feed', target: 'list.label.label')]
    public function onLabel(array $row): string
    {
        $feed = $this->feeds->find((int) $row['id']);

        if ($feed === null) {
            return '';
        }

        $data = $this->serializer->serialize($feed, 'array', ['groups' => ['backend']]);

        return $this->twig->render('@Contao_RevierstilContaoFeedBundle/backend/feed/item.html.twig', $data);
    }

    #[AsCallback(table: 'tl_rs_feed', target: 'fields.location.options')]
    public function locationOptions(): array
    {
        $definition = $this->dcaManager->getDefinition(FeedModel::getTable());

        $group = $definition->get(['fields', 'location', 'eval', 'option_group']);

        if (empty($group)) {
            return [];
        }

        $options = $this->options->findActiveGroupOptions($group);

        return OptionsBuilder::fromCollection($options, 'title')->getOptions();
    }

    #[AsCallback(table: 'tl_rs_feed', target: 'fields.author.options')]
    public function authorOptions(): array
    {
        $members = $this->members->findAll(['sorting' => 'username ASC']);

        return OptionsBuilder::fromCollection($members, function ($row) {
            return sprintf('%s %s (%s)', $row['firstname'], $row['lastname'], $row['username']);
        })->getOptions();
    }

    #[AsCallback(table: 'tl_rs_feed', target: 'fields.dateCreated.load')]
    public function dateCreatedLoad(mixed $value): mixed
    {
        if ((int) $value === 0) {
            return '';
        }

        return $value;
    }
}
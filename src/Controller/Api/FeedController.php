<?php

declare(strict_types=1);

namespace Revierstil\ContaoFeed\Controller\Api;

use BoelterIO\EnrichingSerializer\Serializer\Serializer;
use BoelterIO\Options\Model\GroupRepository;
use BoelterIO\Options\Model\OptionModel;
use BoelterIO\Options\Model\OptionRepository;
use Contao\Config;
use Contao\CoreBundle\Filesystem\FilesystemItem;
use Contao\CoreBundle\Filesystem\VirtualFilesystem;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\FrontendUser;
use Contao\Model\Collection as ContaoCollection;
use Pagerfanta\Adapter\FixedAdapter;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Pagerfanta;
use Revierstil\ContaoFeed\Model\FeedModel;
use Revierstil\ContaoFeed\Model\FeedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsController]
final class FeedController extends AbstractController
{
    private readonly ValidatorInterface $validator;

    private readonly Adapter|Config $config;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        private readonly FeedRepository $feeds,
        private readonly GroupRepository $groups,
        private readonly OptionRepository $options,
        private readonly Serializer $serializer,
        private readonly TranslatorInterface $translator,
        private readonly VirtualFilesystem $filesystem,
        private readonly string $kernelProjectDir,
        private readonly array $sorting,
        private readonly array $filter,
        private readonly array $imageSizes,
        private readonly string $uploadPath,
        ContaoFramework $framework
    ) {
        $framework->initialize();

        $this->validator = (new ValidatorBuilder())
            ->setTranslator($this->translator)
            ->setTranslationDomain('validators')
            ->getValidator();

        $this->config = $framework->getAdapter(Config::class);
    }

    #[Route(
        path: '/contao-feed/list',
        name: 'revierstil_contao_feed_api_list',
        defaults: ['_scope' => 'frontend'],
        methods: ['GET'],
    )]
    public function listing(Request $request): JsonResponse
    {
        $filters         = $request->query->all('filter') ?? [];
        $validFilterKeys = array_merge(array_column($this->filter, 'optionGroup'), ['sorting', 'author']);

        if (array_diff(array_keys($filters), $validFilterKeys) !== []) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $filters = $this->adjustFilters($filters);

        $defaultSorting = array_filter($this->sorting, fn(array $sorting) => $sorting['default'] === true);

        if ($defaultSorting !== [] && ! array_key_exists('sorting', $filters)) {
            $filters['sorting'] = array_values($defaultSorting)[0]['value'];
        }

        $pager = new Pagerfanta(new FixedAdapter($this->feeds->countByFilter($filters), []));
        $pager->setMaxPerPage(10);

        try {
            $pager->setCurrentPage($request->query->getInt('page', 1));
        } catch (OutOfRangeCurrentPageException $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $offset = max(0, $pager->getCurrentPageOffsetStart() - 1);
        $data   = $this->feeds->findByFilter($filters, $pager->getMaxPerPage(), $offset);
        $data   = $this->serializer->serialize($data, 'array',
            ['groups' => ['frontend'], 'imageSizes' => $this->imageSizes]);

        return new JsonResponse([
            'items'      => $data,
            'filters'    => $this->prepareFiltersFromConfig(),
            'pagination' => [
                'currentPage'     => $pager->getCurrentPage(),
                'hasNextPage'     => $pager->hasNextPage(),
                'hasPreviousPage' => $pager->hasPreviousPage(),
                'pages'           => $pager->getNbPages(),
                'total'           => $pager->getNbResults(),
                'perPage'         => $pager->getMaxPerPage(),
            ],
        ]);
    }

    #[Route(
        path: '/contao-feed/manage',
        name: 'revierstil_contao_feed_api_manage',
        defaults: ['_scope' => 'frontend'],
        methods: ['POST']
    )]
    public function manage(Request $request): Response
    {

        $data = [
            'message'  => $request->request->get('message'),
            'location' => $request->request->get('location'),
            'image'    => $request->files->get('image'),
        ];

        $violations = $this->validator->validate($data, new Collection($this->getEditConstraints()));

        if (count($violations) > 0) {
            return new JsonResponse(
                [
                    'message' => [
                        'fields' => array_map(fn(array $message) => $message[0], $this->prepareViolations($violations)),
                    ],
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user = $this->getUser();

        $feed = $this->feeds->find($request->request->getInt('feedId'));

        if ($feed === null && $request->request->getInt('feedId')) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        if ($feed !== null && $feed->author !== $this->getUser()?->id) {
            return new Response(null, Response::HTTP_FORBIDDEN);
        }

        if ($feed === null) {
            $feed              = new FeedModel();
            $feed->dateCreated = time();
        }

        $feed->tstamp    = time();
        $feed->author    = $user instanceof FrontendUser ? $user->id : 2;
        $feed->location  = $data['location'];
        $feed->message   = $data['message'];
        $feed->published = true;
        $feed->save();

        if ($data['image'] instanceof UploadedFile) {
            $image = $data['image'];
            $path  = Path::join($this->uploadPath, 'feed-' . (string) $feed->id);
            if ($this->filesystem->directoryExists($path) === false) {
                $this->filesystem->createDirectory($path);
            }

            $file = $image->move(
                Path::join(
                    $this->kernelProjectDir,
                    $this->filesystem->getPrefix(),
                    $path
                ),
                $image->getClientOriginalName()
            );

            $item = $this->filesystem->get(Path::join($path, $file->getFilename()), VirtualFilesystem::FORCE_SYNC);

            if (! ($item instanceof FilesystemItem)) {
                return new Response(null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $feed->image = $item->getUuid()->toBinary();
            $feed->save();
        }

        return new Response(
            null,
            Response::HTTP_OK
        );
    }

    #[Route(
        path: '/contao-feed/like',
        name: 'revierstil_contao_feed_api_like',
        defaults: ['_scope' => 'frontend'],
        methods: ['POST']
    )]
    public function like(Request $request): Response
    {
        $feed = $this->feeds->find($request->request->getInt('feedId'));

        if ($feed === null) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        $this->feeds->updateLikesForFeed($feed->id);

        return new Response(
            null,
            Response::HTTP_OK
        );
    }

    #[Route(
        path: '/contao-feed/delete',
        name: 'revierstil_contao_feed_api_delete',
        defaults: ['_scope' => 'frontend'],
        methods: ['POST']
    )]
    public function delete(Request $request): Response
    {
        $feed = $this->feeds->find($request->request->getInt('feedId'));

        if ($feed === null) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        if ($feed->author !== $this->getUser()?->id) {
            return new Response(null, Response::HTTP_FORBIDDEN);
        }

        $feed->delete();

        return new Response(
            null,
            Response::HTTP_OK
        );
    }

    private function prepareViolations(ConstraintViolationListInterface $violations): array
    {
        $result = [];

        foreach ($violations as $violation) {
            $property            = str_replace(['[', ']'], '', $violation->getPropertyPath());
            $result[$property][] = $violation->getMessage();
        }

        return $result;
    }

    private function getEditConstraints(): array
    {
        return [
            'message'  => [new NotBlank(), new Length(null, 0, 600)],
            'location' => [new NotBlank(), new Choice(choices: $this->getLocationValidationOptions())],
            'image'    => [
                new File(
                    maxSize: $this->config->get('maxFileSize') . 'k',
                    extensions: explode(',', $this->config->get('allowedDownload'))
                ),
            ],
        ];
    }

    private function getLocationValidationOptions(): array
    {
        return array_map(
            fn(OptionModel $location) => (string) $location->id,
            $this->options->findActiveGroupOptions('location')?->getModels() ?? []
        );
    }

    private function prepareFiltersFromConfig(): array
    {
        $filters = [];

        foreach ($this->filter as $filter) {
            $optionGroup = $this->groups->findByFieldName($filter['optionGroup']);

            $filters[$filter['optionGroup']] = [
                'label'   => $optionGroup->fieldLabel ?? $optionGroup->fieldName,
                'options' => $this->getFilterOptions($this->getFilteredOptions($filter['optionGroup'])),
            ];
        }

        return $filters;

    }

    private function getFilteredOptions(string $filter): array
    {
        $items = $this->feeds->findByFilter();

        if ($items === null || $items->count() === 0) {
            return [];
        }

        $locationIds = $items->fetchEach($filter);

        return array_values(array_filter(
            $this->options->findActiveGroupOptions($filter)->getModels(),
            fn(OptionModel $location): bool => in_array($location->id, $locationIds)
        ));
    }

    private function getFilterOptions(ContaoCollection|array|null $options): array
    {
        if ($options instanceof ContaoCollection) {
            $options = $options->getModels();
        }

        return array_map(
            fn(OptionModel $option): array => ['label' => $option->title, 'value' => $option->id],
            $options ?? []
        );
    }

    private function adjustFilters(array $filters): array
    {
        if (isset($filters['author']) === false) {
            return $filters;
        }
        $user = $this->getUser();

        $filters['author']['x'] = $user->id ?? 2;
        return $filters;
    }
}
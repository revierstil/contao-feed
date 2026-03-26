<?php

declare(strict_types=1);

namespace Revierstil\ContaoFeed\Controller\Element;

use BoelterIO\Options\Model\OptionModel;
use BoelterIO\Options\Model\OptionRepository;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Model\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsContentElement(type: 'rs_feed_list', category: 'revierstil')]
final class FeedListController extends AbstractContentElementController
{
    public function __construct(
        private readonly OptionRepository $optionsRepository,
        private readonly TranslatorInterface $translator,
        private readonly RouterInterface $router,
        private readonly CsrfTokenManagerInterface $tokenManager,
        private readonly string $tokenName,
        private readonly array $sorting,
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('config', $this->getConfig());
        $template->set('assetUrl', 'bundles/revierstilcontaofeed/feed-list/main.js?v=' . time());

        return $template->getResponse();
    }

    private function getConfig(): array
    {
        $config['sorting'] = array_map(
            fn(array $sorting): array => [
                'value'   => $sorting['value'],
                'label'   => $this->translator->trans($sorting['translationKey'], [], 'contao_rs_feed'),
                'default' => $sorting['default'],
            ],
            $this->sorting
        );

        $config['options'] = [
            'location' => [
                'options' => $this->getLocationCreateOptions(),
            ],
        ];

        $config['urls'] = [
            'listing' => $this->router->generate('revierstil_contao_feed_api_list'),
            'create'  => $this->router->generate('revierstil_contao_feed_api_create'),
            'like'    => $this->router->generate('revierstil_contao_feed_api_like'),
        ];

        $config['requestToken'] = $this->tokenManager->getToken($this->tokenName)->getValue();
        return $config;
    }

    private function getFilterOptions(Collection|array|null $options): array
    {
        if ($options instanceof Collection) {
            $options = $options->getModels();
        }

        return array_map(
            fn(OptionModel $option): array => ['label' => $option->title, 'value' => $option->id],
            $options ?? []
        );
    }

    private function getLocationCreateOptions(): array
    {
        return $this->getFilterOptions($this->optionsRepository->findActiveGroupOptions('location') ?? []);
    }
}
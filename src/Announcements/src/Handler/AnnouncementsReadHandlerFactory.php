<?php

declare(strict_types=1);

namespace Announcements\Handler;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Helper\UrlHelper;

class AnnouncementsReadHandlerFactory
{
    public function __invoke(ContainerInterface $container) : AnnouncementsReadHandler
    {
        $entityManager = $container->get(EntityManager::class);

        $urlHelper = $container->get(UrlHelper::class);

        return new AnnouncementsReadHandler(
            $entityManager,
            $container->get('config')['page_size'],
            $urlHelper);
    }
}

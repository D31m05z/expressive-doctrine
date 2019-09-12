<?php

declare(strict_types=1);

namespace Announcements\Handler;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

class AnnouncementsReadHandlerFactory
{
    public function __invoke(ContainerInterface $container) : AnnouncementsReadHandler
    {
        $entityManager = $container->get(EntityManager::class);

        return new AnnouncementsReadHandler($entityManager, $container->get('config')['page_size']);
    }
}

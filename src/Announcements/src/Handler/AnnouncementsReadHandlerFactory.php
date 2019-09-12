<?php

declare(strict_types=1);

namespace Announcements\Handler;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;

class AnnouncementsReadHandlerFactory
{
    public function __invoke(ContainerInterface $container) : AnnouncementsReadHandler
    {
        $entityManager = $container->get(EntityManager::class);

        $resourceGenerator = $container->get(ResourceGenerator::class);
        $halResponseFactory = $container->get(HalResponseFactory::class);

        return new AnnouncementsReadHandler(
            $entityManager,
            $container->get('config')['page_size'],
            $resourceGenerator,
            $halResponseFactory
        );
    }
}

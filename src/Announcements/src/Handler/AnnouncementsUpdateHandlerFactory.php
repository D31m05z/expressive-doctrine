<?php

declare(strict_types=1);

namespace Announcements\Handler;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;

/**
 * Class AnnouncementsUpdateHandlerFactory
 * @package Announcements\Handler
 */
class AnnouncementsUpdateHandlerFactory
{
    /**
     * @param ContainerInterface $container
     * @return AnnouncementsUpdateHandler
     */
    public function __invoke(ContainerInterface $container) : AnnouncementsUpdateHandler
    {
        return new AnnouncementsUpdateHandler(
            $container->get(EntityManager::class),
            $container->get(HalResponseFactory::class),
            $container->get(ResourceGenerator::class)
        );
    }
}

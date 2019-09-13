<?php

declare(strict_types=1);

namespace Announcements\Handler;

use Announcements\Entity\Announcement;
use Announcements\Entity\AnnouncementCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;

class AnnouncementsReadHandler implements RequestHandlerInterface
{
    protected $entityManager;
    protected $pageSize;
    protected $resourceGenerator;
    protected $halResponseFactory;

    public function __construct(
        EntityManager $entityManager,
        $pageSize,
        ResourceGenerator $resourceGenerator,
        HalResponseFactory $halResponseFactory
    ) {

        $this->entityManager = $entityManager;
        $this->pageSize = $pageSize;
        $this->resourceGenerator = $resourceGenerator;
        $this->halResponseFactory = $halResponseFactory;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $repository = $this->entityManager->getRepository(Announcement::class);

        $query = $repository
            ->createQueryBuilder('a')
            ->addOrderBy('a.sort', 'asc')
            ->setMaxResults($this->pageSize)
            ->getQuery();

        $paginator = new AnnouncementCollection($query);

        $resource = $this->resourceGenerator->fromObject($paginator, $request);
        return $this->halResponseFactory->createResponse($request, $resource);
    }
}

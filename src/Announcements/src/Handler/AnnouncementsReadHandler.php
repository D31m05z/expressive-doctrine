<?php

declare(strict_types=1);

namespace Announcements\Handler;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Helper\UrlHelper;

class AnnouncementsReadHandler implements RequestHandlerInterface
{
    protected $entityManager;
    protected $pageSize;
    protected $urlHelper;

    public function __construct(EntityManager $entityManager, $pageSize, UrlHelper $urlHelper)
    {
        $this->entityManager = $entityManager;
        $this->pageSize = $pageSize;
        $this->urlHelper = $urlHelper;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $query = $this->entityManager->getRepository('Announcements\Entity\Announcement')
            ->createQueryBuilder('c')
            ->getQuery();

        $paginator = new Paginator($query);

        $totalItems = count($paginator);
        $currentPage = ($request->getAttribute('page')) ?: 1;
        $totalPageCount = ceil($totalItems / $this->pageSize);
        $nextPage = (($currentPage < $totalPageCount) ? $currentPage + 1 : $totalPageCount);
        $previousPage = (($currentPage > 1) ? $currentPage - 1 : 1);

        $records = $paginator
            ->getQuery()
            ->setFirstResult($this->pageSize * ($currentPage - 1))
            ->setMaxResults($this->pageSize)
            ->getResult(Query::HYDRATE_ARRAY);

        $result['_per_page'] = $this->pageSize;
        $result['_page'] = $currentPage;
        $result['_total'] = $totalItems;
        $result['_total_pages'] = $totalPageCount;

        $result['_links']['self'] = $this->urlHelper->generate('announcements.read', ['page' => $currentPage]);
        $result['_links']['first'] = $this->urlHelper->generate('announcements.read', ['page' => 1]);
        $result['_links']['previous'] = $this->urlHelper->generate('announcements.read', ['page' => $previousPage]);
        $result['_links']['next'] = $this->urlHelper->generate('announcements.read', ['page' => $nextPage]);
        $result['_links']['last'] = $this->urlHelper->generate('announcements.read', ['page' => $totalPageCount]);
        $result['_links']['create'] = $this->urlHelper->generate('announcements.create');
        $result['_links']['read'] = $this->urlHelper->generate('announcements.read', ['page' => 1]);

        // add record specific hypermedia links
        foreach ($records as $key => $value) {
            $records[$key]['_links']['self'] = $this->urlHelper->generate('announcements.view',
                ['id' => $value['id']]);
            $records[$key]['_links']['update'] = $this->urlHelper->generate('announcements.update',
                ['id' => $value['id']]);
            $records[$key]['_links']['delete'] = $this->urlHelper->generate('announcements.delete',
                ['id' => $value['id']]);
        }

        $result['_embedded']['Announcements'] = $records;
        return new JsonResponse($result);
    }
}

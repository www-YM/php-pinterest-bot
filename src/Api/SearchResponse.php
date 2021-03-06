<?php

namespace seregazhuk\PinterestBot\Api;

use seregazhuk\PinterestBot\Api\Contracts\PaginatedResponse;

class SearchResponse implements PaginatedResponse
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Parse bookmarks from response.
     *
     * @return array
     */
    public function getBookmarks()
    {
        $searchBookmarks = $this
            ->response
            ->getData('module.tree.resource.options.bookmarks', []);

        return $searchBookmarks ? [$searchBookmarks[0]] : $this->response->getBookmarks();
    }

    /**
     * @return array
     */
    public function getResponseData()
    {
        $results = $this
            ->response
            ->getData('module.tree.data.results', []);

        return $results ? : $this->response->getResponseData();
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->getResponseData());
    }
}
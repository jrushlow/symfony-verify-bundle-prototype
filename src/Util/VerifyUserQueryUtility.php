<?php

/*
 * This file is part of the SymfonyCasts BUNDLE_NAME_HERE package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\VerifyUser\Util;

use SymfonyCasts\Bundle\VerifyUser\Collection\VerifyUserQueryParamCollection;

/**
 * Provides methods to manipulate a query string in a URI.
 *
 * @author Jesse Rushlow <jr@rushlow.dev>
 *
 * @internal
 * @final
 */
class VerifyUserQueryUtility
{
    private $urlUtility;

    public function __construct(VerifyUserUrlUtility $urlUtility)
    {
        $this->urlUtility = $urlUtility;
    }

    //@TODO remove/add method handle full uri? hmm [scheme] etc.. hmmm let me think
    public function removeQueryParam(VerifyUserQueryParamCollection $collection, string $uri): string
    {
        $urlComponents = $this->urlUtility->parseUrl($uri);
        $params = [];

        $q = $urlComponents->getQuery();

        if (null !== $q) {
            \parse_str($q, $params);
        }

        foreach ($collection as $queryParam) {
            if (isset($params[$queryParam->getKey()])) {
                unset($params[$queryParam->getKey()]);
            }
        }

        $urlComponents->setQuery($this->getSortedQueryString($params));

        return $this->urlUtility->buildUrl($urlComponents);
    }

    public function addQueryParams(VerifyUserQueryParamCollection $collection, string $uri): string
    {
        $parsedUri = \parse_url($uri);
        $params = [];
        if (isset($parsedUri['query'])) {
            \parse_str($parsedUri['query'], $params);
        }

        foreach ($collection as $queryParam) {
            $params[$queryParam->getKey()] = $queryParam->getValue();
        }

        return $parsedUri['path'].'?'.$this->getSortedQueryString($params);
    }

    public function getExpiryTimeStamp(string $uri): int
    {
        $parsedUri = \parse_url($uri);

        if (!isset($parsedUri['query'])) {
            return 0;
        }

        \parse_str($parsedUri['query'], $params);

        return (int) $params['expires'];
    }

    private function getSortedQueryString(array $params): string
    {
        \ksort($params);

        return \http_build_query($params);
    }
}

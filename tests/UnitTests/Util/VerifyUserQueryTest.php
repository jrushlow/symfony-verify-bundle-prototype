<?php

/*
 * This file is part of the SymfonyCasts BUNDLE_NAME_HERE package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\VerifyUser\Tests\UnitTests\Util;

use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\VerifyUser\Collection\VerifyUserQueryParamCollection;
use SymfonyCasts\Bundle\VerifyUser\Model\VerifyUserQueryParam;
use SymfonyCasts\Bundle\VerifyUser\Model\VerifyUserUrlComponents;
use SymfonyCasts\Bundle\VerifyUser\Util\VerifyUserQueryUtility;
use SymfonyCasts\Bundle\VerifyUser\Util\VerifyUserUrlUtility;

class VerifyUserQueryTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|VerifyUserUrlUtility
     */
    private $mockUrlUtility;

    protected function setUp(): void
    {
        $this->mockUrlUtility = $this->createMock(VerifyUserUrlUtility::class);
    }

    public function testRemovesParamsFromQueryString(): void
    {
        $params = ['a' => 'foo', 'b' => 'bar', 'c' => 'baz'];

        $collection = new VerifyUserQueryParamCollection();

        foreach ($params as $key => $value) {
            $collection->add(new VerifyUserQueryParam($key, $value));
        }

        $collection->offsetUnset(1);

        $path = '/verify';
        $uri = $path.\http_build_query($params);

        $components = new VerifyUserUrlComponents();
        $components->setPath('/verify');

        $this->mockUrlUtility
            ->expects($this->once())
            ->method('parseUrl')
            ->with($uri)
            ->willReturn($components)
        ;

        $components->setQuery('b=bar');

        $this->mockUrlUtility
            ->expects($this->once())
            ->method('buildUrl')
            ->with($components)
        ;

        $queryUtility = new VerifyUserQueryUtility($this->mockUrlUtility);

        $queryUtility->removeQueryParam($collection, $uri);
    }

    public function testAddsQueryParamsToUri(): void
    {
        $url = '/verify?a=foo&c=baz';
        $queryParam = [['a' => 'foo', 'c' => 'baz']];

        $components = new VerifyUserUrlComponents();
        $components->setPath('/verify');
        $components->setQuery(\http_build_query($queryParam));

        $this->mockUrlUtility
            ->expects($this->once())
            ->method('parseUrl')
            ->with($url)
            ->willReturn($components)
        ;

        $queryParam['b'] = 'bar';

        $components->setQuery(\ksort($queryParam));

        $this->mockUrlUtility
            ->expects($this->once())
            ->method('buildUrl')
            ->with($components)
        ;

        $collection = new VerifyUserQueryParamCollection();
        $collection->createParam('b', 'bar');

        $queryUtil = new VerifyUserQueryUtility($this->mockUrlUtility);
        $queryUtil->addQueryParams($collection, $url);
    }

    public function testGetsExpiryTimeFromQueryString(): void
    {
        $uri = '/?a=x&expires=1234567890';

        $components = new VerifyUserUrlComponents();
        $components->setPath('/');
        $components->setQuery('a=x&expires=1234567890');

        $this->mockUrlUtility
            ->expects($this->once())
            ->method('parseUrl')
            ->with($uri)
            ->willReturn($components)
        ;

        $queryUtility = new VerifyUserQueryUtility($this->mockUrlUtility);
        $result = $queryUtility->getExpiryTimeStamp($uri);

        self::assertSame(
            1234567890,
            $result
        );
    }
}

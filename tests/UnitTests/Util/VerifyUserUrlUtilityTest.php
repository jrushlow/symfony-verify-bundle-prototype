<?php

/*
 * This file is part of the SymfonyCasts BUNDLE_NAME_HERE package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyCasts\Bundle\VerifyUser\Tests\UnitTests\Util;

use PHPUnit\Framework\TestCase;
use SymfonyCasts\Bundle\VerifyUser\Util\VerifyUserUrlUtility;

class VerifyUserUrlUtilityTest extends TestCase
{
    public function parseUrlDataProvider(): \Generator
    {
        yield ['Scheme', 'https'];
        yield ['Host', 'rushlow.dev'];
        yield ['Port', 1234];
        yield ['User', 'jesse'];
        yield ['Pass', 'password'];
        yield ['Path', '/some-path'];
        yield ['Query', 'a=b'];
        yield ['Fragment', 'test-fragment'];
    }

    /**
     * @dataProvider parseUrlDataProvider
     */
    public function testParseUrl(string $methodName, $expected): void
    {
        $url = 'https://jesse:password@rushlow.dev:1234/some-path?a=b#test-fragment';

        $utility = new VerifyUserUrlUtility();
        $result = $utility->parseUrl($url);

        $getter = 'get'.$methodName;

        self::assertSame($expected, $result->$getter());
    }

    public function testBuildUrl(): void
    {
        $expected = 'https://jesse:password@rushlow.dev:1234/some-path?a=b#test-fragment';
        $utility = new VerifyUserUrlUtility();
        $components = $utility->parseUrl($expected);

        $result = $utility->buildUrl($components);

        self::assertSame($expected, $result);
    }
}

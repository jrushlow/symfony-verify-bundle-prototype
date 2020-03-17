<?php

namespace SymfonyCasts\Bundle\VerifyUser\Util;

use SymfonyCasts\Bundle\VerifyUser\Model\VerifyUserUrlComponents;

class VerifyUserUrlUtility
{
    public function parseUrl(string $url): VerifyUserUrlComponents
    {
        $urlComponents = \parse_url($url);

        $components = new VerifyUserUrlComponents();

        foreach ($urlComponents as $component => $value) {
            $method = 'set'.\ucfirst($component);
            $components->$method($value);
        }

        return $components;
    }

    public function buildUrl(VerifyUserUrlComponents $components): string
    {
        $componentOrder = [
            'Scheme' => '://',
            'User' => null,
            'Host' => null,
            'Port' => ':',
            'Path' => null,
            'Query' => '?',
            'Fragment' => '#',
        ];

        $url = '';

        if ($components->getUser()) {
            $credentials = $components->getUser();
            if ($components->getPass()) {
                $credentials .= ':'.$components->getPass();
            }

            $components->setUser($credentials.'@');
        }

        foreach ($componentOrder as $component => $separator) {
            $getter = 'get'.$component;
            $value = $components->$getter();

            if (null === $value) {
                continue;
            }

            if (null === $separator) {
                $url .= $value;
                continue;
            }

            if ('Scheme' !== $component) {
                $url .= $separator.$value;
                continue;
            }

            $url .= $value.$separator;
        }

        return $url;
    }
}

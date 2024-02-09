<?php

namespace PhpBoot\Utils;

final readonly class ArrayUtils
{
    private function __construct()
    {
    }

    public static function getFirstElement(array $array): mixed
    {
        if (empty($array)) {
            return null;
        }

        return array_values($array)[0];
    }
}
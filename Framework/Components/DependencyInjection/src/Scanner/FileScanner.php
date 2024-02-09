<?php

namespace PhpBoot\Di\Scanner;

use DirectoryIterator;
use ReflectionClass;
use ReflectionException;

final readonly class FileScanner
{

    /**
     * @param string $namespace
     * @param string $directory
     * @param true $true
     * @return ReflectionClass[]
     */
    public static function scanForClassesInNamespace(string $namespace, string $directory, bool $recursive): array
    {
        $classes = [];
        $namespace = rtrim($namespace, '\\');

        foreach (new DirectoryIterator($directory) as $fileInfo) {
            if ($fileInfo->isDot()) continue;

            if ($recursive && $fileInfo->isDir()) {
                $baseName = $fileInfo->getBasename();
                $newNamespace = $namespace . '\\'. $baseName;
                $classes = array_merge($classes, self::scanForClassesInNamespace($newNamespace, $fileInfo->getRealPath(), true));
            } else if ($fileInfo->isFile()) {
                $baseName = $fileInfo->getBasename('.php');
                $class = $namespace . '\\'. $baseName;
                try {
                    $classes[] = new ReflectionClass($class);
                } catch (ReflectionException $ex) {
                    // TODO: Log a warning
                }
            }
        }

        return $classes;
    }
}
<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Promptfoo;

final readonly class CachePath
{
    private const CACHE_DIR = '.promptfoo/cache';

    private const CACHE_FILE = 'cache.json';

    private const PARALLEL_DIR = 'parallel';

    public function __construct(
        private string $homeDir,
    ) {}

    public static function fromEnvironment(): self
    {
        /** @var string $home */
        $home = $_ENV['HOME'] ?? getenv('HOME') ?: $_ENV['USERPROFILE'] ?? getenv('USERPROFILE') ?: '';

        return new self($home);
    }

    public function isValid(): bool
    {
        return $this->homeDir !== '';
    }

    public function cacheDir(): string
    {
        return $this->homeDir.'/'.self::CACHE_DIR;
    }

    public function mainCache(): string
    {
        return $this->cacheDir().'/'.self::CACHE_FILE;
    }

    public function parallelDir(): string
    {
        return $this->cacheDir().'/'.self::PARALLEL_DIR;
    }

    public function parallelCacheDir(int $pid): string
    {
        return $this->parallelDir().'/'.$pid;
    }

    public function parallelCache(int $pid): string
    {
        return $this->parallelCacheDir($pid).'/'.self::CACHE_FILE;
    }
}

<?php

declare(strict_types=1);

namespace KevinPijning\Prompt;

use BadMethodCallException;
use Closure;
use KevinPijning\Prompt\Internal\BuiltProvider;

class Provider
{
    /** @var array<string, Closure> */
    protected static array $extensions = [];

    private ?string $id = null;

    private ?string $label = null;

    /**
     * temperature: Controls randomness (0.0 to 1.0)
     */
    private ?float $temperature = null;

    /**
     * max_tokens: Maximum number of tokens to generate
     */
    private ?int $maxTokens = null;

    /**
     * top_p: Nucleus sampling parameter
     */
    private ?float $topP = null;

    /**
     * frequency_penalty: Penalizes frequent tokens
     */
    private ?float $frequencyPenalty = null;

    /**
     * presence_penalty: Penalizes new tokens based on presence in text
     */
    private ?float $presencePenalty = null;

    /**
     *  stop: Sequences where the API will stop generating further tokens
     *
     * @var null|string[]
     */
    private ?array $stop = null;

    /** @var array<string,mixed> */
    private array $config = [];

    public static function extend(string $name, Closure $callback): void
    {
        static::$extensions[$name] = $callback;
    }

    public static function hasExtension(string $name): bool
    {
        return isset(static::$extensions[$name]);
    }

    public static function flushExtensions(): void
    {
        static::$extensions = [];
    }

    /**
     * @param  array<int, mixed>  $parameters
     */
    public function __call(string $method, array $parameters): mixed
    {
        if (! static::hasExtension($method)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.',
                static::class,
                $method
            ));
        }

        /** @var Closure $callback */
        $callback = static::$extensions[$method]->bindTo($this, static::class);

        $result = $callback(...$parameters);

        return $result ?? $this;
    }

    public static function create(string $id): self
    {
        return (new self)->id($id);
    }

    public function id(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function temperature(?float $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function maxTokens(?int $maxTokens): self
    {
        $this->maxTokens = $maxTokens;

        return $this;
    }

    public function topP(?float $topP): self
    {
        $this->topP = $topP;

        return $this;
    }

    public function frequencyPenalty(?float $frequencyPenalty): self
    {
        $this->frequencyPenalty = $frequencyPenalty;

        return $this;
    }

    public function presencePenalty(?float $presencePenalty): self
    {
        $this->presencePenalty = $presencePenalty;

        return $this;
    }

    /**
     * @param  string[]  $stop
     */
    public function stop(?array $stop): self
    {
        $this->stop = $stop;

        return $this;
    }

    /**
     * @param  array<string,mixed>|Closure(array<string,mixed>): array<string,mixed>  $config
     */
    public function config(array|Closure $config): self
    {
        $this->config = $config instanceof Closure ? $config($this->config) : $config;

        return $this;
    }

    /**
     * @param  array<string,mixed>  $config
     */
    public function mergeConfig(array $config): self
    {
        $this->config = [...$this->config, ...$config];

        return $this;
    }

    public function build(): BuiltProvider
    {
        return new BuiltProvider(
            id: $this->id,
            label: $this->label,
            temperature: $this->temperature,
            maxTokens: $this->maxTokens,
            topP: $this->topP,
            frequencyPenalty: $this->frequencyPenalty,
            presencePenalty: $this->presencePenalty,
            stop: $this->stop,
            config: $this->config,
        );
    }
}

<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Api;

use KevinPijning\Prompt\Internal\BuiltProvider;

class Provider
{
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
     * @param  array<string,mixed>  $array
     * @return $this
     */
    public function config(array $array): self
    {
        $this->config = $array;

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

<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Api;

class Provider
{
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

    public function __construct(public readonly string $id) {}

    public static function id(string $id): self
    {
        return new self($id);
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function temperature(?float $temperature): Provider
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function maxTokens(?int $maxTokens): Provider
    {
        $this->maxTokens = $maxTokens;

        return $this;
    }

    public function topP(?float $topP): Provider
    {
        $this->topP = $topP;

        return $this;
    }

    public function frequencyPenalty(?float $frequencyPenalty): Provider
    {
        $this->frequencyPenalty = $frequencyPenalty;

        return $this;
    }

    public function presencePenalty(?float $presencePenalty): Provider
    {
        $this->presencePenalty = $presencePenalty;

        return $this;
    }

    /**
     * @param  string[]  $stop
     */
    public function stop(?array $stop): Provider
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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function getMaxTokens(): ?int
    {
        return $this->maxTokens;
    }

    public function getTopP(): ?float
    {
        return $this->topP;
    }

    public function getFrequencyPenalty(): ?float
    {
        return $this->frequencyPenalty;
    }

    public function getPresencePenalty(): ?float
    {
        return $this->presencePenalty;
    }

    /**
     * @return string[]|null
     */
    public function getStop(): ?array
    {
        return $this->stop;
    }

    /**
     * @return array<string,mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}

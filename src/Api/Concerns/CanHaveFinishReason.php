<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Api\Concerns;

use KevinPijning\Prompt\Api\Assertion;
use KevinPijning\Prompt\Enums\FinishReason;

trait CanHaveFinishReason
{
    /**
     * @param  FinishReason|string  $reason
     * @param  array<string,mixed>  $options
     */
    public function toHaveFinishReason(FinishReason|string $reason, array $options = []): self
    {
        $reasonValue = $reason instanceof FinishReason ? $reason->value : $reason;

        return $this->assert(new Assertion(
            type: 'finish-reason',
            value: $reasonValue,
            threshold: null,
            options: $options,
        ));
    }

    /**
     * Assert that the model stopped for natural completion (reached end of response, stop sequence matched).
     *
     * @param  array<string,mixed>  $options
     */
    public function toHaveFinishReasonStop(array $options = []): self
    {
        return $this->toHaveFinishReason(FinishReason::Stop, $options);
    }

    /**
     * Assert that the model stopped due to token limit (max_tokens exceeded, context length reached).
     *
     * @param  array<string,mixed>  $options
     */
    public function toHaveFinishReasonLength(array $options = []): self
    {
        return $this->toHaveFinishReason(FinishReason::Length, $options);
    }

    /**
     * Assert that the model stopped due to content filtering (safety policies triggered).
     *
     * @param  array<string,mixed>  $options
     */
    public function toHaveFinishReasonContentFilter(array $options = []): self
    {
        return $this->toHaveFinishReason(FinishReason::ContentFilter, $options);
    }

    /**
     * Assert that the model stopped because it made function/tool calls.
     *
     * @param  array<string,mixed>  $options
     */
    public function toHaveFinishReasonToolCalls(array $options = []): self
    {
        return $this->toHaveFinishReason(FinishReason::ToolCalls, $options);
    }
}


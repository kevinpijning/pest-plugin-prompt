<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;
use KevinPijning\Prompt\Enums\FinishReason;

trait CanHaveFinishReason
{
    /**
     * Assert that the model stopped for the expected reason.
     *
     * @param  FinishReason|string  $reason  The expected finish reason (stop, length, content_filter, tool_calls)
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#finish-reason
     */
    public function toHaveFinishReason(FinishReason|string $reason): self
    {
        $reasonValue = $reason instanceof FinishReason ? $reason->value : $reason;

        return $this->assert(new Assertion(
            type: 'finish-reason',
            value: $reasonValue,
        ));
    }

    /**
     * Assert that the model stopped for natural completion (reached end of response, stop sequence matched).
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#finish-reason
     */
    public function toHaveFinishReasonStop(): self
    {
        return $this->toHaveFinishReason(FinishReason::Stop);
    }

    /**
     * Assert that the model stopped due to token limit (max_tokens exceeded, context length reached).
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#finish-reason
     */
    public function toHaveFinishReasonLength(): self
    {
        return $this->toHaveFinishReason(FinishReason::Length);
    }

    /**
     * Assert that the model stopped due to content filtering (safety policies triggered).
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#finish-reason
     */
    public function toHaveFinishReasonContentFilter(): self
    {
        return $this->toHaveFinishReason(FinishReason::ContentFilter);
    }

    /**
     * Assert that the model stopped because it made function/tool calls.
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#finish-reason
     */
    public function toHaveFinishReasonToolCalls(): self
    {
        return $this->toHaveFinishReason(FinishReason::ToolCalls);
    }
}

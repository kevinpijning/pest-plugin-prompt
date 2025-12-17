<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanBeValid
{
    /**
     * Assert that the output is valid JSON, optionally validating against a JSON schema.
     *
     * @param  array<string,mixed>|null  $schema  Optional JSON schema to validate against
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#is-json
     */
    public function toBeJson(?array $schema = null): self
    {
        return $this->assert(new Assertion(
            type: 'is-json',
            value: $schema,
        ));
    }

    /**
     * Assert that the output is valid HTML.
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#is-html
     */
    public function toBeHtml(): self
    {
        return $this->assert(new Assertion(
            type: 'is-html',
        ));
    }

    /**
     * Assert that the output is a valid SQL statement, optionally specifying database type for syntax validation.
     *
     * @param  array{databaseType?: string}|null  $config  Optional config with databaseType (e.g., "mysql", "postgresql")
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#is-sql
     */
    public function toBeSql(?array $config = null): self
    {
        return $this->assert(new Assertion(
            type: 'is-sql',
            value: $config,
        ));
    }

    /**
     * Assert that the output is valid XML, optionally specifying required elements.
     *
     * @param  array{requiredElements?: string[]}|null  $config  Optional config with requiredElements (e.g., ["root.child", "root.sibling"])
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#is-xml
     */
    public function toBeXml(?array $config = null): self
    {
        return $this->assert(new Assertion(
            type: 'is-xml',
            value: $config,
        ));
    }
}

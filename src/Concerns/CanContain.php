<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

trait CanContain
{
    /**
     * Assert that the output contains the expected substring.
     * Uses case-insensitive matching by default (icontains), use strict: true for case-sensitive (contains).
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#contains
     */
    public function toContain(string $contains, bool $strict = false): self
    {
        return $this->assert(new Assertion(
            type: $strict ? 'contains' : 'icontains',
            value: $contains,
        ));
    }

    /**
     * Assert that the output contains all of the specified substrings.
     * Uses case-insensitive matching by default (icontains-all), use strict: true for case-sensitive (contains-all).
     *
     * @param  string[]  $contains
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#contains-all
     */
    public function toContainAll(array $contains, bool $strict = false): self
    {
        return $this->assert(new Assertion(
            type: $strict ? 'contains-all' : 'icontains-all',
            value: $contains,
        ));
    }

    /**
     * Assert that the output contains at least one of the specified substrings.
     * Uses case-insensitive matching by default (icontains-any), use strict: true for case-sensitive (contains-any).
     *
     * @param  string[]  $contains
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#contains-any
     */
    public function toContainAny(array $contains, bool $strict = false): self
    {
        return $this->assert(new Assertion(
            type: $strict ? 'contains-any' : 'icontains-any',
            value: $contains,
        ));
    }

    /**
     * Assert that the output contains valid JSON, optionally validating against a JSON schema.
     *
     * @param  array<string,mixed>|null  $schema  Optional JSON schema to validate against
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#contains-json
     */
    public function toContainJson(?array $schema = null): self
    {
        return $this->assert(new Assertion(
            type: 'contains-json',
            value: $schema,
        ));
    }

    /**
     * Assert that the output contains HTML content.
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#contains-html
     */
    public function toContainHtml(): self
    {
        return $this->assert(new Assertion(
            type: 'contains-html',
        ));
    }

    /**
     * Assert that the output contains valid SQL, optionally specifying database type for syntax validation.
     *
     * @param  array{databaseType?: string}|null  $config  Optional config with databaseType (e.g., "mysql", "postgresql")
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#contains-sql
     */
    public function toContainSql(?array $config = null): self
    {
        return $this->assert(new Assertion(
            type: 'contains-sql',
            value: $config,
        ));
    }

    /**
     * Assert that the output contains valid XML, optionally specifying required elements.
     *
     * @param  array{requiredElements?: string[]}|null  $config  Optional config with requiredElements (e.g., ["root.child", "root.sibling"])
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/deterministic/#contains-xml
     */
    public function toContainXml(?array $config = null): self
    {
        return $this->assert(new Assertion(
            type: 'contains-xml',
            value: $config,
        ));
    }
}

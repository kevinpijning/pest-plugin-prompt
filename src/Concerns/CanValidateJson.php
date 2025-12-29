<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

use KevinPijning\Prompt\Assertion;

/**
 * Provides JSON validation assertions for structured LLM outputs.
 *
 * All methods use promptfoo's javascript assertion type under the hood,
 * with automatic handling of markdown code blocks (```json...```).
 */
trait CanValidateJson
{
    /**
     * Assert that the JSON output exactly equals the expected value.
     * Object key order is ignored, but array order is preserved.
     * Similar to Laravel's assertExactJson().
     *
     * @param  array<string,mixed>  $expected  The expected JSON structure
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/javascript/
     */
    public function toEqualJson(array $expected): self
    {
        $expectedJson = json_encode($expected, JSON_THROW_ON_ERROR);

        $js = $this->jsDeepEqual()
            .$this->jsParseOutput()
            .<<<JS

                const expected = {$expectedJson};
                const pass = deepEqual(parsed, expected);
                return {
                    pass,
                    score: pass ? 1 : 0,
                    reason: pass ? 'JSON exactly matches expected value' : 'JSON does not match expected value'
                };
                JS;

        return $this->assert(new Assertion(
            type: 'javascript',
            value: $js,
        ));
    }

    /**
     * Assert that the JSON output matches the expected structure.
     * Validates that all specified keys exist (nested keys use arrays).
     * Similar to Laravel's assertJsonStructure().
     *
     * @param  array<int|string,mixed>  $structure  The expected structure
     *                                              Examples:
     *                                              - ['name', 'age'] - keys must exist
     *                                              - ['address' => ['city', 'street']] - nested keys
     *                                              - ['items' => ['*' => ['id', 'name']]] - array items structure
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/javascript/
     */
    public function toMatchJsonStructure(array $structure): self
    {
        $structureJson = json_encode($structure, JSON_THROW_ON_ERROR);

        $js = $this->jsCheckStructure()
            .$this->jsParseOutput()
            .<<<JS

                const structure = {$structureJson};
                const pass = checkStructure(parsed, structure);
                return {
                    pass,
                    score: pass ? 1 : 0,
                    reason: pass ? 'JSON matches expected structure' : 'JSON does not match expected structure'
                };
                JS;

        return $this->assert(new Assertion(
            type: 'javascript',
            value: $js,
        ));
    }

    /**
     * Assert that the JSON output contains the given key-value pairs.
     * Similar to Laravel's assertJsonFragment().
     *
     * @param  array<string,mixed>  $fragment  Key-value pairs that must exist in the output
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/javascript/
     */
    public function toHaveJsonFragment(array $fragment): self
    {
        $fragmentJson = json_encode($fragment, JSON_THROW_ON_ERROR);

        $js = $this->jsDeepEqual()
            .$this->jsContainsFragment()
            .$this->jsParseOutput()
            .<<<JS

                const fragment = {$fragmentJson};
                const pass = containsFragment(parsed, fragment);
                return {
                    pass,
                    score: pass ? 1 : 0,
                    reason: pass ? 'JSON contains expected fragment' : 'JSON does not contain expected fragment'
                };
                JS;

        return $this->assert(new Assertion(
            type: 'javascript',
            value: $js,
        ));
    }

    /**
     * Assert that the JSON output contains all of the given fragments.
     *
     * @param  array<int,array<string,mixed>>  $fragments  Array of fragments to check
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/javascript/
     */
    public function toHaveJsonFragments(array $fragments): self
    {
        $fragmentsJson = json_encode($fragments, JSON_THROW_ON_ERROR);

        $js = $this->jsDeepEqual()
            .$this->jsContainsFragment()
            .$this->jsParseOutput()
            .<<<JS

                const fragments = {$fragmentsJson};
                const pass = fragments.every(fragment => containsFragment(parsed, fragment));
                return {
                    pass,
                    score: pass ? 1 : 0,
                    reason: pass ? 'JSON contains all expected fragments' : 'JSON does not contain all expected fragments'
                };
                JS;

        return $this->assert(new Assertion(
            type: 'javascript',
            value: $js,
        ));
    }

    /**
     * Assert that the JSON output has a value at the given path.
     * Supports dot notation for nested paths, numeric indices, and wildcards.
     *
     * @param  string  $path  Dot-notation path (e.g., 'address.city', 'people.0.name', 'people.*.name')
     * @param  mixed  $expected  Optional expected value at the path
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/javascript/
     */
    public function toHaveJsonPath(string $path, mixed $expected = null): self
    {
        $pathParts = json_encode(explode('.', $path), JSON_THROW_ON_ERROR);
        $hasExpected = func_num_args() > 1;
        $expectedJson = $hasExpected ? json_encode($expected, JSON_THROW_ON_ERROR) : 'undefined';
        $hasExpectedJs = $hasExpected ? 'true' : 'false';

        $js = $this->jsDeepEqual()
            .$this->jsGetValuesAtPath()
            .$this->jsParseOutput()
            .<<<JS

                const pathParts = {$pathParts};
                const hasExpected = {$hasExpectedJs};
                const expected = {$expectedJson};
                const values = getValuesAtPath(parsed, pathParts);

                if (values.length === 0) {
                    return { pass: false, score: 0, reason: 'Path "{$path}" does not exist' };
                }

                if (hasExpected) {
                    const pass = values.every(v => deepEqual(v, expected));
                    return {
                        pass,
                        score: pass ? 1 : 0,
                        reason: pass ? 'Path "{$path}" has expected value' : 'Path "{$path}" does not have expected value'
                    };
                }

                return { pass: true, score: 1, reason: 'Path "{$path}" exists' };
                JS;

        return $this->assert(new Assertion(
            type: 'javascript',
            value: $js,
        ));
    }

    /**
     * Assert that the JSON output has all of the given paths.
     * Supports dot notation, numeric indices, and wildcards.
     *
     * @param  array<int,string>|array<string,mixed>  $paths  Array of paths or path => value pairs
     *                                                        - ['name', 'age'] - paths must exist
     *                                                        - ['name' => 'John', 'age' => 30] - paths must have values
     *                                                        - ['people.*.name'] - wildcard paths must exist
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/javascript/
     */
    public function toHaveJsonPaths(array $paths): self
    {
        $pathsJson = json_encode($paths, JSON_THROW_ON_ERROR);
        $isAssociative = count(array_filter(array_keys($paths), is_string(...))) > 0;
        $isAssociativeJs = $isAssociative ? 'true' : 'false';

        $js = $this->jsDeepEqual()
            .$this->jsGetValuesAtPath()
            .$this->jsParseOutput()
            .<<<JS

                const getPath = (obj, path) => {
                    const parts = path.split('.');
                    const values = getValuesAtPath(obj, parts);
                    return values.length > 0 ? values : undefined;
                };

                const paths = {$pathsJson};
                const isAssociative = {$isAssociativeJs};
                const missingPaths = [];
                const wrongValues = [];

                if (isAssociative) {
                    for (const [path, expected] of Object.entries(paths)) {
                        const values = getPath(parsed, path);
                        if (values === undefined) {
                            missingPaths.push(path);
                        } else if (!values.every(v => deepEqual(v, expected))) {
                            wrongValues.push(path);
                        }
                    }
                } else {
                    for (const path of paths) {
                        const values = getPath(parsed, path);
                        if (values === undefined) {
                            missingPaths.push(path);
                        }
                    }
                }

                if (missingPaths.length > 0) {
                    return { pass: false, score: 0, reason: 'Missing paths: ' + missingPaths.join(', ') };
                }
                if (wrongValues.length > 0) {
                    return { pass: false, score: 0, reason: 'Wrong values at paths: ' + wrongValues.join(', ') };
                }

                return { pass: true, score: 1, reason: 'All paths exist with expected values' };
                JS;

        return $this->assert(new Assertion(
            type: 'javascript',
            value: $js,
        ));
    }

    /**
     * Assert that the JSON value at the given path has the expected type.
     * Supports dot notation, numeric indices, and wildcards.
     *
     * @param  string  $path  Dot-notation path (e.g., 'address.city', 'people.0.name', 'people.*.name')
     * @param  string  $type  Expected type: 'string', 'number', 'boolean', 'array', 'object', 'null'
     *
     * @see https://www.promptfoo.dev/docs/configuration/expected-outputs/javascript/
     */
    public function toHaveJsonType(string $path, string $type): self
    {
        $pathParts = json_encode(explode('.', $path), JSON_THROW_ON_ERROR);
        $typeJson = json_encode($type, JSON_THROW_ON_ERROR);

        $js = $this->jsGetValuesAtPath()
            .$this->jsGetType()
            .$this->jsParseOutput()
            .<<<JS

                const pathParts = {$pathParts};
                const expectedType = {$typeJson};
                const values = getValuesAtPath(parsed, pathParts);

                if (values.length === 0) {
                    return { pass: false, score: 0, reason: 'Path "{$path}" does not exist' };
                }

                const allMatch = values.every(v => getType(v) === expectedType);
                if (!allMatch) {
                    const actualTypes = [...new Set(values.map(v => getType(v)))].join(', ');
                    return {
                        pass: false,
                        score: 0,
                        reason: 'Value at "{$path}" has type(s) "' + actualTypes + '", expected "' + expectedType + '"'
                    };
                }

                return {
                    pass: true,
                    score: 1,
                    reason: 'Value at "{$path}" is of type "' + expectedType + '"'
                };
                JS;

        return $this->assert(new Assertion(
            type: 'javascript',
            value: $js,
        ));
    }

    /**
     * JavaScript helper: Deep equality comparison for objects and arrays.
     */
    private function jsDeepEqual(): string
    {
        return <<<'JAVASCRIPT'
            const deepEqual = (a, b) => {
                if (a === b) return true;
                if (typeof a !== typeof b) return false;
                if (typeof a !== 'object' || a === null) return false;
                if (Array.isArray(a) !== Array.isArray(b)) return false;
                if (Array.isArray(a)) {
                    if (a.length !== b.length) return false;
                    return a.every((v, i) => deepEqual(v, b[i]));
                }
                const keysA = Object.keys(a).sort();
                const keysB = Object.keys(b).sort();
                if (keysA.length !== keysB.length) return false;
                return keysA.every((k, i) => k === keysB[i] && deepEqual(a[k], b[k]));
            };

            JAVASCRIPT;
    }

    /**
     * JavaScript helper: Parse output to JSON object.
     * Handles: already-parsed objects, JSON strings, and markdown code blocks.
     */
    private function jsParseOutput(): string
    {
        return <<<'JAVASCRIPT'
            let parsed;
            if (typeof output === 'object' && output !== null) {
                parsed = output;
            } else if (typeof output === 'string') {
                try {
                    parsed = JSON.parse(output);
                } catch (e) {
                    const match = output.match(/```(?:json)?\s*([\s\S]*?)\s*```/);
                    if (match) {
                        try {
                            parsed = JSON.parse(match[1]);
                        } catch (e2) {
                            return { pass: false, score: 0, reason: 'Output is not valid JSON' };
                        }
                    } else {
                        return { pass: false, score: 0, reason: 'Output is not valid JSON' };
                    }
                }
            } else {
                return { pass: false, score: 0, reason: 'Output is not valid JSON' };
            }

            JAVASCRIPT;
    }

    /**
     * JavaScript helper: Get values at a dot-notation path with wildcard support.
     */
    private function jsGetValuesAtPath(): string
    {
        return <<<'JAVASCRIPT'
            const getValuesAtPath = (obj, parts) => {
                if (parts.length === 0) return [obj];
                const [current, ...rest] = parts;

                if (obj === undefined || obj === null) return [];

                if (current === '*') {
                    if (!Array.isArray(obj)) return [];
                    return obj.flatMap(item => getValuesAtPath(item, rest));
                }

                const key = /^\d+$/.test(current) ? parseInt(current, 10) : current;
                const value = Array.isArray(obj) ? obj[key] : obj[current];

                if (value === undefined) return [];
                return getValuesAtPath(value, rest);
            };

            JAVASCRIPT;
    }

    /**
     * JavaScript helper: Check if object contains a fragment (key-value pairs).
     */
    private function jsContainsFragment(): string
    {
        return <<<'JAVASCRIPT'
            const containsFragment = (obj, fragment) => {
                for (const [key, value] of Object.entries(fragment)) {
                    if (!(key in obj)) return false;
                    if (!deepEqual(obj[key], value)) return false;
                }
                return true;
            };

            JAVASCRIPT;
    }

    /**
     * JavaScript helper: Check if object matches a structure definition.
     */
    private function jsCheckStructure(): string
    {
        return <<<'JAVASCRIPT'
            const checkStructure = (obj, structure) => {
                if (Array.isArray(structure)) {
                    for (const item of structure) {
                        if (typeof item === 'string') {
                            if (!(item in obj)) return false;
                        } else if (typeof item === 'object' && item !== null) {
                            if (!checkStructure(obj, item)) return false;
                        }
                    }
                    return true;
                }

                for (const [key, value] of Object.entries(structure)) {
                    if (key === '*') {
                        if (!Array.isArray(obj)) return false;
                        for (const item of obj) {
                            if (!checkStructure(item, value)) return false;
                        }
                    } else if (typeof value === 'object' && value !== null && !Array.isArray(value)) {
                        if (!(key in obj)) return false;
                        if (!checkStructure(obj[key], value)) return false;
                    } else if (Array.isArray(value)) {
                        if (!(key in obj)) return false;
                        if (!checkStructure(obj[key], value)) return false;
                    } else if (typeof value === 'string') {
                        if (!(value in obj)) return false;
                    } else if (typeof value === 'number') {
                        const keys = Object.keys(obj);
                        if (!(keys[value] in obj || value.toString() in obj)) return false;
                    }
                }
                return true;
            };

            JAVASCRIPT;
    }

    /**
     * JavaScript helper: Get the type of a value (with special handling for null and arrays).
     */
    private function jsGetType(): string
    {
        return <<<'JAVASCRIPT'
            const getType = (value) => {
                if (value === null) return 'null';
                if (Array.isArray(value)) return 'array';
                return typeof value;
            };

            JAVASCRIPT;
    }
}

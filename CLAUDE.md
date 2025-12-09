# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a PestPHP plugin that integrates [promptfoo](https://www.promptfoo.dev/) for LLM prompt testing. It enables developers to write fluent, expressive tests for evaluating AI model prompts using Pest's familiar API style.

## Core Architecture

### Plugin System

The plugin hooks into Pest's lifecycle via `Plugin.php`:
- Implements `Bootable` and `HandlesArguments` interfaces
- Registers an `afterEach` hook that evaluates prompts after each test
- Handles `--output` CLI argument to specify where promptfoo results are saved
- Uses `TestLifecycle::evaluate()` to trigger evaluations

### Fluent API Design

The plugin uses a fluent interface pattern inspired by Pest's expectations:

```php
prompt('You are a helpful assistant')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['name' => 'Alice'])
    ->toContain('Hello Alice')
    ->and(['name' => 'Bob'])
    ->toContain('Hello Bob');
```

**Key classes:**
- `Evaluation` - Root object created by `prompt()`, holds prompts and providers
- `TestCase` - Created by `->expect()`, represents a single test case with variables
- `Assertion` - Immutable value object for assertion types and parameters
- `Provider` - Represents an LLM provider (e.g., OpenAI, Anthropic)

**Traits for extensibility:**
- `CanContain` - Adds `toContain*()` assertion methods
- `CanBeJudged` - Adds `toBeJudged()` for LLM-based evaluation

### Promptfoo Integration

The plugin generates promptfoo configuration files and executes evaluations:

1. `ConfigBuilder` converts `Evaluation` objects to YAML config
2. `PromptfooClient` executes `npx promptfoo@latest eval` via Symfony Process
3. `EvaluationResultBuilder` parses JSON output into result objects
4. `PendingEvaluation` manages temporary config/output file paths

**Important:** The plugin cleans up temporary files after evaluation but preserves user-specified output files.

### Context Management

- `TestContext` - Static class that stores evaluations per test
- `TestLifecycle` - Coordinates evaluation execution in `afterEach` hooks
- `Promptable` - Trait that makes the `prompt()` function available in tests

## Development Commands

### Running Tests
```bash
# Run all tests
composer test:unit
# Or: vendor/bin/pest

# Run single test file
vendor/bin/pest tests/Api/EvaluationTest.php

# Run specific test
vendor/bin/pest --filter="it can be instantiated with prompts"
```

### Code Quality
```bash
# Run all quality checks
composer test

# Individual checks
composer test:lint      # Laravel Pint (code style)
composer test:refacto   # RectorPHP (automated refactoring)
composer test:types     # PHPStan level 8 (static analysis)

# Auto-fix issues
composer lint           # Fix code style
composer refacto        # Apply automated refactors
```

## Testing the Plugin

### Unit Tests Location
All tests are in `tests/` with a mirror structure to `src/`:
- `tests/Api/` - Tests for fluent API classes
- `tests/Promptfoo/` - Tests for promptfoo integration
- `tests/Promptfoo/Results/` - Tests for result parsing

### Testing Patterns

**Testing fluent interfaces:**
- Verify method chaining returns correct object (`toBe($evaluation)`)
- Test that state is properly accumulated (e.g., multiple providers)
- Validate immutability of value objects like `Assertion`

**Testing promptfoo integration:**
- Mock the Symfony Process for command execution tests
- Use fixtures for JSON parsing tests
- Test error handling for malformed output and command failures

## CLI Usage

### Output Option
The plugin supports a `--output` option to persist promptfoo results:

```bash
# Use default path (prompt-tests-output/)
vendor/bin/pest --output

# Custom path
vendor/bin/pest --output=my-results/

# Both syntaxes work
vendor/bin/pest --output my-results/
```

**Implementation:** `Plugin::handleArguments()` intercepts and removes the option before Pest processes args.

## Common Patterns

### Adding New Assertion Types

1. Add a method to an existing trait (e.g., `CanContain`) or create a new trait
2. Return a new `Assertion` with the appropriate `type` string
3. Update `ConfigBuilder` if the assertion needs custom YAML serialization
4. The assertion type string must match promptfoo's expected assertion format

### Supporting New Providers

The `Provider` class uses a simple `id` property that maps directly to promptfoo provider IDs. To add provider-specific configuration, extend `Provider` with additional properties and update `ConfigBuilder::buildProviders()`.

## Configuration

### Default Provider
The default provider is `openai:gpt-4o-mini` (set in `Promptfoo::$defaultProviders`). Tests can override this per-evaluation using `->usingProvider()`.

### Promptfoo Command
The plugin uses `npx promptfoo@latest` by default. This can be overridden by calling `Promptfoo::setCommand()` if needed for testing or custom installations.

## Key Implementation Details

### Path Management
- `Path` class - Represents file paths with `toString()` method
- `OutputPath` - Extends `Path`, nullable for optional output
- Used for temporary config files and optional persistent output

### Evaluation Flow
1. Test calls `prompt()` → creates `Evaluation`, stores in `TestContext`
2. Test chains methods to configure evaluation
3. `afterEach` hook calls `TestLifecycle::evaluate()`
4. For each evaluation: generate config → execute promptfoo → parse results → cleanup

### Error Handling
- `ExecutionException` thrown when promptfoo command fails
- Includes command, output, and exit code for debugging
- 300-second timeout for long-running evaluations

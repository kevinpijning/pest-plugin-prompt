# Pest Plugin for Prompt Testing

[![Tests](https://img.shields.io/github/actions/workflow/status/kevinpijning/pest-plugin-prompt/tests.yml?label=tests&style=flat-square)](https://github.com/kevinpijning/pest-plugin-prompt/actions)
[![PHP Version](https://img.shields.io/packagist/php-v/kevinpijning/pest-plugin-prompt?style=flat-square)](https://packagist.org/packages/kevinpijning/pest-plugin-prompt)
[![License](https://img.shields.io/packagist/l/kevinpijning/pest-plugin-prompt?style=flat-square)](https://github.com/kevinpijning/pest-plugin-prompt/blob/main/LICENSE)
[![Pest](https://img.shields.io/badge/Pest-4.0+-ff69b4?style=flat-square)](https://pestphp.com)

**Test your AI prompts with confidence using Pest's elegant syntax.**

This plugin brings LLM prompt testing to your Pest test suite, powered by [promptfoo](https://www.promptfoo.dev/) under the hood. Write fluent, expressive tests for evaluating AI model prompts using the familiar Pest API you already love.

## Table of Contents

- [Why Use This Plugin?](#why-use-this-plugin)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Documentation](#documentation)
  - [Core Functions](#core-functions)
    - [`prompt()`](#prompt)
    - [`provider()`](#provider)
  - [Evaluation Methods](#evaluation-methods)
    - [`describe()`](#describe)
    - [`usingProvider()`](#usingprovider)
    - [`alwaysExpect()`](#alwaysexpect)
    - [`expect()`](#expect)
    - [`and()`](#and)
  - [Assertion Methods](#assertion-methods)
    - [`toContain()`](#tocontain)
    - [`toContainAll()`](#tocontainall)
    - [`toContainAny()`](#tocontainany)
    - [`toContainJson()`](#tocontainjson)
    - [`toContainHtml()`](#tocontainhtml)
    - [`toContainSql()`](#tocontainsql)
    - [`toContainXml()`](#tocontainxml)
    - [`toEqual()`](#toequal)
    - [`toBe()`](#tobe)
    - [`toBeJudged()`](#tobejudged)
    - [`startsWith()`](#startswith)
    - [`toMatchRegex()`](#tomatchregex)
    - [`toBeJson()`](#tobejson)
    - [`toEqualJson()`](#toequaljson)
    - [`toMatchJsonStructure()`](#tomatchjsonstructure)
    - [`toHaveJsonFragment()`](#tohavejsonfragment)
    - [`toHaveJsonFragments()`](#tohavejsonfragments)
    - [`toHaveJsonPath()`](#tohavejsonpath)
    - [`toHaveJsonPaths()`](#tohavejsonpaths)
    - [`toHaveJsonType()`](#tohavejsontype)
    - [`toBeHtml()`](#tobehtml)
    - [`toBeSql()`](#tobesql)
    - [`toBeXml()`](#tobexml)
    - [`toBeSimilar()`](#tobesimilar)
    - [`toHaveLevenshtein()`](#tohavelevenshtein)
    - [`toHaveRougeN()`](#tohaverougen)
    - [`toHaveFScore()`](#tohavefscore)
    - [`toHavePerplexity()`](#tohaveperplexity)
    - [`toHavePerplexityScore()`](#tohaveperplexityscore)
    - [`toHaveCost()`](#tohavecost)
    - [`toHaveLatency()`](#tohavelatency)
    - [`toHaveValidFunctionCall()`](#tohavevalidfunctioncall)
    - [`toHaveValidOpenaiFunctionCall()`](#tohavevalidopenaifunctioncall)
    - [`toHaveValidOpenaiToolsCall()`](#tohavevalidopenaitoolscall)
    - [`toHaveToolCallF1()`](#tohavetoolcallf1)
    - [`toHaveFinishReason()`](#tohavefinishreason)
    - [`toBeClassified()`](#tobeclassified)
    - [`toBeScoredByPi()`](#tobescoredbypi)
    - [`toBeRefused()`](#toberefused)
    - [`toPassJavascript()`](#topassjavascript)
    - [`toPassPython()`](#topasspython)
    - [`toPassWebhook()`](#topasswebhook)
    - [`toHaveTraceSpanCount()`](#tohavetracespancount)
    - [`toHaveTraceSpanDuration()`](#tohavetracespanduration)
    - [`toHaveTraceErrorSpans()`](#tohavetraceerrorspans)
    - [`not` Modifier](#not-modifier)
  - [Provider Configuration](#provider-configuration)
    - [`id()`](#id)
    - [`label()`](#label)
    - [`temperature()`](#temperature)
    - [`maxTokens()`](#maxtokens)
    - [`topP()`](#topp)
    - [`frequencyPenalty()`](#frequencypenalty)
    - [`presencePenalty()`](#presencepenalty)
    - [`stop()`](#stop)
    - [`config()`](#config)
  - [Usage Examples](#usage-examples)
    - [Basic Example](#basic-example)
    - [Multiple Prompts](#multiple-prompts)
    - [Multiple Providers](#multiple-providers)
    - [Multiple Test Cases](#multiple-test-cases)
    - [Provider Configuration](#provider-configuration-1)
    - [Global Provider Registration](#global-provider-registration)
    - [Advanced Assertions](#advanced-assertions)
    - [LLM-Based Evaluation](#llm-based-evaluation)
    - [Complex Example](#complex-example)
  - [CLI Options](#cli-options)
    - [`--output`](#--output)
- [Credits & License](#credits--license)

## Why Use This Plugin?

- **Test prompts against multiple LLM providers** - Compare OpenAI, Anthropic, and more in a single test
- **Validate responses with content assertions** - Check for specific text, JSON validity, HTML structure, and more
- **Use LLM-based evaluation** - Judge responses with natural language rubrics using AI itself
- **Familiar Pest-style fluent API** - Feels natural if you're already using Pest
- **Automatic cleanup** - Temporary files are managed for you
- **Battle-tested** - Built on promptfoo's proven evaluation framework

## Prerequisites

Before you begin, make sure you have:

- **PHP 8.3** or higher
- **Pest 4.0** or higher
- **Node.js and npm** - Required for promptfoo execution via `npx`
- **API keys for LLM providers** - You'll need keys for the providers you want to test

### Setting up API Keys

Set environment variables for the providers you'll use:

```bash
export OPENAI_API_KEY="your-openai-key-here"
export ANTHROPIC_API_KEY="your-anthropic-key-here"
```

If you're using Laravel or a similar framework with `.env` file support, you can add them there instead.

For more provider options and configuration, check out [promptfoo's provider documentation](https://www.promptfoo.dev/docs/providers/).

## Installation

Install the plugin via Composer:

```bash
composer require kevinpijning/pest-plugin-prompt --dev
```

The plugin automatically registers with Pest via package discovery - no additional configuration needed!

## Quick Start

Here's the simplest possible example to get you started:

```php
test('greeting prompt works correctly', function () {
    prompt('You are a helpful assistant. Greet {{name}} warmly.')
        ->usingProvider('openai:gpt-4o-mini')
        ->expect(['name' => 'Alice'])
        ->toContain('Alice');
});
```

**What's happening here?**

1. We create a prompt with variable interpolation using `{{name}}`
2. We specify OpenAI's GPT-4o-mini as our LLM provider
3. We test with the variable `name` set to "Alice"
4. We assert that the response contains "Alice"

When you run this test, the plugin will:
- Send the prompt to OpenAI with "Alice" substituted for `{{name}}`
- Receive the response
- Verify that "Alice" appears in the response
- Pass or fail the test accordingly

## Documentation

### Core Functions

#### `prompt()`

Create a new evaluation with one or more prompts. Use `{{variable}}` syntax for variable interpolation.

```php
// Single prompt
prompt('You are a helpful assistant.');

// Multiple prompts (tested against each other)
prompt(
    'You are a helpful assistant.',
    'You are a professional assistant.'
);

// With variables
prompt('Greet {{name}} warmly.');
```

#### `provider()`

Register a global provider like Pest datasets that can be reused across multiple tests. Providers registered with this function can be referenced by name in `usingProvider()`.

```php
// Register a simple provider
provider('openai-gpt4')->id('openai:gpt-4');

// Register with full configuration
provider('custom-openai')
    ->id('openai:gpt-4')
    ->label('Custom OpenAI')
    ->temperature(0.7)
    ->maxTokens(2000);

// Use in tests
prompt('Hello')
    ->usingProvider('custom-openai')
    ->expect()
    ->toContain('Hi');
```

### Evaluation Methods

#### `describe()`

Add a description to your evaluation for better test output and debugging.

```php
prompt('You are a helpful assistant.')
    ->describe('Tests basic assistant greeting')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContain('Hello');
```

#### `usingProvider()`

Specify which LLM provider(s) to use for evaluation. You can pass provider IDs, `Provider` instances, callables, or registered provider names.

```php
// Single provider by ID
prompt('Hello')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContain('Hi');

// Multiple providers (compares responses)
prompt('What is 2+2?')
    ->usingProvider('openai:gpt-4o-mini', 'anthropic:claude-3')
    ->expect()
    ->toContain('4');

// Provider instance
$provider = Provider::create('openai:gpt-4')
    ->temperature(0.7);

prompt('Hello')
    ->usingProvider($provider)
    ->expect()
    ->toContain('Hi');

// Use default provider (openai:gpt-4o-mini)
prompt('Hello')
    ->expect()
    ->toContain('Hi');
```

#### `alwaysExpect()`

Set default assertions and variables that apply to **all** test cases in the evaluation. This is useful when you want to ensure certain conditions are met for every test case without repeating the assertions.

```php
prompt('Translate {{message}} to {{language}}.')
    ->usingProvider('openai:gpt-4o-mini')
    ->alwaysExpect(['message' => 'Hello World!'])
    ->toBeJudged('the language is always a friendly variant')
    ->toBeJudged('the source and output language are always mentioned in the response')
    ->expect(['language' => 'es'])
    ->toContain('hola')
    ->toBeJudged('Contains the translation of Hello world! in spanish');
```

**With callback:**

You can pass an optional callback function to configure the default test case:

```php
prompt('Translate {{message}} to {{language}}.')
    ->usingProvider('openai:gpt-4o-mini')
    ->alwaysExpect(
        ['message' => 'Hello World!'],
        function (TestCase $testCase) {
            $testCase
                ->toBeJudged('the language is always a friendly variant')
                ->toBeJudged('the source and output language are always mentioned in the response');
        }
    )
    ->expect(['language' => 'es'])
    ->toContain('hola');
```

**Key points:**
- `alwaysExpect()` returns a `TestCase` instance that supports all assertion methods
- Assertions added via `alwaysExpect()` apply to every test case in the evaluation
- Default variables can be set and will be merged with test case variables
- You can chain multiple assertions after `alwaysExpect()` or use a callback
- The default test case is separate from regular test cases and won't appear in the `testCases()` array
- If `alwaysExpect()` is called multiple times, subsequent calls will execute the callback on the existing default test case

**Use cases:**
- Ensure all responses meet quality standards (e.g., "always be professional")
- Set common variables that apply to all tests
- Enforce safety checks across all test cases
- Apply format requirements universally (e.g., "always contain JSON")

#### `expect()`

Create a test case with variables that will be substituted into your prompt template.

```php
prompt('Greet {{name}} warmly.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['name' => 'Alice'])
    ->toContain('Alice');

// Multiple variables
prompt('{{greeting}}, {{name}}!')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['greeting' => 'Hello', 'name' => 'Bob'])
    ->toContain('Hello')
    ->toContain('Bob');

// Empty variables (no substitution)
prompt('You are a helpful assistant.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContain('assistant');
```

**With callback:**

You can pass an optional callback function that receives the created `TestCase` instance. This is useful for grouping multiple assertions or applying conditional logic.

```php
prompt('Greet {{name}} warmly.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['name' => 'Alice'], function (TestCase $testCase) {
        $testCase
            ->toContain('Alice')
            ->toContain('Hello')
            ->toBeJudged('response is friendly and welcoming');
    });

// Using arrow function
prompt('Translate {{text}} to {{language}}.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(
        ['text' => 'Hello', 'language' => 'Spanish'],
        fn (TestCase $tc) => $tc
            ->toContain('Hola')
            ->toBeJudged('translation is accurate')
    );
```

#### `and()`

Chain multiple test cases for the same evaluation. Each call to `and()` creates a new test case with different variables.

```php
prompt('Greet {{name}} warmly.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['name' => 'Alice'])
    ->toContain('Alice')
    ->and(['name' => 'Bob'])
    ->toContain('Bob')
    ->and(['name' => 'Charlie'])
    ->toContain('Charlie');
```

**With callback:**

You can pass an optional callback function that receives the newly created `TestCase`:

```php
prompt('Greet {{name}} warmly.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['name' => 'Alice'])
    ->toContain('Alice')
    ->and(['name' => 'Bob'], function (TestCase $testCase) {
        $testCase
            ->toContain('Bob')
            ->toBeJudged('response is warm and friendly');
    })
    ->and(['name' => 'Charlie'], fn (TestCase $tc) => $tc->toContain('Charlie'));
```

#### `to()` and `group()`

Group multiple assertions together using a callback. Both `to()` and `group()` are aliases that execute a callback with the current test case, allowing you to organize assertions logically.

```php
prompt('Explain {{topic}} in detail.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['topic' => 'quantum computing'])
    ->to(function (TestCase $testCase) {
        $testCase
            ->toContain('quantum')
            ->toContain('computing')
            ->toBeJudged('explanation is clear and accurate')
            ->toHaveLatency(2000);
    });

// Using group() (same as to())
prompt('Analyze {{data}}.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['data' => 'sales figures'])
    ->group(function (TestCase $testCase) {
        $testCase
            ->toContain('analysis')
            ->toBeJudged('analysis is thorough');
    });

// Chaining multiple groups
prompt('Review {{document}}.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['document' => 'contract'])
    ->to(fn (TestCase $tc) => $tc->toContain('terms'))
    ->group(fn (TestCase $tc) => $tc->toBeJudged('review is comprehensive'))
    ->to(fn (TestCase $tc) => $tc->toHaveLatency(1500));
```

**Key points:**
- `to()` and `group()` are functionally identical - use whichever reads better in your context
- The callback receives the current `TestCase` instance
- Useful for organizing related assertions together
- Can be chained multiple times
- Works with all assertion methods

**Use cases:**
- Group related assertions for better code organization
- Apply conditional logic based on test case variables
- Reuse assertion patterns across multiple test cases

### Assertion Methods

#### `toContain()`

Assert that the response contains specific text. Case-insensitive by default.

```php
prompt('What is the capital of France?')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContain('Paris');

// Case-sensitive matching
prompt('What is the capital of France?')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContain('Paris', strict: true);

// With threshold (similarity score, 0.0 to 1.0)
prompt('Explain quantum computing.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContain('quantum', threshold: 0.8);

// With custom options
prompt('What is AI?')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContain('artificial intelligence', options: ['normalize': true]);
```

#### `toContainAll()`

Assert that the response contains all of the specified strings.

```php
prompt('Describe a healthy meal.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContainAll(['protein', 'vegetables', 'grains']);

// Case-sensitive
prompt('Describe a healthy meal.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContainAll(['Protein', 'Vegetables'], strict: true);

// With threshold
prompt('Describe a healthy meal.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContainAll(['protein', 'vegetables'], threshold: 0.9);
```

#### `toContainAny()`

Assert that the response contains at least one of the specified strings.

```php
prompt('What is the weather like?')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContainAny(['sunny', 'rainy', 'cloudy']);

// Case-sensitive
prompt('What is the weather like?')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContainAny(['Sunny', 'Rainy'], strict: true);
```

#### `toContainJson()`

Assert that the response contains valid JSON.

```php
prompt('Return user data as JSON: name, age, email')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContainJson();
```

#### `toContainHtml()`

Assert that the response contains valid HTML.

```php
prompt('Generate an HTML list of fruits')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContainHtml();
```

#### `toContainSql()`

Assert that the response contains valid SQL.

```php
prompt('Write a SQL query to select all users')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContainSql();
```

#### `toContainXml()`

Assert that the response contains valid XML.

```php
prompt('Generate XML for a product catalog')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContainXml();
```

#### `toEqual()`

Assert that the response exactly equals the expected value. This is useful for deterministic outputs where you expect an exact match.
You can also check whether it matches the expected JSON format.

```php
prompt('Calculate 335 + 85. Return only the number.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toEqual(420);
```

#### `toBe()`

This is a convenience alias of `toEqual()`. 

```php
prompt('Calculate 335 + 85. Return only the number.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBe(420);
```

#### `toBeJudged()`

Use an LLM to evaluate the response against a natural language rubric. This is useful for subjective quality checks.

```php
prompt('Explain quantum computing to a beginner.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeJudged('The explanation should be clear, accurate, and use simple language.');

// With threshold (minimum score 0.0 to 1.0)
prompt('Write a product description.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeJudged('The description should be persuasive and highlight key features.', threshold: 0.8);

// With custom options
prompt('Write a product description.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeJudged('Should be professional and engaging.', options: ['provider': 'openai:gpt-4']);
```

#### `startsWith()`

Assert that the response starts with a specific prefix.

```php
prompt('Generate a greeting.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->startsWith('Hello');

// Case-sensitive
prompt('Generate a greeting.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->startsWith('Hello', strict: true);

// With threshold
prompt('Generate a greeting.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->startsWith('Hello', threshold: 0.9);
```

#### `toMatchRegex()`

Assert that the response matches a regular expression pattern.

```php
prompt('Generate a phone number.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toMatchRegex('/\d{3}-\d{3}-\d{4}/');

// With threshold
prompt('Generate a phone number.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toMatchRegex('/\d{3}-\d{3}-\d{4}/', threshold: 0.9);
```

#### `toBeJson()`

Assert that the response is valid JSON (not just contains JSON).

```php
prompt('Return user data as JSON: name, age, email')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeJson();

// With JSON schema validation
prompt('Return user data as JSON.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeJson([
        'type' => 'object',
        'properties' => [
            'name' => ['type' => 'string'],
            'age' => ['type' => 'number'],
        ],
        'required' => ['name', 'age'],
    ]);
```

#### `toEqualJson()`

Assert that the JSON output exactly equals the expected value. Object key order is ignored, but array order is preserved. This is similar to Laravel's `assertExactJson()`.

```php
prompt('Extract the person info from: {{text}}')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['text' => 'John is 30 years old'])
    ->toEqualJson([
        'name' => 'John',
        'age' => 30,
    ]);

// Works with nested structures
prompt('Extract address info.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toEqualJson([
        'user' => [
            'name' => 'John',
            'address' => [
                'city' => 'Amsterdam',
            ],
        ],
    ]);
```

#### `toMatchJsonStructure()`

Assert that the JSON output contains all expected keys. This validates structure without checking values, similar to Laravel's `assertJsonStructure()`.

```php
// Simple key validation
prompt('Return user data as JSON.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toMatchJsonStructure(['name', 'age', 'email']);

// Nested structure validation
prompt('Return user with address.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toMatchJsonStructure([
        'name',
        'address' => ['street', 'city', 'country'],
    ]);

// Array items with wildcard (*)
prompt('Return a list of users.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toMatchJsonStructure([
        'users' => [
            '*' => ['id', 'name', 'email'],
        ],
    ]);
```

#### `toHaveJsonFragment()`

Assert that the JSON output contains specific key-value pairs. Similar to Laravel's `assertJsonFragment()`.

```php
prompt('Extract person info from: {{text}}')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['text' => 'John Doe is 30 years old'])
    ->toHaveJsonFragment(['name' => 'John Doe'])
    ->toHaveJsonFragment(['age' => 30]);

// Works with nested values
prompt('Extract user with address.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveJsonFragment([
        'address' => ['city' => 'Amsterdam'],
    ]);
```

#### `toHaveJsonFragments()`

Assert that the JSON output contains all specified fragments.

```php
prompt('Extract person info from: {{text}}')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['text' => 'Jane Smith is 25 years old and lives in Berlin'])
    ->toHaveJsonFragments([
        ['name' => 'Jane Smith'],
        ['age' => 25],
        ['city' => 'Berlin'],
    ]);
```

#### `toHaveJsonPath()`

Assert that a value exists at a specific JSON path. Supports dot notation, numeric array indices, and wildcards.

```php
// Check path exists
prompt('Return user with address.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveJsonPath('name')
    ->toHaveJsonPath('address.city');

// Check path has specific value
prompt('Extract person info.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveJsonPath('name', 'John Doe')
    ->toHaveJsonPath('address.city', 'Amsterdam');

// Array index access
prompt('Return list of users.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveJsonPath('users.0.name')
    ->toHaveJsonPath('users.1.name', 'Jane');

// Wildcard for all array items
prompt('Return list of users.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveJsonPath('users.*.name')
    ->toHaveJsonPath('users.*.status', 'active');
```

#### `toHaveJsonPaths()`

Assert that multiple JSON paths exist, optionally with expected values.

```php
// Check paths exist (array of strings)
prompt('Return user data.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveJsonPaths(['name', 'email', 'address.city']);

// Check paths with values (associative array)
prompt('Extract person info from: {{text}}')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['text' => 'Grace Lee is 28 years old and lives in Seoul'])
    ->toHaveJsonPaths([
        'name' => 'Grace Lee',
        'age' => 28,
        'city' => 'Seoul',
    ]);

// Mix of existence and value checks with wildcards
prompt('Return users list.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveJsonPaths([
        'users.*.name',
        'users.*.type' => 'customer',
    ]);
```

#### `toHaveJsonType()`

Assert that the value at a JSON path has the expected type. Supports: `string`, `number`, `boolean`, `array`, `object`, `null`.

```php
// Basic type validation
prompt('Return user data.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveJsonType('name', 'string')
    ->toHaveJsonType('age', 'number')
    ->toHaveJsonType('active', 'boolean');

// Nested path type validation
prompt('Return user with address.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveJsonType('address', 'object')
    ->toHaveJsonType('address.city', 'string');

// Array and wildcard type validation
prompt('Return list of users.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveJsonType('users', 'array')
    ->toHaveJsonType('users.*.name', 'string')
    ->toHaveJsonType('users.*.age', 'number');
```

#### `toBeHtml()`

Assert that the response is valid HTML.

```php
prompt('Generate an HTML list of fruits')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeHtml();
```

#### `toBeSql()`

Assert that the response is valid SQL (not just contains SQL).

```php
prompt('Write a SQL query to select all users')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeSql();

// With authority list (allowed SQL operations)
prompt('Write a SQL query.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeSql(['SELECT', 'INSERT']);
```

#### `toBeXml()`

Assert that the response is valid XML.

```php
prompt('Generate XML for a product catalog')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeXml();
```

#### `toBeSimilar()`

Assert that the response is semantically similar to the expected value using embedding similarity.

```php
prompt('Explain artificial intelligence.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeSimilar('AI is the simulation of human intelligence by machines');

// With threshold (default is 0.75)
prompt('Explain artificial intelligence.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeSimilar('AI explanation', threshold: 0.8);

// With custom embedding provider
prompt('Explain artificial intelligence.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeSimilar('AI explanation', provider: 'huggingface:sentence-similarity:model');

// Multiple expected values
prompt('Explain artificial intelligence.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeSimilar(['AI explanation', 'Machine intelligence', 'Artificial intelligence definition']);
```

#### `toHaveLevenshtein()`

Assert that the Levenshtein (edit) distance between the response and expected value is below a threshold.

```php
prompt('Spell the word "hello".')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveLevenshtein('hello', threshold: 2.0);
```

#### `toHaveRougeN()`

Assert that the ROUGE-N score is above a threshold.

```php
prompt('Summarize this article.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveRougeN(1, 'Expected summary', threshold: 0.7);

// ROUGE-2
prompt('Summarize this article.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveRougeN(2, 'Expected summary', threshold: 0.6);
```

#### `toHaveFScore()`

Assert that the F-score is above a threshold.

```php
prompt('Extract entities from the text.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveFScore('Expected entities', threshold: 0.8);
```

#### `toHavePerplexity()`

Assert that the perplexity is below a threshold.

```php
prompt('Generate coherent text.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHavePerplexity(threshold: 10.0);
```

#### `toHavePerplexityScore()`

Assert that the normalized perplexity score is below a threshold.

```php
prompt('Generate coherent text.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHavePerplexityScore(threshold: 0.5);
```

#### `toHaveCost()`

Assert that the inference cost is below a maximum threshold.

```php
prompt('Generate a short response.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveCost(0.01);
```

#### `toHaveLatency()`

Assert that the response latency is below a maximum threshold (in milliseconds).

```php
prompt('Generate a quick response.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveLatency(1000);
```

#### `toHaveValidFunctionCall()`

Assert that the response contains a valid function call matching the provided schema.

```php
prompt('Call the weather function.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveValidFunctionCall([
        'type' => 'object',
        'properties' => [
            'name' => ['type' => 'string'],
            'arguments' => ['type' => 'object'],
        ],
    ]);
```

#### `toHaveValidOpenaiFunctionCall()`

Assert that the response contains a valid OpenAI function call.

```php
prompt('Call the weather function.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveValidOpenaiFunctionCall();
```

#### `toHaveValidOpenaiToolsCall()`

Assert that the response contains valid OpenAI tool calls.

```php
prompt('Use the available tools.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveValidOpenaiToolsCall();
```

#### `toHaveToolCallF1()`

Assert that the F1 score comparing actual vs expected tool calls is above a threshold.

```php
prompt('Call the weather and time functions.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveToolCallF1(['weather', 'time'], threshold: 0.8);
```

#### `toHaveFinishReason()`

Assert that the model stopped for the expected reason. You can use either a string or the `FinishReason` enum.

**Standard Finish Reasons:**
- `stop`: Natural completion (reached end of response, stop sequence matched)
- `length`: Token limit reached (max_tokens exceeded, context length reached)
- `content_filter`: Content filtering triggered due to safety policies
- `tool_calls`: Model made function/tool calls

```php
use KevinPijning\Prompt\Enums\FinishReason;

// Using string
prompt('Generate a response.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveFinishReason('stop');

// Using enum
prompt('Generate a response.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveFinishReason(FinishReason::Stop);

// Check for tool calls
prompt('Use available tools.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveFinishReason(FinishReason::ToolCalls);
```

**Convenience Methods:**

For each finish reason, there's a dedicated convenience method:

```php
// Natural completion
prompt('Generate a response.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveFinishReasonStop();

// Token limit reached
prompt('Generate a very long response.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveFinishReasonLength();

// Content filter triggered
prompt('Generate harmful content.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveFinishReasonContentFilter();

// Tool calls made
prompt('Use available tools.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveFinishReasonToolCalls();
```

#### `toBeClassified()`

Assert that a HuggingFace classifier returns the expected class above a threshold.

```php
// Sentiment analysis
prompt('Write a positive review.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeClassified(
        'huggingface:text-classification:distilbert-base-uncased-finetuned-sst-2-english',
        'POSITIVE',
        threshold: 0.8
    );

// Hate speech detection
prompt('Write a friendly message.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeClassified(
        'huggingface:text-classification:facebook/roberta-hate-speech-dynabench-r4-target',
        'nothate',
        threshold: 0.9
    );
```

#### `toBeScoredByPi()`

Use Pi Labs' preference scoring model as an alternative to LLM-as-a-judge.

```php
prompt('Write a helpful response.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeScoredByPi('Is the response not apologetic and provides a clear, concise answer?', threshold: 0.8);
```

#### `toBeRefused()`

Assert that the LLM output indicates the model refused to perform the requested task.

```php
prompt('Write harmful content.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeRefused();

// Ensure model does NOT refuse safe requests
prompt('What is 2+2?')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->not->toBeRefused();
```

#### `toPassJavascript()`

Assert that a custom JavaScript function validates the output.

```php
prompt('Generate a response longer than 10 characters.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toPassJavascript('return output.length > 10;');
```

#### `toPassPython()`

Assert that a custom Python function validates the output.

```php
prompt('Generate a response longer than 10 characters.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toPassPython('return len(output) > 10');
```

#### `toPassWebhook()`

Assert that a webhook returns `{pass: true}`.

```php
prompt('Generate a response.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toPassWebhook('https://example.com/validate');
```

#### `toHaveTraceSpanCount()`

Assert that trace spans matching patterns meet min/max thresholds.

```php
prompt('Process the request.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveTraceSpanCount(['pattern1', 'pattern2'], min: 1, max: 5);
```

#### `toHaveTraceSpanDuration()`

Assert that trace span durations meet percentile and max duration thresholds.

```php
prompt('Process the request.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toHaveTraceSpanDuration(['pattern1'], percentile: 0.95, maxDuration: 1000.0);
```

#### `toHaveTraceErrorSpans()`

Detect errors in traces by status codes, attributes, and messages.

```php
prompt('Process the request.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->not->toHaveTraceErrorSpans();
```

#### `not` Modifier

Negate any assertion by using the `not` modifier.

```php
prompt('Write a happy birthday message.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->not->toContain('condolences');
```

### Provider Configuration

When creating or configuring providers, you can use these methods:

#### `id()`

Set the provider identifier (e.g., `'openai:gpt-4'`, `'anthropic:claude-3'`).

```php
Provider::create('openai:gpt-4')
    ->id('openai:gpt-4o-mini');
```

#### `label()`

Set a custom label for the provider (useful in test output).

```php
Provider::create('openai:gpt-4')
    ->label('OpenAI GPT-4 Production');
```

#### `temperature()`

Control randomness in responses (0.0 to 1.0). Lower values make responses more deterministic.

```php
Provider::create('openai:gpt-4')
    ->temperature(0.7);
```

#### `maxTokens()`

Set the maximum number of tokens to generate.

```php
Provider::create('openai:gpt-4')
    ->maxTokens(2000);
```

#### `topP()`

Set nucleus sampling parameter (0.0 to 1.0).

```php
Provider::create('openai:gpt-4')
    ->topP(0.9);
```

#### `frequencyPenalty()`

Penalize frequent tokens (-2.0 to 2.0).

```php
Provider::create('openai:gpt-4')
    ->frequencyPenalty(0.5);
```

#### `presencePenalty()`

Penalize new tokens based on presence in text (-2.0 to 2.0).

```php
Provider::create('openai:gpt-4')
    ->presencePenalty(0.3);
```

#### `stop()`

Set stop sequences where generation should stop.

```php
Provider::create('openai:gpt-4')
    ->stop(['\n', 'Human:', 'AI:']);
```

#### `config()`

Set custom configuration options for the provider.

```php
Provider::create('openai:gpt-4')
    ->config([
        'apiKey' => 'custom-key',
        'baseURL' => 'https://api.example.com',
    ]);
```

### Usage Examples

#### Basic Example

```php
test('assistant greets user correctly', function () {
    prompt('You are a helpful assistant. Greet {{name}} warmly.')
        ->usingProvider('openai:gpt-4o-mini')
        ->expect(['name' => 'Alice'])
        ->toContain('Alice');
});
```

#### Multiple Prompts

Test multiple prompt variations against the same test cases.

```php
test('prompt variations work', function () {
    prompt(
        'You are a helpful assistant.',
        'You are a professional assistant.',
        'You are a friendly assistant.'
    )
        ->usingProvider('openai:gpt-4o-mini')
        ->expect()
        ->toContain('assistant');
});
```

#### Multiple Providers

Compare responses across different LLM providers.

```php
test('providers give consistent answers', function () {
    prompt('What is 2+2?')
        ->usingProvider('openai:gpt-4o-mini', 'anthropic:claude-3')
        ->expect()
        ->toContain('4');
});
```

#### Multiple Test Cases

Test the same prompt with different variable values.

```php
test('greeting works for different names', function () {
    prompt('Greet {{name}} warmly.')
        ->usingProvider('openai:gpt-4o-mini')
        ->expect(['name' => 'Alice'])
        ->toContain('Alice')
        ->and(['name' => 'Bob'])
        ->toContain('Bob')
        ->and(['name' => 'Charlie'])
        ->toContain('Charlie');
});
```

#### Default Test Cases

Use `alwaysExpect()` to set assertions that apply to all test cases.

```php
test('all translations meet quality standards', function () {
    prompt('Translate {{message}} to {{language}} in the style {{style}}.')
        ->usingProvider('openai:gpt-4o-mini')
        ->alwaysExpect(['style' => 'friendly'])
        ->toBeJudged('the translation is always accurate and natural')
        ->toBeJudged('the response is always in a friendly tone')
        ->expect(['message' => 'Hello', 'language' => 'es'])
        ->toContain('hola')
        ->expect(['message' => 'Goodbye', 'language' => 'fr'])
        ->toContain('au revoir');
});
```

#### Provider Configuration

Configure providers with specific parameters.

```php
test('creative writing with high temperature', function () {
    $creativeProvider = Provider::create('openai:gpt-4')
        ->temperature(0.9)
        ->maxTokens(500);

    prompt('Write a creative story about {{topic}}.')
        ->usingProvider($creativeProvider)
        ->expect(['topic' => 'space exploration'])
        ->toContain('space');
});
```

#### Global Provider Registration

Register providers once and reuse them across tests.

```php
provider('openai-gpt4')
    ->id('openai:gpt-4')
    ->temperature(0.7)
    ->maxTokens(2000);

test('uses registered provider', function () {
    prompt('Hello')
        ->usingProvider('openai-gpt4')
        ->expect()
        ->toContain('Hi');
});
```

#### Advanced Assertions

Combine multiple assertion types.

```php
test('response meets multiple criteria', function () {
    prompt('Generate a user profile as JSON with name, email, and age.')
        ->usingProvider('openai:gpt-4o-mini')
        ->expect()
        ->toContainJson()
        ->toContainAll(['name', 'email', 'age'])
        ->toBeJudged('The JSON should be well-structured and include all required fields.');
});
```

#### LLM-Based Evaluation

Use AI to evaluate response quality.

```php
test('response quality meets standards', function () {
    prompt('Explain machine learning to a beginner.')
        ->usingProvider('openai:gpt-4o-mini')
        ->expect()
        ->toBeJudged('The explanation should be clear, accurate, use simple language, and include examples.', threshold: 0.85);
});
```

#### Structured JSON Output Testing

Test structured JSON outputs from LLMs, particularly useful with OpenAI's Responses API and structured output features.

```php
// Register a provider with structured output schema
provider('person-extractor', static fn (Provider $provider): Provider => $provider
    ->id('openai:responses:gpt-4o-mini')
    ->config([
        'response_format' => [
            'name' => 'person_info',
            'type' => 'json_schema',
            'strict' => true,
            'schema' => [
                'type' => 'object',
                'properties' => [
                    'name' => ['type' => 'string'],
                    'age' => ['type' => 'number'],
                    'city' => ['type' => 'string'],
                ],
                'required' => ['name', 'age', 'city'],
                'additionalProperties' => false,
            ],
        ],
    ]));

test('extracts person info with full validation', function () {
    prompt('Extract the person info from this text: {{text}}')
        ->describe('Testing structured JSON output')
        ->usingProvider('person-extractor')
        ->expect(['text' => 'John Doe is 30 years old and lives in Amsterdam.'])
        // Validate structure
        ->toMatchJsonStructure(['name', 'age', 'city'])
        // Validate specific values
        ->toHaveJsonFragment(['name' => 'John Doe', 'city' => 'Amsterdam'])
        // Validate types
        ->toHaveJsonType('name', 'string')
        ->toHaveJsonType('age', 'number')
        // Validate exact match
        ->toEqualJson([
            'name' => 'John Doe',
            'age' => 30,
            'city' => 'Amsterdam',
        ]);
});

// Testing array outputs with nested structures
provider('people-extractor', static fn (Provider $provider): Provider => $provider
    ->id('openai:responses:gpt-4o-mini')
    ->config([
        'response_format' => [
            'name' => 'people_list',
            'type' => 'json_schema',
            'strict' => true,
            'schema' => [
                'type' => 'object',
                'properties' => [
                    'people' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'name' => ['type' => 'string'],
                                'role' => ['type' => 'string'],
                            ],
                            'required' => ['name', 'role'],
                        ],
                    ],
                ],
                'required' => ['people'],
            ],
        ],
    ]));

test('extracts multiple people with array validation', function () {
    prompt('Extract all people from: {{text}}')
        ->usingProvider('people-extractor')
        ->expect(['text' => 'The team has Mike (developer) and Sarah (designer).'])
        // Validate array structure with wildcard
        ->toMatchJsonStructure([
            'people' => [
                '*' => ['name', 'role'],
            ],
        ])
        // Validate array item access
        ->toHaveJsonPath('people.0.name')
        ->toHaveJsonPath('people.1.name')
        // Validate all items have specific type
        ->toHaveJsonType('people', 'array')
        ->toHaveJsonType('people.*.name', 'string')
        ->toHaveJsonType('people.*.role', 'string');
});
```

#### Complex Example

A comprehensive example showing multiple features together.

```php
    // Register global providers
provider('support-gpt4')
    ->id('openai:gpt-4')
    ->temperature(0.3);
    
provider('support-claude')
    ->id('anthropic:claude-3')
    ->temperature(0.3);

test('customer service prompt evaluation', function () {
    // Test multiple prompts across multiple providers
    prompt(
        'You are a customer support agent. Help the customer with: {{issue}}',
        'As a support agent, assist with: {{issue}}'
    )
        ->describe('Customer service prompt evaluation')
        ->usingProvider('support-gpt4', 'support-claude')
        ->expect(['issue' => 'refund request'])
        ->toContainAll(['refund', 'help'], strict: false)
        ->toBeJudged('Response should be professional, empathetic, and helpful.', threshold: 0.8)
        ->and(['issue' => 'product question'])
        ->toContainAny(['product', 'feature', 'specification'])
        ->toBeJudged('Response should accurately answer the product question.');
});
```

### CLI Options

#### `--output`

Save promptfoo evaluation results to a directory. Useful for debugging and analysis.

```bash
# Use default output directory (prompt-tests-output/)
vendor/bin/pest --output

# Specify custom output directory
vendor/bin/pest --output=my-results/

# Alternative syntax
vendor/bin/pest --output my-results/
```

The output directory will contain HTML reports and JSON data from promptfoo evaluations.

## Credits & License

**Created by:** Kevin Pijning

**Built on the shoulders of giants:**

- [Pest](https://pestphp.com) - The elegant PHP testing framework
- [promptfoo](https://www.promptfoo.dev/) - LLM evaluation framework
- [Symfony Components](https://symfony.com) - Process and YAML handling

**License:** MIT License

See the [LICENSE](LICENSE.md) file for full details.

---

**Ready to start testing your prompts?** Install the plugin and write your first test in under a minute. Happy testing!

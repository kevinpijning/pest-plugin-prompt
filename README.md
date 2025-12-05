# Pest Plugin for Prompt Testing

[![Tests](https://img.shields.io/github/actions/workflow/status/kevinpijning/pest-plugin-prompt/tests.yml?label=tests&style=flat-square)](https://github.com/pestphp/pest-plugin-prompt/actions)
[![PHP Version](https://img.shields.io/packagist/php-v/kevinpijning/pest-plugin-prompt?style=flat-square)](https://packagist.org/packages/pestphp/pest-plugin-prompt)
[![License](https://img.shields.io/packagist/l/kevinpijning/pest-plugin-prompt?style=flat-square)](https://github.com/pestphp/pest-plugin-prompt/blob/main/LICENSE)
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
    - [`toBeJudged()`](#tobejudged)
  - [Provider Configuration](#provider-configuration)
  - [Usage Examples](#usage-examples)
  - [CLI Options](#cli-options)
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
use \KevinPijning\Prompt\Api\Provider;
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
use \KevinPijning\Prompt\Api\Provider;

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

See the [LICENSE](LICENSE) file for full details.

---

**Ready to start testing your prompts?** Install the plugin and write your first test in under a minute. Happy testing!

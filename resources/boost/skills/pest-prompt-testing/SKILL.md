---
name: pest-prompt-testing
description: Test AI/LLM prompts with PestPHP's fluent API using promptfoo. Use when writing, debugging, or extending LLM prompt evaluation tests, configuring providers, creating assertion groups, or validating structured AI outputs.
---

# Pest Prompt Testing

## When to use this skill

Use this skill when:

- Writing or modifying Pest tests that evaluate LLM prompts
- Configuring AI providers (OpenAI, Anthropic, etc.) for prompt testing
- Creating assertion groups or reusable assertion patterns
- Debugging failing prompt evaluations
- Validating structured JSON outputs from LLMs
- Working with the `prompt()`, `provider()`, or `assertion()` functions

## Requirements

PHP 8.3+, Pest 4.0+, Node.js/npm (for promptfoo), LLM provider API keys as environment variables.

## Core Functions

### `prompt(string ...$prompts)`

Create an evaluation. Use `{{variable}}` for interpolation. Accepts multiple prompts.

```php
// Single prompt
prompt('Greet {{name}} warmly.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['name' => 'Alice'])
    ->toContain('Alice');

// Multiple prompts (tested against each other)
prompt(
    'You are a helpful assistant.',
    'You are a professional assistant.'
)
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContain('assistant');
```

### `provider(string $name, ?callable $config = null)`

Register a reusable provider. Returns a chainable `Provider` instance.

```php
provider('openai-gpt4')
    ->id('openai:gpt-4')
    ->temperature(0.7)
    ->maxTokens(2000);

// Use in tests
prompt('Hello')
    ->usingProvider('openai-gpt4')
    ->expect()
    ->toContain('Hi');
```

### `assertion(string $name, ?callable $config = null)`

Register a reusable assertion group. Returns a chainable instance for defining shared assertions.

```php
// Fluent definition
assertion('be nice')
    ->toBeJudged('friendly')
    ->toContain('please');

// With parameters
assertion('be kind', function (TestCase $tc, string $tone): void {
    $tc->toBeJudged("response is {$tone} and helpful")
       ->toContain($tone);
});

// Usage via to()/group() or magic methods
prompt('Explain {{topic}}.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['topic' => 'AI'])
    ->to('be nice')
    ->toBeKind(['tone' => 'friendly']);
```

## Evaluation Methods

### `describe(string $description)`

Add description for test output.

### `usingProvider(string|Provider|callable ...$providers)`

Set provider(s). Accepts IDs, `Provider` instances, callables, or registered names. Default: `openai:gpt-4o-mini`.

### `alwaysExpect(array $defaultVariables = [], ?callable $callback = null)`

Set default assertions and variables applied to ALL test cases. Returns a `TestCase` for chaining.

```php
prompt('Translate {{message}} to {{language}}.')
    ->usingProvider('openai:gpt-4o-mini')
    ->alwaysExpect(['message' => 'Hello World!'])
    ->toBeJudged('the language is always a friendly variant')
    ->expect(['language' => 'es'])
    ->toContain('hola');
```

### `expect(array $variables = [], ?callable $callback = null)`

Create a test case with variables for `{{variable}}` substitution.

```php
prompt('Greet {{name}} warmly.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['name' => 'Alice'], function (TestCase $testCase) {
        $testCase->toContain('Alice')->toBeJudged('friendly');
    });
```

### `and(array $variables, ?callable $callback = null)`

Chain additional test cases.

```php
prompt('Greet {{name}}.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['name' => 'Alice'])
    ->toContain('Alice')
    ->and(['name' => 'Bob'])
    ->toContain('Bob');
```

### `to(callable|string $callback, array $args = [])` / `group(callable|string $callback, array $args = [])`

Group assertions using a callback, invokable class FQN, or named assertion group.

```php
// Named group
->to('be nice')

// Callback
->to(function (TestCase $tc) {
    $tc->toContain('quantum')->toBeJudged('clear and accurate');
})

// Invokable class
->to(QualityAssertions::class)
```

## Assertion Methods

### Text Matching

- `toContain(string $text, bool $strict = false)` - Contains text (case-insensitive by default)
- `toContainAll(array $texts, bool $strict = false)` - Contains all strings
- `toContainAny(array $texts, bool $strict = false)` - Contains at least one string
- `startsWith(string $prefix)` - Starts with prefix
- `toMatchRegex(string $pattern)` - Matches regex pattern

### Format Validators (contains)

- `toContainJson(?array $schema = null)` - Contains valid JSON
- `toContainHtml()` - Contains valid HTML
- `toContainSql(?array $config = null)` - Contains valid SQL
- `toContainXml(?array $config = null)` - Contains valid XML

### Format Validators (is)

- `toBeJson(?array $schema = null)` - Is valid JSON (with optional JSON schema)
- `toBeHtml()` - Is valid HTML
- `toBeSql(?array $config = null)` - Is valid SQL
- `toBeXml(?array $config = null)` - Is valid XML

### Equality

- `toEqual(mixed $value)` - Exact equality
- `toBe(mixed $value)` - Alias of `toEqual()`

### JSON Validation

- `toEqualJson(array $expected)` - Exact JSON match (key order ignored)
- `toMatchJsonStructure(array $structure)` - Validates keys exist (supports `*` wildcard for arrays)
- `toHaveJsonFragment(array $fragment)` - Contains key-value pairs
- `toHaveJsonFragments(array $fragments)` - Contains all fragments
- `toHaveJsonPath(string $path, mixed $expected = null)` - Value at path (dot notation, array indices, wildcards)
- `toHaveJsonPaths(array $paths)` - Multiple paths exist/match
- `toHaveJsonType(string $path, string $type)` - Type at path (`string`, `number`, `boolean`, `array`, `object`, `null`)

```php
prompt('Extract person info from: {{text}}')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['text' => 'John is 30 years old from Amsterdam'])
    ->toMatchJsonStructure(['name', 'age', 'city'])
    ->toHaveJsonFragment(['name' => 'John'])
    ->toHaveJsonPath('age', 30)
    ->toHaveJsonType('name', 'string');
```

### Similarity Metrics

- `toBeSimilar(string|array $expected, ?float $threshold = null, ?string $provider = null)` - Semantic similarity via embeddings
- `toHaveLevenshtein(string $expected, ?float $threshold = null)` - Edit distance
- `toHaveRougeN(int $n, string|array $expected, ?float $threshold = null)` - ROUGE-N score
- `toHaveFScore(string|array $expected, ?float $threshold = null)` - F-score
- `toHavePerplexity(?float $threshold = null)` - Perplexity below threshold
- `toHavePerplexityScore(?float $threshold = null)` - Normalized perplexity score

### LLM-Based Evaluation

- `toBeJudged(string $rubric, ?float $threshold = null, ?string $provider = null)` - LLM judges response against rubric
- `toBeScoredByPi(string $rubric, ?float $threshold = null)` - Pi Labs preference scoring
- `toBeClassified(string $provider, string $expectedClass, ?float $threshold = null)` - HuggingFace classification
- `toBeRefused()` - Model refused the request

### Performance

- `toHaveCost(float $maxCost)` - Max inference cost
- `toHaveLatency(int $maxMilliseconds)` - Max response latency

### Function/Tool Calls

- `toHaveValidFunctionCall(?array $schema = null)` - Valid function call
- `toHaveValidOpenaiFunctionCall(?array $schema = null)` - Valid OpenAI function call
- `toHaveValidOpenaiToolsCall(?array $schema = null)` - Valid OpenAI tools call
- `toHaveToolCallF1(array $expected, ?float $threshold = null)` - Tool call F1 score

### Finish Reason

- `toHaveFinishReason(FinishReason|string $reason)` - Stopped for expected reason
- `toHaveFinishReasonStop()` - Natural completion
- `toHaveFinishReasonLength()` - Token limit reached
- `toHaveFinishReasonContentFilter()` - Content filter triggered
- `toHaveFinishReasonToolCalls()` - Tool calls made

### Custom Validation

- `toPassJavascript(string $code, ?float $threshold = null, ?array $config = null)` - Custom JS validation
- `toPassPython(string $code, ?float $threshold = null, ?array $config = null)` - Custom Python validation
- `toPassWebhook(string $url)` - Webhook returns `{pass: true}`

### Tracing

- `toHaveTraceSpanCount(array $patterns, ?int $min = null, ?int $max = null)`
- `toHaveTraceSpanDuration(array $patterns, ?float $percentile = null, ?float $maxDuration = null)`
- `toHaveTraceErrorSpans()`

### `not` Modifier

Negate any assertion: `->not->toContain('condolences')`, `->not->toBeRefused()`

## Provider Configuration

Chain methods: `id()`, `label()`, `temperature()`, `maxTokens()`, `topP()`, `frequencyPenalty()`, `presencePenalty()`, `stop()`, `config()`.

`config()` accepts an array (replaces) or closure (receives current config for merging):

```php
provider()
    ->id('openai:gpt-4')
    ->config(['existing' => 'value'])
    ->config(fn (array $config) => [...$config, 'apiKey' => 'custom-key']);
```

### Extending Provider

The `Provider` class uses Pest's `Extendable` trait:

```php
provider()->extend('withJsonMode', function (Provider $provider): void {
    $provider->config(fn (array $config) => [
        ...$config,
        'response_format' => ['type' => 'json_object'],
    ]);
});

provider()
    ->id('openai:gpt-4')
    ->withJsonMode()
    ->temperature(0.7);
```

### Presets

```php
provider()->extend('preset', function (Provider $provider, string $name): void {
    match ($name) {
        'creative' => $provider->temperature(0.9)->topP(0.95),
        'precise' => $provider->temperature(0.1)->topP(0.1),
        default => null,
    };
});
```

## Invokable Assertion Classes

Create reusable assertion patterns as invokable classes:

```php
class QualityAssertions
{
    public function __invoke(TestCase $testCase): void
    {
        $testCase
            ->toBeJudged('professional and accurate')
            ->toHaveLatency(2000)
            ->not->toBeRefused();
    }
}

prompt('Explain {{topic}}.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['topic' => 'AI'])
    ->to(QualityAssertions::class);
```

## Structured JSON Output Testing

```php
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

test('extracts person info', function () {
    prompt('Extract person info from: {{text}}')
        ->usingProvider('person-extractor')
        ->expect(['text' => 'John Doe is 30 years old and lives in Amsterdam.'])
        ->toMatchJsonStructure(['name', 'age', 'city'])
        ->toHaveJsonFragment(['name' => 'John Doe', 'city' => 'Amsterdam'])
        ->toHaveJsonType('name', 'string')
        ->toHaveJsonType('age', 'number');
});
```

## Best Practices

- Register providers with `provider()` for reuse across tests
- Use `describe()` for clarity in test output
- Chain multiple assertion types (content + format + LLM evaluation)
- Use `alwaysExpect()` for quality standards that apply to all test cases
- Create invokable classes for reusable assertion patterns
- Test multiple providers for consistency across models
- Use named assertion groups for team-wide quality standards

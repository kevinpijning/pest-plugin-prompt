## Pest Plugin Prompt

This package provides LLM prompt testing for PestPHP with a fluent API. Tests evaluate AI prompts against multiple providers using promptfoo.

### Basic Usage

@verbatim
<code-snippet name="Basic test" lang="php">
test('greeting works', function () {
    prompt('Greet \{\{name\}\} warmly.')
        ->usingProvider('openai:gpt-4o-mini')
        ->expect(['name' => 'Alice'])
        ->toContain('Alice');
});
</code-snippet>
@endverbatim

### Core Functions

**`prompt(string ...$prompts)`**: Create evaluation. Use `\{\{variable\}\}` for interpolation. Accepts multiple prompts.

**`provider(string $name, ?callable $config = null)`**: Register reusable provider. Returns chainable `Provider` instance.

@verbatim
<code-snippet name="Provider registration" lang="php">
use \KevinPijning\Prompt\Provider;

provider('openai-gpt4')
    ->id('openai:gpt-4')
    ->temperature(0.7)
    ->maxTokens(2000);
</code-snippet>
@endverbatim

### Evaluation Methods

**`describe(string $description)`**: Add description for test output.

**`usingProvider(string|Provider|callable ...$providers)`**: Set provider(s). Accepts IDs, `Provider` instances, callables, or registered names.

**`alwaysExpect(array $defaultVariables = [], ?callable $callback = null)`**: Set default assertions and variables that apply to all test cases. Returns a `TestCase` for chaining assertions.

**`expect(array $variables = [], ?callable $callback = null)`**: Create test case with variables for `\{\{variable\}\}` substitution.

**`and(array $variables, ?callable $callback = null)`**: Chain additional test cases.

@verbatim
<code-snippet name="Multiple test cases" lang="php">
prompt('Greet \{\{name\}\}.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['name' => 'Alice'])
    ->toContain('Alice')
    ->and(['name' => 'Bob'])
    ->toContain('Bob');
</code-snippet>
@endverbatim

### Assertion Methods

**Text matching**: `toContain(string $text, bool $strict = false)`, `toContainAll(array $texts, bool $strict = false)`, `toContainAny(array $texts, bool $strict = false)`, `startsWith(string $prefix)`, `toMatchRegex(string $pattern)`.

**Format validators (contains)**: `toContainJson(?array $schema = null)`, `toContainHtml()`, `toContainSql(?array $config = null)`, `toContainXml(?array $config = null)`.

**Format validators (is)**: `toBeJson(?array $schema = null)`, `toBeHtml()`, `toBeSql(?array $config = null)`, `toBeXml(?array $config = null)`.

**Equality**: `toEqual(mixed $value)`, `toBe(mixed $value)`.

**Similarity metrics**: `toBeSimilar(string|array $expected, ?float $threshold = null, ?string $provider = null)`, `toHaveLevenshtein(string $expected, ?float $threshold = null)`, `toHaveRougeN(int $n, string|array $expected, ?float $threshold = null)`, `toHaveFScore(string|array $expected, ?float $threshold = null)`, `toHavePerplexity(?float $threshold = null)`, `toHavePerplexityScore(?float $threshold = null)`.

**Performance**: `toHaveCost(float $maxCost)`, `toHaveLatency(int $maxMilliseconds)`.

**Function/tool calls**: `toHaveValidFunctionCall(?array $schema = null)`, `toHaveValidOpenaiFunctionCall(?array $schema = null)`, `toHaveValidOpenaiToolsCall(?array $schema = null)`, `toHaveToolCallF1(array $expected, ?float $threshold = null)`.

**Finish reason**: `toHaveFinishReason(FinishReason|string $reason)`, `toHaveFinishReasonStop()`, `toHaveFinishReasonLength()`, `toHaveFinishReasonContentFilter()`, `toHaveFinishReasonToolCalls()`. Use `FinishReason` enum for type safety.

**Classification**: `toBeClassified(string $provider, string $expectedClass, ?float $threshold = null)`.

**Scoring**: `toBeJudged(string $rubric, ?float $threshold = null, ?string $provider = null)` (LLM-based), `toBeScoredByPi(string $rubric, ?float $threshold = null)`.

**Refusal detection**: `toBeRefused()`.

**Custom validation**: `toPassJavascript(string $code, ?float $threshold = null, ?array $config = null)`, `toPassPython(string $code, ?float $threshold = null, ?array $config = null)`, `toPassWebhook(string $url)`.

**Tracing**: `toHaveTraceSpanCount(array $patterns, ?int $min = null, ?int $max = null)`, `toHaveTraceSpanDuration(array $patterns, ?float $percentile = null, ?float $maxDuration = null)`, `toHaveTraceErrorSpans()`.

@verbatim
<code-snippet name="Assertions" lang="php">
prompt('Explain AI.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContain('artificial intelligence')
    ->toContainAll(['machine', 'learning'])
    ->toContainJson()
    ->toBeJudged('Should be clear and accurate.', threshold: 0.8);
</code-snippet>
@endverbatim

### Provider Configuration

Chain methods: `id()`, `label()`, `temperature()`, `maxTokens()`, `topP()`, `frequencyPenalty()`, `presencePenalty()`, `stop()`, `config()`.

@verbatim
<code-snippet name="Provider config" lang="php">
use \KevinPijning\Prompt\Provider;

$provider = Provider::create('openai:gpt-4')
    ->temperature(0.7)
    ->maxTokens(2000);
</code-snippet>
@endverbatim

### Best Practices

- Register providers with `provider()` for reuse
- Chain multiple assertions
- Use `describe()` for clarity
- Test multiple providers for consistency
- Combine assertion types (content + format + LLM evaluation)

### Requirements

PHP 8.3+, Pest 4.0+, Node.js/npm, LLM provider API keys (environment variables).

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

**`provider(string $name)`**: Register reusable provider. Returns chainable `Provider` instance.

@verbatim
<code-snippet name="Provider registration" lang="php">
use \KevinPijning\Prompt\Api\Provider;

provider('openai-gpt4')
    ->id('openai:gpt-4')
    ->temperature(0.7)
    ->maxTokens(2000);
</code-snippet>
@endverbatim

### Evaluation Methods

**`describe(string $description)`**: Add description for test output.

**`usingProvider(string|Provider ...$providers)`**: Set provider(s). Accepts IDs, `Provider` instances, or registered names.

**`expect(array $variables = [])`**: Create test case with variables for `\{\{variable\}\}` substitution.

**`and(array $variables)`**: Chain additional test cases.

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

**`toContain(string $text, bool $strict = false, ?float $threshold = null)`**: Assert response contains text. Case-insensitive by default.

**`toContainAll(array $texts, bool $strict = false, ?float $threshold = null)`**: Assert all texts are present.

**`toContainAny(array $texts, bool $strict = false, ?float $threshold = null)`**: Assert any text is present.

**Format validators**: `toContainJson()`, `toContainHtml()`, `toContainSql()`, `toContainXml()`.

**`toBeJudged(string $rubric, ?float $threshold = null, array $options = [])`**: LLM-based evaluation with natural language rubric.

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
use \KevinPijning\Prompt\Api\Provider;

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

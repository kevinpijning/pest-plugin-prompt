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

**`provider(string $name, ?callable $config = null)`**: Register reusable provider. Returns chainable `Provider` instance with methods: `id()`, `label()`, `temperature()`, `maxTokens()`, `topP()`, `frequencyPenalty()`, `presencePenalty()`, `stop()`, `config()`.

**`assertion(string $name, ?callable $config = null)`**: Register reusable assertion group. Returns chainable instance for defining shared assertions. Use via `->to('group name')` or magic `->toGroupName()`.

### Evaluation Chain

`prompt()` → `describe()` → `usingProvider()` → `alwaysExpect()` → `expect()` → assertions → `and()` → assertions

- **`alwaysExpect()`**: Default assertions applied to ALL test cases.
- **`expect()`**: Create test case with `\{\{variable\}\}` substitution.
- **`and()`**: Chain additional test cases.
- **`to()` / `group()`**: Group assertions via callback, invokable class, or named group.

### Assertion Categories

- **Text**: `toContain()`, `toContainAll()`, `toContainAny()`, `startsWith()`, `toMatchRegex()`
- **Format (contains)**: `toContainJson()`, `toContainHtml()`, `toContainSql()`, `toContainXml()`
- **Format (is)**: `toBeJson()`, `toBeHtml()`, `toBeSql()`, `toBeXml()`
- **Equality**: `toEqual()`, `toBe()`
- **JSON**: `toEqualJson()`, `toMatchJsonStructure()`, `toHaveJsonFragment()`, `toHaveJsonPath()`, `toHaveJsonType()`
- **Similarity**: `toBeSimilar()`, `toHaveLevenshtein()`, `toHaveRougeN()`, `toHaveFScore()`
- **LLM Evaluation**: `toBeJudged()`, `toBeScoredByPi()`, `toBeClassified()`, `toBeRefused()`
- **Performance**: `toHaveCost()`, `toHaveLatency()`
- **Tool Calls**: `toHaveValidFunctionCall()`, `toHaveValidOpenaiToolsCall()`, `toHaveToolCallF1()`
- **Finish Reason**: `toHaveFinishReason()`, `toHaveFinishReasonStop()`, `toHaveFinishReasonLength()`
- **Custom**: `toPassJavascript()`, `toPassPython()`, `toPassWebhook()`
- **Negation**: `->not->toContain()`, `->not->toBeRefused()`

### Requirements

PHP 8.3+, Pest 4.0+, Node.js/npm, LLM provider API keys (environment variables).

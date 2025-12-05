## Pest Plugin Prompt

This package provides LLM prompt testing capabilities for PestPHP, enabling developers to write fluent, expressive tests for evaluating AI model prompts using Pest's familiar API style. The plugin integrates with promptfoo under the hood to execute evaluations against multiple LLM providers.

### Features

- **Fluent API**: Write tests using a chainable, Pest-style API that feels natural
- **Multiple Providers**: Test prompts against multiple LLM providers (OpenAI, Anthropic, etc.) in a single test
- **Variable Interpolation**: Use `\{\{variable\}\}` syntax in prompts for dynamic content
- **Content Assertions**: Validate responses with text matching, format validation (JSON, HTML, SQL, XML), and LLM-based evaluation
- **Provider Configuration**: Configure provider settings like temperature, max tokens, and more
- **Automatic Cleanup**: Temporary files are managed automatically

### Basic Usage

Tests are written using the `prompt()` function which returns an `Evaluation` object that can be chained with various methods:

@verbatim
<code-snippet name="Basic prompt test" lang="php">
test('greeting prompt works correctly', function () {
    prompt('You are a helpful assistant. Greet \{\{name\}\} warmly.')
        ->usingProvider('openai:gpt-4o-mini')
        ->expect(['name' => 'Alice'])
        ->toContain('Alice');
});
</code-snippet>
@endverbatim

### Core Functions

#### `prompt()`

Create a new evaluation with one or more prompts. Use `\{\{variable\}\}` syntax for variable interpolation:

@verbatim
<code-snippet name="Single prompt" lang="php">
prompt('You are a helpful assistant.');
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Multiple prompts" lang="php">
prompt(
    'You are a helpful assistant.',
    'You are a professional assistant.'
);
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Prompt with variables" lang="php">
prompt('Greet \{\{name\}\} warmly.');
</code-snippet>
@endverbatim

#### `provider()`

Register a global provider that can be reused across multiple tests. Returns a `Provider` instance that can be chained:

@verbatim
<code-snippet name="Register provider" lang="php">
use \KevinPijning\Prompt\Api\Provider;

provider('openai-gpt4')
    ->id('openai:gpt-4')
    ->temperature(0.7)
    ->maxTokens(2000);
</code-snippet>
@endverbatim

### Evaluation Methods

#### `describe()`

Add a description to your evaluation for better test output:

@verbatim
<code-snippet name="Add description" lang="php">
prompt('You are a helpful assistant.')
    ->describe('Tests basic assistant greeting')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContain('Hello');
</code-snippet>
@endverbatim

#### `usingProvider()`

Specify which LLM provider(s) to use. Accepts provider IDs, `Provider` instances, or registered provider names:

@verbatim
<code-snippet name="Single provider" lang="php">
prompt('Hello')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContain('Hi');
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Multiple providers" lang="php">
prompt('What is 2+2?')
    ->usingProvider('openai:gpt-4o-mini', 'anthropic:claude-3')
    ->expect()
    ->toContain('4');
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Provider instance" lang="php">
use \KevinPijning\Prompt\Api\Provider;

$provider = Provider::create('openai:gpt-4')
    ->temperature(0.7);

prompt('Hello')
    ->usingProvider($provider)
    ->expect()
    ->toContain('Hi');
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Registered provider" lang="php">
provider('my-provider')->id('openai:gpt-4');

prompt('Hello')
    ->usingProvider('my-provider')
    ->expect()
    ->toContain('Hi');
</code-snippet>
@endverbatim

#### `expect()`

Create a test case with variables that will be substituted into your prompt template:

@verbatim
<code-snippet name="Test case with variables" lang="php">
prompt('Greet \{\{name\}\} warmly.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['name' => 'Alice'])
    ->toContain('Alice');
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Multiple variables" lang="php">
prompt('\{\{greeting\}\}, \{\{name\}\}!')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['greeting' => 'Hello', 'name' => 'Bob'])
    ->toContain('Hello')
    ->toContain('Bob');
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Empty variables" lang="php">
prompt('You are a helpful assistant.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContain('assistant');
</code-snippet>
@endverbatim

#### `and()`

Chain multiple test cases for the same evaluation:

@verbatim
<code-snippet name="Multiple test cases" lang="php">
prompt('Greet \{\{name\}\} warmly.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect(['name' => 'Alice'])
    ->toContain('Alice')
    ->and(['name' => 'Bob'])
    ->toContain('Bob')
    ->and(['name' => 'Charlie'])
    ->toContain('Charlie');
</code-snippet>
@endverbatim

### Assertion Methods

#### `toContain()`

Assert that the response contains specific text. Case-insensitive by default:

@verbatim
<code-snippet name="Basic contains" lang="php">
prompt('What is the capital of France?')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContain('Paris');
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Case-sensitive contains" lang="php">
prompt('What is the capital of France?')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContain('Paris', strict: true);
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Contains with threshold" lang="php">
prompt('Explain quantum computing.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContain('quantum', threshold: 0.8);
</code-snippet>
@endverbatim

#### `toContainAll()`

Assert that the response contains all of the specified strings:

@verbatim
<code-snippet name="Contains all" lang="php">
prompt('Describe a healthy meal.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContainAll(['protein', 'vegetables', 'grains']);
</code-snippet>
@endverbatim

#### `toContainAny()`

Assert that the response contains at least one of the specified strings:

@verbatim
<code-snippet name="Contains any" lang="php">
prompt('What is the weather like?')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContainAny(['sunny', 'rainy', 'cloudy']);
</code-snippet>
@endverbatim

#### Format Validators

Validate that responses contain valid structured data:

@verbatim
<code-snippet name="JSON validation" lang="php">
prompt('Return user data as JSON: name, age, email')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContainJson();
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="HTML validation" lang="php">
prompt('Generate an HTML list of fruits')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContainHtml();
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="SQL validation" lang="php">
prompt('Write a SQL query to select all users')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContainSql();
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="XML validation" lang="php">
prompt('Generate XML for a product catalog')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContainXml();
</code-snippet>
@endverbatim

#### `toBeJudged()`

Use an LLM to evaluate the response against a natural language rubric:

@verbatim
<code-snippet name="LLM-based evaluation" lang="php">
prompt('Explain quantum computing to a beginner.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeJudged('The explanation should be clear, accurate, and use simple language.');
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="LLM evaluation with threshold" lang="php">
prompt('Write a product description.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toBeJudged('The description should be persuasive and highlight key features.', threshold: 0.8);
</code-snippet>
@endverbatim

### Provider Configuration

When creating or configuring providers, you can chain these methods:

@verbatim
<code-snippet name="Provider configuration" lang="php">
use \KevinPijning\Prompt\Api\Provider;

$provider = Provider::create('openai:gpt-4')
    ->label('Custom OpenAI')
    ->temperature(0.7)
    ->maxTokens(2000)
    ->topP(0.9)
    ->frequencyPenalty(0.5)
    ->presencePenalty(0.3)
    ->stop(['\n', 'Human:', 'AI:'])
    ->config(['apiKey' => 'custom-key']);
</code-snippet>
@endverbatim

### Best Practices

1. **Use `provider()` for reusable configurations**: Register providers once and reuse them across tests
2. **Chain assertions**: Multiple assertions can be chained together for comprehensive validation
3. **Use `describe()` for clarity**: Add descriptions to help identify which evaluation failed
4. **Test multiple providers**: Compare responses across different LLM providers to ensure consistency
5. **Use variable interpolation**: Leverage `\{\{variable\}\}` syntax to test prompts with different inputs
6. **Combine assertion types**: Mix content assertions with format validators and LLM-based evaluation for thorough testing

### Common Patterns

@verbatim
<code-snippet name="Multiple prompts and providers" lang="php">
prompt(
    'You are a customer support agent. Help the customer with: \{\{issue\}\}',
    'As a support agent, assist with: \{\{issue\}\}'
)
    ->describe('Customer service prompt evaluation')
    ->usingProvider('openai:gpt-4', 'anthropic:claude-3')
    ->expect(['issue' => 'refund request'])
    ->toContainAll(['refund', 'help'], strict: false)
    ->toBeJudged('Response should be professional, empathetic, and helpful.', threshold: 0.8);
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Complex validation" lang="php">
prompt('Generate a user profile as JSON with name, email, and age.')
    ->usingProvider('openai:gpt-4o-mini')
    ->expect()
    ->toContainJson()
    ->toContainAll(['name', 'email', 'age'])
    ->toBeJudged('The JSON should be well-structured and include all required fields.');
</code-snippet>
@endverbatim

### Requirements

- PHP 8.3 or higher
- Pest 4.0 or higher
- Node.js and npm (for promptfoo execution)
- API keys for LLM providers (set as environment variables)

### Important Notes

- The plugin automatically registers with Pest via package discovery
- Temporary files are automatically cleaned up after evaluation
- Use `--output` CLI option to save promptfoo evaluation results: `vendor/bin/pest --output=my-results/`
- Variable interpolation uses `\{\{variable\}\}` syntax (double curly braces)
- All methods return the same object instance for fluent chaining


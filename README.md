# Pest Plugin for Prompt Testing

[![Tests](https://img.shields.io/github/actions/workflow/status/kevinpijning/pest-plugin-prompt/tests.yml?label=tests&style=flat-square)](https://github.com/pestphp/pest-plugin-prompt/actions)
[![PHP Version](https://img.shields.io/packagist/php-v/kevinpijning/pest-plugin-prompt?style=flat-square)](https://packagist.org/packages/pestphp/pest-plugin-prompt)
[![License](https://img.shields.io/packagist/l/kevinpijning/pest-plugin-prompt?style=flat-square)](https://github.com/pestphp/pest-plugin-prompt/blob/main/LICENSE)
[![Pest](https://img.shields.io/badge/Pest-4.0+-ff69b4?style=flat-square)](https://pestphp.com)

**Test your AI prompts with confidence using Pest's elegant syntax.**

This plugin brings LLM prompt testing to your Pest test suite, powered by [promptfoo](https://www.promptfoo.dev/) under the hood. Write fluent, expressive tests for evaluating AI model prompts using the familiar Pest API you already love.

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

# PROJECT KNOWLEDGE BASE

**Generated:** 2026-01-05  
**Commit:** e3b8aa0  
**Branch:** main

## OVERVIEW

Pest 4.0 plugin for LLM prompt evaluation via `promptfoo` CLI. PHP 8.3+, trait-heavy assertion system, bridges Pest fluent API to Node.js tooling.

## STRUCTURE

```
pest-plugin-prompts/
├── src/
│   ├── Concerns/        # 17 assertion traits (CanBeJudged, CanContain, CanValidateJson, etc.)
│   ├── Internal/        # Registries, lifecycle, result mapping
│   │   └── Results/     # DTOs mapping promptfoo JSON → PHP objects
│   ├── Promptfoo/       # Bridge to npx promptfoo (config builder, client, cache merger)
│   ├── Helpers/         # Path utilities, global functions
│   ├── Plugin.php       # Pest plugin entry (Bootable, HandlesArguments, Terminable)
│   ├── Autoload.php     # Registers Promptable trait via Plugin::uses()
│   ├── Evaluation.php   # Fluent API entry: prompt() → Evaluation
│   └── TestCase.php     # Test case with all assertion traits composed
├── tests/               # Mirrors src/ structure exactly
└── resources/boost/     # Blade templates for guideline generation
```

## WHERE TO LOOK

| Task | Location | Notes |
|------|----------|-------|
| Add new assertion | `src/Concerns/` | Create trait, add to TestCase |
| Modify promptfoo config | `src/Promptfoo/ConfigBuilder.php` | Generates YAML for promptfoo |
| Change evaluation lifecycle | `src/Internal/TestLifecycle.php` | Called in Plugin::boot afterEach |
| Add CLI argument | `src/Plugin.php` | handleArguments() method |
| Register global provider | `src/Helpers/Functions.php` | provider() function |
| Result parsing | `src/Promptfoo/EvaluationResultBuilder.php` | Maps JSON → EvaluationResult |
| Parallel test support | `src/Promptfoo/CacheMerger.php` | Merges worker caches |

## CONVENTIONS

- **Trait composition**: All assertions in `src/Concerns/`, composed into `TestCase`
- **Registry pattern**: `EvaluationRegistry`, `ProviderRegistry`, `AssertionGroupRegistry` manage state
- **Post-test evaluation**: Unlike typical Pest plugins, evaluations run in `afterEach` hook
- **Test naming**: Tests mirror src structure exactly (e.g., `src/Concerns/CanBeJudged.php` → `tests/Concerns/CanBeJudgedTest.php`)
- **No index files**: No `__init__` or barrel exports; direct class imports

## ANTI-PATTERNS (THIS PROJECT)

- **Never suppress types**: No `@ts-ignore`, `as any` equivalents. PHPStan level 8 enforced
- **No TODO/FIXME**: Codebase is clean of technical debt markers
- **Don't mock promptfoo**: Tests use real integration or fixture JSON, no mock mode

## UNIQUE STYLES

- **Fluent chain returns**: `prompt()` → `Evaluation` → `TestCase` via method chaining
- **Magic assertion groups**: `assertion('be nice')` enables `->toBeNice()` magic method
- **alwaysExpect()**: Sets default assertions applied to ALL test cases in evaluation
- **Invokable assertion classes**: Support `->to(QualityAssertions::class)` pattern

## COMMANDS

```bash
# Full test suite (rector dry-run + pint + phpstan + pest)
composer test

# Individual commands
composer test:unit       # pest --colors=always
composer test:types      # phpstan analyse --ansi
composer test:lint       # pint --test
composer test:refacto    # rector --dry-run

# Auto-fix code style
composer codestyle       # rector + pint

# Run with output
vendor/bin/pest --output=results/
```

## CORE FLOW

```
prompt('...') 
  → EvaluationRegistry::addEvaluation() 
  → TestCase assertions collected
  → Plugin::boot afterEach triggers TestLifecycle::evaluate()
  → ConfigBuilder generates YAML
  → PromptfooClient runs `npx promptfoo eval`
  → EvaluationResultBuilder parses JSON
  → Pest assertions applied
```

## DEPENDENCIES

| Package | Purpose |
|---------|---------|
| `pestphp/pest` ^4.0 | Test framework |
| `pestphp/pest-plugin` ^4.0 | Plugin interface |
| `symfony/yaml` ^7.3 | Config generation |
| **Runtime**: Node.js | Required for `npx promptfoo` |

## NOTES

- **External dependency**: Requires Node.js at runtime for promptfoo execution
- **Parallel support**: Built-in via `CacheMerger` for `--parallel` flag
- **CI matrix**: Tests PHP 8.3, 8.4, 8.5 with both serial and parallel modes
- **No LSP**: PHP LSP not installed; use grep/AST for code navigation

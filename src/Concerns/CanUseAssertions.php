<?php

declare(strict_types=1);

namespace KevinPijning\Prompt\Concerns;

trait CanUseAssertions
{
    use CanBeClassified;
    use CanBeJudged;
    use CanBeRefused;
    use CanBeScored;
    use CanBeSimilar;
    use CanBeValid;
    use CanContain;
    use CanEnclose;
    use CanEqual;
    use CanHaveCustomValidation;
    use CanHaveFinishReason;
    use CanHaveFunctionCalls;
    use CanHavePerformance;
    use CanHaveTraces;
    use CanMatch;
    use CanValidateJson;
}

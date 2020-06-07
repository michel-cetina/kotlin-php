<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


class Evaluation
{
    /** @var int */
    public $rightPosition;
    /** @var int */
    public $wrongPosition;

    public function __construct(int $rightPosition, int $wrongPosition)
    {
        $this->rightPosition = $rightPosition;
        $this->wrongPosition = $wrongPosition;
    }
}

function evaluateGuess(string $secret, string $guess): Evaluation
{
    if ($secret === $guess) {
        return new Evaluation(\strlen($secret), 0);
    }

    /** @var int[] $usedSecretPositions */
    $usedSecretPositions = [];
    /** @var int[] $usedGuessPositions */
    $usedGuessPositions = [];
    $rightPosition = 0;
    $wrongPosition = 0;

    foreach (\str_split($guess) as $guessCharacterIndex => $guessCharacter) {
        $secretOccurrences = findAllOccurrences($guessCharacter, $secret);

        /** count($var) return value:
         * If var is not an array or an object with implemented Countable interface, 1 will be returned.
         * There is one exception, if var is &null;, 0 will be returned.
         * Caution: count may return 0 for a variable that isn't set,but it may also return 0 for a
         * variable that has been initialized with an empty array. Use isset to test if a variable is set. **/
        if (0 === \count($secretOccurrences)) {
            continue;
        }

        if (\in_array($guessCharacterIndex, $secretOccurrences, true)) {
            $rightPosition++;
            $usedSecretPositions[] = $guessCharacterIndex;
            $usedGuessPositions[] = $guessCharacterIndex;
        }
    }

    foreach (\str_split($guess) as $guessCharacterIndex => $guessCharacter) {
        if (\in_array($guessCharacterIndex, $usedGuessPositions, true)) {
            continue;
        }

        $secretOccurrences = findAllOccurrences($guessCharacter, $secret);
        $filteredOccurrences = \array_filter(
            $secretOccurrences,
            static function ($filteredOccurrence) use ($usedSecretPositions) {
                return !\in_array($filteredOccurrence, $usedSecretPositions, true);
            }
        );

        if (0 === \count($filteredOccurrences)) {
            continue;
        }

        $wrongPosition++;
        $usedSecretPositions[] = array_pop($filteredOccurrences);
    }

    return new Evaluation($rightPosition, $wrongPosition);
}

/**
 * @param string $searchCharacter
 * @param string $givenString
 *
 * @return int[]
 */
function findAllOccurrences(string $searchCharacter, string $givenString): array
{
    /** @var int[] $occurrences */
    $occurrences = [];

    foreach (str_split($givenString) as $index => $character) {
        if ($character === $searchCharacter) {
            $occurrences[] = $searchCharacter;
        }
    }

    return $occurrences;
}

function testEvaluation(string $secret, string $guess, int $rightPosition, int $wrongPosition): void
{
    $evaluation = new Evaluation($rightPosition, $wrongPosition);

    $evaluationGuess = evaluateGuess($secret, $guess);
    var_dump($evaluation == $evaluationGuess);
}


testEvaluation("ABCD", "ABCD", 4, 0);
testEvaluation("ABCD", "CDBA", 0, 4);
testEvaluation("ABCD", "ABDC", 2, 2);
testEvaluation("ABCD", "ABCF", 3, 0);
testEvaluation("DAEF", "FECA", 0, 3);
testEvaluation("ACEB", "BCDF", 1, 1);
testEvaluation("FBAE", "ABCD", 1, 1);
testEvaluation("FBAE", "AFDC", 0, 2);
testEvaluation("FBAE", "CBAE", 3, 0);
testEvaluation("FBAE", "CBFE", 2, 1);
testEvaluation("FBAE", "FBAE", 4, 0);
testEvaluation("EBAC", "ABCD", 1, 2);
testEvaluation("EBAC", "AFCB", 0, 3);
testEvaluation("EBAC", "CBDF", 1, 1);
testEvaluation("EBAC", "EBAC", 4, 0);

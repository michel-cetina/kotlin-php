package mastermind

data class Evaluation(val rightPosition: Int, val wrongPosition: Int)

fun evaluateGuess(secret: String, guess: String): Evaluation {
    // Early return when the player guesses the correct answer
    if (secret == guess) {
        return Evaluation(secret.length, 0)
    }

    val usedSecretPositions = mutableListOf<Int>()
    val usedGuessPositions = mutableListOf<Int>()
    var rightPosition = 0
    var wrongPosition = 0

    for ((guessCharacterIndex, guessCharacter) in guess.withIndex()) {
        val secretOccurrences: List<Int> = findAllOccurrences(guessCharacter, secret)

        if (secretOccurrences.isEmpty()) {
            continue;
        }

        if (secretOccurrences.contains(guessCharacterIndex)) {
            ++rightPosition
            usedSecretPositions.add(guessCharacterIndex)
            usedGuessPositions.add(guessCharacterIndex)
        }
    }

    for ((guessCharacterIndex, guessCharacter) in guess.withIndex()) {
        if (usedGuessPositions.contains(guessCharacterIndex)) {
            continue
        }

        val secretOccurrences: List<Int> = findAllOccurrences(guessCharacter, secret)
        val filteredOccurrences = secretOccurrences.filter { !usedSecretPositions.contains(it) }

        if (filteredOccurrences.isEmpty()) {
            continue;
        }

        ++wrongPosition
        usedSecretPositions.add(filteredOccurrences.first())
    }

    return Evaluation(rightPosition, wrongPosition)
}

fun findAllOccurrences(searchCharacter: Char, givenString: String): List<Int> {
    val occurrences = mutableListOf<Int>()

    for ((index, character) in givenString.withIndex()) {
        if (character == searchCharacter) {
            occurrences.add(index)
        }
    }

    return occurrences
}

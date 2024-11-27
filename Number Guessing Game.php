<?php
session_start();

if (!isset($_SESSION['target'])) {
    $_SESSION['target'] = rand(1, 100);
    $_SESSION['hints'] = generateHints($_SESSION['target']);
    $_SESSION['message'] = '';
    $_SESSION['game_over'] = false; 
    $_SESSION['correct_answer_revealed'] = false; 
}

function generateHints($target) {
    $hints = [];
    
    if (isPrime($target)) {
        $hints[] = "The number is prime.";
    } else {
        $hints[] = "The number is composite.";
    }

    if (sqrt($target) == floor(sqrt($target))) {
        $hints[] = "The number is a perfect square.";
    }

    $divisors = [];
    for ($i = 1; $i <= 100; $i++) {
        if ($target % $i == 0) {
            $divisors[] = $i;
        }
    }
    $hints[] = "The number is divisible by: " . implode(', ', $divisors);
    
    return $hints;
}

function isPrime($n) {
    if ($n < 2) return false;
    for ($i = 2; $i <= sqrt($n); $i++) {
        if ($n % $i == 0) return false;
    }
    return true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['guess']) && !$_SESSION['game_over']) {
        $guess = (int)$_POST['guess'];
        if ($guess < $_SESSION['target']) {
            $_SESSION['message'] = "Your guess is too low.";
        } elseif ($guess > $_SESSION['target']) {
            $_SESSION['message'] = "Your guess is too high.";
        } else {
            $_SESSION['message'] = "Congratulations! You guessed it correctly.";
            $_SESSION['game_over'] = true;
        }

        if ($_SESSION['message'] !== "Congratulations! You guessed it correctly.") {
            $_SESSION['game_over'] = true;
        }
    }

    if (isset($_POST['new_game'])) {
        session_destroy();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Number Guessing Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }

        .game-container {
            width: 350px;
            padding: 20px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .game-container input[type="number"] {
            width: 100%;
            padding: 5px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .game-container button {
            width: 48%;
            padding: 10px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        .game-container button:hover {
            background-color: #45a049;
        }

        .game-container button:active {
            background-color: #3e8e41;
        }

        .game-container .reset-button {
            background-color: #f44336;
        }

        .message {
            font-weight: bold;
            margin-top: 10px;
            color: #333;
        }

        .hints {
            font-style: italic;
            color: #555;
            margin-top: 10px;
        }

        .hints ul {
            list-style: none;
            padding: 0;
        }

        .hints ul li {
            margin-bottom: 5px;
        }

        .correct-answer {
            font-weight: bold;
            color: red;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="game-container">
    <h2>Guess the Number (1 to 100)</h2>

    <div class="hints">
        <p><strong>Hints:</strong></p>
        <ul>
            <?php foreach ($_SESSION['hints'] as $hint): ?>
                <li><?= $hint ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="message">
        <?= $_SESSION['message'] ?>
    </div>

    <?php if ($_SESSION['game_over'] && $_SESSION['message'] != "Congratulations! You guessed it correctly"): ?>
        <div class="correct-answer">
            The correct answer was: <?= $_SESSION['target'] ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <?php if (!$_SESSION['game_over']): ?>
            <input type="number" name="guess" min="1" max="100" placeholder="Enter your guess" required>
        <?php endif; ?>
        <br>
        <button type="submit">Submit Guess</button>
        <button type="submit" name="new_game" class="reset-button">Start a New Game</button>
    </form>
</div>

</body>
</html>

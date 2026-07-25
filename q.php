<?php
session_start();

// Function to fetch quiz questions from OpenAI
function fetchQuestionsFromOpenAI($numQuestions = 20)
{
    $apiKey = ''; 
    $url = 'https://api.openai.com/v1/chat/completions';

    $data = [
        'model' => 'gpt-4',
        'messages' => [
            ['role' => 'system', 'content' => 'You are a quiz generator.'],
            ['role' => 'user', 'content' => "Generate $numQuestions common sense (generals) questions with 4 options each. "
                . "Each question should have a clear correct answer and no ambiguous wording. Format the response as:\n"
                . "Question 1: [question text]\nOptions: [option1, option2, option3, option4]\nAnswer: [correct answer]\n\n"]
        ],
        'max_tokens' => 3000,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        die('Curl error: ' . curl_error($ch));
    }

    curl_close($ch);
    $result = json_decode($response, true);

    if (isset($result['choices'][0]['message']['content'])) {
        return $result['choices'][0]['message']['content'];
    } else {
        die('Failed to fetch questions. Please check your API key or request.');
    }
}

// Initialize session variables if not set
if (!isset($_SESSION['questions'])) {
    $rawQuestions = fetchQuestionsFromOpenAI(20); // Fetch 20 questions
    $questions = [];
    foreach (explode("\n\n", $rawQuestions) as $block) {
        if (strpos($block, "Question") === 0) {
            preg_match('/Question \d+: (.+)/', $block, $questionMatch);
            preg_match('/Options: \[(.+)\]/', $block, $optionsMatch);
            preg_match('/Answer: (.+)/', $block, $answerMatch);

            if ($questionMatch && $optionsMatch && $answerMatch) {
                $questions[] = [
                    'question' => $questionMatch[1],
                    'options' => explode(", ", $optionsMatch[1]),
                    'answer' => $answerMatch[1],
                ];
            }
        }
    }

    if (!empty($questions)) {
        $_SESSION['questions'] = $questions;
        $_SESSION['currentQuestion'] = 0;
        $_SESSION['correctAnswers'] = 0; // Count of correct answers
    } else {
        die('No questions generated. Please try again later.');
    }
}

// Ensure session variables exist
$currentQuestion = $_SESSION['currentQuestion'] ?? 0;
$questions = $_SESSION['questions'] ?? [];
$correctAnswers = $_SESSION['correctAnswers'] ?? 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selectedAnswer = $_POST['answer'] ?? '';

    // Check the answer if it is provided
    if (isset($questions[$currentQuestion]) && strtolower(trim($selectedAnswer)) === strtolower(trim($questions[$currentQuestion]['answer']))) {
        $_SESSION['correctAnswers']++; // Increment correct answers count
    }

    // Move to the next question or finish
    $_SESSION['currentQuestion']++;
    if ($_SESSION['currentQuestion'] >= count($questions)) {
        // Quiz is finished, display the result
        $finalCorrectAnswers = $_SESSION['correctAnswers'];
        $totalQuestions = count($questions);

        // Display the result
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Quiz Result</title>
            <style>
                body {
                    font-family: 'Arial', sans-serif;
                    text-align: center;
                    background-color: #f0f4f8;
                    margin: 0;
                    padding: 0;
                }
                .result-container {
                    margin: 100px auto;
                    padding: 20px;
                    background: white;
                    border-radius: 10px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    width: 50%;
                    max-width: 500px;
                }
                h1 {
                    color: #333;
                    margin-bottom: 20px;
                }
                .score {
                    font-size: 40px;
                    color: #007BFF;
                    font-weight: bold;
                }
                .message {
                    font-size: 20px;
                    margin-top: 20px;
                }
                a {
                    display: inline-block;
                    margin-top: 20px;
                    padding: 10px 20px;
                    background: #007BFF;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                }
                a:hover {
                    background: #0056b3;
                }
            </style>
        </head>
        <body>
            <div class='result-container'>
                <h1>Quiz Complete!</h1>
                <div class='score'>Correct Answers: $finalCorrectAnswers / $totalQuestions</div>
                <div class='message'>" . getResultMessage($finalCorrectAnswers, $totalQuestions) . "</div>
                <a href=''>Restart Quiz</a>
            </div>
        </body>
        </html>";
        session_destroy();
        exit;
    }
}

// Function to generate result messages
function getResultMessage($correctAnswers, $totalQuestions)
{
    if ($correctAnswers >= ($totalQuestions * 0.9)) {
        return "Excellent work! You're a genius!";
    } elseif ($correctAnswers >= ($totalQuestions * 0.75)) {
        return "Great job! You did really well!";
    } elseif ($correctAnswers >= ($totalQuestions * 0.5)) {
        return "Not bad! Keep practicing.";
    } else {
        return "Keep learning, you can do better!";
    }
}

// Display the current question
$question = $questions[$currentQuestion] ?? null;

// If there is no question, terminate
if (!$question) {
    die('Unexpected error: No question found. Please restart the quiz.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz App</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f4f8;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 60%;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 20px;
        }
        .question {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 10px;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="radio"] {
            margin-right: 10px;
        }
        button {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Question <?php echo $currentQuestion + 1; ?> / 20</h1>
        <form method="POST">
            <div class="question">
                <h3><?php echo $question['question']; ?></h3>
                <?php foreach ($question['options'] as $option): ?>
                    <label>
                        <input type="radio" name="answer" value="<?php echo htmlspecialchars($option); ?>" required>
                        <?php echo htmlspecialchars($option); ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="submit">Next</button>
        </form>
    </div>
</body>
</html>

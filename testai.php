<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Your Mental Health</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #000;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 500px;
            background: #222;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        h1 {
            text-align: center;
            color: #fff;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .question {
            margin-bottom: 20px;
        }
        .question label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #ccc;
        }
        .question input {
            width: 100%;
            padding: 12px;
            border: 1px solid #555;
            border-radius: 8px;
            font-size: 14px;
            background-color: #333;
            color: #fff;
            transition: border-color 0.3s;
        }
        .question input:focus {
            outline: none;
            border-color: #fff;
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.3);
        }
        button {
            background-color: #444;
            color: white;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 8px;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #555;
        }
        .suggestions {
            margin-top: 20px;
            padding: 15px;
            background-color: #333;
            border-left: 6px solid #fff;
            border-radius: 8px;
            font-size: 14px;
            line-height: 1.6;
            color: #fff;
        }
        .suggestions h3 {
            margin-top: 0;
            color: #fff;
        }
        .suggestions ul {
            padding-left: 20px;
            margin: 10px 0 0;
        }
        .suggestions ul li {
            margin-bottom: 8px;
        }
        .continue-btn {
            margin-top: 20px;
            background-color: #555;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hey Check Your Mental Health</h1>
        <div id="question"></div>
        <button id="nextButton" type="button" onclick="submitAnswer()" disabled>Next</button>
        <div id="output" class="suggestions" style="display: none;">
            <h3>Your Personalized Suggestions:</h3>
            <ul></ul>
            <button class="continue-btn" onclick="continueToDashboard()">Continue</button>
        </div>
    </div>

    <script>
        let currentQuestionIndex = 0;
        let questions = [];
        const answers = {};

        async function generateQuestions() {
            const response = await fetch('https://api.openai.com/v1/chat/completions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer '
                },
                body: JSON.stringify({
                    model: 'gpt-4',
                    messages: [
                        {
                            role: 'system',
                            content: 'You are a helpful assistant specializing in mental health.'
                        },
                        {
                            role: 'user',
                            content: 'Generate 20 simple and relatable mental health questions to identify status of mental health about daily life, focusing on job, relationships, finance, personal growth, and family etc.'
                        }
                    ]
                })
            });

            const result = await response.json();
            questions = result.choices[0].message.content.split('\n').filter(q => q.trim() !== '');
            displayQuestion();
        }

        function displayQuestion() {
            const questionContainer = document.getElementById('question');
            questionContainer.innerHTML = '';

            if (currentQuestionIndex < questions.length) {
                const questionDiv = document.createElement('div');
                questionDiv.classList.add('question');

                const label = document.createElement('label');
                label.textContent = questions[currentQuestionIndex];
                label.setAttribute('for', `q${currentQuestionIndex + 1}`);

                const input = document.createElement('input');
                input.type = 'text';
                input.id = `q${currentQuestionIndex + 1}`;
                input.name = `q${currentQuestionIndex + 1}`;

                questionDiv.appendChild(label);
                questionDiv.appendChild(input);

                questionContainer.appendChild(questionDiv);
            } else {
                finalizeAnswers();
            }
        }

        function submitAnswer() {
            const input = document.querySelector('#question input');
            if (input && input.value.trim() !== '') {
                answers[`q${currentQuestionIndex + 1}`] = input.value;
                currentQuestionIndex++;
                displayQuestion();
            } else {
                alert('Please provide an answer before proceeding.');
            }
        }

        async function finalizeAnswers() {
            const response = await fetch('https://api.openai.com/v1/chat/completions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer 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'
                },
                body: JSON.stringify({
                    model: 'gpt-4',
                    messages: [
                        {
                            role: 'system',
                            content: 'You are a helpful assistant specializing in mental health analysis and suggestions.'
                        },
                        {
                            role: 'user',
                            content: `Analyze the following answers to mental health questions and provide suggestions in the form of a bulleted list with current status of mental health : ${JSON.stringify(answers)}`
                        }
                    ]
                })
            });

            const result = await response.json();
            const suggestions = result.choices[0].message.content;

            const outputDiv = document.getElementById('output');
            outputDiv.style.display = 'block';
            outputDiv.querySelector('ul').innerHTML = suggestions.split('\n').map(item => `<li>${item}</li>`).join('');
        }

        function continueToDashboard() {
            window.location.href = 'user_dashboard.php';
        }

        // Generate questions on page load
        generateQuestions().then(() => {
            document.getElementById('nextButton').disabled = false; // Enable the "Next" button after questions are loaded
        });
    </script>
</body>
</html>

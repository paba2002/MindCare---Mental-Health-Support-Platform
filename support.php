<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cardName = htmlspecialchars($_POST['card_name']);
    $cardNumber = htmlspecialchars($_POST['card_number']);
    $expiryDate = htmlspecialchars($_POST['expiry_date']);
    $cvv = htmlspecialchars($_POST['cvv']);
    echo "<h2>Thanks for your donation!</h2>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
            width: 300px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            margin-top: 15px;
            width: 100%;
            background: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Card Payment</h2>
        <form method="post">
            <label for="card_name">Card Type:</label>
            <select id="card_name" name="card_name" required>
                <option value="Visa">Visa</option>
                <option value="MasterCard">MasterCard</option>
            </select>

            <label for="card_number">Card Number (12 digits):</label>
            <input type="text" id="card_number" name="card_number" pattern="\d{12}" required>

            <label for="expiry_date">Expiry Date:</label>
            <input type="date" id="expiry_date" name="expiry_date" required>

            <label for="cvv">CVV:</label>
            <input type="text" id="cvv" name="cvv" pattern="\d{3}" required>

            <button type="submit" class="btn">Pay</button>
        </form>
    </div>
</body>
</html>

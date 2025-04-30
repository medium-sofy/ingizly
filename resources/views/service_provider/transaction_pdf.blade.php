<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction Receipt - IngiZly</title>
    <style>
        body {
            font-family: 'Poppins', 'Arial', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            background: #fff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            margin: 0;
            font-size: 36px;
            color: #0d6efd;
            letter-spacing: 1px;
        }
        .header p {
            margin-top: 8px;
            font-size: 16px;
            color: #6c757d;
        }
        .section-title {
            font-size: 24px;
            color: #0d6efd;
            margin-bottom: 20px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 5px;
        }
        .details p {
            font-size: 16px;
            margin: 12px 0;
            line-height: 1.5;
        }
        .details p strong {
            color: #212529;
            min-width: 180px;
            display: inline-block;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 14px;
            color: #6c757d;
        }
        .brand {
            margin-bottom: 10px;
        }
        .badge {
            display: inline-block;
            background-color: #0d6efd;
            color: #fff;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        
        <h1>IngiZly</h1>
        <p>Your Trusted Service Platform</p>
        <div class="badge">Transaction Receipt</div>
    </div>

    <div class="details">
        <h2 class="section-title">Transaction Details</h2>

        <p><strong>Order ID:</strong> {{ $payment->order->id }}</p>
        <p><strong>Buyer Name:</strong> {{ $payment->order->buyer->user->name }}</p>
        <p><strong>Service Title:</strong> {{ $payment->order->service->title }}</p>
        <p><strong>Amount:</strong> {{ $payment->amount }} {{ $payment->currency }}</p>
        <p><strong>Transaction ID:</strong> {{ $payment->transaction_id }}</p>
        <p><strong>Payment Date:</strong> {{ $payment->created_at->format('Y-m-d H:i:s') }}</p>
        <p><strong>Service Location:</strong> {{ $payment->order->location }}</p>
        <p><strong>Special Instructions:</strong> {{ $payment->order->special_instructions }}</p>
    </div>

    <div class="footer">
        <p>Thank you for choosing IngiZly. We look forward to serving you again!</p>
    </div>
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

<!-- Navbar -->
<nav class="bg-blue-600 p-4 text-white">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-xl font-bold">Ingizly | Payment Gateway</h1>
    </div>
</nav>

<!-- Payment Container -->
<div class="max-w-md mx-auto mt-10 bg-white shadow-md rounded-lg p-6">
    <!-- Paymob Logo -->
    <div class="flex justify-center mb-6">
        <img src="https://upload.wikimedia.org/wikipedia/commons/d/d4/PayMob_Payments.png" alt="Paymob Logo" class="h-16 object-contain">
    </div>

    <h2 class="text-xl text-center font-semibold mb-4 text-gray-700">Payment Information</h2>

    <div class="space-y-3 text-gray-800 text-sm">
        <div><strong>Amount:</strong> {{ number_format($data['amount_cents'] / 100, 2) }} EGP</div>
        <div><strong>Currency:</strong> {{ $data['currency'] }}</div>
    </div>

    <h3 class="mt-6 mb-2 text-lg font-semibold text-gray-700 border-b pb-1">Shipping Details</h3>
    <div class="space-y-2 text-gray-800 text-sm">
        <div><strong>First Name:</strong> {{ $data['shipping_data']['first_name'] }}</div>
        <div><strong>Last Name:</strong> {{ $data['shipping_data']['last_name'] }}</div>
        <div><strong>Phone:</strong> {{ $data['shipping_data']['phone_number'] }}</div>
        <div><strong>Email:</strong> {{ $data['shipping_data']['email'] }}</div>
    </div>

    <!-- Payment Button -->
    <form method="POST" action="{{ route('payment.process') }}" class="mt-6">
        @csrf
        <input type="hidden" name="amount_cents" value="{{ $data['amount_cents'] }}">
        <input type="hidden" name="currency" value="{{ $data['currency'] }}">
        <input type="hidden" name="first_name" value="{{ $data['shipping_data']['first_name'] }}">
        <input type="hidden" name="last_name" value="{{ $data['shipping_data']['last_name'] }}">
        <input type="hidden" name="phone_number" value="{{ $data['shipping_data']['phone_number'] }}">
        <input type="hidden" name="email" value="{{ $data['shipping_data']['email'] }}">

        <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-lg transition duration-200">
            Pay Now
        </button>
    </form>
</div>

</body>
</html>

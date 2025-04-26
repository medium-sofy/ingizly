<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Your provided CSS for the loading spinner */
        .three-body {
            --uib-size: 35px;
            --uib-speed: 0.8s;
            --uib-color: #5D3FD3;
            position: relative;
            display: inline-block;
            height: var(--uib-size);
            width: var(--uib-size);
            animation: spin78236 calc(var(--uib-speed) * 2.5) infinite linear;
        }

        .three-body__dot {
            position: absolute;
            height: 100%;
            width: 30%;
        }

        .three-body__dot:after {
            content: '';
            position: absolute;
            height: 0%;
            width: 100%;
            padding-bottom: 100%;
            background-color: var(--uib-color);
            border-radius: 50%;
        }

        .three-body__dot:nth-child(1) {
            bottom: 5%;
            left: 0;
            transform: rotate(60deg);
            transform-origin: 50% 85%;
        }

        .three-body__dot:nth-child(1)::after {
            bottom: 0;
            left: 0;
            animation: wobble1 var(--uib-speed) infinite ease-in-out;
            animation-delay: calc(var(--uib-speed) * -0.3);
        }

        .three-body__dot:nth-child(2) {
            bottom: 5%;
            right: 0;
            transform: rotate(-60deg);
            transform-origin: 50% 85%;
        }

        .three-body__dot:nth-child(2)::after {
            bottom: 0;
            left: 0;
            animation: wobble1 var(--uib-speed) infinite
            calc(var(--uib-speed) * -0.15) ease-in-out;
        }

        .three-body__dot:nth-child(3) {
            bottom: -5%;
            left: 0;
            transform: translateX(116.666%);
        }

        .three-body__dot:nth-child(3)::after {
            top: 0;
            left: 0;
            animation: wobble2 var(--uib-speed) infinite ease-in-out;
        }

        @keyframes spin78236 {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes wobble1 {
            0%,
            100% {
                transform: translateY(0%) scale(1);
                opacity: 1;
            }

            50% {
                transform: translateY(-66%) scale(0.65);
                opacity: 0.8;
            }
        }

        @keyframes wobble2 {
            0%,
            100% {
                transform: translateY(0%) scale(1);
                opacity: 1;
            }

            50% {
                transform: translateY(66%) scale(0.65);
                opacity: 0.8;
            }
        }

        /* Overlay Styles */
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black */
            display: none; /* Hidden by default */
            justify-content: center;
            align-items: center;
            z-index: 1000; /* Ensure it's on top */
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">

<nav class="bg-blue-600 p-4 text-white">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-xl font-bold">Ingizly | Payment Gateway</h1>
    </div>
</nav>

<div class="max-w-md mx-auto mt-10 bg-white shadow-md rounded-lg p-6 relative"> {{-- Add relative positioning for overlay --}}
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

    <form method="POST" action="{{ route('payment.process') }}" class="mt-6" id="payment-form">
        @csrf
        <input type="hidden" name="amount_cents" value="{{ $data['amount_cents'] }}">
        <input type="hidden" name="currency" value="{{ $data['currency'] }}">
        <input type="hidden" name="first_name" value="{{ $data['shipping_data']['first_name'] }}">
        <input type="hidden" name="last_name" value="{{ $data['shipping_data']['last_name'] }}">
        <input type="hidden" name="phone_number" value="{{ $data['shipping_data']['phone_number'] }}">
        <input type="hidden" name="email" value="{{ $data['shipping_data']['email'] }}">
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-lg transition duration-200"
                id="pay-now-button">
            Pay Now
        </button>
    </form>

    <div id="loading-overlay" class="absolute top-0 left-0 w-full h-full flex justify-center items-center bg-white bg-opacity-70 rounded-lg" style="display: none;">
        <div class="three-body">
            <div class="three-body__dot"></div>
            <div class="three-body__dot"></div>
            <div class="three-body__dot"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const paymentForm = document.getElementById('payment-form');
        const payNowButton = document.getElementById('pay-now-button');
        const loadingOverlay = document.getElementById('loading-overlay');

        paymentForm.addEventListener('submit', function (event) {
            // Prevent the default form submission
            event.preventDefault();

            // Disable the submit button
            payNowButton.disabled = true;
            payNowButton.classList.add('cursor-not-allowed'); // Optional: Change cursor

            // Show the loading overlay
            loadingOverlay.style.display = 'flex';

            // Optionally, you can submit the form programmatically after a short delay
            // to ensure the visual changes are visible to the user.
            setTimeout(function() {
                paymentForm.submit();
            }, 500); // Adjust the delay as needed (in milliseconds)
        });
    });
</script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Ingizly</title>
    @vite(['resources/js/app.js'])
    @vite(['resources/css/app.css'])
</head>

<body class="pb-20 text-gray-800 bg-gray-50 font-hanken-grotesk">
    <div class="px-10">
        <nav class="flex items-center justify-between py-4 border-b border-gray-200">
            <div>
                <a href="/">
                    Ingizly
                </a>
            </div>
            <div class="space-x-6 font-bold">
                
            </div>
            @auth
            <div class="flex space-x-6 font-bold">
                @if(auth()->user()->role=='employer')
                <a href="/jobs/create">Post a Job</a>
                <a href="/profile">Profile</a>
                @endif
                @if(auth()->user()->role=='candidate')
                <a href="/profile">Profile</a>
                @endif
                <form action="/logout" method="POST">
                    @csrf
                    <button>Log Out</button>
                </form>
            </div>
            @endauth

            @guest
            <div class="space-x-6 font-bold">
                <a href="/register">Sign Up</a>
                <a href="/login">Log In</a>

            </div>
            @endguest
        </nav>
        <main class="mt-10 max-w-[986px] m-auto">
            {{ $slot }}
        </main>
    </div>
</body>

</html>

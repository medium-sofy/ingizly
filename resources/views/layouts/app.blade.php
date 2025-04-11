<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingizly |  Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-indigo-700 text-white">
        <div class="p-6">
            <h1 class="text-2xl font-semibold">Ingizly Admin</h1>
        </div>
        <nav class="mt-6">
            <a href="#" class="flex items-center px-6 py-3 text-white bg-indigo-800">
                <i class="fas fa-chart-bar mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="#" class="flex items-center px-6 py-3 text-white hover:bg-indigo-800">
                <i class="fas fa-users mr-3"></i>
                <span>Users</span>
            </a>
            <a href="#" class="flex items-center px-6 py-3 text-white hover:bg-indigo-800">
                <i class="fas fa-cogs mr-3"></i>
                <span>Services</span>
            </a>
            <a href="#" class="flex items-center px-6 py-3 text-white hover:bg-indigo-800">
                <i class="fas fa-folder mr-3"></i>
                <span>Categories</span>
            </a>
            <a href="#" class="flex items-center px-6 py-3 text-white hover:bg-indigo-800">
                <i class="fas fa-star mr-3"></i>
                <span>Reviews</span>
            </a>
            <a href="#" class="flex items-center px-6 py-3 text-white hover:bg-indigo-800">
                <i class="fas fa-file-alt mr-3"></i>
                <span>Reports</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8">
        @yield('content')
    </main>
</div>
</body>
</html>

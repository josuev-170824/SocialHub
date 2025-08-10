<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SocialHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <div class="max-w-7xl mx-auto p-6">
        <header class="mb-8">
            <a href="/" class="text-2xl font-bold text-indigo-600">SocialHub</a>
        </header>
        <main>
            <!-- Contenido de la página -->
            @yield('content')
            <!-- Fin del contenido de la página -->
        </main>
    </div>
</body>
</html>
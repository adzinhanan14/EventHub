<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - My App</title>
    @vite('resources/css/app.css')
</head>
<body class="font-sans bg-gray-100 text-gray-800">
    <!-- Navbar -->
    <nav class="bg-white shadow-md p-4 flex justify-center space-x-6">
        <a href="/profil" class="text-green-800 font-bold border-b-2 border-green-800">Profil</a>
        <a href="/katalog" class="hover:text-green-800">Katalog</a>
        <a href="/bantuan" class="hover:text-green-800">Bantuan</a>
    </nav>

    <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-xl shadow-lg">
        <div class="flex flex-col items-center">
            <div class="w-24 h-24 bg-green-700 rounded-full mb-4 flex items-center justify-center text-white text-3xl font-bold">JD</div>
            <h1 class="text-2xl font-bold">Abdullah Adzin  Hanan</h1>
            <p class="text-gray-500 italic">24.12.3383</p>
            <div class="mt-4 w-full border-t pt-4">
                <p class="text-sm"><strong>Email:</strong> abdullahadzinhanan@students.amikom.ac.id</p>
                <p class="text-sm"><strong>Lokasi:</strong> Yogyakarta, Indonesia</p>
            </div>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil & Kontak - My App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <!-- Navbar -->
    <nav class="bg-white shadow-md p-4 flex justify-center space-x-6">
        <a href="/profil" class="hover:text-blue-600">Profil</a>
        <a href="/katalog" class="hover:text-blue-600">Katalog</a>
        <a href="/kontak" class="text-blue-600 font-bold border-b-2 border-blue-600">Bantuan</a>
    </nav>

    <div class="max-w-2xl mx-auto mt-10 p-6">
        <!-- Header Profil -->
        <div class="text-center mb-10">
            <div class="w-24 h-24 bg-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center text-white text-3xl font-bold">
                JD
            </div>
            <h1 class="text-2xl font-bold">Abdullah Adzin Hanan</h1>
            <p class="text-gray-500 italic">Web Developer & UI Enthusiast</p>
        </div>

        <!-- Detail Kontak -->
        <div class="space-y-4">
            <h2 class="text-xl font-semibold border-l-4 border-blue-600 pl-4 mb-4">Informasi Kontak</h2>
            
            <div class="bg-white p-4 rounded border flex items-center space-x-4">
                <span class="text-blue-600 font-bold">Email:</span>
                <span class="text-gray-600">abdullahadzinhanan@students.amikom.ac.id</span>
            </div>

            <div class="bg-white p-4 rounded border flex items-center space-x-4">
                <span class="text-blue-600 font-bold">WhatsApp:</span>
                <span class="text-gray-600">+62 81334989862</span>
            </div>

            <div class="bg-white p-4 rounded border flex items-center space-x-4">
                <span class="text-blue-600 font-bold">Lokasi:</span>
                <span class="text-gray-600">Yogyakarta, Indonesia</span>
            </div>
        </div>

        <!-- Social Media / Link Cepat -->
        <div class="mt-8 flex justify-center space-x-4">
            <a href="#" class="text-sm bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">LinkedIn</a>
            <a href="https://github.com/abdullahadzinhanan" class="text-sm bg-gray-800 text-white px-4 py-2 rounded hover:bg-black transition">GitHub</a>
            <a href="https://instagram.com/ziiinnn.___" class="text-sm bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700 transition">Instagram</a>
        </div>
    </div>
</body>
</html>
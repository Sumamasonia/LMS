<?php
$conn = null;
if (file_exists('db.php')) { include 'db.php'; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
    </style>
</head>
<body class="bg-slate-900 text-white selection:bg-teal-500 selection:text-white">

    <!-- NAV -->
    <nav class="absolute top-0 w-full z-50 py-6">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
            <div class="text-2xl font-black tracking-tighter flex items-center gap-2">
                <div class="w-8 h-8 bg-teal-500 rounded-lg transform rotate-45"></div>
                NEXUS
            </div>
            <div class="hidden md:flex gap-8 text-sm font-semibold text-slate-300">
                <a href="#home" class="hover:text-teal-400 transition">Home</a>
                <a href="#programs" class="hover:text-teal-400 transition">Academics</a>
                <a href="#news" class="hover:text-teal-400 transition">News</a>
            </div>
            <a href="login.php" class="px-6 py-2 bg-teal-500 hover:bg-teal-600 text-slate-900 font-bold rounded-full transition transform hover:scale-105">
                Portal Login
            </a>
        </div>
    </nav>

    <!-- HERO -->
    <section id="home" class="relative h-screen flex items-center">
        <!-- Background Image with heavy overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80" class="w-full h-full object-cover opacity-40">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/80 to-transparent"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-teal-400 font-bold tracking-widest uppercase text-sm mb-2 block">Welcome to the future</span>
                <h1 class="text-6xl md:text-8xl font-black leading-tight mb-6">
                    Elevate <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-emerald-600">Learning.</span>
                </h1>
                <p class="text-slate-400 text-lg mb-8 max-w-lg leading-relaxed">
                    Nexus Academy provides a cutting-edge digital environment for students and faculty to collaborate, innovate, and succeed.
                </p>
                <div class="flex gap-4">
                    <a href="#programs" class="px-8 py-4 bg-white text-slate-900 font-bold rounded hover:bg-slate-200 transition">Explore Courses</a>
                    <a href="login.php" class="px-8 py-4 border border-slate-600 hover:border-teal-500 hover:text-teal-400 font-bold rounded transition">Student Login</a>
                </div>
            </div>
            
            <!-- Floating Cards Effect -->
            <div class="hidden md:grid grid-cols-2 gap-4 opacity-90">
                <div class="glass p-6 rounded-2xl transform translate-y-8">
                    <i class="fa-solid fa-code text-4xl text-teal-400 mb-4"></i>
                    <h3 class="font-bold text-xl">Tech First</h3>
                    <p class="text-sm text-slate-400 mt-2">Advanced CS & AI Labs</p>
                </div>
                <div class="glass p-6 rounded-2xl">
                    <i class="fa-solid fa-earth-americas text-4xl text-blue-400 mb-4"></i>
                    <h3 class="font-bold text-xl">Global Reach</h3>
                    <p class="text-sm text-slate-400 mt-2">International Alumni</p>
                </div>
            </div>
        </div>
    </section>

    <!-- NEWS TICKER (Modern) -->
    <div class="bg-teal-500 text-slate-900 py-3 font-bold text-sm">
        <div class="max-w-7xl mx-auto px-6 flex">
            <span class="bg-slate-900 text-white px-3 py-1 rounded mr-4 text-xs uppercase tracking-wider">Updates</span>
            <marquee>
                <?php
                if ($conn) {
                    $res = $conn->query("SELECT content FROM notices ORDER BY created_at DESC LIMIT 5");
                    while($row = $res->fetch_assoc()) { echo "<span class='mr-12'>• " . $row['content'] . "</span>"; }
                } else { echo "Admissions Open for 2025 • Welcome to Nexus Academy"; }
                ?>
            </marquee>
        </div>
    </div>

    <!-- PROGRAMS (Grid) -->
    <section id="programs" class="py-24 bg-slate-50 text-slate-900">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h2 class="text-4xl font-black mb-2">Academic Wings</h2>
                    <p class="text-slate-500">Choose your path to greatness.</p>
                </div>
                <a href="#" class="text-teal-600 font-bold hover:underline">View all departments</a>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="group relative overflow-hidden rounded-2xl h-96 cursor-pointer">
                    <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&q=80" class="absolute inset-0 w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 to-transparent flex flex-col justify-end p-8">
                        <h3 class="text-white text-2xl font-bold mb-1">Computer Science</h3>
                        <p class="text-slate-400 text-sm transform translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition duration-500">Innovation, Code, and Future Tech.</p>
                    </div>
                </div>
                <div class="group relative overflow-hidden rounded-2xl h-96 cursor-pointer">
                    <img src="https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=800&q=80" class="absolute inset-0 w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 to-transparent flex flex-col justify-end p-8">
                        <h3 class="text-white text-2xl font-bold mb-1">Business School</h3>
                        <p class="text-slate-400 text-sm transform translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition duration-500">Leadership & Entrepreneurship.</p>
                    </div>
                </div>
                <div class="group relative overflow-hidden rounded-2xl h-96 cursor-pointer">
                    <img src="https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=800&q=80" class="absolute inset-0 w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 to-transparent flex flex-col justify-end p-8">
                        <h3 class="text-white text-2xl font-bold mb-1">Engineering</h3>
                        <p class="text-slate-400 text-sm transform translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition duration-500">Building the physical world.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-slate-900 border-t border-slate-800 py-12">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center">
            <div class="text-2xl font-black text-white flex items-center gap-2 mb-4 md:mb-0">
                <div class="w-6 h-6 bg-teal-500 rounded transform rotate-45"></div>
                NEXUS
            </div>
            <div class="text-slate-500 text-sm">
                &copy; 2025 Nexus Academy. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>
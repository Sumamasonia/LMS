<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $password = $_POST['password'];
    
    // Determine which field to check (Email vs Roll No)
    if ($role == 'student') {
        $identifier = $_POST['roll_number'];
        $sql = "SELECT * FROM users WHERE roll_number='$identifier' AND role='student'";
    } else {
        $identifier = $_POST['email'];
        $sql = "SELECT * FROM users WHERE email='$identifier' AND role='$role'";
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Ideally use password_verify() here if hashes are used
        if ($row['password'] == $password) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['name'] = $row['name'];
            
            if ($role == 'admin') header("Location: admin_dashboard.php");
            elseif ($role == 'student') header("Location: student_dashboard.php");
            elseif ($role == 'faculty') header("Location: faculty_dashboard.php");
            exit();
        } else {
            $error = "Invalid Password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Nexus Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
    <script>
        function setRole(role) {
            document.getElementById('roleInput').value = role;
            
            // Visual Update for Tabs
            document.querySelectorAll('.role-tab').forEach(el => {
                el.classList.remove('border-teal-500', 'text-teal-600');
                el.classList.add('border-transparent', 'text-gray-400');
            });
            document.getElementById('tab-'+role).classList.add('border-teal-500', 'text-teal-600');
            document.getElementById('tab-'+role).classList.remove('border-transparent', 'text-gray-400');

            // Toggle Inputs
            if(role === 'student') {
                document.getElementById('emailGroup').classList.add('hidden');
                document.getElementById('rollGroup').classList.remove('hidden');
                document.getElementById('rollInput').required = true;
                document.getElementById('emailInput').required = false;
            } else {
                document.getElementById('emailGroup').classList.remove('hidden');
                document.getElementById('rollGroup').classList.add('hidden');
                document.getElementById('rollInput').required = false;
                document.getElementById('emailInput').required = true;
            }
        }
    </script>
</head>
<body class="h-screen flex overflow-hidden bg-white">

    <!-- LEFT SIDE: Image/Brand -->
    <div class="hidden lg:block w-1/2 relative bg-slate-900">
        <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80" class="absolute inset-0 w-full h-full object-cover opacity-40 mix-blend-overlay">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent"></div>
        
        <div class="absolute bottom-0 left-0 p-16 text-white z-10">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-teal-500 rounded-lg transform rotate-45"></div>
                <h1 class="text-4xl font-bold tracking-tight">NEXUS ACADEMY</h1>
            </div>
            <p class="text-slate-300 text-lg max-w-md leading-relaxed">
                Connect, collaborate, and excel. Your academic journey continues here.
            </p>
        </div>
    </div>

    <!-- RIGHT SIDE: Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50">
        <div class="w-full max-w-md bg-white p-10 rounded-2xl shadow-xl border border-gray-100">
            
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-slate-900">Welcome Back</h2>
                <p class="text-slate-500 mt-2">Please login to access your dashboard.</p>
            </div>

            <!-- Role Selector Tabs -->
            <div class="flex border-b border-gray-200 mb-8">
                <button id="tab-student" type="button" onclick="setRole('student')" 
                        class="role-tab flex-1 pb-3 text-center border-b-2 border-teal-500 text-teal-600 font-bold transition hover:text-teal-800">
                    Student
                </button>
                <button id="tab-faculty" type="button" onclick="setRole('faculty')" 
                        class="role-tab flex-1 pb-3 text-center border-b-2 border-transparent text-gray-400 font-bold transition hover:text-slate-600">
                    Faculty
                </button>
                <button id="tab-admin" type="button" onclick="setRole('admin')" 
                        class="role-tab flex-1 pb-3 text-center border-b-2 border-transparent text-gray-400 font-bold transition hover:text-slate-600">
                    Admin
                </button>
            </div>

            <?php if($error): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded text-sm font-medium">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="role" id="roleInput" value="student">

                <!-- Email Input (Faculty/Admin) -->
                <div id="emailGroup" class="hidden mb-5">
                    <label class="block text-slate-700 text-sm font-bold mb-2">Email Address</label>
                    <input type="email" name="email" id="emailInput" placeholder="name@nexus.edu" 
                           class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                </div>

                <!-- Roll Number Input (Student) -->
                <div id="rollGroup" class="mb-5">
                    <label class="block text-slate-700 text-sm font-bold mb-2">Roll Number</label>
                    <input type="text" name="roll_number" id="rollInput" placeholder="e.g. 2025-CS-101" required 
                           class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                </div>

                <!-- Password Input -->
                <div class="mb-8">
                    <label class="block text-slate-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" placeholder="••••••••" required 
                           class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                </div>

                <button type="submit" class="w-full bg-slate-900 text-white font-bold py-3.5 rounded-lg hover:bg-teal-600 transition duration-300 shadow-lg transform active:scale-95">
                    Sign In
                </button>
            </form>

            <p class="mt-6 text-center text-xs text-gray-400">
                &copy; 2025 Nexus Academy. All rights reserved.
            </p>
        </div>
    </div>

</body>
</html>
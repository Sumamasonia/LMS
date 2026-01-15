<?php
session_start();
include 'db.php';

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 1. Fetch System Stats
$student_count = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='student'")->fetch_assoc()['c'];
$faculty_count = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='faculty'")->fetch_assoc()['c'];
// Safe check for departments table
$dept_check = $conn->query("SHOW TABLES LIKE 'departments'");
$dept_count = ($dept_check && $dept_check->num_rows > 0) ? $conn->query("SELECT COUNT(*) as c FROM departments")->fetch_assoc()['c'] : 0;

// 2. Fetch Recent Registrations
$users = $conn->query("SELECT * FROM users ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard - Nexus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="ml-64 p-10">
        
        <!-- Header -->
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">System Overview</h1>
                <p class="text-slate-500 mt-1">Welcome back, Administrator.</p>
            </div>
            <div class="flex gap-3">
                <button class="bg-white border border-slate-200 text-slate-600 px-4 py-2 rounded-lg text-sm font-bold shadow-sm hover:bg-slate-50">
                    <i class="fa-solid fa-gear mr-2"></i>Settings
                </button>
                <button class="bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-teal-600 transition">
                    <i class="fa-solid fa-download mr-2"></i>Reports
                </button>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <!-- Students -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-5 transition hover:-translate-y-1">
                <div class="w-14 h-14 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-slate-900"><?php echo $student_count; ?></h3>
                    <p class="text-sm font-medium text-slate-400">Total Students</p>
                </div>
            </div>

            <!-- Faculty -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-5 transition hover:-translate-y-1">
                <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-slate-900"><?php echo $faculty_count; ?></h3>
                    <p class="text-sm font-medium text-slate-400">Faculty Members</p>
                </div>
            </div>

            <!-- Departments -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-5 transition hover:-translate-y-1">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-slate-900"><?php echo $dept_count; ?></h3>
                    <p class="text-sm font-medium text-slate-400">Departments</p>
                </div>
            </div>
        </div>

        <!-- Recent Registrations Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
                <h3 class="font-bold text-lg text-slate-800">Recent Users</h3>
                <a href="add_student.php" class="text-teal-600 font-bold text-sm hover:underline">Manage Users</a>
            </div>
            
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-400 text-xs uppercase font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-8 py-4">Name</th>
                        <th class="px-8 py-4">Role</th>
                        <th class="px-8 py-4">Department</th>
                        <th class="px-8 py-4 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if ($users->num_rows > 0): ?>
                        <?php while($u = $users->fetch_assoc()): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-8 py-4 font-semibold text-slate-700">
                                <?php echo $u['name']; ?>
                            </td>
                            <td class="px-8 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide 
                                    <?php echo $u['role']=='student' ? 'bg-teal-50 text-teal-700 border border-teal-100' : 'bg-purple-50 text-purple-700 border border-purple-100'; ?>">
                                    <?php echo $u['role']; ?>
                                </span>
                            </td>
                            <td class="px-8 py-4 text-slate-500 text-sm">
                                <?php echo $u['department'] ? $u['department'] : '-'; ?>
                            </td>
                            <td class="px-8 py-4 text-right">
                                <span class="text-xs font-bold text-green-600 flex items-center justify-end gap-1">
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span> Active
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="px-8 py-6 text-center text-slate-400">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>
<?php
// Check role from session to show correct links
$role = $_SESSION['role'];
?>
<div class="w-64 bg-slate-900 text-white min-h-screen flex flex-col fixed left-0 top-0 z-40">
    <!-- Brand -->
    <div class="h-20 flex items-center px-8 border-b border-slate-800">
        <div class="text-xl font-bold tracking-wider flex items-center gap-2">
            <div class="w-6 h-6 bg-teal-500 rounded transform rotate-45"></div>
            NEXUS
        </div>
    </div>

    <!-- Menu -->
    <div class="flex-1 py-6 space-y-2 px-4">
        
        <!-- ADMIN LINKS -->
        <?php if($role == 'admin'): ?>
            <p class="px-4 text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Admin</p>
            <a href="admin_dashboard.php" class="block px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-teal-400 transition flex items-center">
                <i class="fa-solid fa-chart-pie w-6"></i> Dashboard
            </a>
            <a href="add_depart.php" class="block px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-teal-400 transition flex items-center">
                <i class="fa-solid fa-building w-6"></i> Departments
            </a>
            <a href="add_student.php" class="block px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-teal-400 transition flex items-center">
                <i class="fa-solid fa-user-graduate w-6"></i> Students
            </a>
            <a href="add_faculty.php" class="block px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-teal-400 transition flex items-center">
                <i class="fa-solid fa-chalkboard-user w-6"></i> Faculty
            </a>
            <a href="admin_booking.php" class="block px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-teal-400 transition flex items-center">
                <i class="fa-solid fa-calendar-check w-6"></i> Room Booking
            </a>
        <?php endif; ?>

        <!-- STUDENT LINKS -->
        <?php if($role == 'student'): ?>
            <p class="px-4 text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Student</p>
            <a href="student_dashboard.php" class="block px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-teal-400 transition flex items-center">
                <i class="fa-solid fa-house w-6"></i> Dashboard
            </a>
            <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-teal-400 transition flex items-center">
                <i class="fa-solid fa-book w-6"></i> Courses
            </a>
        <?php endif; ?>

        <!-- FACULTY LINKS -->
        <?php if($role == 'faculty'): ?>
            <p class="px-4 text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Faculty</p>
            <a href="faculty_dashboard.php" class="block px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-teal-400 transition flex items-center">
                <i class="fa-solid fa-columns w-6"></i> Dashboard
            </a>
            <a href="faculty_dashboard.php?view=noticeboard" class="block px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-teal-400 transition flex items-center">
                <i class="fa-solid fa-clipboard w-6"></i> Noticeboard
            </a>
            <a href="faculty_booking.php" class="block px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-teal-400 transition flex items-center">
        <i class="fa-solid fa-calendar-plus w-6"></i> Book a Room
    </a>
        <?php endif; ?>
    </div>

    <!-- User Profile at Bottom -->
    <div class="p-4 border-t border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-teal-500 flex items-center justify-center font-bold text-slate-900">
                <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
            </div>
            <div>
                <p class="text-sm font-bold"><?php echo $_SESSION['name']; ?></p>
                <a href="logout.php" class="text-xs text-red-400 hover:text-red-300">Logout</a>
            </div>
        </div>
    </div>
</div>
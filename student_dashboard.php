<?php
session_start();
include 'db.php';

// 1. Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

// 2. Handle Profile Picture Upload
if (isset($_POST['upload_pic']) && !empty($_FILES['image']['name'])) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) { mkdir($target_dir); }
    
    $target_file = $target_dir . basename($_FILES['image']['name']);
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $image_name = $_FILES['image']['name'];
        $conn->query("UPDATE users SET profile_pic='$image_name' WHERE id=$user_id");
        $msg = "<p class='text-teal-600 text-xs mt-2 font-bold flex items-center justify-center'><i class='fa-solid fa-check-circle mr-1'></i> Photo Updated!</p>";
    } else {
        $msg = "<p class='text-red-500 text-xs mt-2 text-center'>Upload Failed.</p>";
    }
}

// Check for update message
if (isset($_GET['msg']) && $_GET['msg'] == 'updated') {
    $msg = "<p class='text-teal-600 text-xs mt-2 font-bold flex items-center justify-center'><i class='fa-solid fa-check-circle mr-1'></i> Profile Updated!</p>";
}

// 3. Fetch User Data
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

// 4. Fetch Notices
$notices = $conn->query("SELECT * FROM notices ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Portal - Nexus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">

    <?php include 'sidebar.php'; ?>
    <div class="ml-64 p-10">
        
        <!-- HEADER -->
        <div class="mb-10 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Student Portal</h1>
                <p class="text-slate-500 mt-1">Welcome back, <strong><?php echo $user['name']; ?></strong>.</p>
            </div>
            <div class="text-right hidden md:block">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Current Semester</p>
                <p class="text-xl font-bold text-teal-600">Fall 2025</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- LEFT COLUMN: PROFILE CARD -->
            <div class="lg:col-span-1">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 text-center sticky top-6">
                    
                    <!-- Profile Image -->
                    <div class="relative w-32 h-32 mx-auto mb-6 group cursor-pointer">
                        <?php 
                            $pic = (!empty($user['profile_pic']) && $user['profile_pic'] != 'default.png') ? 'uploads/'.$user['profile_pic'] : 'https://via.placeholder.com/150';
                        ?>
                        <img src="<?php echo $pic; ?>" class="w-full h-full rounded-full object-cover border-4 border-slate-50 shadow-inner group-hover:opacity-75 transition">
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <i class="fa-solid fa-camera text-slate-800 text-2xl drop-shadow-md"></i>
                        </div>
                    </div>
                    
                    <h2 class="text-xl font-bold text-slate-900"><?php echo $user['name']; ?></h2>
                    <p class="text-slate-500 font-mono text-sm mb-4"><?php echo $user['roll_number']; ?></p>
                    
                    <span class="inline-block bg-teal-50 text-teal-700 text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-wide border border-teal-100">
                        <?php echo $user['department']; ?>
                    </span>

                    <!-- EDIT PROFILE BUTTON -->
                    <div class="mt-6 mb-6">
                        <a href="student_edit.php" class="inline-block text-sm font-bold text-slate-500 hover:text-teal-600 transition border border-slate-200 px-4 py-2 rounded-lg hover:border-teal-500">
                            <i class="fa-solid fa-pen-to-square mr-2"></i>Edit Profile
                        </a>
                    </div>

                    <!-- Upload Form -->
                    <form method="POST" enctype="multipart/form-data" class="pt-6 border-t border-slate-50 text-left">
                        <label class="block text-xs font-bold text-slate-400 mb-2 uppercase">Update Photo</label>
                        <div class="flex gap-2">
                            <input type="file" name="image" required class="block w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                            <button type="submit" name="upload_pic" class="bg-slate-900 text-white px-3 rounded-lg hover:bg-teal-600 transition shadow-md">
                                <i class="fa-solid fa-arrow-up"></i>
                            </button>
                        </div>
                        <?php echo $msg; ?>
                    </form>
                </div>
            </div>
            <!-- RIGHT COLUMN: NEWS FEED -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden h-[650px] flex flex-col">
                    
                    <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30 flex justify-between items-center">
                        <h2 class="font-bold text-slate-800 text-lg">Campus Feed</h2>
                        <i class="fa-solid fa-rss text-teal-500"></i>
                    </div>
                    
                    <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-4">
                        <?php if($notices->num_rows > 0): ?>
                            <?php while($notice = $notices->fetch_assoc()): 
                                $type = $notice['type'];
                                $bg = ($type == 'event') ? 'bg-amber-50 border-amber-200' : (($type == 'news') ? 'bg-blue-50 border-blue-200' : 'bg-slate-50 border-slate-200');
                                $icon = ($type == 'event') ? 'text-amber-500 fa-calendar-day' : (($type == 'news') ? 'text-blue-500 fa-newspaper' : 'text-slate-500 fa-bell');
                            ?>
                            
                            <div class="p-5 rounded-xl border <?php echo $bg; ?> hover:shadow-md transition group">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid <?php echo $icon; ?>"></i>
                                        <span class="text-xs font-bold uppercase tracking-wide opacity-70">
                                            <?php echo $type ? $type : 'Notification'; ?>
                                        </span>
                                    </div>
                                    <span class="text-xs text-slate-400 font-mono">
                                        <?php echo date('M d, h:i A', strtotime($notice['created_at'])); ?>
                                    </span>
                                </div>
                                <h3 class="font-bold text-slate-900 mb-1 text-lg group-hover:text-teal-700 transition-colors">
                                    <?php echo $notice['title']; ?>
                                </h3>
                                <p class="text-slate-600 text-sm leading-relaxed whitespace-pre-line">
                                    <?php echo $notice['content']; ?>
                                </p>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-20 text-slate-400">
                                <i class="fa-regular fa-folder-open text-4xl mb-3 opacity-30"></i>
                                <p>No updates available yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
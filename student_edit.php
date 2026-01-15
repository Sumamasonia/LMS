<?php
session_start();
include 'db.php';

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    
    // Only update password if not empty
    $pass_sql = "";
    if(!empty($_POST['password'])) {
        $p = $_POST['password'];
        $pass_sql = ", password='$p'";
    }

    $sql = "UPDATE users SET name='$name' $pass_sql WHERE id=$user_id";
    
    if ($conn->query($sql) === TRUE) {
        // Update session name if changed
        $_SESSION['name'] = $name;
        header("Location: student_dashboard.php?msg=updated");
        exit();
    } else {
        $error = "Error updating: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Profile - Nexus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800">

    <?php include 'sidebar.php'; ?>

    <div class="ml-64 p-10">
        <div class="max-w-2xl mx-auto bg-white p-10 rounded-2xl shadow-sm border border-slate-100">
            
            <div class="flex justify-between items-center mb-8 border-b border-slate-100 pb-4">
                <h1 class="text-2xl font-bold text-slate-900">Edit My Profile</h1>
                <a href="student_dashboard.php" class="text-slate-400 hover:text-teal-600 transition"><i class="fa-solid fa-xmark text-xl"></i></a>
            </div>

            <?php if(isset($error)) echo "<p class='text-red-500 mb-4'>$error</p>"; ?>

            <form method="POST" class="space-y-6">
                
                <!-- Read Only Fields -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Roll Number</label>
                        <input type="text" value="<?php echo $user['roll_number']; ?>" disabled 
                               class="w-full p-3 bg-slate-100 border border-slate-200 rounded-lg text-slate-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Department</label>
                        <input type="text" value="<?php echo $user['department']; ?>" disabled 
                               class="w-full p-3 bg-slate-100 border border-slate-200 rounded-lg text-slate-500 cursor-not-allowed">
                    </div>
                </div>

                <!-- Editable Fields -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required 
                           class="w-full p-3 bg-slate-50 border-b-2 border-slate-200 focus:border-teal-500 focus:outline-none transition font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">New Password</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current password" 
                           class="w-full p-3 bg-slate-50 border-b-2 border-slate-200 focus:border-teal-500 focus:outline-none transition">
                </div>

                <div class="pt-6 flex gap-4">
                    <button type="submit" class="flex-1 bg-slate-900 text-white font-bold py-3 rounded-lg hover:bg-teal-600 transition shadow-lg">
                        Save Changes
                    </button>
                    <a href="student_dashboard.php" class="flex-1 bg-white border border-slate-200 text-slate-600 font-bold py-3 rounded-lg hover:bg-slate-50 text-center transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
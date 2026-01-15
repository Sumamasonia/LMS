<?php
session_start();
include 'db.php';

// 1. Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. Get User ID
if (!isset($_GET['id'])) {
    header("Location: add_faculty.php");
    exit();
}
$id = $_GET['id'];

// 3. Fetch Existing Data
$query = "SELECT * FROM users WHERE id=$id";
$result = $conn->query($query);
if ($result->num_rows == 0) {
    echo "User not found.";
    exit();
}
$row = $result->fetch_assoc();

$error_msg = "";

// 4. Handle Update Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $dept = $_POST['dept'];
    $type = $_POST['type'];
    
    // Password Logic: Only update if user typed something
    $pass_sql = "";
    if(!empty($_POST['password'])) {
        $p = $_POST['password'];
        $pass_sql = ", password='$p'";
    }

    // Update Query
    $sql = "UPDATE users SET name='$name', email='$email', department='$dept', faculty_type='$type' $pass_sql WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: add_faculty.php?msg=updated");
        exit();
    } else {
        $error_msg = "Error updating record: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Faculty - Nexus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800">

    <?php include 'sidebar.php'; ?>

    <div class="ml-64 p-10">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Edit Faculty Profile</h1>
        
        <div class="max-w-3xl bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            
            <?php if($error_msg): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-slate-600 font-bold mb-2">Full Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required 
                               class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label class="block text-slate-600 font-bold mb-2">Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required 
                               class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <!-- Department -->
                    <div>
                        <label class="block text-slate-600 font-bold mb-2">Department</label>
                        <select name="dept" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                            <?php 
                            $d_res = $conn->query("SELECT * FROM departments");
                            if ($d_res->num_rows > 0) {
                                while($d = $d_res->fetch_assoc()) { 
                                    // Check if this dept matches user's current dept
                                    $selected = ($d['name'] == $row['department']) ? 'selected' : '';
                                    echo "<option value='".$d['name']."' $selected>".$d['name']."</option>"; 
                                }
                            } else {
                                echo "<option value='".$row['department']."'>".$row['department']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <!-- Role Type -->
                    <div>
                        <label class="block text-slate-600 font-bold mb-2">Role Type</label>
                        <select name="type" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                            <option value="instructor" <?php echo ($row['faculty_type']=='instructor')?'selected':''; ?>>Instructor</option>
                            <option value="focal_person" <?php echo ($row['faculty_type']=='focal_person')?'selected':''; ?>>Focal Person</option>
                        </select>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-slate-600 font-bold mb-2">New Password (Optional)</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current password" 
                           class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 bg-purple-700 text-white font-bold py-3 rounded-lg hover:bg-purple-800 transition shadow-lg transform active:scale-95">
                        Update Faculty
                    </button>
                    <a href="add_faculty.php" class="flex-1 bg-slate-200 text-slate-700 font-bold py-3 rounded-lg hover:bg-slate-300 text-center transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
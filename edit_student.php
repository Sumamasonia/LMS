<?php
session_start();
include 'db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

$id = $_GET['id'];
$row = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $roll = $_POST['roll'];
    $dept = $_POST['dept'];
    
    // Only update password if user typed a new one
    $pass_sql = "";
    if(!empty($_POST['password'])) {
        $p = $_POST['password'];
        $pass_sql = ", password='$p'";
    }

    $sql = "UPDATE users SET name='$name', roll_number='$roll', department='$dept' $pass_sql WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: add_student.php?msg=updated");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Student - Nexus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php include 'sidebar.php'; ?>
    <div class="ml-64 p-10">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Edit Student Profile</h1>
        <div class="max-w-3xl bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-slate-600 font-bold mb-2">Full Name</label>
                        <input type="text" name="name" value="<?php echo $row['name']; ?>" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-slate-600 font-bold mb-2">Roll Number</label>
                        <input type="text" name="roll" value="<?php echo $row['roll_number']; ?>" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>
                <div>
                    <label class="block text-slate-600 font-bold mb-2">Department</label>
                    <select name="dept" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <?php 
                        $d_res = $conn->query("SELECT * FROM departments");
                        while($d = $d_res->fetch_assoc()) { 
                            $sel = ($d['name'] == $row['department']) ? 'selected' : '';
                            echo "<option value='".$d['name']."' $sel>".$d['name']."</option>"; 
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-600 font-bold mb-2">New Password (Optional)</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current password" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 bg-slate-900 text-white font-bold py-3 rounded-lg hover:bg-teal-600 transition">Update Student</button>
                    <a href="add_student.php" class="flex-1 bg-slate-200 text-slate-700 font-bold py-3 rounded-lg hover:bg-slate-300 text-center">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
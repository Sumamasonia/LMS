<?php
session_start();
include 'db.php';

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$data = $conn->query("SELECT * FROM departments WHERE id=$id")->fetch_assoc();

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    
    $stmt = $conn->prepare("UPDATE departments SET name=?, description=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $desc, $id);
    
    if ($stmt->execute()) {
        header("Location: add_depart.php?msg=updated");
        exit();
    } else {
        $error = "Error updating: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Department - Nexus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800">

    <?php include 'sidebar.php'; ?>

    <div class="ml-64 p-10">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Edit Department</h1>
        
        <div class="max-w-2xl bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <h2 class="text-xl font-bold text-slate-800 mb-6 border-b pb-4">Update Details</h2>
            <form method="POST" class="space-y-6">
                <!-- Name Field -->
                <div>
                    <label class="block text-slate-600 font-bold mb-2">Department Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($data['name']); ?>" required 
                           class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                </div>

                <!-- Description Field -->
                <div>
                    <label class="block text-slate-600 font-bold mb-2">Description</label>
                    <textarea name="description" rows="5" required 
                              class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition"><?php echo htmlspecialchars($data['description']); ?></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 bg-slate-900 text-white font-bold py-3 rounded-lg hover:bg-teal-600 transition shadow-lg transform active:scale-95">
                        Save Changes
                    </button>
                    <a href="add_depart.php" class="flex-1 bg-slate-200 text-slate-700 font-bold py-3 rounded-lg hover:bg-slate-300 text-center transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
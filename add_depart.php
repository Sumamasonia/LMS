<?php
session_start();
include 'db.php';

// 1. Fixed the Syntax Error here
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$msg = "";

// 2. Handle Add Department
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    
    if (!empty($name)) {
        $sql = "INSERT INTO departments (name, description) VALUES ('$name', '$desc')";
        if ($conn->query($sql) === TRUE) {
            $msg = "<div class='bg-teal-50 border-l-4 border-teal-500 text-teal-700 p-4 mb-4 rounded shadow-sm'>Department Added!</div>";
        } else {
            $msg = "<div class='bg-red-50 text-red-700 p-4 mb-4 rounded shadow-sm'>Error: " . $conn->error . "</div>";
        }
    }
}

// 3. Handle Messages
if (isset($_GET['msg']) && $_GET['msg'] == 'deleted') $msg = "<div class='bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm'>Department Deleted.</div>";
if (isset($_GET['msg']) && $_GET['msg'] == 'updated') $msg = "<div class='bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-4 rounded shadow-sm'>Department Updated.</div>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Departments - Nexus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">

    <?php include 'sidebar.php'; ?>

    <div class="ml-64 p-10">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Departments</h1>
        <p class="text-slate-500 mb-8">Manage academic departments and units.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- ADD FORM -->
            <div class="md:col-span-1">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 sticky top-24">
                    <h2 class="text-lg font-bold mb-6 text-slate-800 border-b pb-2">Create New</h2>
                    <?php echo $msg; ?>
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-slate-600 font-bold mb-1">Name</label>
                            <input type="text" name="name" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block text-slate-600 font-bold mb-1">Description</label>
                            <textarea name="description" rows="4" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-slate-900 text-white font-bold py-3 rounded-lg hover:bg-teal-600 transition shadow-lg">
                            Add Department
                        </button>
                    </form>
                </div>
            </div>

            <!-- LIST TABLE -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                        <h2 class="font-bold text-slate-800">Active Departments</h2>
                        <span class="bg-teal-100 text-teal-800 text-xs font-bold px-3 py-1 rounded-full">
                            Total: <?php echo $conn->query("SELECT count(*) as c FROM departments")->fetch_assoc()['c']; ?>
                        </span>
                    </div>
                    
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-slate-400 text-xs uppercase font-bold border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4">Name</th>
                                <th class="px-6 py-4">Description</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php 
                            $res = $conn->query("SELECT * FROM departments ORDER BY id DESC");
                            while ($row = $res->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-bold text-slate-700"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td class="px-6 py-4 text-sm text-slate-500"><?php echo substr($row['description'], 0, 80); ?>...</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <!-- Edit Button (Links to edit_depart.php) -->
                                    <a href="edit_depart.php?id=<?php echo $row['id']; ?>" class="inline-flex items-center justify-center w-8 h-8 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <!-- Delete Button (Links to delete_depart.php) -->
                                    <a href="delete_depart.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this department?');" class="inline-flex items-center justify-center w-8 h-8 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
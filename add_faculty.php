<?php
session_start();
include 'db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

$msg = "";
// Handle Add Faculty Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name']; 
    $email = $_POST['email']; 
    $password = $_POST['password']; 
    $dept = $_POST['dept']; 
    $type = $_POST['type'];
    
    $sql = "INSERT INTO users (name, email, password, department, role, faculty_type) VALUES ('$name', '$email', '$password', '$dept', 'faculty', '$type')";
    
    if ($conn->query($sql) === TRUE) { 
        $msg = "<div class='bg-teal-50 border-l-4 border-teal-500 text-teal-700 p-4 mb-6 rounded shadow-sm'>Faculty Added Successfully!</div>"; 
    } else { 
        $msg = "<div class='bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm'>Error: " . $conn->error . "</div>"; 
    }
}

if(isset($_GET['msg']) && $_GET['msg']=='updated') {
    $msg = "<div class='bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded shadow-sm'>Faculty Profile Updated.</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Faculty - Nexus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800">

    <?php include 'sidebar.php'; ?>

    <div class="ml-64 p-10">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Manage Faculty</h1>
                <p class="text-slate-500 mt-1">Add instructors or focal persons to the system.</p>
            </div>
        </div>
        
        <?php echo $msg; ?>

        <!-- REGISTRATION FORM -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 mb-10">
            <h2 class="text-xl font-bold mb-6 text-slate-900 border-b border-slate-100 pb-4">
                <i class="fa-solid fa-chalkboard-user mr-2 text-purple-600"></i>Register Faculty Member
            </h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-slate-600 font-bold mb-2">Full Name</label>
                    <input type="text" name="name" placeholder="Dr. Sarah Smith" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-slate-600 font-bold mb-2">Email Address</label>
                    <input type="email" name="email" placeholder="sarah@nexus.edu" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-slate-600 font-bold mb-2">Department</label>
                    <select name="dept" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Select Department</option>
                        <?php 
                        $d = $conn->query("SELECT * FROM departments"); 
                        while($r = $d->fetch_assoc()){ echo "<option value='".$r['name']."'>".$r['name']."</option>"; } 
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-600 font-bold mb-2">Role Type</label>
                    <select name="type" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="instructor">Instructor</option>
                        <option value="focal_person">Focal Person</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-slate-600 font-bold mb-2">Password</label>
                    <input type="password" name="password" placeholder="••••••" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="bg-purple-700 text-white font-bold py-3 px-8 rounded-lg hover:bg-purple-800 transition shadow-lg w-full md:w-auto">
                        Add Faculty Member
                    </button>
                </div>
            </form>
        </div>
        <!-- FACULTY LIST TABLE -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h2 class="font-bold text-lg text-slate-800">Faculty Directory</h2>
                <span class="bg-purple-100 text-purple-800 text-xs font-bold px-3 py-1 rounded-full">
                    Total: <?php echo $conn->query("SELECT count(*) as c FROM users WHERE role='faculty'")->fetch_assoc()['c']; ?>
                </span>
            </div>
            
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-400 text-xs uppercase font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-8 py-4">Name</th>
                        <th class="px-8 py-4">Email</th>
                        <th class="px-8 py-4">Role</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php 
                    $users = $conn->query("SELECT * FROM users WHERE role='faculty' ORDER BY id DESC");
                    if($users->num_rows > 0):
                        while($u = $users->fetch_assoc()): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-8 py-4 font-bold text-slate-700"><?php echo $u['name']; ?></td>
                            <td class="px-8 py-4 text-slate-500 text-sm"><?php echo $u['email']; ?></td>
                            <td class="px-8 py-4">
                                <span class="bg-purple-50 text-purple-700 px-3 py-1 rounded-full border border-purple-100 text-xs uppercase font-bold">
                                    <?php echo str_replace('_', ' ', $u['faculty_type']); ?>
                                </span>
                            </td>
                            <td class="px-8 py-4 text-right space-x-2">
                                <a href="edit_faculty.php?id=<?php echo $u['id']; ?>" class="inline-flex items-center justify-center w-8 h-8 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="delete_user.php?id=<?php echo $u['id']; ?>" onclick="return confirm('Are you sure you want to delete this faculty member?');" class="inline-flex items-center justify-center w-8 h-8 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="px-8 py-8 text-center text-slate-400">No faculty members found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
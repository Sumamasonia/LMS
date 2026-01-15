<?php
session_start();
include 'db.php';

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'faculty' || $_SESSION['faculty_type'] !== 'focal_person') {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$data = $conn->query("SELECT * FROM notices WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $type = $_POST['type'];

    $stmt = $conn->prepare("UPDATE notices SET title=?, content=?, type=? WHERE id=?");
    $stmt->bind_param("sssi", $title, $content, $type, $id);
    
    if ($stmt->execute()) {
        header("Location: faculty_dashboard.php?view=noticeboard&msg=updated");
        exit();
    } else {
        $error = "Error updating: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Post - Nexus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800">

    <?php include 'sidebar.php'; ?>

    <div class="ml-64 p-10">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Edit Post</h1>
        
        <div class="max-w-3xl bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                <h2 class="text-xl font-bold text-slate-800">Update Information</h2>
                <a href="faculty_dashboard.php?view=noticeboard" class="text-slate-400 hover:text-red-500 transition"><i class="fa-solid fa-xmark text-xl"></i></a>
            </div>
            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <!-- Post Type -->
                    <div>
                        <label class="block text-slate-600 font-bold mb-2">Post Type</label>
                        <select name="type" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                            <option value="news" <?php echo ($data['type']=='news')?'selected':''; ?>>News Update</option>
                            <option value="event" <?php echo ($data['type']=='event')?'selected':''; ?>>Campus Event</option>
                            <option value="notification" <?php echo ($data['type']=='notification')?'selected':''; ?>>General Notification</option>
                        </select>
                    </div>

                    <!-- Title -->
                    <div>
                        <label class="block text-slate-600 font-bold mb-2">Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($data['title']); ?>" required 
                               class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <label class="block text-slate-600 font-bold mb-2">Content Details</label>
                    <textarea name="content" rows="8" required 
                              class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 transition"><?php echo htmlspecialchars($data['content']); ?></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 bg-slate-900 text-white font-bold py-3 rounded-lg hover:bg-teal-600 transition shadow-lg">
                        Save Changes
                    </button>
                    <a href="faculty_dashboard.php?view=noticeboard" class="flex-1 bg-slate-200 text-slate-700 font-bold py-3 rounded-lg hover:bg-slate-300 text-center transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
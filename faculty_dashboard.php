<?php

session_start();

include 'db.php';



// Security Check

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'faculty') {

    header("Location: login.php");

    exit();

}



$user_id = $_SESSION['user_id'];

$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

$_SESSION['faculty_type'] = $user['faculty_type'];

$my_dept = $user['department']; // Get Faculty's Department



// --- Post Logic ---

$msg = "";

if (isset($_POST['submit_post']) && $user['faculty_type'] == 'focal_person') {

    $title = $_POST['title'];

    $content = $_POST['content'];

    $type = $_POST['type'];

   

    // CRITICAL: Insert with the Faculty's Department

    $stmt = $conn->prepare("INSERT INTO notices (title, content, type, department) VALUES (?, ?, ?, ?)");

    $stmt->bind_param("ssss", $title, $content, $type, $my_dept);

   

    if ($stmt->execute()) {

        $msg = "<div class='bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-8 rounded shadow-sm font-light flex items-center'><i class='fa-solid fa-check-circle mr-2'></i> Posted to $my_dept Board!</div>";

    } else {

        $msg = "<div class='bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-8 rounded shadow-sm font-light'>Error: " . $conn->error . "</div>";

    }

}



// Simple Router

$view = isset($_GET['view']) ? $_GET['view'] : 'dashboard';

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <title>Faculty Portal - Lumina</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>body { font-family: 'Lato', sans-serif; } h1, h2, h3 { font-family: 'Playfair Display', serif; }</style>

</head>

<body class="bg-[#FFFBF7] text-slate-800">



    <?php include 'sidebar.php'; ?>



    <main class="ml-64 min-h-screen">

       

        <div class="max-w-7xl mx-auto px-8 py-10">

           

            <?php if($view == 'dashboard'): ?>

                <header class="flex justify-between items-end mb-10">

                    <div>

                        <h1 class="text-4xl font-bold text-slate-900">Faculty Overview</h1>

                        <p class="text-slate-500 mt-2 font-light">Welcome, <?php echo $user['name']; ?>

                            <span class="bg-orange-100 text-orange-800 text-xs px-3 py-1 rounded-full uppercase tracking-wider ml-2 font-bold">

                                <?php echo str_replace('_', ' ', $user['faculty_type']); ?>

                            </span>

                        </p>

                        <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest font-bold">Dept: <?php echo $my_dept; ?></p>

                    </div>

                </header>

               

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">

                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-orange-100 flex items-center justify-between hover:shadow-md transition">

                        <div>

                            <p class="text-slate-400 font-bold text-sm uppercase tracking-widest mb-2">Dept Posts</p>

                            <h3 class="text-5xl font-bold text-slate-800"><?php echo $conn->query("SELECT COUNT(*) as c FROM notices WHERE department='$my_dept'")->fetch_assoc()['c']; ?></h3>

                        </div>

                        <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center text-2xl"><i class="fa-solid fa-layer-group"></i></div>

                    </div>

                   

                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-orange-100 flex items-center justify-between hover:shadow-md transition">

                        <div>

                            <p class="text-slate-400 font-bold text-sm uppercase tracking-widest mb-2">My Events</p>

                            <h3 class="text-5xl font-bold text-slate-800"><?php echo $conn->query("SELECT COUNT(*) as c FROM notices WHERE type='event' AND department='$my_dept'")->fetch_assoc()['c']; ?></h3>

                        </div>

                        <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center text-2xl"><i class="fa-solid fa-calendar-day"></i></div>

                    </div>



                    <?php if($user['faculty_type'] == 'focal_person'): ?>

                    <a href="faculty_dashboard.php?view=add" class="bg-slate-900 text-white p-8 rounded-3xl shadow-lg hover:bg-orange-600 transition flex items-center justify-between group cursor-pointer transform hover:-translate-y-1">

                        <div>

                            <h3 class="font-bold text-2xl group-hover:text-white">Create Post</h3>

                            <p class="text-slate-400 text-sm mt-1 font-light group-hover:text-orange-100">For <?php echo $my_dept; ?></p>

                        </div>

                        <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center text-xl group-hover:bg-white/20 transition"><i class="fa-solid fa-pen-nib"></i></div>

                    </a>

                    <?php endif; ?>

                </div>

               

                <div class="bg-white rounded-3xl shadow-sm border border-orange-100 overflow-hidden">

                    <div class="px-8 py-6 border-b border-orange-50 flex justify-between items-center bg-orange-50/20">

                        <h3 class="font-bold text-xl text-slate-800">Recent Department Activity</h3>

                        <a href="?view=noticeboard" class="text-orange-600 text-sm font-bold hover:underline flex items-center">View All <i class="fa-solid fa-arrow-right ml-2 text-xs"></i></a>

                    </div>

                    <?php

                    $recent = $conn->query("SELECT * FROM notices WHERE department='$my_dept' ORDER BY created_at DESC LIMIT 3");

                    while($r = $recent->fetch_assoc()): ?>

                    <div class="p-6 border-b border-orange-50 hover:bg-orange-50/30 transition flex justify-between items-center">

                        <div>

                            <h4 class="font-bold text-slate-800 text-lg mb-1"><?php echo $r['title']; ?></h4>

                            <p class="text-slate-500 font-light text-sm"><?php echo substr($r['content'], 0, 80); ?>...</p>

                        </div>

                        <div class="text-right">

                            <span class="inline-block bg-slate-100 text-slate-500 text-xs font-bold px-3 py-1 rounded-full uppercase mb-1 tracking-wider"><?php echo $r['type']; ?></span>

                            <span class="text-xs text-slate-400 block mt-1"><?php echo date('M d', strtotime($r['created_at'])); ?></span>

                        </div>

                    </div>

                    <?php endwhile; ?>

                </div>



            <?php elseif($view == 'add'): ?>

                <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-lg border border-orange-100 p-10">

                    <div class="flex items-center justify-between mb-8 border-b border-orange-50 pb-6">

                        <div>

                            <h2 class="text-3xl font-bold text-slate-900">Create New Post</h2>

                            <p class="text-sm text-slate-500 mt-1">Posting to: <strong class="text-orange-600"><?php echo $my_dept; ?></strong></p>

                        </div>

                        <a href="?view=dashboard" class="text-slate-400 hover:text-orange-500 transition"><i class="fa-solid fa-xmark text-2xl"></i></a>

                    </div>

                   

                    <?php echo $msg; ?>

                   

                    <form method="POST" class="space-y-8">

                        <div class="grid grid-cols-2 gap-8">

                            <div>

                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Post Type</label>

                                <select name="type" class="w-full p-3 bg-slate-50 border-b-2 border-slate-200 focus:border-orange-500 focus:outline-none transition">

                                    <option value="news">News Update</option>

                                    <option value="event">Campus Event</option>

                                    <option value="notification">General Notification</option>

                                </select>

                            </div>

                            <div>

                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Title</label>

                                <input type="text" name="title" placeholder="Enter headline..." required class="w-full p-3 bg-slate-50 border-b-2 border-slate-200 focus:border-orange-500 focus:outline-none transition">

                            </div>

                        </div>

                        <div>

                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Content Details</label>

                            <textarea name="content" rows="6" placeholder="Write full details here..." required class="w-full p-3 bg-slate-50 border-b-2 border-slate-200 focus:border-orange-500 focus:outline-none transition"></textarea>

                        </div>

                        <div class="flex gap-4 pt-2">

                            <button type="submit" name="submit_post" class="flex-1 bg-slate-900 text-white font-bold py-3 rounded-full hover:bg-orange-600 transition shadow-lg">Publish Post</button>

                            <a href="?view=dashboard" class="flex-1 bg-white border border-slate-300 text-slate-600 font-bold py-3 rounded-full hover:bg-slate-50 text-center transition">Cancel</a>

                        </div>

                    </form>

                </div>



            <?php elseif($view == 'noticeboard'): ?>

                <div class="bg-white rounded-3xl shadow-sm border border-orange-100 overflow-hidden">

                    <div class="bg-slate-900 text-white px-8 py-6 flex justify-between items-center">

                        <h2 class="font-bold text-xl font-serif tracking-wide"><?php echo $my_dept; ?> Noticeboard</h2>

                        <?php if($user['faculty_type'] == 'focal_person'): ?>

                            <a href="?view=add" class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold px-4 py-2 rounded-full uppercase tracking-wider transition shadow-lg transform hover:scale-105">+ New Post</a>

                        <?php endif; ?>

                    </div>

                   

                    <div class="p-8 grid gap-6">

                        <?php

                        $all = $conn->query("SELECT * FROM notices WHERE department='$my_dept' ORDER BY created_at DESC");

                        if($all->num_rows > 0):

                            while($row = $all->fetch_assoc()):

                                $border_color = ($row['type'] == 'event') ? 'border-amber-300' : (($row['type'] == 'news') ? 'border-blue-300' : 'border-slate-200');

                        ?>

                        <div class="border-l-4 <?php echo $border_color; ?> bg-white p-6 rounded-r-xl shadow-sm hover:shadow-md transition border-y border-r border-slate-100 group">

                            <div class="flex justify-between items-start mb-3">

                                <h3 class="font-bold text-xl text-slate-900 group-hover:text-orange-600 transition font-serif"><?php echo $row['title']; ?></h3>

                                <span class="text-xs text-slate-400 font-mono flex items-center gap-1"><i class="fa-regular fa-clock"></i><?php echo date('M d, Y', strtotime($row['created_at'])); ?></span>

                            </div>

                            <p class="text-slate-600 leading-relaxed mb-4 font-light"><?php echo $row['content']; ?></p>

                           

                            <div class="flex justify-between items-center pt-4 border-t border-slate-50">

                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 bg-slate-100 px-3 py-1 rounded-full"><?php echo $row['type']; ?></span>

                                <?php if($user['faculty_type'] == 'focal_person'): ?>

                                <div class="flex gap-3 opacity-60 group-hover:opacity-100 transition">

                                    <a href="edit_notice.php?id=<?php echo $row['id']; ?>" class="text-slate-400 hover:text-blue-600 transition" title="Edit"><i class="fa-solid fa-pen"></i></a>

                                    <a href="delete_notice.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this post?');" class="text-slate-400 hover:text-red-600 transition" title="Delete"><i class="fa-solid fa-trash"></i></a>

                                </div>

                                <?php endif; ?>

                            </div>

                        </div>

                        <?php endwhile; ?>

                        <?php else: ?>

                            <p class="text-center text-slate-400 py-10">No notices found for <?php echo $my_dept; ?>.</p>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endif; ?>



        </div>

    </main>



</body>

</html>
<?php
session_start();
include 'db.php';

// Security
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }
// Logic: Add Room (SECURE VERSION)
if (isset($_POST['add_room'])) {
    $name = $_POST['name'];
    $capacity = $_POST['capacity'];
    
    // Prepared Statement use karein
    $stmt = $conn->prepare("INSERT INTO rooms (name, capacity) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $capacity); // s=string, i=integer
    
    if($stmt->execute()) {
        $msg = "Room Added!";
    }
}

// Logic: Handle Status Change (SECURE VERSION)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = $_GET['action']; 
    
    // Check karein ke status valid hai ya nahi
    if(in_array($status, ['approved', 'rejected'])) {
        $stmt = $conn->prepare("UPDATE bookings SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
    }
    
    header("Location: admin_booking.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Room Booking Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800">
    <?php include 'sidebar.php'; ?>
    <main class="ml-64 p-10">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Booking Management</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 h-fit">
                <h3 class="font-bold text-lg mb-4">Add New Room</h3>
                <?php if(isset($msg)) echo "<p class='text-green-600 text-sm mb-2'>$msg</p>"; ?>
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase">Room Name/ID</label>
                        <input type="text" name="name" placeholder="e.g. Seminar Hall A" required class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase">Capacity</label>
                        <input type="number" name="capacity" placeholder="e.g. 100" required class="w-full p-2 border rounded">
                    </div>
                    <button type="submit" name="add_room" class="w-full bg-slate-900 text-white py-2 rounded font-bold hover:bg-teal-600 transition">Add Room</button>
                </form>

                <div class="mt-8">
                    <h4 class="font-bold text-xs uppercase text-slate-400 mb-2">Existing Rooms</h4>
                    <ul class="space-y-2">
                        <?php 
                        $rooms = $conn->query("SELECT * FROM rooms");
                        while($r = $rooms->fetch_assoc()) {
                            echo "<li class='flex justify-between text-sm bg-slate-50 p-2 rounded'><span>{$r['name']}</span> <span class='text-slate-400'>Cap: {$r['capacity']}</span></li>";
                        }
                        ?>
                    </ul>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <h3 class="font-bold text-lg mb-4">Incoming Booking Requests</h3>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs uppercase text-slate-400 border-b">
                            <th class="py-2">Dept</th>
                            <th class="py-2">Room</th>
                            <th class="py-2">Date & Slot</th>
                            <th class="py-2">Status</th>
                            <th class="py-2 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y">
                        <?php 
                        // Fetch bookings with Room names
                        $sql = "SELECT b.*, r.name as room_name FROM bookings b 
                                JOIN rooms r ON b.room_id = r.id 
                                ORDER BY b.created_at DESC";
                        $reqs = $conn->query($sql);
                        
                        if($reqs->num_rows > 0):
                            while($row = $reqs->fetch_assoc()): 
                                $status_color = $row['status'] == 'pending' ? 'text-orange-500' : ($row['status'] == 'approved' ? 'text-green-600' : 'text-red-500');
                        ?>
                        <tr>
                            <td class="py-3 font-bold"><?php echo $row['department']; ?></td>
                            <td class="py-3"><?php echo $row['room_name']; ?></td>
                            <td class="py-3">
                                <div class="font-bold"><?php echo $row['booking_date']; ?></div>
                                <div class="text-xs text-slate-500"><?php echo $row['time_slot']; ?></div>
                            </td>
                            <td class="py-3 <?php echo $status_color; ?> font-bold uppercase text-xs"><?php echo $row['status']; ?></td>
                            <td class="py-3 text-right">
                                <?php if($row['status'] == 'pending'): ?>
                                    <a href="?action=approved&id=<?php echo $row['id']; ?>" class="text-green-600 hover:underline font-bold mr-2">Accept</a>
                                    <a href="?action=rejected&id=<?php echo $row['id']; ?>" class="text-red-500 hover:underline">Reject</a>
                                <?php else: ?>
                                    <span class="text-slate-400 italic">Closed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="5" class="py-4 text-center text-slate-400">No requests found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
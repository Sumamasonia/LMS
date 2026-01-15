<?php
session_start();
include 'db.php';

// Security: Focal Person Only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'faculty' || $_SESSION['faculty_type'] !== 'focal_person') {
    header("Location: faculty_dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// Fetch Faculty Department
$u_data = $conn->query("SELECT department FROM users WHERE id=$user_id")->fetch_assoc();
$my_dept = $u_data['department'];

$msg = "";
$error = "";

// --- Handle Form Submission ---
if (isset($_POST['book_room'])) {
    $room_id = $_POST['room_id'];
    $date = $_POST['date'];
    $slot = $_POST['time_slot'];
    $persons = $_POST['num_persons'];

    // --- STEP 1: STRICT CHECK FOR APPROVED SLOTS ---
    // We use prepare() here to ensure the data is matched perfectly
    $check_stmt = $conn->prepare("SELECT id FROM bookings WHERE room_id = ? AND booking_date = ? AND time_slot = ? AND status = 'approved'");
    $check_stmt->bind_param("iss", $room_id, $date, $slot);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // CLASH FOUND: Slot is already approved
        $error = "❌ Booking Failed: This slot is ALREADY BOOKED (Approved) by another department.";
        $check_stmt->close();
    } else {
        $check_stmt->close();

        // --- STEP 2: CHECK FOR PENDING REQUESTS (Optional but Recommended) ---
        // This prevents multiple people from requesting the same slot, saving Admin's time
        $pending_stmt = $conn->prepare("SELECT id FROM bookings WHERE room_id = ? AND booking_date = ? AND time_slot = ? AND status = 'pending'");
        $pending_stmt->bind_param("iss", $room_id, $date, $slot);
        $pending_stmt->execute();
        $pending_stmt->store_result();

        if ($pending_stmt->num_rows > 0) {
            $error = "⚠️ Booking Failed: Someone else has already requested this slot. It is pending approval.";
            $pending_stmt->close();
        } else {
            $pending_stmt->close();

            // --- STEP 3: NO CONFLICTS - INSERT REQUEST ---
            $stmt = $conn->prepare("INSERT INTO bookings (room_id, user_id, department, booking_date, time_slot, num_persons, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param("iisssi", $room_id, $user_id, $my_dept, $date, $slot, $persons);
            
            if ($stmt->execute()) {
                $msg = "✅ Request sent successfully! Please wait for Admin approval.";
            } else {
                $error = "Database Error: " . $conn->error;
            }
            $stmt->close();
        }
    }
}

// Time Slots
$slots = ["08:00 - 09:00", "09:00 - 10:00", "10:00 - 11:00", "11:00 - 12:00", "12:00 - 01:00", "01:00 - 02:00", "02:00 - 03:00", "03:00 - 04:00"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Room Booking Schedule</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#FFFBF7] text-slate-800">

    <?php include 'sidebar.php'; ?>

    <main class="ml-64 p-10 min-h-screen">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Room Booking & Schedule</h1>
        <p class="text-slate-500 mb-8">Logged in as: <span class="font-bold text-orange-600"><?php echo $my_dept; ?></span> Focal Person</p>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-4 h-fit bg-white p-8 rounded-3xl shadow-lg border border-orange-100 sticky top-10">
                <h3 class="font-bold text-xl mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-plus text-orange-500"></i> Book a Slot
                </h3>
                
                <?php if($msg) echo "<div class='bg-green-100 text-green-700 p-3 rounded mb-4 text-sm border-l-4 border-green-500'>$msg</div>"; ?>
                <?php if($error) echo "<div class='bg-red-100 text-red-700 p-3 rounded mb-4 text-sm border-l-4 border-red-500'>$error</div>"; ?>

                <form method="POST" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Select Room</label>
                        <select name="room_id" required class="w-full p-3 bg-slate-50 border-b-2 border-slate-200 focus:border-orange-500 outline-none">
                            <option value="">-- Choose Room --</option>
                            <?php 
                            $rooms = $conn->query("SELECT * FROM rooms");
                            while($r = $rooms->fetch_assoc()) {
                                echo "<option value='{$r['id']}'>{$r['name']} (Cap: {$r['capacity']})</option>";
                            } 
                            ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Date</label>
                        <input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>" class="w-full p-3 bg-slate-50 border-b-2 border-slate-200 focus:border-orange-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Time Slot</label>
                        <select name="time_slot" required class="w-full p-3 bg-slate-50 border-b-2 border-slate-200 focus:border-orange-500 outline-none">
                            <?php foreach($slots as $s): ?>
                                <option value="<?php echo $s; ?>"><?php echo $s; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">No. of Persons</label>
                        <input type="number" name="num_persons" placeholder="e.g. 40" required class="w-full p-3 bg-slate-50 border-b-2 border-slate-200 focus:border-orange-500 outline-none">
                    </div>

                    <button type="submit" name="book_room" class="w-full bg-slate-900 text-white font-bold py-3 rounded-full hover:bg-orange-600 transition shadow-lg mt-4">Check & Submit</button>
                </form>
            </div>

            <div class="lg:col-span-8">
                <div class="bg-white rounded-3xl shadow-sm border border-orange-100 overflow-hidden">
                    <div class="bg-slate-900 text-white px-6 py-4 flex justify-between items-center">
                        <div class="font-bold"><i class="fa-solid fa-list-check mr-2"></i> All Bookings (Global)</div>
                        <div class="text-xs font-normal text-slate-400">View other depts to avoid conflicts</div>
                    </div>
                    
                    <div class="p-6">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-xs text-slate-400 uppercase border-b border-slate-100">
                                    <th class="pb-3 pl-2">Date & Time</th>
                                    <th class="pb-3">Room</th>
                                    <th class="pb-3">Department</th>
                                    <th class="pb-3">Status</th>
                                    <th class="pb-3 text-right pr-2">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <?php 
                                // SQL: Fetch ALL bookings (Global List), Order by Date DESC
                                // We are NOT filtering by user_id here.
                                $sql = "SELECT b.*, r.name as room_name 
                                        FROM bookings b 
                                        JOIN rooms r ON b.room_id = r.id 
                                        ORDER BY b.booking_date DESC, b.time_slot ASC";
                                $all_books = $conn->query($sql);

                                if($all_books->num_rows > 0):
                                    while($row = $all_books->fetch_assoc()):
                                        $st = $row['status'];
                                        $is_mine = ($row['department'] == $my_dept);
                                        
                                        // Dynamic Styling
                                        $row_bg = $is_mine ? 'bg-orange-50/50' : ''; // Highlight my rows slightly
                                        $status_badge = $st == 'pending' ? 'bg-orange-100 text-orange-600' : ($st == 'approved' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600');
                                        $dept_style = $is_mine ? 'font-bold text-slate-900' : 'text-slate-500';
                                ?>
                                <tr class="hover:bg-slate-50 transition <?php echo $row_bg; ?>">
                                    <td class="py-4 pl-2">
                                        <div class="font-bold text-sm text-slate-700"><?php echo date('M d, Y', strtotime($row['booking_date'])); ?></div>
                                        <div class="text-xs text-slate-400"><?php echo $row['time_slot']; ?></div>
                                    </td>
                                    <td class="py-4 text-sm font-medium text-slate-600"><?php echo $row['room_name']; ?></td>
                                    <td class="py-4 text-sm <?php echo $dept_style; ?>">
                                        <?php echo $row['department']; ?> 
                                        <?php if($is_mine) echo '<span class="text-[10px] bg-slate-200 text-slate-600 px-1 rounded ml-1">YOU</span>'; ?>
                                    </td>
                                    <td class="py-4">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?php echo $status_badge; ?>"><?php echo $st; ?></span>
                                    </td>
                                    <td class="py-4 text-right pr-2">
                                        <?php if($st == 'approved' && $is_mine): ?>
                                            <a href="print_voucher.php?id=<?php echo $row['id']; ?>" target="_blank" class="inline-flex items-center bg-slate-800 text-white text-xs px-3 py-1.5 rounded hover:bg-orange-600 transition shadow-sm">
                                                <i class="fa-solid fa-print mr-1"></i> Voucher
                                            </a>
                                        <?php elseif($st == 'approved' && !$is_mine): ?>
                                            <span class="text-xs text-red-300 font-bold"><i class="fa-solid fa-lock mr-1"></i> Reserved</span>
                                        <?php elseif($st == 'pending' && !$is_mine): ?>
                                            <span class="text-xs text-orange-300 italic">Requested</span>
                                        <?php else: ?>
                                            <span class="text-slate-300 text-xl">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-8 text-slate-400 italic">No bookings found in the system.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>
</body>
</html>
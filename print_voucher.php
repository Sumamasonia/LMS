<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) exit("Invalid Request");
$id = $_GET['id'];

// Fetch Booking Details with Room & User info
$sql = "SELECT b.*, r.name as room_name, u.name as user_name 
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.id 
        JOIN users u ON b.user_id = u.id 
        WHERE b.id = $id AND b.status = 'approved'";
$res = $conn->query($sql);

if ($res->num_rows == 0) exit("Booking not found or not approved.");
$data = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Booking Voucher #<?php echo $id; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .voucher { border: 2px solid #000; }
        }
    </style>
</head>
<body class="bg-gray-100 p-10 flex justify-center items-center min-h-screen">

    <div class="voucher bg-white w-[800px] p-10 shadow-xl relative">
        
        <div class="flex justify-between items-center border-b-2 border-slate-800 pb-6 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-slate-900 text-white flex items-center justify-center font-bold text-2xl rounded">N</div>
                <div>
                    <h1 class="text-3xl font-bold uppercase tracking-widest text-slate-900">Nexus</h1>
                    <p class="text-sm text-slate-500 uppercase tracking-widest">Room Booking Voucher</p>
                </div>
            </div>
            <div class="text-right">
                <p class="font-bold text-xl">#BKG-<?php echo str_pad($data['id'], 4, '0', STR_PAD_LEFT); ?></p>
                <p class="text-sm text-slate-500">Date Issued: <?php echo date('d M Y'); ?></p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-10 mb-10">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase mb-1">Department</p>
                <p class="text-lg font-bold border-b border-slate-200 pb-2"><?php echo $data['department']; ?></p>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase mb-1">Focal Person</p>
                <p class="text-lg font-bold border-b border-slate-200 pb-2"><?php echo $data['user_name']; ?></p>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase mb-1">Room Allocated</p>
                <p class="text-lg font-bold border-b border-slate-200 pb-2"><?php echo $data['room_name']; ?></p>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase mb-1">Participants</p>
                <p class="text-lg font-bold border-b border-slate-200 pb-2"><?php echo $data['num_persons']; ?> Persons</p>
            </div>
        </div>

        <div class="bg-slate-50 border border-slate-200 p-6 rounded-lg text-center mb-12">
            <p class="text-sm text-slate-500 mb-2">Authorized Time Slot</p>
            <h2 class="text-4xl font-bold text-slate-800"><?php echo date('d M, Y', strtotime($data['booking_date'])); ?></h2>
            <p class="text-2xl font-bold text-orange-600 mt-2"><?php echo $data['time_slot']; ?></p>
        </div>

        <div class="flex justify-between mt-16 pt-8">
            <div class="text-center w-64">
                <div class="border-t border-slate-400 pt-2 font-bold text-sm">Focal Person Signature</div>
            </div>
            <div class="text-center w-64">
                <div class="border-t border-slate-400 pt-2 font-bold text-sm">Admin Approval Stamp</div>
                <div class="text-xs text-green-600 mt-1 font-mono uppercase font-bold">[ Verified Approved ]</div>
            </div>
        </div>

        <button onclick="window.print()" class="no-print fixed bottom-10 right-10 bg-blue-600 text-white px-6 py-3 rounded-full shadow-lg font-bold hover:bg-blue-700 transition">
            <i class="fa-solid fa-print mr-2"></i> Print Voucher
        </button>

    </div>

</body>
</html>
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['student'])) {
  header("Location: login.php");
  exit();
}

$id = $_SESSION['student'];
$result = mysqli_query($conn, "
  SELECT c.course_name, c.credit_hours, t.name AS teacher_name
  FROM enrollments e
  JOIN courses c ON e.course_id = c.id
  JOIN teachers t ON c.teacher_id = t.id
  WHERE e.student_id = '$id'
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Enrolled Courses</title>
  <style>
    body { margin: 0; font-family: Arial, sans-serif; background: #f5f6fa; }
    .main { margin-left: 260px; padding: 40px; }
    table { width: 90%; margin: 0 auto; border-collapse: collapse; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: center; }
    th { background: #004d00; color: #ffcc00; font-size: 16px; }
    tr:nth-child(even) { background: #f9f9f9; }
    h2 { text-align: center; color: #004d00; margin-bottom: 20px; }
  </style>
</head>
<body>
  <?php include 'student_sidebar.php'; ?>

  <div class="main">
    <h2>📚 Enrolled Courses</h2>
    <table>
      <tr>
        <th>Course Name</th>
        <th>Teacher</th>
        <th>Credit Hours</th>
      </tr>

      <?php while($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?= htmlspecialchars($row['course_name']) ?></td>
        <td><?= htmlspecialchars($row['teacher_name']) ?></td>
        <td><?= htmlspecialchars($row['credit_hours']) ?></td>
      </tr>
      <?php } ?>
    </table>
  </div>
</body>
</html>

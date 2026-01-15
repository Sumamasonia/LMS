<?php
session_start();
include 'db.php';
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users | Admin Panel</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #f8f8f8;
  display: flex;
}

.main {
  margin-left: 260px;
  padding: 40px;
  width: 100%;
}

h2 {
  color: #004d00;
  text-align: center;
  margin-bottom: 25px;
}

table {
  width: 90%;
  margin: 0 auto;
  border-collapse: collapse;
  background: white;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  border-radius: 8px;
  overflow: hidden;
}

th, td {
  padding: 12px;
  text-align: center;
  border-bottom: 1px solid #ddd;
}

th {
  background-color: #004d00;
  color: #ffcc00;
}

tr:hover {
  background-color: #f2f2f2;
}

a {
  text-decoration: none;
  color: #004d00;
  font-weight: bold;
}

a:hover {
  color: #006600;
}
</style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">
  <h2>Manage Users</h2>

  <table>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Role</th>
      <th>Actions</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
      <td><?= (int)$row['id'] ?></td>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= htmlspecialchars($row['email']) ?></td>
      <td><?= htmlspecialchars($row['role']) ?></td>
      <td>
        <a href="edit_user.php?id=<?= $row['id'] ?>">✏️ Edit</a> | 
        <a href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this user?');">🗑️ Delete</a>
      </td>
    </tr>
    <?php } ?>
  </table>
</div>

</body>
</html>

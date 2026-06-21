<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include '../config/db.php';

$sql = "SELECT * FROM contact_messages
        ORDER BY created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>

    <title>Customer Messages</title>

    <style>

        body{
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 40px;
        }

        h1{
            margin-bottom: 30px;
        }

        table{
            width: 100%;
            background: white;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td{
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th{
            background: black;
            color: white;
        }

        tr:hover{
            background: #f9f9f9;
        }

    </style>

</head>

<body>
<?php include 'admin-nav.php'; ?>

    <h1>Customer Messages</h1>

    <table>

        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Message</th>
            <th>Date</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($result)){ ?>

        <tr>

            <td><?php echo $row['name']; ?></td>

            <td><?php echo $row['email']; ?></td>

            <td><?php echo $row['message']; ?></td>

            <td><?php echo $row['created_at']; ?></td>

        </tr>

        <?php } ?>

    </table>

</body>
</html>
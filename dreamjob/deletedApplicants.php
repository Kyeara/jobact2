<?php
require_once 'dbConfig.php';
require_once 'models.php';

session_start();

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit();
}


$sql = "SELECT da.*, u.username AS deleted_by_user 
        FROM deleted_applicants da 
        LEFT JOIN users u ON da.deleted_by = u.userID 
        ORDER BY da.deleted_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$deletedApplicants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleted Applicants</title>
</head>
<body>
    <h1>Deleted Applicants</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Deleted ID</th>
                <th>Applicant ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Contact Info</th>
                <th>Deleted By</th>
                <th>Deleted At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($deletedApplicants as $applicant) { ?>
                <tr>
                    <td><?php echo $applicant['deletedID']; ?></td>
                    <td><?php echo $applicant['applicantID']; ?></td>
                    <td><?php echo $applicant['first_name']; ?></td>
                    <td><?php echo $applicant['last_name']; ?></td>
                    <td><?php echo $applicant['age']; ?></td>
                    <td><?php echo $applicant['gender']; ?></td>
                    <td><?php echo $applicant['email']; ?></td>
                    <td><?php echo $applicant['contact_info']; ?></td>
                    <td><?php echo $applicant['deleted_by_user']; ?></td>
                    <td><?php echo $applicant['deleted_at']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>

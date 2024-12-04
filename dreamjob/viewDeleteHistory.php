<?php
require_once "core/dbConfig.php";

try {
    
    $sql = "SELECT * FROM deleted_applicants ORDER BY deleted_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $deleteHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = "Error fetching delete history: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleted Applicants History</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
        }

        table {
            width: 100%;
            max-width: 1200px;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .back-link {
            display: block;
            text-align: center;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #2ecc71;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            width: 200px;
            text-align: center;
        }

        .back-link:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>
    <h1>Deleted Applicants History</h1>

    <?php if (isset($errorMessage)) { ?>
        <p style="color: red; text-align: center;"><?php echo $errorMessage; ?></p>
    <?php } else { ?>
        <table>
            <thead>
                <tr>
                    <th>Applicant ID</th>
                    <th>Name</th>
                    <th>Deleted By</th>
                    <th>Deletion Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($deleteHistory) > 0) {
                    foreach ($deleteHistory as $record) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['applicantID']); ?></td>
                            <td><?php echo htmlspecialchars($record['applicant_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['deleted_by']); ?></td>
                            <td><?php echo htmlspecialchars($record['deleted_at']); ?></td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No delete history available.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>

    <a href="index.php" class="back-link">Back to Dashboard</a>
</body>
</html>

<?php 
require_once "core/dbConfig.php";  
require_once "core/models.php";     

session_start();

if (!$pdo) {
    die("Database connection failed.");
}

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'] ?? 'Unknown';

if (!isset($_SESSION['showDeleteHistory'])) {
    $_SESSION['showDeleteHistory'] = false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggleDeleteHistory'])) {
    $_SESSION['showDeleteHistory'] = !$_SESSION['showDeleteHistory'];
}

$showDeleteHistory = $_SESSION['showDeleteHistory'];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Job Application</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }

            header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px;
                background-color: #5c6bc0;
                color: white;
                font-size: 18px;
            }

            .welcome-message {
                font-size: 24px;
                font-weight: bold;
            }

            .logout-button {
                background-color: #3f51b5;
                color: white;
                border: none;
                padding: 10px 20px;
                cursor: pointer;
                font-size: 16px;
            }

            .logout-button:hover {
                background-color: #303f9f;
            }

            h2 {
                text-align: center;
                color: #333;
                margin-top: 20px;
            }

            table {
                width: 90%;
                margin: 0 auto;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th, td {
                padding: 12px;
                border: 1px solid #ccc;
                text-align: left;
            }

            th {
                background-color: #5c6bc0;
                color: white;
            }

            td {
                background-color: #fff;
            }

            input[type="submit"] {
                background-color: #5c6bc0;
                color: white;
                padding: 8px 16px;
                border: none;
                cursor: pointer;
                margin: 5px;
            }

            input[type="submit"]:hover {
                background-color: #3f51b5;
            }

            .search-form {
                text-align: center;
                margin-top: 20px;
            }

            hr {
                width: 99%;
                height: 2px;
                color: black;
                background-color: black;
            }

            .message {
                color: green;
                font-size: 18px;
                text-align: center;
            }

            .delete-history {
                margin-top: 30px;
                background-color: #f2f2f2;
                padding: 15px;
                border-radius: 5px;
            }

            .delete-history h2 {
                text-align: center;
                color: #2c3e50;
            }

            .delete-history table {
                width: 100%;
                border-collapse: collapse;
            }

            .delete-history th, .delete-history td {
                padding: 10px;
                border: 1px solid #ddd;
                text-align: left;
            }

            .delete-history th {
                background-color: #2ecc71;
                color: white;
            }

            .delete-history td {
                background-color: #f9f9f9;
            }

            .toggle-button {
                display: block;
                margin: 20px auto;
                padding: 10px 20px;
                background-color: #3498db;
                color: white;
                border: none;
                cursor: pointer;
                text-align: center;
            }

            .toggle-button:hover {
                background-color: #2980b9;
            }
        </style>
    </head>
    <body>
        <header>
            <div class="welcome-message">
                Welcome, <?php echo htmlspecialchars($username); ?>!
            </div>
            <form action="logout.php" method="POST">
                <button type="submit" class="logout-button">Logout</button>
            </form>
        </header>

        <h2>Job Application</h2>

        <?php if (isset($_SESSION['message'])) { ?>
            <h3 class="message">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
            </h3>
        <?php } unset($_SESSION['message']); ?>

        <div style="text-align: center;">
            <input type="submit" value="Submit New Applicant" onclick="window.location.href='insertApplicant.php'">
        </div>

        <hr>

        <div class="search-form">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="GET">
                <label for="searchBar">Search</label>
                <input type="text" name="searchBar" placeholder="Search by name or email">
                <input type="submit" name="searchButton" value="Search Application">
                <input type="submit" name="clearButton" value="Clear Search" onclick="window.location.href='index.php'">
            </form>
        </div>

        <table>
            <tr>
                <th colspan="11">Applicants</th>
            </tr>
            <tr>
                <th>Applicant ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Email Address</th>
                <th>Contact Number</th>
                <th>Date Added</th>
                <th>Last Edited By</th>
                <th>Added By</th>
                <th>Actions</th>
            </tr>

            <?php
            $searchedApplicationsData = isset($_GET['searchButton']) 
                ? searchForApplicant($pdo, $_GET['searchBar'])['querySet'] 
                : getAllApplicants($pdo)['querySet'];

            foreach ($searchedApplicationsData as $row) {
                $addedByQuery = "SELECT username FROM users WHERE userID = :userID";
                $stmt = $pdo->prepare($addedByQuery);
                $stmt->bindParam(':userID', $row['added_by']);
                $stmt->execute();
                $addedBy = $stmt->fetch(PDO::FETCH_ASSOC)['username'] ?? "Unknown";
            ?>
            <tr>
                <td><?php echo htmlspecialchars($row['applicantID']); ?></td>
                <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                <td><?php echo htmlspecialchars($row['age']); ?></td>
                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['contact_info']); ?></td>
                <td><?php echo htmlspecialchars($row['date_added']); ?></td>
                <td>
                    <?php
                    if ($row['edited_by']) {
                        $editedByQuery = "SELECT username FROM users WHERE userID = :userID";
                        $editedStmt = $pdo->prepare($editedByQuery);
                        $editedStmt->bindParam(':userID', $row['edited_by']);
                        $editedStmt->execute();
                        $editedBy = $editedStmt->fetch(PDO::FETCH_ASSOC)['username'] ?? "Unknown";
                        echo htmlspecialchars($editedBy . " on " . $row['last_edited']);
                    } else {
                        echo "No edits yet";
                    }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($addedBy); ?></td>
                <td>
                    <input type="submit" value="EDIT" onclick="window.location.href='editApplicant.php?applicantID=<?php echo $row['applicantID']; ?>';">
                    <input type="submit" value="DELETE" onclick="window.location.href='deleteApplicant.php?applicantID=<?php echo $row['applicantID']; ?>';">
                </td>
            </tr>
            <?php } ?>
        </table>

        <form method="POST" style="text-align: center;">
            <button type="submit" name="toggleDeleteHistory" class="toggle-button">
                <?php echo $showDeleteHistory ? "Hide Delete History" : "Show Delete History"; ?>
            </button>
        </form>

        <?php if ($showDeleteHistory): ?>
            <div class="delete-history">
                <h2>Delete History</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Applicant ID</th>
                            <th>Name</th>
                            <th>Deleted By</th>
                            <th>Deleted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $historyStmt = $pdo->prepare("SELECT * FROM deleted_applicants ORDER BY deleted_at DESC");
                        $historyStmt->execute();
                        $deleteHistory = $historyStmt->fetchAll(PDO::FETCH_ASSOC);

                        if (count($deleteHistory) > 0) {
                            foreach ($deleteHistory as $history) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($history['applicantID']) . "</td>";
                                echo "<td>" . htmlspecialchars($history['first_name'] . " " . $history['last_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($history['deleted_by']) . "</td>";
                                echo "<td>" . htmlspecialchars($history['deleted_at']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No delete history found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </body>
</html>

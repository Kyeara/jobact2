<?php
require_once "core/dbConfig.php";
require_once "core/models.php"; 
session_start(); 

if (isset($_GET['applicantID'])) {
    $applicantID = $_GET['applicantID'];

    
    $userID = $_SESSION['userID'] ?? null;

    if ($userID) {
        try {
            
            $query = "SELECT username FROM users WHERE userID = :userID";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            $username = $stmt->fetch(PDO::FETCH_ASSOC)['username'] ?? 'Unknown';

            
            $result = deleteApplicantByID($pdo, $applicantID, $username);

            if ($result['statusCode'] === 200) {
               
                $_SESSION['message'] = $result['message'];
                header('Location: index.php');
                exit();
            } else {
                
                $errorMessage = $result['message'];
            }
        } catch (Exception $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    } else {
        $errorMessage = "Error: User not logged in.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Applicant</title>
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
            margin-bottom: 30px;
        }

        .message {
            text-align: center;
            color: #e74c3c;
            font-size: 18px;
            margin-bottom: 20px;
        }

        .success {
            color: #2ecc71;
        }

        .form-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container a {
            display: block;
            text-align: center;
            margin-top: 20px;
            padding: 12px;
            background-color: #2ecc71;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .form-container a:hover {
            background-color: #27ae60;
        }

        .delete-action-container {
            text-align: center;
            margin-top: 30px;
        }

        .delete-action-container input {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .delete-action-container input:hover {
            background-color: #c0392b;
        }

        .cancel-action-container input {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #95a5a6;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .cancel-action-container input:hover {
            background-color: #7f8c8d;
        }
    </style>
</head>
<body>
    <h1>Delete Applicant</h1>

    <div class="form-container">
        <?php if (isset($errorMessage)) { ?>
            <div class="message"><?php echo $errorMessage; ?></div>
        <?php } ?>

        <?php if (isset($_SESSION['message'])) { ?>
            <div class="message success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php } ?>

        <div class="delete-action-container">
            <p>Are you sure you want to delete this applicant?</p>

            <form method="POST" action="deleteApplicant.php?applicantID=<?php echo htmlspecialchars($applicantID); ?>">
                <input type="submit" value="Yes, Delete" onclick="return confirm('Are you sure you want to delete this applicant?');">
            </form>
        </div>

        <div class="cancel-action-container">
            <a href="index.php">Cancel and Go Back</a>
        </div>
    </div>
</body>
</html>

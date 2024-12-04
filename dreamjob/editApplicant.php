<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


require_once "core/dbConfig.php";  
require_once "core/models.php";     


session_start();


if (!isset($_SESSION['userID'])) {
    
    header('Location: login.php');
    exit();
}


$userID = $_SESSION['userID'];


if (isset($_GET['applicantID'])) {
    $applicantID = $_GET['applicantID'];

    
    $query = "SELECT * FROM applicant WHERE applicantID = :applicantID";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':applicantID', $applicantID);
    $stmt->execute();
    $applicant = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$applicant) {
        
        header("Location: index.php");
        exit();
    }
} else {
    
    header("Location: index.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $contactInfo = $_POST['contact_info'];

    
    $sql = "UPDATE applicant SET 
            first_name = :first_name, 
            last_name = :last_name, 
            age = :age, 
            gender = :gender, 
            email = :email, 
            contact_info = :contact_info, 
            edited_by = :edited_by, 
            last_edited = NOW() 
            WHERE applicantID = :applicantID";

    
    $stmt = $pdo->prepare($sql);
    
   
    $stmt->bindParam(':applicantID', $applicantID);
    $stmt->bindParam(':first_name', $firstName);
    $stmt->bindParam(':last_name', $lastName);
    $stmt->bindParam(':age', $age);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':contact_info', $contactInfo);
    $stmt->bindParam(':edited_by', $userID);  

    
    if ($stmt->execute()) {
       
        $_SESSION['message'] = "Applicant updated successfully.";
        header("Location: index.php");
        exit();
    } else {
        
        $_SESSION['message'] = "Failed to update applicant.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Applicant</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>

<h2>Edit Applicant</h2>


<?php if (isset($_SESSION['message'])) { ?>
    <h3 class="message"><?php echo $_SESSION['message']; ?></h3>
<?php } unset($_SESSION['message']); ?>


<form action="editApplicant.php?applicantID=<?php echo $applicantID; ?>" method="POST">
    <p>
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($applicant['first_name']); ?>" required>
    </p>
    <p>
        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($applicant['last_name']); ?>" required>
    </p>
    <p>
        <label for="age">Age:</label>
        <input type="number" name="age" id="age" value="<?php echo $applicant['age']; ?>" required>
    </p>
    <p>
        <label for="gender">Gender:</label>
        <select name="gender" id="gender" required>
            <option value="Male" <?php if ($applicant['gender'] == 'Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if ($applicant['gender'] == 'Female') echo 'selected'; ?>>Female</option>
            <option value="Other" <?php if ($applicant['gender'] == 'Other') echo 'selected'; ?>>Other</option>
        </select>
    </p>
    <p>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($applicant['email']); ?>" required>
    </p>
    <p>
        <label for="contact_info">Contact Info:</label>
        <input type="text" name="contact_info" id="contact_info" value="<?php echo htmlspecialchars($applicant['contact_info']); ?>" required>
    </p>
    <p>
        <input type="submit" name="submit" value="Update Applicant">
    </p>
</form>

</body>
</html>

<?php
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_POST['insertApplicantBtn'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $contact_info = $_POST['contact_info'];
 
    $function = addApplicant($pdo, $first_name, $last_name, $age, $gender, $email, $contact_info);

    if($function){
        $_SESSION['message'] = "Successfully inserted applicant!";
            header("Location: ../index.php");

            if($function['statusCode'] == "200") {
                $_SESSION['message'] = $function['message'];
                header('Location: ../index.php');
            } else {
                $_SESSION['message'] = "Error " . $function['statusCode'] . ": " . $function['message'];
                header('Location: ../index.php');
        }
    }
}

if(isset($_POST['editApplicantBtn'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $contact_info = $_POST['contact_info'];
    $applicantID = $_GET['applicantID'];

    $function = editApplicant($pdo, $first_name, $last_name, $age, $gender, $email, $contact_info, $applicantID);

    if($function['statusCode'] == "200") {
        $_SESSION['message'] = $function['message'];
        header('Location: ../index.php');
    } else {
        $_SESSION['message'] = "Error " . $function['statusCode'] . ": " . $function['message'];
        header('Location: ../index.php');
    }
}

if(isset($_POST['deleteApplicantBtn'])) {
    $function = deleteApplicantByID($pdo, $_GET['applicantID']);

    if($function['statusCode'] == "200") {
        $_SESSION['message'] = $function['message'];
        header('Location: ../index.php');
    } else {
        $_SESSION['message'] = "Successfully Deleted " . $function['statusCode'] . ": " . $function['message'];
        header('Location: ../index.php');
    }
}

if (isset($_GET['searchBtn'])) {
    $searchForApplicant = searchForApplicant($pdo, $_GET['searchInput']);
    foreach ($searchForApplicant as $row) {
        echo "<try>
                <td>{$row['applicantID']}</td>
				<td>{$row['first_name']}</td>
				<td>{$row['last_name']}</td>
				<td>{$row['age']}</td>
				<td>{$row['gender']}</td>
				<td>{$row['email']}</td>
				<td>{$row['contact_info']}</td>
                </tr>";
    }
}
?>
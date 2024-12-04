<?php

require_once 'dbConfig.php';

function getAllApplicants($pdo, $limit = null, $offset = null) {
    $sql = "SELECT * FROM applicant ORDER BY applicantID ASC";
    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT :limit OFFSET :offset";
    }

    $stmt = $pdo->prepare($sql);
    if ($limit !== null && $offset !== null) {
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    }

    if ($stmt->execute()) {
        return [
            "statusCode" => 200,
            "querySet" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    } else {
        return [
            "statusCode" => 500,
            "message" => "Database error while fetching applicants."
        ];
    }
}

function getApplicantByID($pdo, $applicantID) {
    $sql = "SELECT * FROM applicant WHERE applicantID = :applicantID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':applicantID', $applicantID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return [
                "statusCode" => 200,
                "querySet" => $result
            ];
        } else {
            return [
                "statusCode" => 404,
                "message" => "Applicant not found."
            ];
        }
    } else {
        return [
            "statusCode" => 500,
            "message" => "Database error while fetching the applicant."
        ];
    }
}

function searchForApplicant($pdo, $searchQuery) {
    $sql = "SELECT * FROM applicant 
            WHERE first_name LIKE :query 
               OR last_name LIKE :query 
               OR email LIKE :query 
               OR contact_info LIKE :query";
    $stmt = $pdo->prepare($sql);
    $param = "%$searchQuery%";
    $stmt->bindParam(':query', $param, PDO::PARAM_STR);

    if ($stmt->execute()) {
        return [
            "statusCode" => 200,
            "querySet" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    } else {
        return [
            "statusCode" => 500,
            "message" => "Error while searching applicants."
        ];
    }
}

function editApplicant($pdo, $applicantID, $first_name, $last_name, $age, $gender, $email, $contact_info, $edited_by) {
    $query = "UPDATE applicant 
              SET first_name = :first_name, last_name = :last_name, age = :age, 
                  gender = :gender, email = :email, contact_info = :contact_info, 
                  edited_by = :edited_by 
              WHERE applicantID = :applicantID";
    $stmt = $pdo->prepare($query);

    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':age', $age, PDO::PARAM_INT);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':contact_info', $contact_info);
    $stmt->bindParam(':edited_by', $edited_by);
    $stmt->bindParam(':applicantID', $applicantID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        return [
            "statusCode" => 200,
            "message" => "Applicant updated successfully."
        ];
    } else {
        return [
            "statusCode" => 500,
            "message" => "Failed to update applicant."
        ];
    }
}

function deleteApplicantByID($pdo, $applicantID, $deletedBy) {
    try {
        $pdo->beginTransaction();

        $query = "SELECT * FROM applicant WHERE applicantID = :applicantID";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':applicantID', $applicantID, PDO::PARAM_INT);
        $stmt->execute();
        $applicantData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$applicantData) {
            throw new Exception("Applicant not found.");
        }

        $logQuery = "INSERT INTO deleted_applicants 
                     (applicantID, first_name, last_name, age, gender, email, contact_info, deleted_by) 
                     VALUES (:applicantID, :first_name, :last_name, :age, :gender, :email, :contact_info, :deleted_by)";
        $logStmt = $pdo->prepare($logQuery);
        $logStmt->execute([
            ':applicantID' => $applicantData['applicantID'],
            ':first_name' => $applicantData['first_name'],
            ':last_name' => $applicantData['last_name'],
            ':age' => $applicantData['age'],
            ':gender' => $applicantData['gender'],
            ':email' => $applicantData['email'],
            ':contact_info' => $applicantData['contact_info'],
            ':deleted_by' => $deletedBy
        ]);

        $deleteQuery = "DELETE FROM applicant WHERE applicantID = :applicantID";
        $deleteStmt = $pdo->prepare($deleteQuery);
        $deleteStmt->bindParam(':applicantID', $applicantID, PDO::PARAM_INT);
        $deleteStmt->execute();

        $pdo->commit();
        return [
            "statusCode" => 200,
            "message" => "Applicant deleted successfully and logged."
        ];
    } catch (Exception $e) {
        $pdo->rollBack();
        return [
            "statusCode" => 500,
            "message" => "Failed to delete applicant: " . $e->getMessage()
        ];
    }
}

function addApplicant($pdo, $first_name, $last_name, $age, $gender, $email, $contact_info, $added_by) {
    $query = "INSERT INTO applicant 
              (first_name, last_name, age, gender, email, contact_info, added_by) 
              VALUES (:first_name, :last_name, :age, :gender, :email, :contact_info, :added_by)";
    $stmt = $pdo->prepare($query);

    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':age', $age, PDO::PARAM_INT);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':contact_info', $contact_info);
    $stmt->bindParam(':added_by', $added_by);

    if ($stmt->execute()) {
        return [
            "statusCode" => 200,
            "message" => "Applicant added successfully."
        ];
    } else {
        return [
            "statusCode" => 500,
            "message" => "Failed to add applicant."
        ];
    }
}

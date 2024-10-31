<?php
session_start();

// Include database connection file
require_once 'db_connection.php';

// Get the event ID from the query string
$eventId = isset($_GET['eventId']) ? htmlspecialchars($_GET['eventId']) : null;
if (!$eventId) {
    die(json_encode(["message" => "Event ID is required."]));
}

// Ensure that the required fields are present in the POST request
$action = $_POST['action'] ?? null;
$registrationNumber = $_POST['registrationNumber'] ?? null;
if (!$action || !$registrationNumber) {
    die(json_encode(["message" => "Invalid request parameters."]));
}

// Define the new approval status based on the action
$approvalStatus = $action === 'confirm' ? 'Confirmed' : 'Rejected';

try {
    // Prepare the SQL statement to update the approval status
    $stmt = $pdo->prepare("UPDATE registration 
                           SET approval = :approval 
                           WHERE eventID = :eventId AND studentRegNo = :registrationNumber");
    $stmt->bindParam(':approval', $approvalStatus, PDO::PARAM_STR);
    $stmt->bindParam(':eventId', $eventId, PDO::PARAM_STR);
    $stmt->bindParam(':registrationNumber', $registrationNumber, PDO::PARAM_STR);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(["message" => "Registration status updated successfully."]);
    } else {
        echo json_encode(["message" => "Failed to update registration status."]);
    }
} catch (PDOException $e) {
    echo json_encode(["message" => "Error: " . $e->getMessage()]);
}
?>

<?php
session_start();

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

// Path to the JSON file based on event ID
$file = "registrations/registration_{$eventId}.json";

// Load the registrations data
$registrations = json_decode(file_get_contents($file), true);

// Update the registration based on the action
$updated = false;
foreach ($registrations as &$registration) {
    if ($registration['registrationNumber'] === $registrationNumber) {
        $registration['approval'] = $action === 'confirm' ? 'Confirmed' : 'Rejected';
        $updated = true;
        break;
    }
}

// Save the updated registrations back to the file
if ($updated) {
    file_put_contents($file, json_encode($registrations, JSON_PRETTY_PRINT));
    echo json_encode(["message" => "Registration status updated successfully."]);
} else {
    echo json_encode(["message" => "Registration not found."]);
}

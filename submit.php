<?php
require_once 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request method.");
}

$form_id = isset($_POST['form_id']) ? (int)$_POST['form_id'] : 0;
if ($form_id === 0) {
    die("Error: Form ID is missing from submission.");
}

$conn->begin_transaction();
$response_id = null;

try {

    $response_stmt = $conn->prepare("INSERT INTO form_responses (form_id, user_id) VALUES (?, NULL)");
    if (!$response_stmt) throw new Exception("Response header prep failed: " . $conn->error);
    
    $response_stmt->bind_param("i", $form_id);
    $response_stmt->execute();
    $response_id = $conn->insert_id;
    $response_stmt->close();
    $value_stmt = $conn->prepare(
        "INSERT INTO form_response_values (response_id, field_id, value) VALUES (?, ?, ?)"
    );
    if (!$value_stmt) throw new Exception("Response value prep failed: " . $conn->error);
    foreach ($_POST as $key => $post_value) {
        if ($key === 'form_id') continue;
        if (preg_match('/^field_(\d+)/', $key, $matches)) {
            $field_id = (int)$matches[1];
            $value = '';

            if (is_array($post_value)) {
                $value = implode(", ", array_map('trim', $post_value));
            } else {
                $value = trim($post_value);
            }

            if (!empty($value)) {
                $value_stmt->bind_param("iis", $response_id, $field_id, $value);
                $value_stmt->execute();
            }
        }
    }
    
    $value_stmt->close();
    $conn->commit();
    echo "<!DOCTYPE html><html><head><title>Success</title><style>body { font-family: Arial, sans-serif; padding: 40px; text-align: center; } h1 { color: #4CAF50; }</style></head><body>";
    echo "<h1>Submission Successful!</h1>";
    echo "<p>Your response has been recorded (Response ID: **{$response_id}**).</p>";
    echo "<p><a href='forms_list.php'>Return to Forms List</a></p>";
    echo "</body></html>";
    
} catch (Exception $e) {
    $conn->rollback();
    echo "<!DOCTYPE html><html><head><title>Error</title><style>body { font-family: Arial, sans-serif; padding: 40px; text-align: center; } h1 { color: red; }</style></head><body>";
    echo "<h1>Submission Failed!</h1>";
    echo "<p>An error occurred. Please try again later. Error details: " . $e->getMessage() . "</p>";
    echo "<p><a href='forms_list.php'>Return to Forms List</a></p>";
    echo "</body></html>";
}

$conn->close();
?>
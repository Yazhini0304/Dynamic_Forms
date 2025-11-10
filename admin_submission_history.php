<?php
require_once 'db.php';

$form_id = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
if ($form_id === 0) die("Missing Form ID.");


$form_name = "Unknown Form";
$name_stmt = $conn->prepare("SELECT form_name FROM forms WHERE id = ?");
$name_stmt->bind_param("i", $form_id);
$name_stmt->execute();
$name_result = $name_stmt->get_result();
if ($name_data = $name_result->fetch_assoc()) {
    $form_name = $name_data['form_name'];
}
$name_stmt->close();
$responses = [];
$responses_sql = "SELECT id, submitted_at, user_id FROM form_responses WHERE form_id = ? ORDER BY submitted_at DESC";
$responses_stmt = $conn->prepare($responses_sql);
$responses_stmt->bind_param("i", $form_id);
$responses_stmt->execute();
$responses_result = $responses_stmt->get_result();

while($row = $responses_result->fetch_assoc()) {
    $responses[] = $row;
}
$responses_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submissions for <?php echo htmlspecialchars($form_name); ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #a3ace0ff}
        .response-table { width: 80%; border-collapse: collapse; margin-top: 20px; }
        .response-table th, .response-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .response-table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Submissions for: <?php echo htmlspecialchars($form_name); ?></h1>
    <a href="admin_responses_list.php">Back to Forms List</a>

    <?php if (!empty($responses)): ?>
        <table class="response-table">
            <thead>
                <tr>
                    <th>Response ID</th>
                    <th>Submitted At</th>
                    <th>User ID</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($responses as $response): ?>
                    <tr>
                        <td><?php echo $response['id']; ?></td>
                        <td><?php echo date("Y-m-d H:i:s", strtotime($response['submitted_at'])); ?></td>
                        <td><?php echo $response['user_id'] ?? 'Guest'; ?></td>
                        <td>
                            <a href="view_response.php?response_id=<?php echo $response['id']; ?>">View Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No submissions have been recorded for this form yet.</p>
    <?php endif; ?>
</body>
</html>
<?php
require_once 'db.php';

$response_id = isset($_GET['response_id']) ? (int)$_GET['response_id'] : 0;
if ($response_id === 0) {
    die("Response ID is missing.");
}
$details_sql = "
    SELECT 
        f.form_name,
        ff.label AS field_label,
        rv.value AS response_value,
        r.submitted_at
    FROM form_response_values rv
    JOIN form_fields ff ON rv.field_id = ff.id
    JOIN form_responses r ON rv.response_id = r.id
    JOIN forms f ON r.form_id = f.id
    WHERE rv.response_id = ?
    ORDER BY ff.sort_order ASC
";

$details_stmt = $conn->prepare($details_sql);
$details_stmt->bind_param("i", $response_id);
$details_stmt->execute();
$details_result = $details_stmt->get_result();

$responses = [];
$form_name = "Unknown Form";
$submitted_at = "";

while($row = $details_result->fetch_assoc()) {
    if (empty($form_name)) {
        $form_name = $row['form_name'];
        $submitted_at = date("Y-m-d H:i:s", strtotime($row['submitted_at']));
    }
    $responses[] = $row;
}
$details_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Response <?php echo $response_id; ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px;background-color: #a3ace0ff }
        table { width: 60%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Response Details <?php echo $response_id; ?></h1>
    
    <?php if (!empty($responses)): ?>
        
        <p>Submitted: <?php echo $submitted_at; ?></p>
        
        <table>
            <thead>
                <tr>
                    <th>Field Label</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($responses as $response): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($response['field_label']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($response['response_value'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No data found for this submission.</p>
    <?php endif; ?>
    
    <br>
    <a href="javascript:history.back()"> Back to Submissions History</a>
</body>
</html>
<?php
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request method.");
}

$form_name = trim($_POST['form_name'] ?? '');
$labels = $_POST['label'] ?? [];
$types = $_POST['type'] ?? [];
$required = $_POST['required'] ?? [];
$placeholders = $_POST['placeholder'] ?? [];
$sort_orders = $_POST['sort_order'] ?? [];
$options_map = $_POST['option_text'] ?? []; 

if (empty($form_name) || empty($labels)) {
    die("Form name and at least one field are required.");
}

$conn->begin_transaction();
$form_id = null; 

try {

    $form_stmt = $conn->prepare("INSERT INTO forms (form_name) VALUES (?)");
    if (!$form_stmt) throw new Exception("Form statement preparation failed: " . $conn->error);
    
    $form_stmt->bind_param("s", $form_name);
    $form_stmt->execute();
    $form_id = $conn->insert_id;
    $form_stmt->close();

   
    $field_stmt = $conn->prepare(
        "INSERT INTO form_fields (form_id, label, type, required, placeholder, sort_order) 
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    if (!$field_stmt) throw new Exception("Field statement preparation failed: " . $conn->error);

    $option_stmt = $conn->prepare(
        "INSERT INTO field_options (field_id, option_text) VALUES (?, ?)"
    );
    if (!$option_stmt) throw new Exception("Option statement preparation failed: " . $conn->error);

    $field_count = count($labels);
    
    for ($i = 0; $i < $field_count; $i++) {
        
        $label = trim($labels[$i]);
        $type = $types[$i];
        $is_required = (int)($required[$i] ?? 0); 
        $placeholder = !empty($placeholders[$i]) ? trim($placeholders[$i]) : null;
        $sort_order = (int)($sort_orders[$i] ?? $i); 
        
        $field_stmt->bind_param("issisi", $form_id, $label, $type, $is_required, $placeholder, $sort_order);
        $field_stmt->execute();
        $field_id = $conn->insert_id; 

        if (in_array($type, ['dropdown', 'radio', 'checkbox'])) {
            $current_field_index = (int)($sort_orders[$i] ?? $i); 

            if (isset($options_map[$current_field_index]) && is_array($options_map[$current_field_index])) {
                foreach ($options_map[$current_field_index] as $option_text) {
                    $opt_text = trim($option_text);
                    if (!empty($opt_text)) {
                        $option_stmt->bind_param("is", $field_id, $opt_text);
                        $option_stmt->execute();
                    }
                }
            }
        }
    }

    $field_stmt->close();
    $option_stmt->close();

    
    $conn->commit();
    
    echo "<h1>Form Configuration Saved!</h1>";
    echo "<p>The form **'{$form_name}'** (ID: **{$form_id}**) with {$field_count} fields was successfully configured.</p>";
    echo "<p><a href='form.php?form_id={$form_id}'>View the new dynamic form</a></p>";
    
} catch (Exception $e) {
    $conn->rollback();
    
    echo "<h1> Error Saving Form!</h1>";
    echo "<p>An error occurred: " . $e->getMessage() . "</p>";
}

$conn->close();
?>
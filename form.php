<?php
require_once 'db.php'; 

$form_id = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
if ($form_id === 0) {
    die("Error: Form ID is missing.");
}

$form_data = null;
$fields = [];
$name_stmt = $conn->prepare("SELECT form_name FROM forms WHERE id = ?");
$name_stmt->bind_param("i", $form_id);
$name_stmt->execute();
$name_result = $name_stmt->get_result();
if ($name_data = $name_result->fetch_assoc()) {
    $form_data = $name_data;
} else {
    die("Error: Form not found.");
}
$name_stmt->close();

$sql = "
    SELECT 
        ff.id AS field_id, 
        ff.label, 
        ff.type, 
        ff.required, 
        ff.placeholder,
        fo.option_text
    FROM form_fields ff
    LEFT JOIN field_options fo ON ff.id = fo.field_id
    WHERE ff.form_id = ?
    ORDER BY ff.sort_order ASC
";

$fields_stmt = $conn->prepare($sql);
$fields_stmt->bind_param("i", $form_id);
$fields_stmt->execute();
$fields_result = $fields_stmt->get_result();


while ($row = $fields_result->fetch_assoc()) {
    $field_id = $row['field_id'];
    
    
    if (!isset($fields[$field_id])) {
        $fields[$field_id] = [
            'id' => $field_id,
            'label' => $row['label'],
            'type' => $row['type'],
            'required' => (bool)$row['required'],
            'placeholder' => $row['placeholder'],
            'options' => []
        ];
    }
    
    
    if ($row['option_text']) {
        $fields[$field_id]['options'][] = $row['option_text'];
    }
}
$fields_stmt->close();
$conn->close();


function renderField($field) {
    $html = '<div class="form-group">';
    $required_attr = $field['required'] ? 'required' : '';
    $label_text = htmlspecialchars($field['label']) . ($field['required'] ? ' <span class="required-star">*</span>' : '');


    $name_attr = 'field_' . $field['id']; 

    $html .= '<label for="' . $name_attr . '">' . $label_text . '</label>';

    switch ($field['type']) {
        case 'text':
        case 'number':
        case 'date':
            $html .= '<input type="' . $field['type'] . '" name="' . $name_attr . '" id="' . $name_attr . '" ' . $required_attr . ' placeholder="' . htmlspecialchars($field['placeholder'] ?? '') . '">';
            break;

        case 'textarea':
            $html .= '<textarea name="' . $name_attr . '" id="' . $name_attr . '" rows="4" ' . $required_attr . ' placeholder="' . htmlspecialchars($field['placeholder'] ?? '') . '"></textarea>';
            break;

        case 'dropdown':
            $html .= '<select name="' . $name_attr . '" id="' . $name_attr . '" ' . $required_attr . '>';
            $html .= '<option value="">-- Select an option --</option>';
            foreach ($field['options'] as $option) {
                $opt_val = htmlspecialchars($option);
                $html .= '<option value="' . $opt_val . '">' . $opt_val . '</option>';
            }
            $html .= '</select>';
            break;

        case 'radio':
            $html .= '<div class="radio-group">';
            foreach ($field['options'] as $i => $option) {
                $opt_val = htmlspecialchars($option);
                $option_id = $name_attr . '_' . $i;
                $html .= '<input type="radio" name="' . $name_attr . '" id="' . $option_id . '" value="' . $opt_val . '" ' . $required_attr . '>';
                $html .= '<label for="' . $option_id . '">' . $opt_val . '</label><br>';
            }
            $html .= '</div>';
            break;

        case 'checkbox':
           
            $name_attr_array = $name_attr . '[]';
            $html .= '<div class="checkbox-group">';
            foreach ($field['options'] as $i => $option) {
                $opt_val = htmlspecialchars($option);
                $option_id = $name_attr . '_' . $i;
                $html .= '<input type="checkbox" name="' . $name_attr_array . '" id="' . $option_id . '" value="' . $opt_val . '">';
                $html .= '<label for="' . $option_id . '">' . $opt_val . '</label><br>';
            }
            $html .= '</div>';
            break;
    }

    $html .= '</div>';
    return $html;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($form_data['form_name'] ?? 'Dynamic Form'); ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #a3ace0ff }
        .form-group { margin-bottom: 20px; border: 1px solid #ddd; padding: 15px; border-radius: 4px; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input[type="text"], input[type="number"], input[type="date"], textarea, select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .required-star { color: red; }
        .radio-group label, .checkbox-group label { display: inline; font-weight: normal; margin-left: 5px; }
        .radio-group input, .checkbox-group input { width: auto; }
    </style>
</head>
<body>
    <h1>📋 <?php echo htmlspecialchars($form_data['form_name'] ?? 'Dynamic Form'); ?></h1>
    <p><a href="forms_list.php">← Back to Forms List</a></p>

    <form action="submit.php" method="POST">
        <input type="hidden" name="form_id" value="<?php echo $form_id; ?>">
        
        <?php 
        if (!empty($fields)) {
            foreach ($fields as $field) {
                echo renderField($field);
            }
        } else {
            echo "<p>This form is currently empty or improperly configured.</p>";
        }
        ?>

        <button type="submit" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Submit Form
        </button>
    </form>
</body>
</html>
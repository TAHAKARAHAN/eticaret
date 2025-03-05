<?php
// Helper function to generate properly formatted input fields with icons
function createInputField($type, $name, $id, $placeholder, $icon, $isRequired = true, $errorMsg = '') {
    $required = $isRequired ? 'required' : '';
    $errorMessage = !empty($errorMsg) ? $errorMsg : "Lütfen bu alanı doldurunuz.";
    
    $html = '<div class="form-group input-icon">';
    $html .= '<input type="'.$type.'" class="form-control" name="'.$name.'" id="'.$id.'" placeholder="'.$placeholder.'" '.$required.'>';
    $html .= '<i class="fas '.$icon.'"></i>';
    
    // Add toggle icon for password fields
    if ($type === 'password') {
        $html .= '<i class="fas fa-eye toggle-password" data-toggle="#'.$id.'"></i>';
    }
    
    $html .= '<div class="invalid-feedback">'.$errorMessage.'</div>';
    $html .= '</div>';
    
    return $html;
}

// Helper function for select fields
function createSelectField($name, $id, $options, $icon, $isRequired = true, $errorMsg = '') {
    $required = $isRequired ? 'required' : '';
    $errorMessage = !empty($errorMsg) ? $errorMsg : "Lütfen bir seçenek seçiniz.";
    
    $html = '<div class="form-group">';
    $html .= '<div class="custom-select-form">';
    $html .= '<select class="form-control" name="'.$name.'" id="'.$id.'" '.$required.'>';
    
    foreach ($options as $value => $label) {
        $html .= '<option value="'.$value.'">'.$label.'</option>';
    }
    
    $html .= '</select>';
    $html .= '<i class="fas '.$icon.'"></i>';
    $html .= '</div>';
    $html .= '<div class="invalid-feedback">'.$errorMessage.'</div>';
    $html .= '</div>';
    
    return $html;
}

// Helper function for checkbox/radio fields
function createCheckboxField($name, $id, $label, $value = "1", $isRequired = true, $errorMsg = '') {
    $required = $isRequired ? 'required' : '';
    $errorMessage = !empty($errorMsg) ? $errorMsg : "Bu alanı işaretlemeniz gerekiyor.";
    
    $html = '<div class="form-group">';
    $html .= '<label class="container_check">'.$label;
    $html .= '<input type="checkbox" name="'.$name.'" id="'.$id.'" value="'.$value.'" '.$required.'>';
    $html .= '<span class="checkmark"></span>';
    $html .= '</label>';
    $html .= '<div class="invalid-feedback">'.$errorMessage.'</div>';
    $html .= '</div>';
    
    return $html;
}

// Example usage:
// echo createInputField('email', 'mail', 'email', 'E-mail adresiniz', 'fa-envelope', true, 'Lütfen geçerli bir e-mail adresi giriniz.');
// echo createInputField('password', 'sifre', 'password_in', 'Şifreniz', 'fa-lock', true, 'Lütfen şifrenizi giriniz.');
?>

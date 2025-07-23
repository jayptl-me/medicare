<?php
// Form wrapper component for Medicare
function render_form($fields, $action = '', $method = 'post', $submitLabel = 'Submit') {
  echo '<form class="form" action="' . htmlspecialchars($action) . '" method="' . htmlspecialchars($method) . '">';
  foreach ($fields as $field) {
    echo '<label class="form__label" for="' . htmlspecialchars($field['id']) . '">' . htmlspecialchars($field['label']) . '</label>';
    if ($field['type'] === 'textarea') {
      echo '<textarea class="form__textarea" id="' . htmlspecialchars($field['id']) . '" name="' . htmlspecialchars($field['name']) . '" ' . ($field['required'] ? 'required' : '') . '>' . htmlspecialchars($field['value'] ?? '') . '</textarea>';
    } else {
      echo '<input class="form__input" type="' . htmlspecialchars($field['type']) . '" id="' . htmlspecialchars($field['id']) . '" name="' . htmlspecialchars($field['name']) . '" placeholder="' . htmlspecialchars($field['placeholder'] ?? '') . '" value="' . htmlspecialchars($field['value'] ?? '') . '" ' . ($field['required'] ? 'required' : '') . ' ' . ($field['pattern'] ? 'pattern="' . htmlspecialchars($field['pattern']) . '"' : '') . '>';
    }
  }
  echo '<input class="btn" type="submit" value="' . htmlspecialchars($submitLabel) . '">';
  echo '</form>';
}

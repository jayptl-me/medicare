<?php
// Alert component for Medicare
function render_alert($message, $type = 'error') {
  $class = 'alert';
  if ($type === 'success') $class .= ' alert--success';
  else $class .= ' alert--error';
  echo '<div class="' . $class . '">' . htmlspecialchars($message) . '</div>';
}

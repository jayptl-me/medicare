<?php
// Button component for Medicare
function render_button($label, $type = 'primary', $href = '#', $extra = '') {
  $class = 'btn';
  if ($type === 'secondary') $class .= ' btn--secondary';
  if ($type === 'danger') $class .= ' btn--danger';
  echo '<a href="' . htmlspecialchars($href) . '" class="' . $class . '" ' . $extra . '>' . htmlspecialchars($label) . '</a>';
}

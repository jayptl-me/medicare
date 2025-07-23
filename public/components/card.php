<?php
// Card component for Medicare
function render_card($title, $content) {
  echo '<div class="card">';
  if ($title) echo '<h2>' . htmlspecialchars($title) . '</h2>';
  echo $content;
  echo '</div>';
}

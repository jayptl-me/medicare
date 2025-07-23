<?php
// Header component for Medicare
?>
<header class="header">
  <div class="header__logo">Medicare</div>
  <nav class="header__nav">
    <a href="/home" class="header__nav-link">Home</a>
    <a href="/schedule" class="header__nav-link">Schedule</a>
    <a href="/logout" class="header__nav-link btn btn--danger" style="margin-left:8px;">Logout</a>
  </nav>
  <button class="header__menu" aria-label="Open menu" onclick="document.querySelector('.header__nav').classList.toggle('show')">&#9776;</button>
</header>
<style>
@media (max-width: 600px) {
  .header__nav.show {
    display: flex !important;
    flex-direction: column;
    position: absolute;
    top: 60px;
    right: 16px;
    background: var(--primary);
    border-radius: 0 0 8px 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    z-index: 10;
  }
}
</style>

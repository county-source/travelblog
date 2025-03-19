<?php
// Ověříme, jestli session neběží
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar">
  <div class="logo">Travel Blog<span>*</span></div>
  <div class="menu-toggle" id="mobile-menu">
    <span class="bar"></span>
    <span class="bar"></span>
    <span class="bar"></span>
  </div>
  <ul class="nav-links">
    <li><a href="index.php">Home</a></li>
    <li><a href="bloglist.php">Blogs</a></li>
    <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
      <li><a href="adminpanel.php">Panel</a></li>
      <li><a href="logout.php">Logout</a></li>
    <?php else: ?>
      <li><a href="login.php">Login</a></li>
      <li><a href="register.php">Register</a></li>
    <?php endif; ?>
  </ul>
</nav>

<script>
  // Mobile menu toggle
  const mobileMenu = document.getElementById("mobile-menu");
  const navLinks = document.querySelector(".nav-links");

  mobileMenu.addEventListener("click", () => {
    navLinks.classList.toggle("active");
  });
</script>

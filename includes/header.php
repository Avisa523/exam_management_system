<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
?>
<header>
    <!-- Left: Home Icon + Logo -->
    <div class="logo-section">
        <a href="dashboard.php" class="home-icon" title="Go to Dashboard">🏠</a>
        <span class="logo-text">My College</span>
    </div>

    <!-- Right: Profile Dropdown -->
    <div class="header-right">
        <span class="menu-dots" onclick="toggleDropdown()">⋮</span>
        <div class="dropdown-content" id="dropdown">
            <a href="profile.php">Profile</a>
            <a href="../authentication/logout.php">Logout</a>
        </div>
    </div>
</header>

<script>
function toggleDropdown() {
    document.getElementById("dropdown").classList.toggle("show");
}
window.onclick = function(e) {
    if (!e.target.matches('.menu-dots')) {
        var dropdown = document.getElementById("dropdown");
        if (dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
        }
    }
}
</script>
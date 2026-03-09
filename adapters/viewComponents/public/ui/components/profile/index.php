<?php
// Display user profile section with icon, username, and logout button
$userEmail = $_COOKIE['user'] ?? 'User';
$username = explode('@', $userEmail)[0];
?>
<div class="profile-container">
    <div class="profile-card">
        <div class="user-icon">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($username); ?>" 
                 alt="User Avatar" class="avatar">
        </div>
        <p class="username"><?php echo htmlspecialchars($username); ?></p>
        <form class="fetchform" method="POST" onEnd="auth.onLogout">
            <button type="submit" class="logout-btn">Log Out</button>
            <input type="hidden" name="adapter" value="userManager">
            <input type="hidden" name="endpoint" value="logout">
            <input type="hidden" name="sessionId" value="<?= $_SESSION['sessionId']; ?>">
        </form>
    </div>
</div>

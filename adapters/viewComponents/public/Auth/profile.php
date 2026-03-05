<?php
// Display user profile section with icon, username, and logout button
$userEmail = $_SESSION['user'] ?? 'User';
$username = explode('@', $userEmail)[0];
?>

<div class="profile-container">
    <div class="profile-card">
        <div class="user-icon">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($username); ?>" 
                 alt="User Avatar" class="avatar">
        </div>
        <p class="username"><?php echo htmlspecialchars($username); ?></p>
        <form class="fetchform" method="POST" onEnd="auth.onLogin">
            <button type="submit" class="logout-btn">Log Out</button>
            <input type="hidden" name="adapter" value="userManager">
            <input type="hidden" name="endpoint" value="logout">
            <input type="hidden" name="sessionId" value="<?= $_SESSION['sessionId']; ?>">
        </form>
    </div>
</div>

<style>
.profile-container {
    display: flex;
    justify-content: center;
    padding: 20px;
}

.profile-card {
    text-align: center;
    padding: 20px;
    border-radius: 8px;
    background-color: #f5f5f5;
}

.user-icon {
    margin-bottom: 15px;
}

.avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 2px solid #ddd;
}

.username {
    font-size: 18px;
    font-weight: 600;
    margin: 10px 0;
}

.logout-btn {
    padding: 10px 20px;
    background-color: #dc3545;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

.logout-btn:hover {
    background-color: #c82333;
}
</style>
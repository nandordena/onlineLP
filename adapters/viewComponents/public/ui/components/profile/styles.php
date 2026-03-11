<style>
    .com_profile {
        display: flex;
        flex-direction:column;
        justify-content: center;
        padding: var(--spacing);
        margin: calc(var(--spacing) * 2);
        text-align: center;
        border-radius: var(--spacing);
        color:var(--fnt-3);
        background: var(--bg-2);
    }
    .com_profile .user-icon {
        margin-bottom: var(--spacing);
    }
    .com_profile .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid #ddd;
    }
    .com_profile .username {
        font-size: 12px;
        font-weight: 600;
        margin: var(--spacing) 0;
    }
    .com_profile .logout-btn {
        padding: var(--spacing);
        margin: var(--spacing) 0;
        background: var(--bg-1);
        color: white;
        border: none;
        border-radius: var(--spacing);
        cursor: pointer;
        font-size: 14px;
    }
    .com_profile .logout-btn:hover {
        background: var(--color-exit);
    }
</style>

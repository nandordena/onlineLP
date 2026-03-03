<style>
    .auth {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        margin-bottom: 16px;
    }
    .auth form{
        margin: 8px 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }
    .auth hr{
        margin: 8px 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }
    .auth form[data-error]::after {
        content: "\A" attr(data-error);
        display: block;
        color: #d32f2f;
        background: none;
        font-size: 0.95em;
        margin-top: 6px;
        text-align: center;
        white-space: pre-line;
    }
</style>
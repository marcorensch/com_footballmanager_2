function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        UIkit.notification({
            message: Joomla.Text._("COM_FOOTBALLMANAGER_MATCHBALL_COPY_MAIL_SUCCESS"),
            status: 'success',
            pos: 'bottom-right',
            timeout: 2000
        });
    }).catch(err => {
        UIkit.notification({
            message: Joomla.Text._("COM_FOOTBALLMANAGER_MATCHBALL_COPY_MAIL_ERROR"),
            status: 'error',
            pos: 'bottom-right',
            timeout: 2000
        });
    });
}
/**
 * Theme Specific WP Rollback
 *
 * Adds a rollback option to themes
 */
document.addEventListener('DOMContentLoaded', () => {

    observeThemeChanges();

    const themes = wp.themes = wp.themes || {};
    themes.data = typeof _wpThemeSettings !== 'undefined' ? _wpThemeSettings : '';

    if (themes.data.themes.length === 1) {
        wprThemeRollback(themes.data.themes[0].id);
    }

    document.body.addEventListener('click', e => {
        if (e.target.matches('.wpr-theme-rollback')) {
            e.preventDefault();
            window.location.href = e.target.getAttribute('href');
        }
    });

});

const observeThemeChanges = () => {
    const observer = new MutationObserver(mutations => {
        for (let mutation of mutations) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(node => {
                    if (node.matches && node.matches('.theme-overlay')) {
                        // Check if the rollback button is not there, then add it
                        if (!isRollbackButtonThere()) {
                            const queryArgs = new URLSearchParams(window.location.search);
                            const theme = queryArgs.get('theme');
                            wprThemeRollback(theme);
                        }
                    }
                });
            }
        }
    });

    const stableParent = document.querySelector('.wrap');
    if (stableParent) {
        observer.observe(stableParent, { childList: true, subtree: true });
    }
};


const isRollbackButtonThere = () => document.querySelector('.wpr-theme-rollback') !== null;

const wprThemeRollback = theme => {
    const themeData = wprGetThemeData(theme);

    if (themeData !== null && themeData.hasRollback) {
        const rollbackButtonHtml = `<a href="index.php?page=wp-rollback&type=theme&theme_file=${theme}&current_version=${themeData.version}&rollback_name=${encodeURIComponent(themeData.name)}&_wpnonce=${wprData.nonce}" class="button wpr-theme-rollback">${wprData.text_rollback_label}</a>`;
        document.querySelector('.theme-wrap .theme-actions').insertAdjacentHTML('beforeend', rollbackButtonHtml);
    } else {
        document.querySelector('.theme-wrap .theme-actions').insertAdjacentHTML('beforeend', `<span class="no-rollback" style="position: absolute;left: 23px;bottom: 16px;font-size: 12px;font-style: italic;color: rgb(181, 181, 181);">${wprData.text_not_rollbackable}</span>`);
    }
};

const wprGetThemeData = theme => {
    const themeData = wp.themes?.data?.themes;
    if (!Array.isArray(themeData)) {
        console.error('Invalid theme data');
        return null;
    }
    return themeData.find(t => t.id === theme) || null;
};

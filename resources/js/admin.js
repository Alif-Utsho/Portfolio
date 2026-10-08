const sidebar = document.querySelector('#admin-sidebar');
const sidebarToggle = document.querySelector('.sidebar-toggle');

sidebarToggle?.addEventListener('click', () => {
    setSidebarOpen(sidebarToggle.getAttribute('aria-expanded') !== 'true');
});

const sidebarOverlay = document.querySelector('.admin-sidebar-overlay');
const setSidebarOpen = (open) => {
    sidebarToggle?.setAttribute('aria-expanded', String(open));
    sidebarToggle?.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
    sidebar?.classList.toggle('is-open', open);
    sidebarOverlay?.classList.toggle('is-visible', open);
    document.body.classList.toggle('admin-menu-open', open);
};
sidebarOverlay?.addEventListener('click', () => setSidebarOpen(false));
sidebar?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setSidebarOpen(false)));
document.addEventListener('keydown', (event) => { if (event.key === 'Escape') setSidebarOpen(false); });
document.addEventListener('click', (event) => {
    if (sidebar?.classList.contains('is-open') && !sidebar.contains(event.target) && !sidebarToggle?.contains(event.target)) setSidebarOpen(false);
});

document.querySelectorAll('[data-analytics-filter-toggle]').forEach((toggle) => {
    const fields = document.getElementById(toggle.getAttribute('aria-controls'));
    const form = fields?.closest('.analytics-filters');
    if (!form) return;
    toggle.addEventListener('click', () => {
        const isOpen = form.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(isOpen));
        const label = isOpen ? 'Hide filters' : 'Show filters';
        toggle.setAttribute('aria-label', label);
        toggle.setAttribute('title', label);
        toggle.querySelector('.sr-only').textContent = label;
    });
});

const realtimeWidget = document.querySelector('[data-realtime-widget]');
if (realtimeWidget) {
    const visitorList = realtimeWidget.querySelector('[data-live-list]');
    const liveStatus = realtimeWidget.querySelector('[data-live-status]');
    const liveCount = document.querySelector('[data-live-count]');
    const renderLive = async () => {
        if (document.visibilityState !== 'visible') return;
        liveStatus.textContent = 'Refreshing live activity…';
        try {
            const response = await fetch(realtimeWidget.dataset.liveUrl, { headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error('Live analytics request failed.');
            const data = await response.json();
            liveCount.textContent = data.count;
            liveStatus.textContent = 'Updated just now';
            visitorList.replaceChildren();
            if (!data.visitors.length) {
                const empty = document.createElement('div');
                empty.className = 'empty-panel';
                empty.textContent = 'No visitors are active right now.';
                visitorList.append(empty);
            } else {
                data.visitors.forEach((visitor) => {
                    const row = document.createElement('div');
                    row.className = 'analytics-live-row';
                    const page = document.createElement('strong');
                    page.textContent = visitor.path;
                    const details = document.createElement('span');
                    details.textContent = [visitor.city, visitor.country, visitor.device].filter(Boolean).join(' · ');
                    const time = document.createElement('time');
                    time.textContent = new Date(`${visitor.last_activity_at.replace(' ', 'T')}Z`).toLocaleTimeString();
                    row.append(page, details, time);
                    visitorList.append(row);
                });
            }
        } catch {
            liveStatus.textContent = 'Could not load live activity.';
            const retry = document.createElement('button');
            retry.type = 'button';
            retry.className = 'admin-button admin-button-quiet';
            retry.textContent = 'Retry';
            retry.addEventListener('click', renderLive, { once: true });
            visitorList.replaceChildren(retry);
        }
    };
    renderLive();
    window.setInterval(renderLive, 30000);
    document.addEventListener('visibilitychange', renderLive);
}

document.querySelectorAll('[data-date-preset]').forEach((selector) => {
    const form = selector.closest('form');
    const from = form?.querySelector('[name="from"]');
    const to = form?.querySelector('[name="to"]');
    selector.addEventListener('change', () => {
        const value = selector.value;
        if (!from || !to || value === 'custom') return;
        const today = new Date();
        const end = new Date(Date.UTC(today.getUTCFullYear(), today.getUTCMonth(), today.getUTCDate()));
        if (value === 'yesterday') end.setUTCDate(end.getUTCDate() - 1);
        const start = new Date(end);
        if (value !== 'today' && value !== 'yesterday') start.setUTCDate(end.getUTCDate() - (Number(value) - 1));
        const formatDate = (date) => `${date.getUTCFullYear()}-${String(date.getUTCMonth() + 1).padStart(2, '0')}-${String(date.getUTCDate()).padStart(2, '0')}`;
        from.value = formatDate(start);
        to.value = formatDate(end);
    });
    from?.addEventListener('change', () => { selector.value = 'custom'; });
    to?.addEventListener('change', () => { selector.value = 'custom'; });
});

const dashboardOnline = document.querySelector('[data-dashboard-online]');
if (dashboardOnline) {
    const updateCount = () => {
        if (document.visibilityState !== 'visible') return;
        fetch(dashboardOnline.dataset.liveUrl, { headers: { Accept: 'application/json' } }).then((response) => response.json()).then((data) => {
            dashboardOnline.textContent = data.count;
            document.querySelectorAll('[data-dashboard-online-copy]').forEach((element) => { element.textContent = data.count; });
        }).catch(() => {});
    };
    updateCount();
    window.setInterval(updateCount, 30000);
    document.addEventListener('visibilitychange', updateCount);
}

document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

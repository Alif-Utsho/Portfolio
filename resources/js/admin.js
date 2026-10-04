const sidebar = document.querySelector('#admin-sidebar');
const sidebarToggle = document.querySelector('.sidebar-toggle');

sidebarToggle?.addEventListener('click', () => {
    const isExpanded = sidebarToggle.getAttribute('aria-expanded') === 'true';

    sidebarToggle.setAttribute('aria-expanded', String(!isExpanded));
    sidebar?.classList.toggle('is-open', !isExpanded);
});

document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

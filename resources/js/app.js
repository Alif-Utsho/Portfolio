const root = document.documentElement;
const header = document.querySelector('.site-header');
const themeToggle = document.querySelector('.theme-toggle');
const menuToggle = document.querySelector('.menu-toggle');
const navigation = document.querySelector('.nav-links');

const savedTheme = window.localStorage.getItem('portfolio-theme');

if (savedTheme === 'light' || savedTheme === 'dark') {
    root.dataset.theme = savedTheme;
}

const updateThemeControl = () => {
    const isDark = root.dataset.theme === 'dark';

    themeToggle?.setAttribute('aria-label', `Switch to ${isDark ? 'light' : 'dark'} theme`);
    const themeIcon = themeToggle?.querySelector('.theme-icon');
    if (themeIcon) themeIcon.textContent = isDark ? '◐' : '☼';
    document.querySelector('meta[name="theme-color"]').setAttribute('content', isDark ? '#111411' : '#f3f3ee');
};

updateThemeControl();

themeToggle?.addEventListener('click', () => {
    root.dataset.theme = root.dataset.theme === 'dark' ? 'light' : 'dark';
    window.localStorage.setItem('portfolio-theme', root.dataset.theme);
    updateThemeControl();
});

const updateHeader = () => {
    header?.classList.toggle('is-scrolled', window.scrollY > 18);
};

updateHeader();
window.addEventListener('scroll', updateHeader, { passive: true });

menuToggle?.addEventListener('click', () => {
    const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';

    menuToggle.setAttribute('aria-expanded', String(!isExpanded));
    menuToggle.setAttribute('aria-label', isExpanded ? 'Open navigation' : 'Close navigation');
    navigation.classList.toggle('is-open', !isExpanded);
});

navigation?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
        navigation.classList.remove('is-open');
        menuToggle.setAttribute('aria-expanded', 'false');
        menuToggle.setAttribute('aria-label', 'Open navigation');
    });
});

const revealObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.12 });

document.querySelectorAll('.reveal').forEach((element) => revealObserver.observe(element));

const sectionObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (!entry.isIntersecting) {
            return;
        }

        navigation?.querySelectorAll('a').forEach((link) => {
            link.classList.toggle('is-active', link.hash === `#${entry.target.id}`);
        });
    });
}, { rootMargin: '-28% 0px -62% 0px' });

document.querySelectorAll('main section[id]').forEach((section) => sectionObserver.observe(section));

document.querySelectorAll('.timeline-expand').forEach((button) => {
    button.addEventListener('click', () => {
        const item = button.closest('.timeline-item');
        const isExpanded = button.getAttribute('aria-expanded') === 'true';

        button.setAttribute('aria-expanded', String(!isExpanded));
        button.setAttribute('aria-label', isExpanded ? 'Show role details' : 'Hide role details');
        item.classList.toggle('is-open', !isExpanded);
    });
});

document.querySelectorAll('.filter-button').forEach((button) => {
    button.addEventListener('click', () => {
        const selectedFilter = button.dataset.filter;

        document.querySelectorAll('.filter-button').forEach((filterButton) => {
            const isSelected = filterButton === button;

            filterButton.classList.toggle('is-active', isSelected);
            filterButton.setAttribute('aria-pressed', String(isSelected));
        });

        document.querySelectorAll('.project-card').forEach((card) => {
            card.hidden = selectedFilter !== 'all' && card.dataset.category !== selectedFilter;
        });
    });
});

const updateGitHubSnapshot = async () => {
    const githubCard = document.querySelector('[data-github-username]');
    if (!githubCard) return;
    const username = githubCard.dataset.githubUsername;
    const profileResponse = await fetch(`https://api.github.com/users/${encodeURIComponent(username)}`, {
        headers: { Accept: 'application/vnd.github+json' },
    });

    if (!profileResponse.ok) {
        throw new Error('GitHub profile data is unavailable.');
    }

    const profile = await profileResponse.json();
    const repositoriesResponse = await fetch(`https://api.github.com/users/${encodeURIComponent(username)}/repos?per_page=100`, {
        headers: { Accept: 'application/vnd.github+json' },
    });

    if (!repositoriesResponse.ok) {
        throw new Error('GitHub repository data is unavailable.');
    }

    const repositories = await repositoriesResponse.json();
    const totalStars = repositories.reduce((total, repository) => total + repository.stargazers_count, 0);
    const languageTotals = repositories.reduce((totals, repository) => {
        if (repository.language) {
            totals[repository.language] = (totals[repository.language] ?? 0) + 1;
        }

        return totals;
    }, {});
    const visibleLanguages = [
        ['PHP', 'php'],
        ['JavaScript', 'js'],
        ['C#', 'csharp'],
        ['Java', 'java'],
    ];
    const visibleCount = visibleLanguages.reduce((total, [language]) => total + (languageTotals[language] ?? 0), 0);
    const otherCount = Math.max(repositories.length - visibleCount, 0);
    const languageBar = document.querySelector('[data-language-bar]');

    document.querySelector('[data-github-stat="repos"]').textContent = profile.public_repos;
    document.querySelector('[data-github-stat="followers"]').textContent = profile.followers;
    document.querySelector('[data-github-stat="stars"]').textContent = totalStars;

    visibleLanguages.forEach(([language, className]) => {
        const count = languageTotals[language] ?? 0;
        const segment = languageBar.querySelector(`.language-${className}`);

        segment.style.width = `${repositories.length ? (count / repositories.length) * 100 : 0}%`;
        segment.title = `${language}: ${count} repositories`;
    });

    const otherSegment = languageBar.querySelector('.language-other');

    otherSegment.style.width = `${repositories.length ? (otherCount / repositories.length) * 100 : 0}%`;
    otherSegment.title = `Other or unclassified: ${otherCount} repositories`;
};

updateGitHubSnapshot().catch(() => {
    const status = document.querySelector('.github-live');
    if (status) status.textContent = 'PROFILE SNAPSHOT';
});

const analyticsBody = document.body;
const analyticsEventUrl = analyticsBody.dataset.analyticsEventUrl;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
const sendAnalyticsEvent = (event, extra = {}) => {
    if (!analyticsEventUrl || document.cookie.split('; ').includes('analytics_opt_out=1')) return;
    fetch(analyticsEventUrl, {
        method: 'POST',
        credentials: 'same-origin',
        keepalive: true,
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken ?? '' },
        body: JSON.stringify({ event, path: `${location.pathname}${location.hash}`, project_id: analyticsBody.dataset.projectId || null, ...extra }),
    }).catch(() => {});
};

let pageStartedAt = Date.now();
let formStarted = false;
document.querySelector('#contact-form')?.addEventListener('focusin', () => {
    if (!formStarted) { formStarted = true; sendAnalyticsEvent('contact_form_start'); }
}, { once: true });
const contactSection = document.querySelector('#contact');
if (contactSection && 'IntersectionObserver' in window) {
    const sectionObserver = new IntersectionObserver((entries) => {
        if (entries.some((entry) => entry.isIntersecting)) {
            sendAnalyticsEvent('contact_section_view');
            sectionObserver.disconnect();
        }
    }, { threshold: 0.25 });
    sectionObserver.observe(contactSection);
}
document.querySelectorAll('a[href]').forEach((link) => {
    link.addEventListener('click', () => {
        const href = link.getAttribute('href') || '';
        const label = `${link.textContent || ''} ${href}`.toLowerCase();
        if (href.startsWith('mailto:')) sendAnalyticsEvent('email_click', { target: 'email' });
        else if (/\.pdf(?:$|[?#])/i.test(href) || /resume|curriculum vitae|download cv/i.test(label)) {
            sendAnalyticsEvent('cv_click', { target: 'cv' });
            if (/\.pdf(?:$|[?#])/i.test(href) || /download cv/i.test(label)) sendAnalyticsEvent('cv_download', { target: 'cv' });
        }
        else if (/repository|github|source code/i.test(label)) sendAnalyticsEvent('repository_click', { target: 'repository' });
        else if (/live project|live demo|demo/i.test(label)) sendAnalyticsEvent('demo_click', { target: 'demo' });
        else if (link.closest('.hero-socials, .site-footer')) sendAnalyticsEvent('social_click', { target: 'social' });
        else if (link.closest('.nav-links')) sendAnalyticsEvent('navigation_click', { target: 'navigation' });
    });
});
window.addEventListener('pagehide', () => sendAnalyticsEvent('page_duration', { duration: Math.round((Date.now() - pageStartedAt) / 1000) }));

const analyticsNotice = document.querySelector('[data-analytics-notice]');
const analyticsOptOut = document.cookie.split('; ').includes('analytics_opt_out=1') || localStorage.getItem('analytics-opted-out') === '1';
if (analyticsNotice && !analyticsOptOut && !localStorage.getItem('analytics-notice-dismissed')) analyticsNotice.hidden = false;
document.querySelector('[data-analytics-opt-out]')?.addEventListener('click', async () => {
    document.cookie = 'analytics_opt_out=1; Max-Age=34128000; Path=/; SameSite=Lax';
    localStorage.setItem('analytics-opted-out', '1');
    localStorage.setItem('analytics-notice-dismissed', '1');
    analyticsNotice.hidden = true;
    try { await fetch(analyticsBody.dataset.analyticsOptOutUrl, { method: 'POST', credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': csrfToken ?? '', Accept: 'application/json' } }); } catch {}
});
document.querySelector('[data-analytics-notice-close]')?.addEventListener('click', () => {
    analyticsNotice.hidden = true;
    localStorage.setItem('analytics-notice-dismissed', '1');
});
document.querySelector('[data-analytics-preferences]')?.addEventListener('click', () => {
    if (analyticsNotice) analyticsNotice.hidden = false;
});

if (analyticsBody.dataset.analyticsActivityUrl && !analyticsOptOut) {
    let lastVisitorActivity = Date.now();
    ['pointerdown', 'keydown', 'scroll', 'touchstart'].forEach((eventName) => window.addEventListener(eventName, () => { lastVisitorActivity = Date.now(); }, { passive: true }));
    const sendActivity = () => {
        if (document.visibilityState === 'visible' && Date.now() - lastVisitorActivity < 120000) fetch(analyticsBody.dataset.analyticsActivityUrl, { method: 'POST', credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': csrfToken ?? '', Accept: 'application/json' } }).catch(() => {});
    };
    window.setInterval(sendActivity, 30000);
    document.addEventListener('visibilitychange', sendActivity);
}

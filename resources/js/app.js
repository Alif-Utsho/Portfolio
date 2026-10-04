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

    themeToggle.setAttribute('aria-label', `Switch to ${isDark ? 'light' : 'dark'} theme`);
    themeToggle.querySelector('.theme-icon').textContent = isDark ? '◐' : '☼';
    document.querySelector('meta[name="theme-color"]').setAttribute('content', isDark ? '#111411' : '#f3f3ee');
};

updateThemeControl();

themeToggle.addEventListener('click', () => {
    root.dataset.theme = root.dataset.theme === 'dark' ? 'light' : 'dark';
    window.localStorage.setItem('portfolio-theme', root.dataset.theme);
    updateThemeControl();
});

const updateHeader = () => {
    header.classList.toggle('is-scrolled', window.scrollY > 18);
};

updateHeader();
window.addEventListener('scroll', updateHeader, { passive: true });

menuToggle.addEventListener('click', () => {
    const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';

    menuToggle.setAttribute('aria-expanded', String(!isExpanded));
    menuToggle.setAttribute('aria-label', isExpanded ? 'Open navigation' : 'Close navigation');
    navigation.classList.toggle('is-open', !isExpanded);
});

navigation.querySelectorAll('a').forEach((link) => {
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

        navigation.querySelectorAll('a').forEach((link) => {
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
    const profileResponse = await fetch('https://api.github.com/users/Alif-Utsho', {
        headers: { Accept: 'application/vnd.github+json' },
    });

    if (!profileResponse.ok) {
        throw new Error('GitHub profile data is unavailable.');
    }

    const profile = await profileResponse.json();
    const repositoriesResponse = await fetch('https://api.github.com/users/Alif-Utsho/repos?per_page=100', {
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
    document.querySelector('.github-live').textContent = 'PROFILE SNAPSHOT';
});

document.querySelector('#contact-form').addEventListener('submit', (event) => {
    event.preventDefault();
    document.querySelector('#form-status').textContent = 'No email service is connected yet, so this message was not sent. Please connect through GitHub.';
});

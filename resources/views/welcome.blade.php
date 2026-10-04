<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111411">
    <meta name="description" content="Md Shahoriar Alif Utsho is a software engineer focused on PHP and Laravel backend development, with experience across modern web technologies.">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Md Shahoriar Alif Utsho — Software Engineer">
    <meta property="og:description" content="Software engineer building useful web applications with a backend-first perspective.">
    <meta property="og:url" content="{{ url('/') }}">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Md Shahoriar Alif Utsho — Software Engineer">
    <meta name="twitter:description" content="Software engineer focused on PHP, Laravel and modern web technologies.">
    <link rel="canonical" href="{{ url('/') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <title>Md Shahoriar Alif Utsho — Software Engineer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => 'Md Shahoriar Alif Utsho',
        'alternateName' => 'Alif',
        'jobTitle' => 'Software Engineer',
        'url' => url('/'),
        'sameAs' => ['https://github.com/Alif-Utsho', 'https://www.facebook.com/utsho.aiub', 'https://www.instagram.com/alif_utsho'],
    ], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>
    <div class="page-grain" aria-hidden="true"></div>
    <header class="site-header" id="top">
        <nav class="nav-shell" aria-label="Main navigation">
            <a class="wordmark" href="#top" aria-label="Alif — home"><span class="wordmark-mark">A<span>.</span></span><span class="wordmark-name">ALIF UTSHO</span></a>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-menu" aria-label="Open navigation"><span></span><span></span></button>
            <div class="nav-links" id="primary-menu">
                <a href="#about">About</a><a href="#experience">Experience</a><a href="#skills">Stack</a><a href="#work">Selected work</a><a href="#beyond">Beyond code</a>
            </div>
            <div class="nav-actions">
                <button class="theme-toggle" type="button" aria-label="Switch to light theme" title="Toggle theme"><span class="theme-icon" aria-hidden="true">◐</span></button>
                <a class="nav-cta" href="#contact">Let’s talk <span aria-hidden="true">↗</span></a>
            </div>
        </nav>
    </header>

    <main id="main">
        <section class="hero section-wrap" id="home" aria-labelledby="hero-title">
            <div class="hero-copy reveal">
                <div class="eyebrow"><span class="status-dot"></span> SOFTWARE ENGINEER <span class="eyebrow-divider">/</span> DHAKA, BD</div>
                <h1 id="hero-title">Making the<br>complex feel <span class="serif-word">simple.</span></h1>
                <p class="hero-intro">I’m <strong>Md Shahoriar Alif Utsho</strong> — a software engineer focused on Laravel and PHP backend development, building useful web applications from the systems up.</p>
                <div class="hero-actions">
                    <a class="button button-primary" href="#work">Explore my work <span aria-hidden="true">↘</span></a>
                    <a class="button button-quiet" href="https://drive.google.com/uc?export=download&amp;id=1x0cHwrepUGZ88Qo826ODzY5xA0TQrRQ2" target="_blank" rel="noreferrer">Download CV <span aria-hidden="true">↓</span></a>
                </div>
                <div class="hero-socials"><span>FIND ME ON</span><a href="https://github.com/Alif-Utsho" target="_blank" rel="noreferrer">GitHub ↗</a><a href="https://www.facebook.com/utsho.aiub" target="_blank" rel="noreferrer">Facebook ↗</a><a href="https://www.instagram.com/alif_utsho" target="_blank" rel="noreferrer">Instagram ↗</a></div>
            </div>
            <div class="hero-visual reveal" aria-label="Illustration of a software request flowing through an application to a database">
                <div class="visual-topline"><span>FIELD NOTES / 001</span><span class="visual-live"><i></i> SYSTEMS THINKING</span></div>
                <div class="blueprint-grid" aria-hidden="true"></div>
                <div class="orbit orbit-one" aria-hidden="true"></div><div class="orbit orbit-two" aria-hidden="true"></div>
                <div class="diagram-node node-client"><span class="node-icon">⌘</span><span>CLIENT</span><small>request</small></div>
                <div class="diagram-node node-api"><span class="node-icon">⇄</span><span>API</span><small>interface</small></div>
                <div class="diagram-node node-core"><span class="core-spark">✳</span><span>APPLICATION</span><small>Laravel · PHP</small></div>
                <div class="diagram-node node-data"><span class="node-icon">▤</span><span>DATA</span><small>persistence</small></div>
                <svg class="flow-lines" viewBox="0 0 600 460" role="img" aria-label="Flow from client to API, application, and data"><path d="M114 214 H210"/><path d="M294 214 H365"/><path d="M447 214 H506"/><circle cx="162" cy="214" r="3"/><circle cx="329" cy="214" r="3"/><circle cx="475" cy="214" r="3"/></svg>
                <div class="visual-caption"><span>01 — REQUEST IN</span><span>02 — LOGIC APPLIED</span><span>03 — RESPONSE OUT</span></div>
                <div class="visual-index">A / 26</div>
            </div>
            <div class="hero-foot"><span>BACKEND-FOCUSED. PRODUCT-MINDED.</span><a href="#about" aria-label="Scroll to about section">SCROLL TO EXPLORE <span>↓</span></a></div>
        </section>

        <section class="intro-strip" aria-label="Professional focus"><div class="intro-strip-inner"><span>PHP</span><i>✳</i><span>LARAVEL</span><i>✳</i><span>REACT</span><i>✳</i><span>ASP.NET</span><i>✳</i><span>WEB SYSTEMS</span></div></section>

        <section class="section-wrap section-block about-section" id="about" aria-labelledby="about-title">
            <div class="section-heading reveal"><span class="section-kicker">01 / A LITTLE ABOUT ME</span><h2 id="about-title">Strong foundations.<br><span class="serif-word">Useful outcomes.</span></h2></div>
            <div class="about-layout">
                <div class="about-copy reveal"><p class="lead-copy">I’m a software engineer with a backend-first approach to building for the web.</p><p>My work sits around PHP and Laravel, with experience spanning React, Next.js and ASP.NET/C#. I care about the parts that make an application dependable: clear boundaries, understandable code, and thoughtful delivery.</p><p>From shaping an API to getting a release onto a server, I like seeing how each decision connects to the end product.</p><a class="text-link" href="#experience">A little more about my path <span>↘</span></a></div>
                <div class="about-aside reveal"><div class="aside-quote-mark">“</div><p>Build for the people using it.<br>Design for the people maintaining it.</p><span>HOW I APPROACH SOFTWARE</span><div class="aside-rule"></div><div class="aside-meta"><span>BASED IN</span><strong>Dhaka, Bangladesh</strong></div></div>
            </div>
        </section>

        <section class="section-wrap section-block experience-section" id="experience" aria-labelledby="experience-title">
            <div class="section-heading section-heading-row reveal"><div><span class="section-kicker">02 / THE JOURNEY</span><h2 id="experience-title">Experience<span class="serif-word"> in practice.</span></h2></div><p>Growing through hands-on software development across different teams and products.</p></div>
            <div class="timeline">
                <article class="timeline-item reveal"><div class="timeline-marker"><span></span></div><div class="timeline-date">JAN 2025 — FEB 2026</div><div class="timeline-content"><div class="timeline-title-row"><div><h3>Associate Software Engineer</h3><p>Olivine Limited</p></div><button class="timeline-expand" type="button" aria-expanded="false" aria-label="Show role details">+</button></div><div class="timeline-details"><p>Role scope, contributions and technologies are omitted until they can be verified against the CV.</p><div class="tag-row"><span>Associate Software Engineer</span></div></div></div></article>
                <article class="timeline-item reveal"><div class="timeline-marker"><span></span></div><div class="timeline-date">JUL 2023 — DEC 2024</div><div class="timeline-content"><div class="timeline-title-row"><div><h3>Junior Software Developer</h3><p>QuickTech-IT Ltd</p></div><button class="timeline-expand" type="button" aria-expanded="false" aria-label="Show role details">+</button></div><div class="timeline-details"><p>Role scope, contributions and technologies are omitted until they can be verified against the CV.</p><div class="tag-row"><span>Junior Software Developer</span></div></div></div></article>
            </div>
        </section>

        <section class="skills-band" id="skills" aria-labelledby="skills-title"><div class="section-wrap skills-inner"><div class="section-heading reveal"><span class="section-kicker">03 / TOOLS OF THE TRADE</span><h2 id="skills-title">A practical <span class="serif-word">toolkit.</span></h2><p class="skills-intro">The technologies I reach for across application layers and delivery.</p></div><div class="skills-grid">
            <article class="skill-card reveal"><span class="skill-number">01</span><h3>Backend</h3><p>Business logic, APIs and application structure.</p><div class="skill-tags"><span>PHP</span><span>Laravel</span><span>ASP.NET</span><span>C#</span></div></article>
            <article class="skill-card reveal"><span class="skill-number">02</span><h3>Frontend</h3><p>Interfaces that connect clearly to the system behind them.</p><div class="skill-tags"><span>React.js</span><span>Next.js</span><span>JavaScript</span><span>HTML / CSS</span></div></article>
            <article class="skill-card reveal"><span class="skill-number">03</span><h3>Engineering</h3><p>Patterns and principles for maintainable software.</p><div class="skill-tags"><span>OOP</span><span>MVC</span><span>REST APIs</span><span>SOLID</span></div></article>
            <article class="skill-card reveal"><span class="skill-number">04</span><h3>Delivery</h3><p>Version control, release workflows and deployment.</p><div class="skill-tags"><span>Git</span><span>GitHub</span><span>CI/CD</span><span>cPanel</span></div></article>
        </div></div></section>

        <section class="section-wrap section-block work-section" id="work" aria-labelledby="work-title">
            <div class="section-heading section-heading-row reveal"><div><span class="section-kicker">04 / SELECTED WORK</span><h2 id="work-title">Built, shipped, <span class="serif-word">and learned.</span></h2></div><a class="text-link desktop-link" href="https://github.com/Alif-Utsho?tab=repositories" target="_blank" rel="noreferrer">All repositories <span>↗</span></a></div>
            <div class="project-filters reveal" role="group" aria-label="Filter projects"><button class="filter-button is-active" type="button" data-filter="all" aria-pressed="true">All work</button><button class="filter-button" type="button" data-filter="php" aria-pressed="false">PHP</button><button class="filter-button" type="button" data-filter="csharp" aria-pressed="false">ASP.NET / C#</button><button class="filter-button" type="button" data-filter="javascript" aria-pressed="false">JavaScript</button></div>
            <div class="projects-grid">
                <article class="project-card project-featured reveal" data-category="php"><div class="project-art art-postbook"><div class="art-window"><div class="window-bar"><i></i><i></i><i></i><span>POSTBOOK / API</span></div><div class="art-code"><span class="code-muted">01</span> <b>Route</b>::<em>resource</em>(<q>'posts'</q>);<br><span class="code-muted">02</span> <b>Auth</b>::<em>user</em>()<br><span class="code-muted">03</span> <b>return</b> <em>response</em>()-&gt;<b>json</b>(...);</div><div class="art-footer"><span>APPLICATION LAYER</span><span><i></i> PHP BACKEND</span></div></div><span class="art-label">SYSTEM STUDY / 01</span></div><div class="project-info"><div class="project-title-row"><div><span class="project-type">BACKEND REPOSITORY</span><h3>Postbook — PHP backend</h3></div><a class="project-arrow" href="https://github.com/Alif-Utsho/Postbook-backend" target="_blank" rel="noreferrer" aria-label="View Postbook PHP backend on GitHub">↗</a></div><p>A PHP repository named for the Postbook application, paired with the separate Postbook frontend repository on GitHub.</p><div class="tag-row"><span>PHP</span><span>Laravel repository</span></div></div></article>
                <article class="project-card reveal" data-category="csharp"><div class="project-art art-commerce"><div class="commerce-layout"><div class="commerce-side"><div class="commerce-mark">EC<span>.</span></div><span>OVERVIEW</span><span>PRODUCTS</span><span>ORDERS</span><span>SETTINGS</span></div><div class="commerce-main"><div class="commerce-top"><span>Store overview</span><span>●</span></div><div class="commerce-score"><span>CATALOG</span><b>PRODUCT<br>MANAGEMENT</b><i>ASP.NET MVC</i></div><div class="commerce-bars"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div></div></div><span class="art-label">SYSTEM STUDY / 02</span></div><div class="project-info"><div class="project-title-row"><div><span class="project-type">WEB APPLICATION</span><h3>E-Commerce with ASP.NET MVC</h3></div><a class="project-arrow" href="https://github.com/Alif-Utsho/e-Commerce-with-ASP.Net-MVC" target="_blank" rel="noreferrer" aria-label="View E-Commerce with ASP.NET MVC on GitHub">↗</a></div><p>An e-commerce repository built around the ASP.NET MVC stack, with a solution file and application project in the repository.</p><div class="tag-row"><span>ASP.NET MVC</span><span>C#</span></div></div></article>
                <article class="project-card reveal" data-category="csharp"><div class="project-art art-postbook-net"><div class="architecture-map"><div class="arch-box">API</div><span>↓</span><div class="arch-row"><div class="arch-box">BLL</div><div class="arch-box">DAL</div></div><div class="arch-box arch-db">DATABASE LAYER</div></div><span class="art-label">SYSTEM STUDY / 03</span></div><div class="project-info"><div class="project-title-row"><div><span class="project-type">BACKEND REPOSITORY</span><h3>Postbook — ASP.NET backend</h3></div><a class="project-arrow" href="https://github.com/Alif-Utsho/Postbook-backend-ASP.Net" target="_blank" rel="noreferrer" aria-label="View Postbook ASP.NET backend on GitHub">↗</a></div><p>A second Postbook backend implementation. Its repository separates API, business logic and data access into distinct projects.</p><div class="tag-row"><span>C#</span><span>ASP.NET</span><span>Layered architecture</span></div></div></article>
            </div>
            <a class="text-link mobile-link" href="https://github.com/Alif-Utsho?tab=repositories" target="_blank" rel="noreferrer">All repositories <span>↗</span></a>
        </section>

        <section class="github-section" id="github" aria-labelledby="github-title"><div class="section-wrap github-layout"><div class="github-copy reveal"><span class="section-kicker">05 / OPEN SOURCE FOOTPRINT</span><h2 id="github-title">The work lives<br>on <span class="serif-word">GitHub.</span></h2><p>A public record of projects, experiments and code over time. Explore the repositories for implementation details and source.</p><a class="button button-light" href="https://github.com/Alif-Utsho" target="_blank" rel="noreferrer">View GitHub profile <span>↗</span></a></div><div class="github-card reveal"><div class="github-card-head"><span class="github-dot">●</span><span>ALIF-UTSHO / PUBLIC PROFILE</span><span class="github-live">LIVE DATA</span></div><div class="github-stat-grid"><div class="github-stat"><strong data-github-stat="repos">53</strong><span>PUBLIC REPOSITORIES</span></div><div class="github-stat"><strong data-github-stat="followers">7</strong><span>FOLLOWERS</span></div><div class="github-stat"><strong data-github-stat="stars">5</strong><span>STARS EARNED</span></div></div><div class="language-heading"><span>REPOSITORY LANGUAGES</span><span>FROM PUBLIC REPOS</span></div><div class="language-bar" aria-label="Languages in public repositories" data-language-bar><i class="language-php"></i><i class="language-js"></i><i class="language-csharp"></i><i class="language-java"></i><i class="language-other"></i></div><div class="language-legend"><span><i class="language-php"></i>PHP</span><span><i class="language-js"></i>JavaScript</span><span><i class="language-csharp"></i>C#</span><span><i class="language-java"></i>Java</span><span><i class="language-other"></i>Other</span></div><div class="github-card-foot"><span><i></i> PUBLIC PROFILE DATA</span><a href="https://github.com/Alif-Utsho?tab=repositories" target="_blank" rel="noreferrer">Explore repositories ↗</a></div></div></div></section>

        <section class="section-wrap section-block beyond-section" id="beyond" aria-labelledby="beyond-title"><div class="section-heading reveal"><span class="section-kicker">06 / WHEN I STEP AWAY</span><h2 id="beyond-title">Beyond the <span class="serif-word">screen.</span></h2><p class="beyond-intro">Long roads, changing skies and time outside — the things that offer a different kind of perspective.</p></div><div class="beyond-grid"><article class="beyond-card beyond-road reveal"><div class="road-illustration" aria-hidden="true"><div class="road-sun"></div><div class="road-horizon"></div><div class="road-line"></div><div class="road-track"></div><div class="road-bike">◉</div></div><div class="beyond-caption"><span>01 / THE LONG WAY</span><h3>Motorcycle touring</h3></div></article><article class="beyond-card beyond-water reveal"><div class="water-illustration" aria-hidden="true"><div class="water-sun"></div><div class="mountain mountain-back"></div><div class="mountain mountain-front"></div><div class="water-lines"><i></i><i></i><i></i><i></i></div></div><div class="beyond-caption"><span>02 / FINDING STILLNESS</span><h3>Nature &amp; open places</h3></div></article><article class="beyond-note reveal"><span class="section-kicker">A CHANGE OF PACE</span><p>Some of the best ideas arrive when you give them room to breathe.</p><span class="note-coordinate">BANGLADESH / OPEN ROAD</span></article></div></section>

        <section class="contact-section" id="contact" aria-labelledby="contact-title"><div class="section-wrap contact-layout"><div class="contact-copy reveal"><span class="section-kicker">07 / START A CONVERSATION</span><h2 id="contact-title">Let’s build<br>something <span class="serif-word">useful.</span></h2><p>Have a project, opportunity or interesting problem in mind? The best way to reach me is through GitHub.</p><a class="button button-primary" href="https://github.com/Alif-Utsho" target="_blank" rel="noreferrer">Connect on GitHub <span>↗</span></a></div><form class="contact-form reveal" id="contact-form"><div class="form-eyebrow"><span>QUICK NOTE</span><span>01 — 04</span></div><label for="contact-name">Your name</label><input id="contact-name" name="name" autocomplete="name" placeholder="How should I address you?" required><label for="contact-email">Your email</label><input id="contact-email" name="email" type="email" autocomplete="email" placeholder="you@example.com" required><label for="contact-message">What’s on your mind?</label><textarea id="contact-message" name="message" rows="3" placeholder="A little context goes a long way…" required></textarea><button class="form-submit" type="submit">Prepare message <span>↗</span></button><p class="form-status" id="form-status" role="status">Front-end preview only; the form does not send messages yet.</p></form></div></section>
    </main>
    <footer class="site-footer"><div class="section-wrap footer-inner"><a class="wordmark" href="#top"><span class="wordmark-mark">A<span>.</span></span><span class="wordmark-name">ALIF UTSHO</span></a><span>DESIGNED TO BE USEFUL. BUILT TO LAST.</span><a href="#top">BACK TO TOP ↑</a><span class="footer-year">© {{ date('Y') }} ALIF UTSHO</span></div></footer>
</body>
</html>

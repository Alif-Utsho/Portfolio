@extends('portfolio.layout')

@section('content')
<section class="hero section-wrap" id="home" aria-labelledby="hero-title">
    <div class="hero-copy reveal">
        <div class="eyebrow"><span class="status-dot"></span> SOFTWARE ENGINEER <span class="eyebrow-divider">/</span> {{ strtoupper($settings['location'] ?? 'BANGLADESH') }}</div>
        <h1 id="hero-title">{{ $settings['hero_title'] ?? 'Making the complex feel simple.' }}</h1>
        <p class="hero-intro">{{ $settings['hero_intro'] ?? 'I build useful web applications with a backend-first approach.' }}</p>
        <div class="hero-actions">
            <a class="button button-primary" href="{{ route('projects.index') }}">Explore my work <span aria-hidden="true">↘</span></a>
            @if (filled($settings['cv_url'] ?? null))<a class="button button-quiet" href="{{ $settings['cv_url'] }}" target="_blank" rel="noreferrer">Download CV <span aria-hidden="true">↓</span></a>@endif
        </div>
        <div class="hero-socials"><span>FIND ME ON</span>@foreach ($socialLinks as $social)<a href="{{ $social->url }}" target="_blank" rel="noreferrer">{{ $social->title }} ↗</a>@endforeach</div>
    </div>
    <div class="hero-visual reveal" aria-label="Illustration of a software request flowing through an application to a database">
        <div class="visual-topline"><span>FIELD NOTES / 001</span><span class="visual-live"><i></i> SYSTEMS THINKING</span></div>
        <div class="blueprint-grid" aria-hidden="true"></div>
        <div class="orbit orbit-one" aria-hidden="true"></div>
        <div class="orbit orbit-two" aria-hidden="true"></div>
        <div class="diagram-node node-client"><span class="node-icon">⌘</span><span>CLIENT</span><small>request</small></div>
        <div class="diagram-node node-api"><span class="node-icon">⇄</span><span>API</span><small>interface</small></div>
        <div class="diagram-node node-core"><span class="core-spark">✳</span><span>APPLICATION</span><small>Laravel · PHP</small></div>
        <div class="diagram-node node-data"><span class="node-icon">▤</span><span>DATA</span><small>persistence</small></div>
        <svg class="flow-lines" viewBox="0 0 600 460" role="img" aria-label="Flow from client to API, application, and data">
            <path d="M114 214 H210" />
            <path d="M294 214 H365" />
            <path d="M447 214 H506" />
            <circle cx="162" cy="214" r="3" />
            <circle cx="329" cy="214" r="3" />
            <circle cx="475" cy="214" r="3" />
        </svg>
        <div class="visual-caption"><span>01 — REQUEST IN</span><span>02 — LOGIC APPLIED</span><span>03 — RESPONSE OUT</span></div>
        <div class="visual-index">A / 26</div>
    </div>
    <div class="hero-foot"><span>BACKEND-FOCUSED. PRODUCT-MINDED.</span><a href="#about">SCROLL TO EXPLORE <span>↓</span></a></div>
</section>
<section class="intro-strip" aria-label="Professional focus">
    <div class="intro-strip-inner"><span>PHP</span><i>✳</i><span>LARAVEL</span><i>✳</i><span>REACT</span><i>✳</i><span>ASP.NET</span><i>✳</i><span>WEB SYSTEMS</span></div>
</section>
<section class="section-wrap section-block about-section" id="about" aria-labelledby="about-title">
    <div class="section-heading reveal"><span class="section-kicker">01 / A LITTLE ABOUT ME</span>
        <h2 id="about-title">Strong foundations.<br><span class="serif-word">Useful outcomes.</span></h2>
    </div>
    <div class="about-layout">
        <div class="about-copy reveal">
            <p class="lead-copy">{{ $settings['about_lead'] ?? 'A software engineer with a backend-first approach to building for the web.' }}</p>
            <p>{{ $settings['about_body'] ?? '' }}</p><a class="text-link" href="#experience">A little more about my path <span>↘</span></a>
        </div>
        <div class="about-aside reveal">
            <div class="aside-quote-mark">“</div>
            <p>Build for the people using it.<br>Design for the people maintaining it.</p><span>HOW I APPROACH SOFTWARE</span>
            <div class="aside-rule"></div>
            <div class="aside-meta"><span>BASED IN</span><strong>{{ $settings['location'] ?? 'Bangladesh' }}</strong></div>
        </div>
    </div>
</section>
@if ($experiences->isNotEmpty())
<section class="section-wrap section-block experience-section" id="experience" aria-labelledby="experience-title">
    <div class="section-heading section-heading-row reveal">
        <div><span class="section-kicker">02 / THE JOURNEY</span>
            <h2 id="experience-title">Experience<span class="serif-word"> in practice.</span></h2>
        </div>
        <p>Growing through hands-on software development across different teams and products.</p>
    </div>
    <div class="timeline">
        @foreach ($experiences as $experience)<article class="timeline-item reveal">
            <div class="timeline-marker"><span></span></div>
            <div class="timeline-date">{{ $experience->metadata['period'] ?? $experience->eyebrow }}</div>
            <div class="timeline-content">
                <div class="timeline-title-row">
                    <div>
                        <h3>{{ $experience->title }}</h3>
                        <p>{{ $experience->metadata['organization'] ?? $experience->subtitle }}</p>
                    </div>@if ($experience->body || $experience->technologies)<button class="timeline-expand" type="button" aria-expanded="false" aria-label="Show role details">+</button>@endif
                </div>
                <div class="timeline-details">
                    <p>{{ $experience->summary }} {{ $experience->body }}</p>
                    <div class="tag-row">@foreach ($experience->technologies ?? [] as $technology)<span>{{ $technology }}</span>@endforeach</div>
                </div>
            </div>
        </article>@endforeach
    </div>
</section>
@endif
@if ($skills->isNotEmpty())<section class="skills-band" id="skills" aria-labelledby="skills-title">
    <div class="section-wrap skills-inner">
        <div class="section-heading reveal"><span class="section-kicker">03 / TOOLS OF THE TRADE</span>
            <h2 id="skills-title">A practical <span class="serif-word">toolkit.</span></h2>
        </div>
        <div class="skills-grid">@foreach ($skills as $skill)<article class="skill-card reveal"><span class="skill-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <h3>{{ $skill->title }}</h3>
                <p>{{ $skill->summary ?? $skill->subtitle }}</p>@if ($skill->technologies)<div class="skill-tags">@foreach ($skill->technologies as $technology)<span>{{ $technology }}</span>@endforeach</div>@endif
            </article>@endforeach</div>
    </div>
</section>@endif
@if ($featuredProjects->isNotEmpty())<section class="section-wrap section-block work-section" id="work" aria-labelledby="work-title">
    <div class="section-heading section-heading-row reveal">
        <div><span class="section-kicker">04 / SELECTED WORK</span>
            <h2 id="work-title">Built, shipped, <span class="serif-word">and learned.</span></h2>
        </div><a class="text-link" href="{{ route('projects.index') }}">All projects <span>↗</span></a>
    </div>
    <div class="projects-grid">@foreach ($featuredProjects as $project)<article class="project-card {{ $loop->first ? 'project-featured' : '' }} reveal"><a class="project-card-link" href="{{ route('projects.show', $project->slug) }}">
                <div class="project-art {{ $loop->first ? 'art-postbook' : 'art-postbook-net' }}">@if ($project->image_path)<img src="{{ Storage::disk('public')->url($project->image_path) }}" alt="{{ $project->metadata['alt_text'] ?? $project->title }}" loading="lazy">@else<div class="architecture-map">
                        <div class="arch-box">{{ $project->technologies[0] ?? 'PROJECT' }}</div><span>↓</span>
                        <div class="arch-box arch-db">{{ $project->subtitle ?? 'APPLICATION' }}</div>
                    </div>@endif<span class="art-label">{{ $project->eyebrow ?? 'SELECTED WORK' }}</span></div>
                <div class="project-info">
                    <div class="project-title-row">
                        <div><span class="project-type">{{ $project->eyebrow ?? 'PROJECT' }}</span>
                            <h3>{{ $project->title }}</h3>
                        </div><span class="project-arrow" aria-hidden="true">↗</span>
                    </div>
                    <p>{{ $project->summary }}</p>
                    <div class="tag-row">@foreach ($project->technologies ?? [] as $technology)<span>{{ $technology }}</span>@endforeach</div>
                </div>
            </a></article>@endforeach</div>
</section>@endif
<section class="github-section" id="github" aria-labelledby="github-title">
    <div class="section-wrap github-layout">
        <div class="github-copy reveal"><span class="section-kicker">05 / OPEN SOURCE FOOTPRINT</span>
            <h2 id="github-title">The work lives<br>on <span class="serif-word">GitHub.</span></h2>
            <p>A public record of projects, experiments and code over time.</p><a class="button button-light" href="{{ $settings['github_url'] ?? 'https://github.com/'.config('portfolio.github_username') }}" target="_blank" rel="noreferrer">View GitHub profile <span>↗</span></a>
        </div>
        <div class="github-card reveal" data-github-username="{{ config('portfolio.github_username') }}">
            <div class="github-card-head"><span class="github-dot">●</span><span>PUBLIC GITHUB PROFILE</span><span class="github-live">LIVE DATA</span></div>
            <div class="github-stat-grid">
                <div class="github-stat"><strong data-github-stat="repos">53</strong><span>PUBLIC REPOSITORIES</span></div>
                <div class="github-stat"><strong data-github-stat="followers">7</strong><span>FOLLOWERS</span></div>
                <div class="github-stat"><strong data-github-stat="stars">5</strong><span>STARS EARNED</span></div>
            </div>
            <div class="language-heading"><span>REPOSITORY LANGUAGES</span><span>FROM PUBLIC REPOS</span></div>
            <div class="language-bar" aria-label="Languages in public repositories" data-language-bar><i class="language-php"></i><i class="language-js"></i><i class="language-csharp"></i><i class="language-java"></i><i class="language-other"></i></div>
            <div class="language-legend"><span><i class="language-php"></i>PHP</span><span><i class="language-js"></i>JavaScript</span><span><i class="language-csharp"></i>C#</span><span><i class="language-java"></i>Java</span><span><i class="language-other"></i>Other</span></div>
            <div class="github-card-foot"><span><i></i> PROFILE DATA</span><a href="{{ $settings['github_url'] ?? 'https://github.com/'.config('portfolio.github_username') }}?tab=repositories" target="_blank" rel="noreferrer">Explore repositories ↗</a></div>
        </div>
    </div>
</section>
@if ($personalItems->isNotEmpty())<section class="section-wrap section-block beyond-section" id="beyond" aria-labelledby="beyond-title">
    <div class="section-heading reveal"><span class="section-kicker">06 / WHEN I STEP AWAY</span>
        <h2 id="beyond-title">Beyond the <span class="serif-word">screen.</span></h2>
        <p class="beyond-intro">Long roads, changing skies and time outside — the things that offer a different kind of perspective.</p>
    </div>
    <div class="beyond-grid">@foreach ($personalItems as $personal)<article class="beyond-card {{ $loop->even ? 'beyond-water' : 'beyond-road' }} reveal">
            <div class="road-illustration" aria-hidden="true">@if ($personal->image_path)<img src="{{ Storage::disk('public')->url($personal->image_path) }}" alt="" loading="lazy">@else<div class="road-sun"></div>
                <div class="road-horizon"></div>
                <div class="road-line"></div>
                <div class="road-track"></div>@endif
            </div>
            <div class="beyond-caption"><span>{{ $personal->eyebrow ?? 'BEYOND THE SCREEN' }}</span>
                <h3>{{ $personal->title }}</h3>
                <p>{{ $personal->summary }}</p>
            </div>
        </article>@endforeach</div>
</section>@endif
<section class="contact-section" id="contact" aria-labelledby="contact-title">
    <div class="section-wrap contact-layout">
        <div class="contact-copy reveal"><span class="section-kicker">07 / START A CONVERSATION</span>
            <h2 id="contact-title">Let’s build<br>something <span class="serif-word">useful.</span></h2>
            <p>Have a project, opportunity or interesting problem in mind? Send a note.</p>@if (filled($settings['contact_email'] ?? null))<a class="button button-primary" href="mailto:{{ $settings['contact_email'] }}">Email me <span>↗</span></a>@endif
        </div>
        <form class="contact-form reveal" id="contact-form" method="POST" action="{{ route('contact.store') }}">
            <div class="form-eyebrow"><span>QUICK NOTE</span><span>01 — 04</span></div>@csrf<label for="contact-name">Your name</label><input id="contact-name" name="name" autocomplete="name" value="{{ old('name') }}" placeholder="How should I address you?" required>@error('name')<span class="field-error">{{ $message }}</span>@enderror<label for="contact-email">Your email</label><input id="contact-email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" placeholder="you@example.com" required>@error('email')<span class="field-error">{{ $message }}</span>@enderror<label for="contact-subject">Subject</label><input id="contact-subject" name="subject" required value="{{ old('subject') }}" placeholder="A short subject">@error('subject')<span class="field-error">{{ $message }}</span>@enderror<label for="contact-message">What’s on your mind?</label><textarea id="contact-message" name="message" rows="3" placeholder="A little context goes a long way…" required>{{ old('message') }}</textarea>@error('message')<span class="field-error">{{ $message }}</span>@enderror<label class="honeypot" aria-hidden="true">Website<input name="website" tabindex="-1" autocomplete="off"></label><button class="form-submit" type="submit">Send message <span>↗</span></button>@if (session('contact_submitted'))<p class="form-status" role="status">Thanks for reaching out. Your message is in the inbox.</p>@else<p class="form-status">Your message goes to the portfolio inbox. No email is sent from this form.</p>@endif
        </form>
    </div>
</section>
@endsection
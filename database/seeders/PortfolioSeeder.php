<?php

namespace Database\Seeders;

use App\Models\PortfolioItem;
use App\Models\PortfolioSetting;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $siteSettings = PortfolioSetting::query()->where('key', 'site')->first();

        if ($siteSettings?->value['cv_url'] === 'https://drive.google.com/uc?export=download&id=1x0cHwrepUGZ88Qo826ODzY5xA0TQrRQ2') {
            $value = $siteSettings->value;
            unset($value['cv_url']);
            $siteSettings->update(['value' => $value]);
        }

        PortfolioSetting::query()->firstOrCreate(['key' => 'site'], [
            'value' => [
                'site_title' => 'Md Shahoriar Alif Utsho — Software Engineer',
                'site_description' => 'Software engineer focused on PHP and Laravel backend development.',
                'seo_title' => 'Md Shahoriar Alif Utsho — Software Engineer | Laravel & PHP',
                'seo_description' => 'Software engineer building useful web applications with Laravel, PHP and modern web technologies.',
                'hero_title' => 'Making the complex feel simple.',
                'hero_intro' => 'I build useful web applications with a backend-first approach, focused on Laravel, PHP and modern web technologies.',
                'about_lead' => 'I’m a software engineer with a backend-first approach to building for the web.',
                'about_body' => 'My work sits around PHP and Laravel, with experience spanning React, Next.js and ASP.NET/C#. I care about clear boundaries, understandable code, and thoughtful delivery.',
                'location' => 'Dhaka, Bangladesh',
                'github_url' => 'https://github.com/Alif-Utsho',
                'contact_email' => '',
            ],
        ]);

        $entries = [
            ['type' => 'project', 'title' => 'Postbook — PHP backend', 'slug' => 'postbook-php-backend', 'eyebrow' => 'Backend repository', 'subtitle' => 'Postbook', 'summary' => 'A PHP repository named for the Postbook application, paired with a separate Postbook frontend repository on GitHub.', 'body' => 'The public repository contains a Laravel application structure. Explore the source for implementation details.', 'url' => 'https://github.com/Alif-Utsho/Postbook-backend', 'technologies' => ['PHP', 'Laravel'], 'metadata' => ['category' => 'php'], 'is_featured' => true, 'is_published' => true, 'sort_order' => 10],
            ['type' => 'project', 'title' => 'E-Commerce with ASP.NET MVC', 'slug' => 'e-commerce-aspnet-mvc', 'eyebrow' => 'Web application', 'subtitle' => 'E-Commerce', 'summary' => 'An e-commerce project repository built around the ASP.NET MVC stack.', 'body' => 'The repository includes a Visual Studio solution and application project. Explore the source for implementation details.', 'url' => 'https://github.com/Alif-Utsho/e-Commerce-with-ASP.Net-MVC', 'technologies' => ['ASP.NET MVC', 'C#'], 'metadata' => ['category' => 'csharp'], 'is_featured' => true, 'is_published' => true, 'sort_order' => 20],
            ['type' => 'project', 'title' => 'Postbook — ASP.NET backend', 'slug' => 'postbook-aspnet-backend', 'eyebrow' => 'Backend repository', 'subtitle' => 'Postbook', 'summary' => 'A second Postbook backend implementation, organized into API, business logic and data access projects.', 'body' => 'The repository structure separates API, BLL and DAL projects. Explore the source for implementation details.', 'url' => 'https://github.com/Alif-Utsho/Postbook-backend-ASP.Net', 'technologies' => ['C#', 'ASP.NET'], 'metadata' => ['category' => 'csharp'], 'is_featured' => true, 'is_published' => true, 'sort_order' => 30],
            ['type' => 'experience', 'title' => 'Associate Software Engineer', 'subtitle' => 'Olivine Limited', 'eyebrow' => 'Professional experience', 'summary' => 'Associate Software Engineer at Olivine Limited.', 'metadata' => ['organization' => 'Olivine Limited', 'period' => 'January 2025 – February 2026'], 'is_published' => true, 'sort_order' => 10],
            ['type' => 'experience', 'title' => 'Junior Software Developer', 'subtitle' => 'QuickTech-IT Ltd', 'eyebrow' => 'Professional experience', 'summary' => 'Junior Software Developer at QuickTech-IT Ltd.', 'metadata' => ['organization' => 'QuickTech-IT Ltd', 'period' => 'July 2023 – December 2024'], 'is_published' => true, 'sort_order' => 20],
            ['type' => 'skill', 'title' => 'PHP', 'subtitle' => 'Backend', 'metadata' => ['group' => 'Backend'], 'is_published' => true, 'sort_order' => 10],
            ['type' => 'skill', 'title' => 'Laravel', 'subtitle' => 'Backend', 'metadata' => ['group' => 'Backend'], 'is_published' => true, 'sort_order' => 20],
            ['type' => 'skill', 'title' => 'ASP.NET · C#', 'subtitle' => 'Backend', 'metadata' => ['group' => 'Backend'], 'is_published' => true, 'sort_order' => 30],
            ['type' => 'skill', 'title' => 'React.js · Next.js', 'subtitle' => 'Frontend', 'metadata' => ['group' => 'Frontend'], 'is_published' => true, 'sort_order' => 40],
            ['type' => 'skill', 'title' => 'JavaScript · HTML · CSS', 'subtitle' => 'Frontend', 'metadata' => ['group' => 'Frontend'], 'is_published' => true, 'sort_order' => 50],
            ['type' => 'skill', 'title' => 'OOP · MVC · SOLID · REST APIs', 'subtitle' => 'Engineering', 'metadata' => ['group' => 'Engineering'], 'is_published' => true, 'sort_order' => 60],
            ['type' => 'skill', 'title' => 'Git · GitHub · CI/CD · cPanel', 'subtitle' => 'Delivery', 'metadata' => ['group' => 'Delivery'], 'is_published' => true, 'sort_order' => 70],
            ['type' => 'personal', 'title' => 'Motorcycle touring', 'eyebrow' => 'The long way', 'summary' => 'Long roads and time outside.', 'is_published' => true, 'sort_order' => 10],
            ['type' => 'personal', 'title' => 'Nature and open places', 'eyebrow' => 'Finding stillness', 'summary' => 'Changing skies, mountains, rivers and lakes.', 'is_published' => true, 'sort_order' => 20],
            ['type' => 'social', 'title' => 'GitHub', 'url' => 'https://github.com/Alif-Utsho', 'is_published' => true, 'sort_order' => 10],
            ['type' => 'social', 'title' => 'Facebook', 'url' => 'https://www.facebook.com/utsho.aiub', 'is_published' => true, 'sort_order' => 20],
            ['type' => 'social', 'title' => 'Instagram', 'url' => 'https://www.instagram.com/alif_utsho', 'is_published' => true, 'sort_order' => 30],
            ['type' => 'navigation', 'title' => 'About', 'url' => '/#about', 'is_published' => true, 'sort_order' => 10],
            ['type' => 'navigation', 'title' => 'Experience', 'url' => '/#experience', 'is_published' => true, 'sort_order' => 20],
            ['type' => 'navigation', 'title' => 'Stack', 'url' => '/#skills', 'is_published' => true, 'sort_order' => 30],
            ['type' => 'navigation', 'title' => 'Projects', 'url' => '/projects', 'is_published' => true, 'sort_order' => 40],
            ['type' => 'navigation', 'title' => 'Beyond code', 'url' => '/#beyond', 'is_published' => true, 'sort_order' => 50],
        ];

        foreach ($entries as $entry) {
            PortfolioItem::query()->firstOrCreate(
                ['type' => $entry['type'], 'title' => $entry['title']],
                $entry,
            );
        }
    }
}

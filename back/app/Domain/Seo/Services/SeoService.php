<?php

namespace App\Domain\Seo\Services;

use App\Domain\Seo\Repositories\SeoPageRepositoryInterface;

class SeoService
{
    public function __construct(
        private readonly SeoPageRepositoryInterface $seoPageRepository,
    ) {
    }

    public function getSeoData(?string $routeName = null, ?string $url = null): ?array
    {
        $seoPage = null;

        if ($routeName) {
            $seoPage = $this->seoPageRepository->findByRouteName($routeName);
        }

        if (!$seoPage && $url) {
            $seoPage = $this->seoPageRepository->findByUrl($url);
        }

        if (!$seoPage) {
            return null;
        }

        return [
            'title' => $seoPage->meta_title ?: $seoPage->page_title,
            'description' => $seoPage->meta_description,
            'keywords' => $seoPage->meta_keywords ? implode(', ', $seoPage->meta_keywords) : null,
        ];
    }

    public function getDefaultPages(): array
    {
        return [
            [
                'route_name' => 'home',
                'page_url' => '/',
                'page_title' => 'Home',
                'meta_title' => 'Gold To Cash - Buy Gold and Silver | Professional Precious Metals Dealer',
                'meta_description' => 'Buy gold and silver at competitive prices. Professional precious metals dealer offering appraisal services, kit requests, and secure transactions.',
                'meta_keywords' => ['gold', 'silver', 'precious metals', 'gold buyer', 'silver buyer', 'appraisal', 'kit request'],
                'is_active' => true,
            ],
            [
                'route_name' => 'about',
                'page_url' => '/about',
                'page_title' => 'About Us',
                'meta_title' => 'About Gold To Cash - Professional Gold & Silver Dealers',
                'meta_description' => 'Learn about Gold To Cash, your trusted professional precious metals dealer. We offer competitive prices for gold and silver buying and selling.',
                'meta_keywords' => ['about', 'gold dealer', 'silver dealer', 'precious metals', 'professional service'],
                'is_active' => true,
            ],
            [
                'route_name' => 'contact',
                'page_url' => '/contact',
                'page_title' => 'Contact Us',
                'meta_title' => 'Contact Gold To Cash - Get In Touch With Our Precious Metals Experts',
                'meta_description' => 'Contact Gold To Cash for all your gold and silver buying needs. Our professional team is ready to assist you with appraisals and transactions.',
                'meta_keywords' => ['contact', 'gold buyer', 'silver buyer', 'appraisal service', 'precious metals dealer'],
                'is_active' => true,
            ],
            [
                'route_name' => 'services',
                'page_url' => '/services',
                'page_title' => 'Our Services',
                'meta_title' => 'Services - Gold & Silver Buying, Appraisal & Kit Requests',
                'meta_description' => 'Discover our comprehensive precious metals services including gold and silver buying, professional appraisals, and kit requests.',
                'meta_keywords' => ['services', 'gold buying', 'silver buying', 'appraisal', 'kit request', 'precious metals'],
                'is_active' => true,
            ],
            [
                'route_name' => 'privacy-policy',
                'page_url' => '/privacy-policy',
                'page_title' => 'Privacy Policy',
                'meta_title' => 'Privacy Policy - Gold To Cash',
                'meta_description' => 'Read our privacy policy to understand how Gold To Cash protects your personal information and ensures secure transactions.',
                'meta_keywords' => ['privacy policy', 'data protection', 'personal information', 'secure transactions'],
                'is_active' => true,
            ],
            [
                'route_name' => 'terms-and-conditions',
                'page_url' => '/terms-and-conditions',
                'page_title' => 'Terms and Conditions',
                'meta_title' => 'Terms and Conditions - Gold To Cash',
                'meta_description' => 'Review our terms and conditions for gold and silver transactions, appraisals, and kit requests.',
                'meta_keywords' => ['terms and conditions', 'transaction terms', 'appraisal terms', 'kit request terms'],
                'is_active' => true,
            ],
        ];
    }
}

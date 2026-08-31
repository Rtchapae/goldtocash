import { getCurrentRouteSeoData } from '@/api/seo.js'
import { BLOG_POST_ROUTE_NAMES } from '@/constants/blogPostRoutes.js'

const GA_MEASUREMENT_ID = 'G-YCPB4K7QYZ'

class SeoService {
    constructor() {
        this.defaultMeta = {
            title: 'Gold To Cash - Buy Gold and Silver | Professional Precious Metals Dealer',
            description: 'Buy gold and silver at competitive prices. Professional precious metals dealer offering appraisal services, kit requests, and secure transactions.',
            keywords: 'gold, silver, precious metals, gold buyer, silver buyer, appraisal, kit request'
        };
    }

    setMeta(meta) {
        if (typeof document === 'undefined') return; // SSR safety

        const title = meta.title || this.defaultMeta.title;
        const description = meta.description || this.defaultMeta.description;
        const keywords = Array.isArray(meta.keywords)
            ? meta.keywords.join(', ')
            : (meta.keywords || this.defaultMeta.keywords);

        document.title = title;

        this.updateMetaTag('name', 'description', description);
        this.updateMetaTag('name', 'keywords', keywords);

        this.updateMetaTag('property', 'og:title', title);
        this.updateMetaTag('property', 'og:description', description);
        this.updateMetaTag('property', 'og:type', 'website');
    }

    updateMetaTag(attribute, value, content) {
        if (typeof document === 'undefined') return;

        let metaTag = document.querySelector(`meta[${attribute}="${value}"]`);

        if (metaTag) {
            metaTag.setAttribute('content', content);
        } else {
            metaTag = document.createElement('meta');
            metaTag.setAttribute(attribute, value);
            metaTag.setAttribute('content', content);
            document.head.appendChild(metaTag);
        }
    }

    resetMeta() {
        this.setMeta(this.defaultMeta);
    }

    getRouteName(route) {
        return route?.name || null;
    }

    getUrlPath(route) {
        return route?.path || '/';
    }

	sendGaPageView(route) {
		if (typeof window === 'undefined' || typeof window.gtag !== 'function') return
		const path = route?.fullPath || route?.path || window.location.pathname
		window.gtag('config', GA_MEASUREMENT_ID, {
			page_path: path,
			page_title: document.title,
		})
	}

	async loadSeoForRoute(route) {
		if (route?.name && BLOG_POST_ROUTE_NAMES.includes(route.name)) {
			return
		}
		try {
			const seoData = await getCurrentRouteSeoData(route)
			if (seoData) {
				this.setMeta({
					title: seoData.title,
					description: seoData.description,
					keywords: seoData.keywords,
				})
			} else {
				this.resetMeta()
			}
		} catch (error) {
			console.warn('Failed to load SEO data:', error)
		}
		this.sendGaPageView(route)
	}
}

const seoService = new SeoService();

export default seoService;

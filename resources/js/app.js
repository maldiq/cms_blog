import './bootstrap';
import { initAffiliateHeroSwiper } from './affiliate-swiper';
import { initThemeTestimonialSwiper } from './theme-swiper';

window.initThemeTestimonialSwiper = initThemeTestimonialSwiper;

function bootStorefront() {
    initAffiliateHeroSwiper();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootStorefront);
} else {
    bootStorefront();
}

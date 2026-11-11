import Swiper from 'swiper';
import { Autoplay, Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

export function initAffiliateHeroSwiper() {
    document.querySelectorAll('[data-hero-swiper]').forEach((element) => {
        const slider = element.closest('[data-hero-slider]') ?? element;
        const slideCount = element.querySelectorAll('.swiper-slide').length;
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        new Swiper(element, {
            modules: [Autoplay, Navigation, Pagination],
            loop: slideCount > 1,
            speed: 650,
            autoplay: reduceMotion || slideCount < 2
                ? false
                : {
                    delay: 6000,
                    disableOnInteraction: false,
                },
            pagination: {
                el: slider.querySelector('.hero-pagination'),
                clickable: true,
            },
            navigation: {
                nextEl: slider.querySelector('.hero-next'),
                prevEl: slider.querySelector('.hero-prev'),
            },
        });
    });
}

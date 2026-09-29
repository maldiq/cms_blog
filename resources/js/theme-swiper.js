import Swiper from 'swiper';
import { Autoplay, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';

export function initThemeTestimonialSwiper(element) {
    const paginationEl = element.querySelector('.swiper-pagination');

    return new Swiper(element, {
        modules: [Autoplay, Pagination],
        slidesPerView: 1,
        spaceBetween: 24,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: paginationEl,
            clickable: true,
        },
        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
        },
    });
}

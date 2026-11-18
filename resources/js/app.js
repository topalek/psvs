import Alpine from 'alpinejs';
import Swiper from 'swiper';
import { A11y, EffectFade, Keyboard, Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/effect-fade';

window.Alpine = Alpine;
window.Swiper = Swiper;

const initHomeHero = () => {
    document.querySelectorAll('[data-home-hero]').forEach((element) => {
        new Swiper(element, {
            modules: [A11y, EffectFade, Keyboard, Navigation, Pagination],
            effect: 'fade',
            fadeEffect: {
                crossFade: true,
            },
            speed: 400,
            rewind: true,
            grabCursor: true,
            slidesPerView: 1,
            keyboard: {
                enabled: true,
                onlyInViewport: true,
            },
            navigation: {
                prevEl: element.querySelector('[data-home-hero-prev]'),
                nextEl: element.querySelector('[data-home-hero-next]'),
            },
            pagination: {
                el: element.querySelector('[data-home-hero-pagination]'),
                clickable: true,
            },
            a11y: {
                containerRoleDescriptionMessage: 'карусель',
                itemRoleDescriptionMessage: 'слайд',
                prevSlideMessage: 'Предыдущий слайд',
                nextSlideMessage: 'Следующий слайд',
                firstSlideMessage: 'Это первый слайд',
                lastSlideMessage: 'Это последний слайд',
                paginationBulletMessage: 'Перейти к слайду {{index}}',
                slideLabelMessage: 'Слайд {{index}} из {{total}}',
            },
        });
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHomeHero);
} else {
    initHomeHero();
}

Alpine.start();

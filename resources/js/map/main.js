import { createApp } from 'vue';
import { createPinia } from 'pinia';
import NetworkMap from './components/NetworkMap.vue';

const mountPoint = document.getElementById('network-map-app');

if (mountPoint) {
    const dataElement = document.getElementById('network-map-data');
    let areas = [];

    if (dataElement) {
        try {
            areas = JSON.parse(dataElement.textContent ?? '[]');
        } catch (error) {
            console.error('Не удалось прочитать данные карты сети', error);
        }
    }

    const center = (mountPoint.dataset.center ?? '44.940183,37.990017')
        .split(',')
        .map((value) => Number.parseFloat(value.trim()));

    createApp(NetworkMap, {
        areas,
        apiKey: mountPoint.dataset.apiKey ?? '',
        center: center.every((value) => Number.isFinite(value)) ? center : [44.940183, 37.990017],
        zoom: Number.parseFloat(mountPoint.dataset.zoom ?? '16') || 16,
    })
        .use(createPinia())
        .mount(mountPoint);
}

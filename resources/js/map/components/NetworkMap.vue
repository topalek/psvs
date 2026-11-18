<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useNetworkMapStore } from '../stores/networkMap';

const props = defineProps({
    areas: {
        type: Array,
        default: () => [],
    },
    apiKey: {
        type: String,
        default: '',
    },
    center: {
        type: Array,
        default: () => [44.940183, 37.990017],
    },
    zoom: {
        type: Number,
        default: 16,
    },
});

const mapStore = useNetworkMapStore();
const { query, district, districts, filteredAreas, selectedArea } = storeToRefs(mapStore);

mapStore.setAreas(props.areas);

watch(
    () => props.areas,
    (value) => mapStore.setAreas(value),
);

const mapElement = ref(null);
const mapMessage = ref('');
let mapInstance = null;

async function initMap() {
    if (!props.apiKey) {
        mapMessage.value = 'Не задан ключ Яндекс Карт. Проверьте YANDEX_MAP_API в .env и services.yandex_maps.key в config/services.php.';
        return;
    }

    try {
        if (!window.ymaps3) {
            throw new Error('ymaps3 is not loaded');
        }

        await window.ymaps3.ready;

        const { YMap, YMapDefaultSchemeLayer } = window.ymaps3;

        mapInstance = new YMap(mapElement.value, {
            location: {
                center: props.center,
                zoom: props.zoom,
            },
        });

        mapInstance.addChild(new YMapDefaultSchemeLayer());
    } catch (error) {
        console.error('Не удалось инициализировать карту', error);
        mapMessage.value = 'Не удалось загрузить Яндекс Карты. Проверьте ключ API и подключение к интернету.';
    }
}

onMounted(initMap);

onBeforeUnmount(() => {
    mapInstance?.destroy?.();
    mapInstance = null;
});
</script>

<template>
    <div class="nm">
        <aside class="nm-panel">
            <label class="nm-search-wrap">
                <span class="nm-sr-only">Поиск населённого пункта</span>
                <input
                    v-model="query"
                    type="search"
                    class="nm-search"
                    placeholder="Найти населённый пункт"
                >
            </label>

            <div v-if="districts.length" class="nm-chips">
                <button
                    v-for="name in districts"
                    :key="name"
                    type="button"
                    class="nm-chip"
                    :class="{ 'nm-chip--active': district === name }"
                    @click="mapStore.selectDistrict(name)"
                >
                    {{ name }}
                </button>
            </div>

            <button
                v-if="query || district"
                type="button"
                class="nm-reset"
                @click="mapStore.resetFilters()"
            >
                Сбросить фильтры
            </button>

            <ul class="nm-list">
                <li v-for="area in filteredAreas" :key="area.id">
                    <button
                        type="button"
                        class="nm-area"
                        :class="{ 'nm-area--active': selectedArea?.id === area.id }"
                        @click="mapStore.selectArea(area.id)"
                    >
                        <span>
                            <span class="nm-area-name">{{ area.name }}</span>
                            <span v-if="area.district" class="nm-area-district">{{ area.district }}</span>
                        </span>
                        <span class="nm-badge">проверка</span>
                    </button>
                </li>
                <li v-if="!filteredAreas.length" class="nm-empty">
                    Ничего не найдено. Измените запрос или сбросьте фильтры.
                </li>
            </ul>
        </aside>

        <div class="nm-map-wrap">
            <div ref="mapElement" class="nm-map"></div>

            <div v-if="mapMessage" class="nm-overlay">
                <p>{{ mapMessage }}</p>
            </div>

            <span class="nm-counter">
                Населённых пунктов: {{ filteredAreas.length }} из {{ mapStore.areas.length }}
            </span>
        </div>
    </div>
</template>

<style scoped>
.nm {
    display: grid;
    gap: 1rem;
}

@media (min-width: 1024px) {
    .nm {
        grid-template-columns: 320px minmax(0, 1fr);
        align-items: start;
    }
}

.nm-sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

.nm-panel {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding: 1rem;
    border: 1px solid var(--gray-200);
    border-radius: 0.75rem;
    background: var(--gray-50);
    color: var(--gray-950);
}

.nm-search-wrap {
    display: block;
}

.nm-search {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid var(--gray-300);
    border-radius: 0.5rem;
    background: #fff;
    color: var(--gray-950);
    font-size: 0.875rem;
    line-height: 1.25rem;
}

.nm-search:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 2px color-mix(in oklab, var(--primary-500) 25%, transparent);
}

.nm-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.375rem;
}

.nm-chip {
    padding: 0.25rem 0.625rem;
    border: 1px solid var(--gray-300);
    border-radius: 9999px;
    background: transparent;
    color: var(--gray-600);
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
}

.nm-chip:hover {
    border-color: var(--primary-500);
    color: var(--primary-500);
}

.nm-chip--active {
    border-color: var(--primary-600);
    background: var(--primary-600);
    color: #fff;
}

.nm-reset {
    align-self: flex-start;
    padding: 0.375rem 0.75rem;
    border: 1px solid var(--gray-300);
    border-radius: 0.5rem;
    background: transparent;
    color: var(--gray-600);
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
}

.nm-reset:hover {
    color: var(--gray-950);
    border-color: var(--gray-500);
}

.nm-list {
    display: grid;
    gap: 0.375rem;
    max-height: calc(100vh - 22rem);
    margin: 0;
    padding: 0;
    overflow-y: auto;
    list-style: none;
}

.nm-area {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid var(--gray-200);
    border-radius: 0.5rem;
    background: #fff;
    cursor: pointer;
    text-align: left;
}

.nm-area:hover {
    border-color: var(--primary-400);
}

.nm-area--active {
    border-color: var(--primary-500);
    box-shadow: inset 0 0 0 1px var(--primary-500);
}

.nm-area-name {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: inherit;
}

.nm-area-district {
    display: block;
    font-size: 0.75rem;
    color: var(--gray-500);
}

.nm-badge {
    flex-shrink: 0;
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
    background: var(--gray-100);
    color: var(--gray-600);
    font-size: 0.6875rem;
    font-weight: 600;
}

.nm-empty {
    font-size: 0.875rem;
    color: var(--gray-500);
}

.nm-map-wrap {
    position: relative;
    height: calc(100vh - 13rem);
    min-height: 500px;
    overflow: hidden;
    border: 1px solid var(--gray-200);
    border-radius: 0.75rem;
    background: var(--gray-100);
}

.nm-map {
    position: absolute;
    inset: 0;
}

.nm-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    background: var(--gray-100);
    color: var(--gray-600);
    font-size: 0.875rem;
    text-align: center;
}

.nm-overlay p {
    max-width: 26rem;
    margin: 0;
}

.nm-counter {
    position: absolute;
    bottom: 0.75rem;
    left: 0.75rem;
    z-index: 1;
    padding: 0.25rem 0.625rem;
    border-radius: 9999px;
    background: #fff;
    color: var(--gray-700);
    font-size: 0.6875rem;
    font-weight: 600;
    box-shadow: 0 1px 3px rgb(0 0 0 / 15%);
}

.dark .nm-panel {
    border-color: var(--gray-800);
    background: var(--gray-900);
    color: var(--gray-100);
}

.dark .nm-search {
    border-color: var(--gray-700);
    background: var(--gray-950);
    color: var(--gray-100);
}

.dark .nm-chip {
    border-color: var(--gray-700);
    color: var(--gray-300);
}

.dark .nm-chip--active {
    border-color: var(--primary-600);
    background: var(--primary-600);
    color: #fff;
}

.dark .nm-reset {
    border-color: var(--gray-700);
    color: var(--gray-300);
}

.dark .nm-reset:hover {
    color: var(--gray-100);
    border-color: var(--gray-500);
}

.dark .nm-area {
    border-color: var(--gray-800);
    background: var(--gray-950);
}

.dark .nm-area--active {
    border-color: var(--primary-500);
}

.dark .nm-area-district {
    color: var(--gray-400);
}

.dark .nm-badge {
    background: var(--gray-800);
    color: var(--gray-300);
}

.dark .nm-empty {
    color: var(--gray-400);
}

.dark .nm-map-wrap {
    border-color: var(--gray-800);
    background: var(--gray-900);
}

.dark .nm-overlay {
    background: var(--gray-900);
    color: var(--gray-300);
}

.dark .nm-counter {
    background: var(--gray-800);
    color: var(--gray-200);
}
</style>

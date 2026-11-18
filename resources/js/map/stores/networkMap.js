import { computed, ref } from 'vue';
import { defineStore } from 'pinia';

export const useNetworkMapStore = defineStore('network-map', () => {
    const areas = ref([]);
    const query = ref('');
    const district = ref(null);
    const selectedAreaId = ref(null);

    const districts = computed(() => {
        const unique = new Set();

        areas.value.forEach((area) => {
            if (area.district) {
                unique.add(area.district);
            }
        });

        return [...unique].sort((a, b) => a.localeCompare(b, 'ru'));
    });

    const filteredAreas = computed(() => {
        const needle = query.value.trim().toLowerCase();

        return areas.value.filter((area) => {
            if (district.value && area.district !== district.value) {
                return false;
            }

            if (!needle) {
                return true;
            }

            return `${area.name} ${area.district ?? ''}`.toLowerCase().includes(needle);
        });
    });

    const selectedArea = computed(
        () => areas.value.find((area) => area.id === selectedAreaId.value) ?? null,
    );

    function setAreas(items) {
        areas.value = Array.isArray(items) ? items : [];
    }

    function selectArea(id) {
        selectedAreaId.value = selectedAreaId.value === id ? null : id;
    }

    function selectDistrict(name) {
        district.value = district.value === name ? null : name;
    }

    function resetFilters() {
        query.value = '';
        district.value = null;
    }

    return {
        areas,
        query,
        district,
        districts,
        filteredAreas,
        selectedArea,
        selectedAreaId,
        setAreas,
        selectArea,
        selectDistrict,
        resetFilters,
    };
});

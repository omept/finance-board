<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BehavioralSummaryChart from './Partials/BehavioralSummaryChart.vue';
export default {
    layout: AuthenticatedLayout,
};
</script>

<script setup>
import { Head } from '@inertiajs/vue3';


defineProps({
    auth: {
        type: Object,
    },
    errors: {
        type: Object,
    },
    sensorData: {
        type: Object,
    },
});

</script>

<template>

    <Head title="Dashboard" />
    <div>
        <div class="flex flex-wrap items-center justify-center">
            <!-- Render a separate chart for each sensor -->
            <BehavioralSummaryChart v-for="(sensor, index) in sensorData" :key="index" :sensorName="sensor.label"
                :initialData="sensor.data" :sensorId="sensor.id" />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, toRaw, watch } from 'vue';
import Chart from 'chart.js/auto';

const chartCanvas = ref(null);
let chartInstance = null;

const props = defineProps({
    sensorName: {
        type: String,
        required: true,
    },
    sensorId: {
        required: true,
    },
    initialData: {
        type: Array,
        default: () => [],
    },
});

const defaultTimeRange = '30_minutes';
let timeRangeWatcherEnabled = true;
const loading = ref(false);  // loading state
const selectedTimeRange = ref(defaultTimeRange); // Default time range
const timeRanges = [
    { value: defaultTimeRange, label: '30 Minutes ago' },
    { value: '1_hour', label: '1 Hour ago' },
    { value: '6_hours', label: '6 Hours ago' },
    { value: '24_hours', label: '24 Hours ago' },
    { value: '7_days', label: '7 Days ago' },
    { value: '14_days', label: '14 Days ago' },
    { value: '1_month', label: '1 Month ago' },
    { value: '6_month', label: '6 Months ago' },
    { value: '1_year', label: '1 Year ago' },
];

const reloadComponent = () => {
    if (chartInstance) {
        timeRangeWatcherEnabled = false;
        loading.value = true;
        selectedTimeRange.value = defaultTimeRange;
        const dataset = chartInstance.data.datasets[0];
        dataset.data = [...props.initialData]; // Overwrite the dataset with the new data
        chartInstance.data.labels = Array.from({ length: 40 }, (_, i) => i + 1),
            chartInstance.update();
        loading.value = false;
        timeRangeWatcherEnabled = true;
    }
};

const fetchDataForTimeRange = async (timeRange) => {
    console.log('Loading data for time range:', timeRange);
    loading.value = true; // Set loading to true when data fetch starts

    try {
        // Fetch the streamed response
        const response = await fetch(`${route('raw.sensor.data')}?sensor_id=${props.sensorId}&time_stamp=${timeRange}`);

        if (!response.ok) {
            throw new Error(`Error fetching data: ${response.statusText}`);
        }

        // Initialize variables for processing the streamed data
        const reader = response.body.getReader();
        let decoder = new TextDecoder('utf-8');
        let accumulatedData = []; // Accumulated data for the chart
        let buffer = ''; // Buffer for partial chunks

        let hasParts = false;
        // eslint-disable-next-line no-constant-condition
        while (true) {
            const { done, value } = await reader.read();
            if (done) break;

            // Decode the chunk and add it to the buffer
            buffer += decoder.decode(value, { stream: true });

            // Attempt to split JSON array chunks
            let parts = buffer.split('][');

            // Process each part while keeping the last incomplete one in the buffer
            parts.forEach((part, index) => {
                try {
                    if (parts.length > 1) {
                        hasParts = true;
                        // Reconstruct valid JSON for each part
                        if (index === 0 && parts.length > 1) {
                            part += ']'; // Add closing bracket to the first chunk
                        } else if (index === parts.length - 1) {
                            part = '[' + part; // Add opening bracket to the last chunk
                            buffer = part; // Keep incomplete chunk in the buffer
                        } else {
                            part = '[' + part + ']'; // Add both brackets for middle chunks
                        }
                        // Parse the chunk and merge it into accumulatedData
                        const parsedChunk = JSON.parse(part);
                        accumulatedData = accumulatedData.concat(parsedChunk);
                    }

                } catch (error) {
                    if (index === parts.length - 1) {
                        // Keep the last part in the buffer if it can't be parsed
                        buffer = part;
                    } else {
                        console.error('Error parsing chunk:', error, part);
                    }
                }
            });
        }

        // Clear the remaining buffer (if any) after the stream ends
        // Final buffer parsing after the stream ends
        if (buffer.trim()) {
            try {
                if (hasParts) {
                    // Ensure the buffer has a valid JSON structure
                    if (hasParts && buffer.startsWith('[') && buffer.endsWith(']]')) {
                        buffer = buffer.slice(0, -1); // Remove the extra closing bracket
                    }
                    if (buffer.startsWith('[') && buffer.endsWith(']')) {
                        const parsedChunk = JSON.parse(buffer);
                        accumulatedData = accumulatedData.concat(parsedChunk);
                    } else {
                        console.error('Invalid JSON structure in remaining buffer:', buffer);
                    }
                } else {
                    // Ensure the buffer has a valid JSON structure
                    if (buffer.startsWith('[') && buffer.endsWith(']')) {
                        const parsedChunk = JSON.parse(buffer);
                        accumulatedData = accumulatedData.concat(parsedChunk.flat());
                    }
                }
            } catch (error) {
                console.error('Error parsing remaining buffer:', error, buffer);
            }
        }

        // Update the chart with the new data
        if (chartInstance) {
            const dataset = chartInstance.data.datasets[0];
            dataset.data = accumulatedData; // Overwrite the dataset with the new data
            chartInstance.data.labels = Array.from(
                { length: accumulatedData.length },
                (_, i) => i + 1
            ); // Update labels
            chartInstance.update();
            console.log('Chart updated for time range:', timeRange);
        }
    } catch (error) {
        console.error('Error fetching data for time range:', error);
    } finally {
        loading.value = false; // Set loading to false when data fetching finishes
    }
};


onMounted(() => {
    // Initialize the chart
    chartInstance = new Chart(chartCanvas.value.getContext('2d'), {
        type: 'line',
        data: {
            labels: Array.from({ length: 40 }, (_, i) => i + 1),
            datasets: [
                {
                    label: props.sensorName,
                    data: [...toRaw(props.initialData)],
                    borderColor: getRandomColor(),
                    backgroundColor: 'transparent',
                    borderWidth: 1,
                    pointRadius: 2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { display: false, beginAtZero: true },
                y: {
                    beginAtZero: true,
                    grid: { color: '#1F1F1F' },
                    ticks: { color: '#6B7280' },
                },
            },
            plugins: {
                legend: { display: true }, // Enable legend for dynamic datasets
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: (context) =>
                            `${context.dataset.label}: ${context.parsed.y}`,
                    },
                },
            },
        },
    });

    // Listen for sensor data updates
    window.Echo.private('sensor-data').listen('SensorDataAdded', (event) => {

        if (selectedTimeRange.value != defaultTimeRange) { // If the selected time range is not the default one, then do not update the chart
            return;
        }

        if (event.sensorData.sensor_name === props.sensorName) {

            const { value } = event.sensorData;

            // Update the chart with new data
            if (chartInstance) {
                const dataset = chartInstance.data.datasets[0];
                dataset.data.push(value);

                // Maintain the last 30 values
                if (dataset.data.length > 30) {
                    dataset.data.shift();
                }

                chartInstance.update();
            }
        }
    });
});

watch(selectedTimeRange, (newTimeRange) => {
    // Fetch data when time range changes
    if (timeRangeWatcherEnabled) {
        fetchDataForTimeRange(newTimeRange);
    }
    timeRangeWatcherEnabled = false;
},{ flush: 'sync' });

const exportChartToCSV = () => {
    // Get the data from the chart instance
    const chartData = chartInstance.data.datasets[0].data;
    const labels = chartInstance.data.labels;

    // Convert data to CSV format
    const csvRows = [];

    // Add header row (labels)
    csvRows.push(['Label', 'Value']);

    // Add data rows
    chartData.forEach((value, index) => {
        csvRows.push([labels[index], value]);
    });

    // Convert the array of rows into a CSV string
    const csvString = csvRows.map(row => row.join(',')).join('\n');

    // Create a Blob from the CSV string and create a download link
    const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'chart_data.csv');
    link.style.visibility = 'hidden';

    // Append the link to the DOM and trigger a click to download
    document.body.appendChild(link);
    link.click();

    // Clean up the DOM
    document.body.removeChild(link);
};

// Utility function to generate random colors for datasets
function getRandomColor() {
    const letters = 'ABCDEF0123456789';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}
</script>

<template>
    <div class="w-full bg-[#1E1E1E] rounded-lg shadow-lg">
        <!-- Header -->
        <div class="flex justify-between items-center my-4">
            <h2 class="text-lg font-semibold text-white">Behavioral Summary Chart : {{ sensorName }}</h2>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-1 text-sm text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6h4.5m5.25-2.25A9 9 0 11.75 12a9 9 0 0118 0z" />
                    </svg>
                    <select v-model="selectedTimeRange"
                        class="bg-[#1E1E1E] text-gray-400 text-sm rounded-md p-1 focus:outline-none focus:ring focus:ring-gray-600">
                        <option v-for="range in timeRanges" :key="range.value" :value="range.value">
                            {{ range.label }}
                        </option>
                    </select>
                </div>
                <button @click="exportChartToCSV"
                    class="flex items-center gap-1 text-gray-400 hover:text-gray-200 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 16.5v2.25a2.25 2.25 0 002.25 2.25h13.5a2.25 2.25 0 002.25-2.25V16.5M7.5 12l4.5-4.5 4.5 4.5M12 3v9" />
                    </svg>
                    <span>Export</span>
                </button>
                <button @click="reloadComponent"
                    class="flex items-center gap-1 text-gray-400 hover:text-gray-200 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0zM12 7.5v4.5h4.5" />
                    </svg>
                    <span>Refresh</span>
                </button>
            </div>
        </div>

        <!-- Chart Area -->
        <div class="relative bg-[#111111] rounded-md h-64 mb-4">
            <canvas id="chartCanvas" ref="chartCanvas" class="w-full h-full"></canvas>
            <div v-if="loading"
                class="absolute top-0 left-0 right-0 bottom-0 flex items-center justify-center bg-[#00000080]">
                <span class="text-white text-xl">Loading...</span>
            </div>
        </div>
    </div>
</template>

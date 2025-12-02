<script setup>
import { ref } from "vue";
import getChartColorsArray from "@/common/getChartColorsArray";
import { computed } from "vue";

const props = defineProps({
  revinue: Number,
  expense: Number,
  monthWise: Array,
  montlyRevinue: Array,
});

// profit ratio in %
const profitRatio = computed(() => {
  if (!props.revinue || props.revinue === 0) return 0;
  return (((props.revinue - props.expense) / props.revinue) * 100).toFixed(2);
});


const series = ref([
  {
    name: "Revenue",
    data: props.montlyRevinue,
  },
  {
    name: "Expenses",
    data: props.monthWise,
  },
]);

const chartOptions = ref({
  chart: {
    height: 290,
    type: "area",
    toolbar: "false",
  },
  dataLabels: {
    enabled: false,
  },
  stroke: {
    curve: "smooth",
    width: 2,
  },
  xaxis: {
    categories: [
      "Jan","Feb","Mar","Apr","May","Jun",
      "Jul","Aug","Sep","Oct","Nov","Dec",
    ],
  },
  yaxis: {
    labels: {
       formatter: (value) => "$" + (value / 1000).toFixed(0) + "k",
    },
    tickAmount: 6,
    min: 0,
    max: 50000, 
  },
  colors: getChartColorsArray('["--vz-success", "--vz-danger"]'),
  fill: {
    opacity: 0.06,
    colors: ["#0AB39C", "#F06548"],
    type: "solid",
  },
});

</script>


<template>
  <BCard no-body class="card-height-100">
    <!-- Header with Dropdown -->
    <BCardHeader class="align-items-center d-flex py-0">
      <BCardTitle class="mb-0 flex-grow-1 py-3">Expense and Profit Summaries</BCardTitle>
    </BCardHeader>

    <!-- Body with summary numbers -->
    <BCardBody class="px-0">
      <ul class="list-inline main-chart text-center mb-0">
        <li class="list-inline-item chart-border-left me-0 border-0">
          <h4 class="text-primary">
            {{ props.revinue }}
            <span class="text-muted d-inline-block fs-13 align-middle ms-2">Revenue</span>
          </h4>
        </li>
        <li class="list-inline-item chart-border-left me-0">
          <h4>
            {{ props.expense }}
            <span class="text-muted d-inline-block fs-13 align-middle ms-2">Expenses</span>
          </h4>
        </li>
        <li class="list-inline-item chart-border-left me-0">
          <h4>
            {{ profitRatio }} %
            <span class="text-muted d-inline-block fs-13 align-middle ms-2">Profit Ratio</span>
          </h4>
        </li>
      </ul>

      <!-- ApexChart -->
      <apexchart class="apex-charts" height="290" dir="ltr" :series="series" :options="chartOptions">
      </apexchart>
    </BCardBody>
  </BCard>
</template>

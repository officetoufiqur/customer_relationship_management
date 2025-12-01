<script setup>
import { ref } from "vue";
import getChartColorsArray from "@/common/getChartColorsArray";

const props = defineProps({
  dealPending: Number,
  dealWon: Number,
  dealLoss: Number,
})
const series = ref([
  {
    name: "Pending",
    data: [props.dealPending],
  },
  {
    name: "Won",
    data: [props.dealWon],
  },
  {
    name: "Loss",
    data: [props.dealLoss],
  },
]);

const chartOptions = ref({
  chart: {
    type: "bar",
    height: 341,
    toolbar: {
      show: false,
    },
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: "65%",
    },
  },
  stroke: {
    show: true,
    width: 5,
    colors: ["transparent"],
  },
  xaxis: {
    categories: [""],
    axisTicks: {
      show: false,
      borderType: "solid",
      color: "#78909C",
      height: 6,
      offsetX: 0,
      offsetY: 0,
    },
    title: {
      text: "Total Deals Value",
      offsetX: 0,
      offsetY: -30,
      style: {
        color: "#78909C",
        fontSize: "12px",
        fontWeight: 400,
      },
    },
  },
  yaxis: {
    min: 0,
    max: 5, 
    tickAmount: 5,
    labels: {
      formatter(value) {
        return value;
      },
    },
  },
  fill: {
    opacity: 1,
  },
  legend: {
    show: true,
    position: "bottom",
    horizontalAlign: "center",
    fontWeight: 500,
    offsetX: 0,
    offsetY: -14,
    itemMargin: {
      horizontal: 8,
      vertical: 0,
    },
    markers: {
      width: 10,
      height: 10,
    },
  },
  colors: getChartColorsArray('["--vz-primary", "--vz-success", "--vz-warning"]'),
});
</script>


<template>
  <BCard no-body>
    <BCardHeader class="align-items-center d-flex py-3">
      <BCardTitle class="mb-0 flex-grow-1">Deals Forecast</BCardTitle>
    </BCardHeader>

    <BCardBody class="pb-0">
      <apexchart class="apex-charts" height="341" dir="ltr" :series="series" :options="chartOptions"></apexchart>
    </BCardBody>
  </BCard>
</template>
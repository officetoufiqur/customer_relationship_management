<script setup>
import { ref, computed } from "vue";
import getChartColorsArray from "@/common/getChartColorsArray";

// Props for reusable component
const props = defineProps({
  monthlyData: {
    type: Array,
    default: () => []
  }
});

// Dropdown state
const selectedFilter = ref("Current Year");

// Compute summary numbers based on selected filter
const filteredSummary = computed(() => {
  if (!props.monthlyData.length) return { revenue: 0, expenses: 0, profitRatio: 0 };

  const now = new Date();
  const currentMonth = now.getMonth() + 1;

  let filtered = [];

  switch (selectedFilter.value) {
    case "Today":
      filtered = props.monthlyData.filter(item => item.month === currentMonth);
      break;
    case "Last Week":
      filtered = props.monthlyData.filter(item => item.month === currentMonth); // approximate for monthly data
      break;
    case "Last Month":
      const lastMonth = currentMonth - 1 || 12;
      filtered = props.monthlyData.filter(item => item.month === lastMonth);
      break;
    case "Current Year":
    default:
      filtered = props.monthlyData;
      break;
  }

  const revenue = filtered.reduce((sum, item) => sum + item.revenue, 0);
  const expenses = filtered.reduce((sum, item) => sum + item.expenses, 0);
  const profitRatio = revenue > 0 ? ((revenue - expenses) / revenue * 100).toFixed(2) : 0;


  return { revenue, expenses, profitRatio };
});

// Chart series (always full data)
const series = computed(() => [
  { name: "Revenue", data: props.monthlyData.map(item => item.revenue) },
  { name: "Expenses", data: props.monthlyData.map(item => item.expenses) }
]);

// Chart options (always full data)
const chartOptions = computed(() => ({
  chart: { height: 290, type: "area", toolbar: false },
  dataLabels: { enabled: false },
  stroke: { curve: "smooth", width: 2 },
  xaxis: {
    categories: props.monthlyData.map(item => {
      const date = new Date(0, item.month - 1);
      return date.toLocaleString("default", { month: "short" });
    }),
  },
  yaxis: {
    labels: { formatter(value) { return "$" + value + "k"; } },
    tickAmount: 5,
    min: 0,
    max: Math.max(...props.monthlyData.map(d => Math.max(d.revenue, d.expenses))) + 20
  },
  colors: getChartColorsArray('["--vz-success", "--vz-danger"]'),
  fill: { opacity: 0.06, colors: ["#0AB39C", "#F06548"], type: "solid" },
}));
</script>

<template>
  <BCard no-body class="card-height-100">
    <!-- Header with Dropdown -->
    <BCardHeader class="align-items-center d-flex py-0">
      <BCardTitle class="mb-0 flex-grow-1">Expense and Profit Summaries</BCardTitle>
      <div class="flex-shrink-0">
        <BDropdown variant="link"
                  class="card-header-dropdown"
                  toggle-class="text-reset dropdown-btn arrow-none"
                  menu-class="dropdown-menu-end"
                  aria-haspopup="true"
                  :offset="{ alignmentAxis: -3, crossAxis: 0, mainAxis: 0 }">
          <template #button-content>
            <span class="fw-semibold text-uppercase fs-12">Sort by: </span>
            <span class="text-muted">{{ selectedFilter }}<i class="mdi mdi-chevron-down ms-1"></i></span>
          </template>
          <BDropdownItem @click="selectedFilter = 'Today'">Today</BDropdownItem>
          <BDropdownItem @click="selectedFilter = 'Last Week'">Last Week</BDropdownItem>
          <BDropdownItem @click="selectedFilter = 'Last Month'">Last Month</BDropdownItem>
          <BDropdownItem @click="selectedFilter = 'Current Year'">Current Year</BDropdownItem>
        </BDropdown>
      </div>
    </BCardHeader>

    <!-- Body with summary numbers -->
    <BCardBody class="px-0">
      <ul class="list-inline main-chart text-center mb-0">
        <li class="list-inline-item chart-border-left me-0 border-0">
          <h4 class="text-primary">
            {{ filteredSummary.revenue }}
            <span class="text-muted d-inline-block fs-13 align-middle ms-2">Revenue</span>
          </h4>
        </li>
        <li class="list-inline-item chart-border-left me-0">
          <h4>
            {{ filteredSummary.expenses }}
            <span class="text-muted d-inline-block fs-13 align-middle ms-2">Expenses</span>
          </h4>
        </li>
        <li class="list-inline-item chart-border-left me-0">
          <h4>
            {{ filteredSummary.profitRatio }}%
            <span class="text-muted d-inline-block fs-13 align-middle ms-2">Profit Ratio</span>
          </h4>
        </li>
      </ul>

      <!-- ApexChart -->
      <apexchart class="apex-charts"
                 height="290"
                 dir="ltr"
                 :series="series"
                 :options="chartOptions">
      </apexchart>
    </BCardBody>
  </BCard>
</template>

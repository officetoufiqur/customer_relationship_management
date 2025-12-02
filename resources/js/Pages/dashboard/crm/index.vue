<script setup>
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import SalesForecast from "./forecast.vue";
import DealType from "./deal-type.vue";
import BalanceOverview from "./balance-overview.vue";
import DealStatus from "./deals-status.vue";
import Tasks from "./tasks.vue";
import UpcomingActivity from "./activities.vue";
import ClosingDeals from "./closing-deals.vue";
import { ref } from "vue";
import { CountTo } from "vue3-count-to";

const props = defineProps({
  empolyes: Number,
  companys: Number,
  address: Number,
  dealWon: Number,
  dealPending: Number,
  dealLoss: Number,
  performance: Array,
  tasks: Array,
  expense: Number,
  revinue: Number,
  monthWise: Array,
  montlyRevinue: Array,
  totalAmount: Number
});

//============== count ==============
const crmWidgets = ref([
  {
    id: 1,
    label: "Total Amount",
    badge: "ri-arrow-up-circle-line text-success",
    icon: "bx bx-money",
    counter: props.totalAmount,
    decimals: 0,
    suffix: "",
    prefix: "",
  },
  {
    id: 2,
    label: "Total Employees",
    badge: "ri-arrow-up-circle-line text-success",
    icon: "las la-user-friends",
    counter: props.empolyes,
    decimals: 0,
    suffix: "",
    prefix: "",
  },
  {
    id: 3,
    label: "Total Companies",
    badge: "ri-arrow-up-circle-line text-success",
    icon: "las la-building",
    counter: props.companys,
    decimals: 0,
    suffix: "",
    prefix: "",
  },
  {
    id: 4,
    label: "Total Active Address",
    badge: "ri-arrow-down-circle-line text-danger",
    icon: "las la-location-arrow",
    counter: props.address,
    decimals: 0,
    suffix: "",
    prefix: "",
  }
]);




</script>

<template>
  <Layout>
    <PageHeader title="CRM" pageTitle="Dashboards" />

    <BRow>
      <BCol xl="12">
        <BCard no-body class="crm-widget">
          <BCardBody class="p-0">
            <BRow class="row-cols-xxl-4 row-cols-md-3 row-cols-1 g-0">
              <BCol v-for="(item, index) of crmWidgets" :key="index">
                <div class="py-4 px-3">
                  <h5 class="text-muted text-uppercase fs-13">
                    {{ item.label }}
                    <i :class="`${item.badge} fs-18 float-end align-middle`"></i>
                  </h5>
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      <i :class="`${item.icon} display-6 text-muted`"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h2 class="mb-0">
                        {{ item.prefix }}<count-to :startVal='0' :endVal='item.counter' :duration='5000'></count-to>
                        {{ item.suffix }}
                      </h2>
                    </div>
                  </div>
                </div>
              </BCol>
            </BRow>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>

    <BRow>
      <BCol xxl="5" md="6">
        <SalesForecast :dealWon="props.dealWon" :dealPending="props.dealPending" :dealLoss="props.dealLoss" />
      </BCol>
      <BCol xxl="7">
        <BalanceOverview :revinue="props.revinue" :expense="props.expense" :monthWise="props.monthWise"
          :montlyRevinue="props.montlyRevinue" />
      </BCol>
    </BRow>
    <BRow>
      <BCol xl="6">
        <DealStatus :performance="performance" />
      </BCol>
      <BCol xl="6">
        <Tasks :tasks="tasks" />
      </BCol>
    </BRow>
  </Layout>
</template>

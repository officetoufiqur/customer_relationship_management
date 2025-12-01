<script setup>
import { ref, computed, watch } from "vue";

const props = defineProps({
  performance: {
    type: Array,
    default: () => []
  }
});

const dealsStatus = ref(props.performance);

// Pagination
const currentPage = ref(1);
const perPage = ref(5);

const totalPages = computed(() => Math.ceil(dealsStatus.value.length / perPage.value));

const paginatedDeals = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  const end = start + perPage.value;
  return dealsStatus.value.slice(start, end);
});

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}

function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

// Reset page if performance array changes
watch(() => props.performance, (newVal) => {
  dealsStatus.value = newVal;
  currentPage.value = 1;
});
</script>

<template>
  <BCard no-body class="position-relative">
    <BCardHeader class="align-items-center d-flex py-3">
      <BCardTitle class="mb-0 flex-grow-1">Employee Performance Overview</BCardTitle>
    </BCardHeader>

    <BCardBody style="height: 325px; padding-bottom: 60px;">
      <div class="table-responsive table-card">
        <table class="table table-borderless table-hover table-nowrap align-middle mb-0">
          <thead class="table-light">
            <tr class="text-muted">
              <th scope="col">ID</th>
              <th scope="col" style="width: 20%">Name</th>
              <th scope="col">Email</th>
              <th scope="col" style="width: 12%">Performance</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="item in paginatedDeals" :key="item.id" class="border-bottom">
              <td>{{ item.id }}</td>
              <td>{{ item.name }}</td>
              <td>{{ item.email }}</td>
              <td>{{ item.performance }}%</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="d-flex gap-2 align-items-center p-3 position-absolute bottom-0 start-0 w-100 bg-white border-top">
        <div class="nextBtn" @click="prevPage" :disabled="currentPage === 1">
          <i class="bx bxs-chevron-left"></i>
        </div>
        <div class="nextBtn" variant="secondary" @click="nextPage" :disabled="currentPage === totalPages">
          <i class="bx bxs-chevron-right"></i>
        </div>
        <div>Page {{ currentPage }} of {{ totalPages }}</div>
      </div>
      
    </BCardBody>
  </BCard>
</template>

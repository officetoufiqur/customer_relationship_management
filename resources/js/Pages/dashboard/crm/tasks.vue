<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  tasks: {
    type: Array,
    default: [],
  },
});

const tasks = ref(props.tasks);


const currentPage = ref(1);
const perPage = ref(5);

// Computed to slice tasks for current page
const paginatedTasks = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  const end = start + perPage.value;
  return tasks.value.slice(start, end);
});

const totalPages = computed(() => Math.ceil(tasks.value.length / perPage.value));

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}

function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}
</script>

<template>
  <BCard no-body class="card-height-100 position-relative">
    <BCardHeader class="align-items-center d-flex py-3">
      <BCardTitle class="mb-0 flex-grow-1">All Tasks</BCardTitle>
    </BCardHeader>

    <BCardBody style="height: 325px;">
      <div class="table-responsive table-card">
        <table class="table table-borderless table-hover table-nowrap align-middle mb-0">
          <thead class="table-light">
            <tr class="text-muted">
              <th scope="col">#</th>
              <th scope="col">Attachment</th>
              <th scope="col">Title</th>
              <th scope="col">Description</th>
              <th scope="col">Deadline</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(item, index) of paginatedTasks" :key="index" class="border-bottom">
              <td>{{ item.id }}</td>
              <td>
                <img v-if="item.attachment" :src="item.attachment" alt="" class="avatar-xs rounded-circle me-2" />
                <p v-else class="text-muted">N/A</p>
              </td>
              <td>{{ item.title }}</td>
              <td>{{ item.description }}</td>
              <td>{{ item.deadline }}</td>
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
<style>
.nextBtn {
  background: #F3F3F9;
  cursor: pointer;
  border-radius: 2px;
}

.nextBtn i {
  font-size: 20px;
  color: #878A99;
}
</style>
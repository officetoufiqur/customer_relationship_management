<script setup>
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, computed, watch, onMounted, nextTick } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import { CountTo } from "vue3-count-to";
import Swal from "sweetalert2";
import $ from "jquery";
import { reactive } from "vue";

const props = defineProps({
    logs: Array,
});

// ========== show all data ==========
const tableHeaders = [
    { key: "id", label: "ID", sortable: true },
    { key: "company_name", label: "Company Name", sortable: true },
    { key: "recipient_name", label: "Recipient Name", sortable: true },
    { key: "payment_method", label: "Payment Method", sortable: true },
    { key: "amount", label: "Amount", sortable: true },
];

const searchQuery = ref("");

const filteredData = computed(() => {
    let data = props.logs ?? [];

    if (searchQuery.value) {
        const s = searchQuery.value.toLowerCase();

        data = data.filter((c) => {
            return (
                String(c.company_name ?? "").toLowerCase().includes(s) ||
                String(c.recipient_name ?? "").toLowerCase().includes(s) ||
                String(c.payment_method ?? "").toLowerCase().includes(s) ||
                String(c.amount ?? "").toLowerCase().includes(s) ||
                String(c.client_paid ?? "").toLowerCase().includes(s)
            );
        });
    }

    return [...data].sort((a, b) => {
        const x = String(a[sortKey.value] ?? "").toLowerCase();
        const y = String(b[sortKey.value] ?? "").toLowerCase();

        return sortOrder.value === "asc"
            ? x.localeCompare(y)
            : y.localeCompare(x);
    });
});

// ============== PAGINATION ==============
const sortKey = ref("id");
const sortOrder = ref("asc");
const page = ref(1);
const perPage = ref(5);

const pages = computed(() => {
    const total = Math.ceil(filteredData.value.length / perPage.value);
    return Array.from({ length: total }, (_, i) => i + 1);
});

const resultQuery = computed(() => {
    const start = (page.value - 1) * perPage.value;
    const end = start + perPage.value;
    return filteredData.value.slice(start, end);
});

const onSort = (key) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
    } else {
        sortKey.value = key;
        sortOrder.value = "asc";
    }
};

// Reset page when search changes
watch(searchQuery, () => {
    page.value = 1;
});


</script>

<template>
    <Layout>
        <PageHeader title="List View" pageTitle="logs" />

        <BRow>
            <BCol lg="12">
                <BCard no-body>
                    <!-- HEADER -->
                    <BCardHeader class="border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                All Financial logs
                            </h5>
                        </div>
                    </BCardHeader>

                    <!-- SEARCH / ENTRIES -->
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <BForm>
                            <BRow class="g-3 justify-content-between">
                                <BCol xxl="2" sm="12" class="d-flex align-items-center gap-2">
                                    <select v-model="perPage" class="form-select shadow-none w-auto cursor-pointer"
                                        style="background-color: #f3f6f9">
                                        <option :value="5">5</option>
                                        <option :value="10">10</option>
                                        <option :value="20">20</option>
                                    </select>
                                    <span>Entries per page</span>
                                </BCol>

                                <BCol xxl="4" sm="12">
                                    <div class="search-box">
                                        <input v-model="searchQuery" type="text"
                                            class="form-control search bg-light border" placeholder="Search..." />
                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                </BCol>
                            </BRow>
                        </BForm>
                    </BCardBody>

                    <!-- TABLE -->
                    <BCardBody>
                        <div class="table-responsive table-card mb-4">
                            <table class="table align-middle table-nowrap mb-0">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th style="width: 46px">
                                            <input class="form-check-input" type="checkbox" />
                                        </th>
                                        <th v-for="header in tableHeaders" :key="header.key" :data-sort="header.key"
                                            :style="{
                                                width: header.width || 'auto',
                                            }" class="sort" @click="
                                                header.sortable
                                                    ? onSort(header.key)
                                                    : null
                                                ">
                                            {{ header.label }}
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="(log, i) in resultQuery" :key="i">
                                        <td>
                                            <input class="form-check-input" type="checkbox" />
                                        </td>

                                        <td>{{ log.id }}</td>
                                        <td>{{ log.expense.company_name }}</td>
                                        <td>{{ log.expense.recipient_name }}</td>
                                        <td>{{ log.expense.payment_method }}</td>
                                        <td>{{ log.expense.amount }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- No results -->
                            <div v-if="resultQuery.length < 1" class="noresult text-center mt-5">
                                <h5 class="mt-2">No Results Found</h5>
                                <p class="text-muted">
                                    Try changing your search term.
                                </p>
                            </div>
                        </div>

                        <!-- PAGINATION -->
                        <div class="d-flex justify-content-end" v-if="resultQuery.length >= 1">
                            <div class="pagination-wrap hstack gap-2">
                                <BLink class="page-item pagination-prev" href="#" :disabled="page <= 1" @click="page--">
                                    Previous
                                </BLink>
                                <ul class="pagination listjs-pagination mb-0">
                                    <li :class="{
                                        active: pageNumber == page,
                                        disabled: pageNumber == '...',
                                    }" v-for="(pageNumber, index) in pages" :key="index" @click="page = pageNumber">
                                        <BLink class="page" href="#" data-i="1" data-page="8">{{ pageNumber }}</BLink>
                                    </li>
                                </ul>
                                <BLink class="page-item pagination-next" href="#" :disabled="page >= pages.length"
                                    @click="page++">
                                    Next
                                </BLink>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>

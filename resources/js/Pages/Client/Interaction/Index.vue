<script setup>
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, computed, watch, onMounted } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import { CountTo } from "vue3-count-to";
import Swal from "sweetalert2";
import $ from "jquery";

const props = defineProps({ 
    clientInteractions: Array,
    todayContact: Array
});

// ========== show all data ==========
const tableHeaders = [
    { key: "id", label: "ID", sortable: true },
    { key: "name", label: "Name", sortable: true },
    { key: "phone", label: "Phone", sortable: true },
    { key: "email", label: "Email", sortable: true },
    { key: "documents", label: "Documents", sortable: true },
    { key: "project_cost", label: "Project Cost", sortable: true },
    { key: "follow_up_date", label: "Follow Up Date", sortable: true },
    { key: "quotation_sent_status", label: "Quotation Sent Status", sortable: true }
];

const searchQuery = ref("");

const filteredData = computed(() => {
    let data = props.clientInteractions ?? [];

    // search filter
    if (searchQuery.value) {
        const s = searchQuery.value.toLowerCase();
        data = data.filter((c) => {
            return (
                (c.name || "").toLowerCase().includes(s) ||
                (c.email || "").toLowerCase().includes(s) ||
                (c.phone || "").toLowerCase().includes(s) ||
                (c.project_cost || "").toLowerCase().includes(s) ||
                (c.follow_up_date || "").toLowerCase().includes(s)
            );
        });
    }

    // sorting
    return [...data].sort((a, b) => {
        const x = (a[sortKey.value] ?? "").toString().toLowerCase();
        const y = (b[sortKey.value] ?? "").toString().toLowerCase();

        if (sortOrder.value === "asc") {
            return x > y ? 1 : x < y ? -1 : 0;
        } else {
            return x < y ? 1 : x > y ? -1 : 0;
        }
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
        <PageHeader title="Client Interaction" pageTitle="clientInteractions" />

        <BRow>
            <BCol xxl="3" sm="6">
                <BCard no-body class="card-animate">
                    <BCardBody>
                        <div class="d-flex justify-content-between mx-3">
                            <div>
                                <p class="fw-medium text-muted mb-0">
                                    Today Contacts
                                </p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    {{ todayContact }}
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span
                                        class="avatar-title bg-info-subtle text-info rounded-circle fs-4"
                                    >
                                        <i class="ri-ticket-2-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
            
        </BRow>

        <BRow>
            <BCol lg="12">
                <BCard no-body>
                    <!-- HEADER -->
                    <BCardHeader class="border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                All clientInteractions Quotation List
                            </h5>
                        </div>
                    </BCardHeader>

                    <!-- SEARCH / ENTRIES -->
                    <BCardBody
                        class="border border-dashed border-end-0 border-start-0"
                    >
                        <BForm>
                            <BRow class="g-3 justify-content-between">
                                <BCol
                                    xxl="2"
                                    sm="12"
                                    class="d-flex align-items-center gap-2"
                                >
                                    <select
                                        v-model="perPage"
                                        class="form-select shadow-none w-auto cursor-pointer"
                                        style="background-color: #f3f6f9"
                                    >
                                        <option :value="5">5</option>
                                        <option :value="10">10</option>
                                        <option :value="20">20</option>
                                    </select>
                                    <span>Entries per page</span>
                                </BCol>

                                <BCol xxl="4" sm="12">
                                    <div class="search-box">
                                        <input
                                            v-model="searchQuery"
                                            type="text"
                                            class="form-control search bg-light border"
                                            placeholder="Search..."
                                        />
                                        <i
                                            class="ri-search-line search-icon"
                                        ></i>
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
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                            />
                                        </th>
                                        <th
                                            v-for="header in tableHeaders"
                                            :key="header.key"
                                            :data-sort="header.key"
                                            :style="{
                                                width: header.width || 'auto',
                                            }"
                                            class="sort"
                                            @click="
                                                header.sortable
                                                    ? onSort(header.key)
                                                    : null
                                            "
                                        >
                                            {{ header.label }}
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr
                                        v-for="(client, i) in resultQuery"
                                        :key="i"
                                    >
                                        <td>
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                            />
                                        </td>

                                        <td>{{ client.id }}</td>
                                        <td>{{ client.client.name }}</td>
                                        <td>{{ client.client.phone }}</td>
                                        <td>{{ client.client.email }}</td>
                                        <td>
                                            <img :src="client.documents" alt="" class="avatar-sm">
                                        </td>
                                        <td>{{ client.client.project_cost }}</td>
                                        <td>{{ client.follow_up_date }}</td>
                                        <td>
                                            <span v-if="client.quotation_sent_status == 1" class="badge bg-success-subtle text-success">Sent</span>
                                            <span v-else class="badge bg-danger-subtle text-danger">Not Sent</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- No results -->
                            <div
                                v-if="resultQuery.length < 1"
                                class="noresult text-center mt-5"
                            >
                                <h5 class="mt-2">No Results Found</h5>
                                <p class="text-muted">
                                    Try changing your search term.
                                </p>
                            </div>
                        </div>

                        <!-- PAGINATION -->
                        <div
                            class="d-flex justify-content-end"
                            v-if="resultQuery.length >= 1"
                        >
                            <div class="pagination-wrap hstack gap-2">
                                <BLink
                                    class="page-item pagination-prev"
                                    href="#"
                                    :disabled="page <= 1"
                                    @click="page--"
                                >
                                    Previous
                                </BLink>
                                <ul class="pagination listjs-pagination mb-0">
                                    <li
                                        :class="{
                                            active: pageNumber == page,
                                            disabled: pageNumber == '...',
                                        }"
                                        v-for="(pageNumber, index) in pages"
                                        :key="index"
                                        @click="page = pageNumber"
                                    >
                                        <BLink
                                            class="page"
                                            href="#"
                                            data-i="1"
                                            data-page="8"
                                            >{{ pageNumber }}</BLink
                                        >
                                    </li>
                                </ul>
                                <BLink
                                    class="page-item pagination-next"
                                    href="#"
                                    :disabled="page >= pages.length"
                                    @click="page++"
                                >
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
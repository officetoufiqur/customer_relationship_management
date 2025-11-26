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
    expenses: Array,
    companys: Array,
    allExpenses: Number,
    approvedExpenses: Number,
    rejectedExpenses: Number,
    pendingExpenses: Number,
});

// ========== base form ==========
const baseForm = ref({
    id: "",
    company_id: "",
    amount: "",
    recipient_name: "",
    payment_method: "",
    client_paid: "",
    reason: "",
    status: "",
});

const viewForm = useForm({ ...baseForm.value });

// ========== show all data ==========
const tableHeaders = [
    { key: "id", label: "ID", sortable: true },
    { key: "company_name", label: "Company Name", sortable: true },
    { key: "recipient_name", label: "Recipient Name", sortable: true },
    { key: "payment_method", label: "Payment Method", sortable: true },
    { key: "amount", label: "Amount", sortable: true },
    { key: "client_paid", label: "Client Paid", sortable: true },
    { key: "status", label: "Status", sortable: true },
    { key: "actions", label: "Actions", sortable: false },
];

const searchQuery = ref("");

const filteredData = computed(() => {
    let data = props.expenses ?? [];

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



// ============== view ==============
const toggleViewModal = ref(false);

const viewModal = (expense) => {
    toggleViewModal.value = true;
    viewForm.id = expense.id;
    viewForm.company_name = expense.company_name;
    viewForm.recipient_name = expense.recipient_name;
    viewForm.payment_method = expense.payment_method;
    viewForm.amount = expense.amount;
    viewForm.client_paid = expense.client_paid;
    viewForm.reason = expense.reason;
    viewForm.status = expense.status;
};

const handleStatusSubmit = () => {
    viewForm.post(`/expense/status/update/${viewForm.id}`, {
        onSuccess: () => {
            Swal.fire("Updated!", "Expense status updated successfully", "success");
            toggleViewModal.value = false;
            viewForm.reset();
        },
    });
};

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

const filters = reactive({
    month: '',
    status: 'all',
});

watch(filters, (val) => {
    router.get('/expense/request', val, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}, { deep: true });


</script>

<template>
    <Layout>
        <PageHeader title="List View" pageTitle="Expenses Request" />

        <div class="mb-3">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <h6 class="mb-0 text-muted fw-semibold">Filter by Month</h6>

                <input type="month" v-model="filters.month" class="form-control form-control-sm cursor-pointer"
                    style="max-width: 234px;">
            </div>
        </div>

        <BRow>
            <BCol xxl="3" sm="6">
                <BCard no-body class="card-animate">
                    <BCardBody>
                        <div class="d-flex justify-content-between mx-3">
                            <div>
                                <p class="fw-medium text-muted mb-0">
                                    All Expenses
                                </p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    <count-to :startVal="0" :endVal="allExpenses"></count-to>
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle text-info rounded-circle fs-4">
                                        <i class="ri-ticket-2-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
            <BCol xxl="3" sm="6">
                <BCard no-body class="card-animate">
                    <BCardBody>
                        <div class="d-flex justify-content-between mx-3">
                            <div>
                                <p class="fw-medium text-muted mb-0">
                                    Approved Expenses
                                </p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    <count-to :startVal="0" :endVal="approvedExpenses"></count-to>
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-4">
                                        <i class="mdi mdi-timer-sand"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
            <BCol xxl="3" sm="6">
                <BCard no-body class="card-animate">
                    <BCardBody>
                        <div class="d-flex justify-content-between mx-3">
                            <div>
                                <p class="fw-medium text-muted mb-0">
                                    Rejected Expenses
                                </p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    <count-to :startVal="0" :endVal="rejectedExpenses"></count-to>
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle text-success rounded-circle fs-4">
                                        <i class="ri-checkbox-circle-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
            <BCol xxl="3" sm="6">
                <BCard no-body class="card-animate">
                    <BCardBody>
                        <div class="d-flex justify-content-between mx-3">
                            <div>
                                <p class="fw-medium text-muted mb-0">
                                    Pending Expenses
                                </p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    <count-to :startVal="0" :endVal="pendingExpenses"></count-to>
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle text-success rounded-circle fs-4">
                                        <i class="ri-checkbox-circle-line"></i>
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
                                All Expense Requests
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
                                    <div class="d-flex align-items-center justify-content-between gap-2">

                                        <!-- SEARCH BOX -->
                                        <div class="flex-grow-1 position-relative">
                                            <input v-model="searchQuery" type="text"
                                                class="form-control bg-light border ps-5" placeholder="Search..."
                                                style="height: 40px;" />
                                            <i class="ri-search-line position-absolute top-50 translate-middle-y"
                                                style="left: 15px; font-size: 16px; color: #6c757d;"></i>
                                        </div>

                                        <!-- PER PAGE DROPDOWN -->
                                        <select v-model="filters.status" class="form-select shadow-none cursor-pointer"
                                            style="background-color: #f3f6f9; width: 115px; height: 40px;">
                                            <option value="all">All</option>
                                            <option value="pending">Pending</option>
                                            <option value="approved">Approved</option>
                                            <option value="rejected">Rejected</option>
                                        </select>

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
                                    <tr v-for="(expense, i) in resultQuery" :key="i">
                                        <td>
                                            <input class="form-check-input" type="checkbox" />
                                        </td>

                                        <td>{{ expense.id }}</td>
                                        <td>{{ expense.company_name }}</td>
                                        <td>{{ expense.recipient_name }}</td>
                                        <td>{{ expense.payment_method }}</td>
                                        <td>{{ expense.amount }}</td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success"
                                                v-if="expense.client_paid">Paid</span>
                                            <span class="badge bg-danger-subtle text-danger" v-else>Not Paid</span>
                                        </td>

                                        <td>
                                            <span class="badge bg-info-subtle text-info"
                                                v-if="expense.status == 'pending'">Pending</span>
                                            <span class="badge bg-success-subtle text-success"
                                                v-if="expense.status == 'approved'">Approved</span>
                                            <span class="badge bg-danger-subtle text-danger"
                                                v-if="expense.status == 'rejected'">Rejected</span>
                                        </td>

                                        <td class="d-flex gap-2">
                                            <BButton variant="info px-3" @click="viewModal(expense)">
                                                View
                                            </BButton>
                                        </td>
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

        <!-- expense view modal -->
        <BModal v-model="toggleViewModal" id="showmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle expenseModal" class="v-modal-custom" centered size="lg"
            :title="'View Expense Information'">
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="">
                    <!-- company Name -->
                    <BCol lg="6">
                        <label class="form-label">Company Name</label>
                        <p class="form-control">{{ viewForm.company_name }}</p>
                    </BCol>

                    <!-- amount -->
                    <BCol lg="6">
                        <label class="form-label">Amount</label>
                        <p class="form-control">{{ viewForm.amount }}</p>
                    </BCol>

                    <!-- recipient Name -->
                    <BCol lg="6">
                        <label class="form-label">Recipient Name</label>
                        <p class="form-control">{{ viewForm.recipient_name }}</p>
                    </BCol>

                    <!-- payment_method -->
                    <BCol lg="6">
                        <label class="form-label">Payment Method</label>
                        <p class="form-control">{{ viewForm.payment_method }}</p>
                    </BCol>

                    <!-- Reason -->
                    <BCol lg="12">
                        <label class="form-label">Reason (Optional)</label>
                        <p class="form-control">{{ viewForm.reason }}</p>
                    </BCol>

                    <!-- client_paid -->
                    <BCol lg="6">
                        <label class="form-label">Client Paid</label>
                        <p class="form-control">
                            <span v-if="viewForm.client_paid">Paid</span>
                            <span v-else>Not Paid</span>
                        </p>
                    </BCol>
                    <BCol lg="6">
                        <label class="form-label">Status</label>
                        <select class="form-control" v-model="viewForm.status" :class="{
                            'is-invalid': submitted && !viewForm.status,
                        }">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter Status.
                        </div>
                    </BCol>

                    <div class="hstack gap-2 justify-content-end mt-3">
                        <BButton type="button" variant="light" @click="toggleViewModal = false">
                            Close
                        </BButton>

                        <BButton type="submit" variant="success" @click="handleStatusSubmit">
                            Update Request
                        </BButton>
                    </div>
                </BRow>
            </BFrom>
        </BModal>
    </Layout>
</template>

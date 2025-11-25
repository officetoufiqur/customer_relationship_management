<script setup>
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, computed, watch, onMounted, nextTick } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import { CountTo } from "vue3-count-to";
import Swal from "sweetalert2";
import $ from "jquery";
import { Inertia } from "@inertiajs/inertia";
import { reactive } from "vue";

const props = defineProps({
    address: Array,
    clients: Array,
    yearlyReports: Number,
    monthlyReports: Number,
    quarterlyReports: Number
});

// ========== base form ==========
const baseForm = ref({
    id: "",
    client_id: "",
    contact_type: "",
    start_date: "",
    end_date: "",
    payment_status: "",
    status: "",
    contact_value: "",
    business_center_cost: "",
    net_profit: "",
});

const form = useForm({ ...baseForm.value });
const editForm = useForm({ ...baseForm.value });

// ========== show all data ==========
const tableHeaders = [
    { key: "id", label: "ID", sortable: true },
    { key: "name", label: "Name", sortable: true },
    { key: "contact_type", label: "Contact Type", sortable: true },
    { key: "start_date", label: "Start Date", sortable: true },
    { key: "end_date", label: "End Date", sortable: true },
    { key: "payment_status", label: "Payment Status", sortable: true },
    { key: "status", label: "Status", sortable: true },
    { key: "contact_value", label: "Contact Value", sortable: true },
    { key: "net_profit", label: "Net Profit", sortable: true },
    { key: "actions", label: "Actions", sortable: false },
];

const searchQuery = ref("");

const filteredData = computed(() => {
    let data = props.address ?? [];

    // search filter
    if (searchQuery.value) {
        const s = searchQuery.value.toLowerCase();
        data = data.filter((c) => {
            return (
                (c.name || "").toLowerCase().includes(s) ||
                (c.contact_type || "").toLowerCase().includes(s) ||
                (c.payment_status || "").toLowerCase().includes(s) ||
                (c.contact_value || "").toLowerCase().includes(s) ||
                (c.start_date || "").toLowerCase().includes(s)
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

// ============== Create ==============
const toggleCreateModal = ref(false);

const addresCreateModal = () => {
    toggleCreateModal.value = true;
};

const handleSubmit = async () => {
    if (!form.client_id || !form.contact_type || !form.start_date || !form.end_date || !form.payment_status || !form.contact_value || !form.business_center_cost) {
        Swal.fire(
            "Validation Error",
            "Please fill all required fields",
            "warning"
        );
        return;
    }

    form.post("/commercial/address/store", {
        onSuccess: () => {
            Swal.fire("Created!", "Commercial Address added successfully", "success");
            toggleCreateModal.value = false;
            form.reset();
        },
        onError: (errors) => {
            console.error(errors);
            Swal.fire("Error", "Creation failed", "error");
        },
    });
};

// ============== Edit modal ==============
const toggleEditModal = ref(false);

const editModal = (addres) => {
    toggleEditModal.value = true;

    editForm.id = addres.id;
    editForm.name = addres.name;
    editForm.contact_type = addres.contact_type;
    editForm.start_date = addres.start_date;
    editForm.end_date = addres.end_date;
    editForm.payment_status = addres.payment_status;
    editForm.status = addres.status;
    editForm.contact_value = addres.contact_value;
    editForm.business_center_cost = addres.business_center_cost;
    editForm.net_profit = addres.net_profit;
};

const handleUpdateSubmit = () => {
    editForm.post(`/commercial/address/update/${editForm.id}`, {
        onSuccess: () => {
            Swal.fire("Updated!", "Commercial address updated successfully", "success");
            toggleEditModal.value = false;
            editForm.reset();
        },
    });
};

// ============== delete ==============
const deleteData = (addres) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(`/commercial/address/destroy/${addres.id}`, {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "Task deleted successfully",
                        "success"
                    );
                },
                onError: (error) => {
                    console.error(error);
                    Swal.fire("Error", "Something went wrong", "error");
                },
            });
        }
    });
};

// ============== view ==============
const toggleViewModal = ref(false);

const viewModal = (addres) => {
    toggleViewModal.value = true;
    editForm.id = addres.id;
    editForm.name = addres.name;
    editForm.contact_type = addres.contact_type;
    editForm.start_date = addres.start_date;
    editForm.end_date = addres.end_date;
    editForm.payment_status = addres.payment_status;
    editForm.status = addres.status;
    editForm.contact_value = addres.contact_value;
    editForm.net_profit = addres.net_profit;
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


// ============== RANGE monthly ==============
const range = reactive({
    start_month: '',
    end_month: '',
    total_profit: 0
});

const fetchRangeReport = async () => {
    if (!range.start_month || !range.end_month) return;
    try {
        const response = await axios.get('/commercial/address/range', {
            params: {
                start_month: range.start_month,
                end_month: range.end_month
            }
        });
        range.total_profit = response.data.total_profit;
    } catch (error) {
        console.error('Error fetching range report:', error);
    }
};

watch(
    () => [range.start_month, range.end_month],
    () => {
        fetchRangeReport();
    }
);



</script>

<template>
    <Layout>
        <PageHeader title="List View" pageTitle="Commercial Address" />

        <BRow>
            <BCol xxl="3" sm="6">
                <BCard no-body class="card-animate">
                    <BCardBody>
                        <div class="d-flex justify-content-between mx-3">
                            <div>
                                <p class="fw-medium text-muted mb-0">
                                    Current Month Profit Reports
                                </p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    <count-to :startVal="0" :endVal="monthlyReports.total_profit"></count-to>
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle text-info rounded-circle fs-4">
                                        <i class="bx bx-calendar-week"></i>
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
                                    Quarterly Profit Reports
                                </p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    <count-to :startVal="0" :endVal="quarterlyReports"></count-to>
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-4">
                                        <i class="bx bxs-pie-chart-alt"></i>
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
                                    Yearly Profit Reports
                                </p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    <count-to :startVal="0" :endVal="yearlyReports"></count-to>
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle text-success rounded-circle fs-2">
                                        <i class=" bx bxs-timer"></i>
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
                        <div class="mx-3">
                            <div>
                                <span class="fw-medium text-muted">Monthly Filter</span>
                                <div class="d-flex gap-2 mt-1">
                                    <input type="month" v-model="range.start_month"
                                        class="form-control form-control-sm" />
                                    <input type="month" v-model="range.end_month"
                                        class="form-control form-control-sm" />
                                </div>
                                <h2 class="mt-1 mb-0 ff-secondary fw-semibold">
                                    <count-to :startVal="0" :endVal="range.total_profit"></count-to>
                                </h2>
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
                                All Companies
                            </h5>
                            <div class="d-flex gap-2">
                                <BButton variant="danger" @click="addresCreateModal">
                                    <i class="ri-add-line me-1"></i>
                                    Create addres
                                </BButton>
                            </div>
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
                                    <tr v-for="(addres, i) in resultQuery" :key="i">
                                        <td>
                                            <input class="form-check-input" type="checkbox" />
                                        </td>

                                        <td>{{ addres.id }}</td>
                                        <td>{{ addres.name }}</td>
                                        <td>{{ addres.contact_type }}</td>
                                        <td>{{ addres.start_date }}</td>
                                        <td>
                                            {{ addres.end_date }}
                                        </td>
                                        <td>
                                            <span class="badge bg-success"
                                                v-if="addres.payment_status == 'paid'">Paid</span>
                                            <span v-else-if="addres.payment_status == 'unpaid'"
                                                class="badge bg-warning">
                                                Unpaid
                                            </span>
                                            <span class="badge bg-danger" v-else>Overdue</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success" v-if="addres.status == 1">Active</span>
                                            <span class="badge bg-danger" v-else>Inactive</span>
                                        </td>
                                        <td>{{ addres.contact_value }}</td>
                                        <td>{{ addres.net_profit }}</td>
                                        <td class="d-flex gap-2">
                                            <BButton variant="success px-3" @click="editModal(addres)">
                                                Edit
                                            </BButton>

                                            <BButton variant="danger px-3" @click="deleteData(addres)">
                                                Delete
                                            </BButton>

                                            <BButton variant="info px-3" @click="viewModal(addres)">
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

        <!-- addres create modal -->
        <BModal v-model="toggleCreateModal" id="showmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle addresModal" class="v-modal-custom" centered size="lg"
            :title="'Add Address'">
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- Client Name -->
                    <BCol lg="6">
                        <label class="form-label">Client Name</label>
                        <select class="form-control" v-model="form.client_id" :class="{
                            'is-invalid': submitted && !form.client_id,
                        }">
                            <option value="">Select Client Name</option>
                            <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}
                            </option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter Client Name.
                        </div>
                    </BCol>

                    <!-- Contact Type -->
                    <BCol lg="6">
                        <label class="form-label">Contact Type</label>
                        <select class="form-control" v-model="form.contact_type" :class="{
                            'is-invalid': submitted && !form.contact_type,
                        }">
                            <option value="">Select Contact Type</option>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter Contact Type.
                        </div>
                    </BCol>

                    <!-- Start Date -->
                    <BCol lg="6">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" placeholder="Start date" v-model="form.start_date"
                            :class="{
                                'is-invalid': submitted && !form.start_date,
                            }" />
                        <div class="invalid-feedback">Please enter start date.</div>
                    </BCol>

                    <!-- End Date -->
                    <BCol lg="6">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" placeholder="End date" v-model="form.end_date" :class="{
                            'is-invalid': submitted && !form.end_date,
                        }" />
                        <div class="invalid-feedback">Please enter end date.</div>
                    </BCol>

                    <!-- Payment Status -->
                    <BCol lg="6">
                        <label class="form-label">Payment Status</label>
                        <select class="form-control" v-model="form.payment_status" :class="{
                            'is-invalid': submitted && !form.payment_status,
                        }">
                            <option value="">Select Payment Status</option>
                            <option value="paid">Paid</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="overdue">Overdue</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter Payment Status.
                        </div>
                    </BCol>

                    <!-- contact_value -->
                    <BCol lg="6">
                        <label class="form-label">Contact Value</label>
                        <input type="number" class="form-control" placeholder="Contact value"
                            v-model="form.contact_value" :class="{
                                'is-invalid': submitted && !form.contact_value,
                            }" />
                        <div class="invalid-feedback">Please enter contact value.</div>
                    </BCol>

                    <!-- business_center_cost -->
                    <BCol lg="6">
                        <label class="form-label">Business Center Cost</label>
                        <input type="number" class="form-control" v-model="form.business_center_cost"
                            placeholder="Enter business center cost" :class="{
                                'is-invalid': submitted && !form.business_center_cost,
                            }" />
                        <div class="invalid-feedback">
                            Please select business center cost.
                        </div>
                    </BCol>
                </BRow>

                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton type="button" variant="light" @click="toggleCreateModal = false">
                        Close
                    </BButton>

                    <BButton type="submit" variant="success" @click="handleSubmit">
                        Add Address
                    </BButton>
                </div>
            </BFrom>
        </BModal>

        <!-- addres Edit modal -->
        <BModal v-model="toggleEditModal" id="showmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle addresModal" class="v-modal-custom" centered size="lg"
            :title="'Edit Commercial Address'">
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- Client Name -->
                    <BCol lg="6">
                        <label class="form-label">Client Name</label>
                        <select class="form-control" v-model="editForm.name" :class="{
                            'is-invalid': submitted && !editForm.name,
                        }">
                            <option value="">Select Client Name</option>
                            <option v-for="client in clients" :key="client.id" :value="client.name">{{ client.name }}
                            </option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter Client Name.
                        </div>
                    </BCol>

                    <!-- Contact Type -->
                    <BCol lg="6">
                        <label class="form-label">Contact Type</label>
                        <select class="form-control" v-model="editForm.contact_type" :class="{
                            'is-invalid': submitted && !editForm.contact_type,
                        }">
                            <option value="">Select Contact Type</option>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter Contact Type.
                        </div>
                    </BCol>

                    <!-- Start Date -->
                    <BCol lg="6">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" placeholder="Start date" v-model="editForm.start_date"
                            :class="{
                                'is-invalid': submitted && !editForm.start_date,
                            }" />
                        <div class="invalid-feedback">Please enter start date.</div>
                    </BCol>

                    <!-- End Date -->
                    <BCol lg="6">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" placeholder="End date" v-model="editForm.end_date"
                            :class="{
                                'is-invalid': submitted && !editForm.end_date,
                            }" />
                        <div class="invalid-feedback">Please enter end date.</div>
                    </BCol>

                    <!-- Payment Status -->
                    <BCol lg="6">
                        <label class="form-label">Payment Status</label>
                        <select class="form-control" v-model="editForm.payment_status" :class="{
                            'is-invalid': submitted && !editForm.payment_status,
                        }">
                            <option value="">Select Payment Status</option>
                            <option value="paid">Paid</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="overdue">Overdue</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter Payment Status.
                        </div>
                    </BCol>

                    <!-- contact_value -->
                    <BCol lg="6">
                        <label class="form-label">Contact Value</label>
                        <input type="contact_value" class="form-control" placeholder="Contact value"
                            v-model="editForm.contact_value" :class="{
                                'is-invalid': submitted && !editForm.contact_value,
                            }" />
                        <div class="invalid-feedback">Please enter contact value.</div>
                    </BCol>

                    <!-- business_center_cost -->
                    <BCol lg="6">
                        <label class="form-label">Business Center Cost</label>
                        <input type="text" class="form-control" v-model="editForm.business_center_cost"
                            placeholder="Enter business center cost" :class="{
                                'is-invalid': submitted && !editForm.business_center_cost,
                            }" />
                        <div class="invalid-feedback">
                            Please select business center cost.
                        </div>
                    </BCol>

                    <!-- Status -->
                    <BCol lg="6">
                        <label class="form-label">Status</label>
                        <select class="form-control" v-model="editForm.status" :class="{
                            'is-invalid': submitted && !editForm.status,
                        }">
                            <option value="">Select Status</option>
                            <option value="0">Inactive</option>
                            <option value="1">Active</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter Status.
                        </div>
                    </BCol>
                </BRow>

                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton type="button" variant="light" @click="toggleEditModal = false">
                        Close
                    </BButton>

                    <BButton type="submit" variant="success" @click="handleUpdateSubmit">
                        Update Address
                    </BButton>
                </div>
            </BFrom>
        </BModal>

        <!-- addres View modal -->
        <BModal v-model="toggleViewModal" id="showmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle addresModal" class="v-modal-custom" centered size="lg"
            :title="'View Commercial Address'">
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- Client Name -->
                    <BCol lg="6">
                        <label class="form-label">Client Name</label>
                        <p class="form-control">{{ editForm.name }}</p>
                    </BCol>

                    <!-- Contact Type -->
                    <BCol lg="6">
                        <label class="form-label">Contact Type</label>
                        <p class="form-control">{{ editForm.contact_type }}</p>
                    </BCol>

                    <!-- Start Date -->
                    <BCol lg="6">
                        <label class="form-label">Start Date</label>
                        <p class="form-control">{{ editForm.start_date }}</p>
                    </BCol>

                    <!-- End Date -->
                    <BCol lg="6">
                        <label class="form-label">End Date</label>
                        <p class="form-control">{{ editForm.end_date }}</p>
                    </BCol>

                    <!-- Payment Status -->
                    <BCol lg="6">
                        <label class="form-label">Payment Status</label>
                        <p class="form-control">{{ editForm.payment_status }}</p>
                    </BCol>

                    <!-- contact_value -->
                    <BCol lg="6">
                        <label class="form-label">Contact Value</label>
                        <p class="form-control">{{ editForm.contact_value }}</p>
                    </BCol>

                    <!-- business_center_cost -->
                    <BCol lg="6">
                        <label class="form-label">Business Center Cost</label>
                        <p class="form-control">{{ editForm.business_center_cost }}</p>
                    </BCol>

                    <!-- Status -->
                    <BCol lg="6">
                        <label class="form-label">Status</label>
                        <p v-if="editForm.status == 1" class="form-control">Active</p>
                        <p v-else class="form-control">Inactive</p>
                    </BCol>

                    <!-- Net Profit -->
                    <BCol lg="6">
                        <label class="form-label">Net Profit</label>
                        <p class="form-control">{{ editForm.net_profit }}</p>
                    </BCol>
                </BRow>

                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton type="button" variant="light" @click="toggleViewModal = false">
                        Close
                    </BButton>
                </div>
            </BFrom>
        </BModal>


    </Layout>
</template>

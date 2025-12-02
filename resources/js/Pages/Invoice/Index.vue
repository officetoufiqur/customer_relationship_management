<script setup>
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, computed, watch, onMounted, nextTick } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import { CountTo } from "vue3-count-to";
import Swal from "sweetalert2";
import $ from "jquery";
import { reactive } from "vue";
import { can } from "@/helpers/can";
import axios from "axios";

const props = defineProps({
    invoices: Array,
    clients: Array,
});

// ========== base form ==========
const baseForm = ref({
    id: "",
    client_id: "",
    invoices_number: "",
    service_name: "",
    service_description: "",
    quantity: "",
    unit_price: "",
    tax_value: "",
    status: "",
});

const form = useForm({ ...baseForm.value });
const editForm = useForm({ ...baseForm.value });

// ========== show all data ==========
const tableHeaders = [
    { key: "id", label: "ID", sortable: true },
    { key: "invoice_number", label: "Invoices Number", sortable: true },
    { key: "service_name", label: "Service Name", sortable: true },
    { key: "service_description", label: "Service Description", sortable: true },
    { key: "quantity", label: "Quantity", sortable: true },
    { key: "unit_price", label: "Unit Price", sortable: true },
    { key: "tax_value", label: "Tax Value", sortable: true },
    { key: "status", label: "Status", sortable: true },
    { key: "actions", label: "Actions", sortable: false },
];

const searchQuery = ref("");

const filteredData = computed(() => {
    let data = props.invoices ?? [];

    if (searchQuery.value) {
        const s = searchQuery.value.toLowerCase();

        data = data.filter((c) => {
            return (
                String(c.invoices_number ?? "").toLowerCase().includes(s) ||
                String(c.service_name ?? "").toLowerCase().includes(s) ||
                String(c.service_description ?? "").toLowerCase().includes(s) ||
                String(c.quantity ?? "").toLowerCase().includes(s) ||
                String(c.unit_price ?? "").toLowerCase().includes(s) ||
                String(c.tax_value ?? "").toLowerCase().includes(s)
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


// ============== Create ==============
const toggleCreateModal = ref(false);

const invoicesCreateModal = () => {
    toggleCreateModal.value = true;
};

const handleSubmit = async () => {
    if (!form.client_id || !form.service_name || !form.service_description || !form.quantity || !form.unit_price || !form.tax_value) {
        Swal.fire(
            "Validation Error",
            "Please fill all required fields",
            "warning"
        );
        return;
    }

    form.post("/invoices/store", {
        onSuccess: () => {
            Swal.fire("Created!", "Invoices added successfully", "success");
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

const editModal = (invoices) => {
    toggleEditModal.value = true;

    editForm.id = invoices.id;
    editForm.client_id = invoices.client_id;
    editForm.invoices_number = invoices.invoices_number;
    editForm.service_name = invoices.service_name;
    editForm.service_description = invoices.service_description;
    editForm.quantity = invoices.quantity;
    editForm.unit_price = invoices.unit_price;
    editForm.tax_value = invoices.tax_value;
};

const handleUpdateSubmit = () => {
    editForm.post(`/invoices/update/${editForm.id}`, {
        onSuccess: () => {
            Swal.fire("Updated!", "Invoices updated successfully", "success");
            toggleEditModal.value = false;
            editForm.reset();
        },
    });
};

// ============== Invoice payment ==============
const togglePayModal = ref(false);

const pay = useForm({
    id: "",
    payment_type: "",
    partial_amount: "",
    total: "",
    payment_method: "",
});

const payModal = (invoices) => {
    togglePayModal.value = true;
    pay.id = invoices.id;
    pay.total = invoices.total;
    pay.partial_amount = invoices.remaining_balance;
};


const handlePaymentSubmit = () => {
    if (!pay.payment_type || !pay.payment_method) {
        Swal.fire(
            "Validation Error",
            "Please fill all required fields",
            "warning"
        );
        return;
    }
    pay.post(`/invoices/payment/${pay.id}`, {
        onSuccess: () => {
            Swal.fire("Success!", "Payment successfully", "success");
            togglePayModal.value = false;
            pay.reset();
        },
        onError: (errors) => {
            console.error(errors);
            Swal.fire("Error", errors.invoice, "error");
        },
    });
}


// ============== Invoice download ==============
const invoiceDownload = (invoices) => {
    axios({
        url: `/invoice/download/${invoices.id}`,
        method: "GET",
        responseType: "blob"
    }).then((res) => {
        const fileURL = window.URL.createObjectURL(new Blob([res.data]));
        const fileLink = document.createElement("a");

        fileLink.href = fileURL;
        fileLink.setAttribute("download", `invoice_${invoices.id}.pdf`);
        document.body.appendChild(fileLink);

        fileLink.click();
        Swal.fire("Download!", "Invoice downloaded successfully", "success");
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


</script>

<template>
    <Layout>
        <PageHeader title="List View" pageTitle="Invoices" />

        <BRow>
            <BCol lg="12">
                <BCard no-body>
                    <!-- HEADER -->
                    <BCardHeader class="border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                All invoices
                            </h5>
                            <div class="d-flex gap-2">
                                <BButton v-if="can('invoices_create')" variant="danger" @click="invoicesCreateModal">
                                    <i class="ri-add-line me-1"></i>
                                    Create Invoices
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
                                    <tr v-for="(invoices, i) in resultQuery" :key="i">
                                        <td>
                                            <input class="form-check-input" type="checkbox" />
                                        </td>

                                        <td>{{ invoices.id }}</td>
                                        <!-- <td>{{ invoices.client.name }}</td> -->
                                        <td>{{ invoices.invoice_number }}</td>
                                        <td>{{ invoices.service_name }}</td>
                                        <td>{{ invoices.service_description }}</td>
                                        <td>{{ invoices.quantity }}</td>
                                        <td>{{ invoices.unit_price }}</td>
                                        <td>{{ invoices.tax_value }}</td>
                                        <td>
                                        <td>
                                            <span v-if="invoices.status === 'draft'"
                                                class="badge bg-secondary-subtle text-secondary">
                                                Draft
                                            </span>

                                            <span v-else-if="invoices.status === 'sent'"
                                                class="badge bg-info-subtle text-info">
                                                Sent
                                            </span>

                                            <span v-else-if="invoices.status === 'partial'"
                                                class="badge bg-warning-subtle text-warning">
                                                Partial
                                            </span>

                                            <span v-else-if="invoices.status === 'paid'"
                                                class="badge bg-success-subtle text-success">
                                                Paid
                                            </span>

                                            <span v-else-if="invoices.status === 'cancelled'"
                                                class="badge bg-danger-subtle text-danger">
                                                Cancelled
                                            </span>
                                        </td>
                                        </td>
                                        <td class="d-flex gap-2">
                                            <BButton v-if="can('invoices_edit')" variant="warning px-3"
                                                @click="editModal(invoices)">
                                                Edit
                                            </BButton>

                                            <BButton v-if="can('invoices_pay')" variant="info px-3"
                                                @click="payModal(invoices)">
                                                Pay
                                            </BButton>
                                            <BButton variant="success px-3" @click="invoiceDownload(invoices)">
                                                Download
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

        <!-- invoices create modal -->
        <BModal v-model="toggleCreateModal" id="showmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle invoicesModal" class="v-modal-custom" centered size="lg"
            :title="'Add invoices'">
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- Client -->
                    <BCol lg="6">
                        <label class="form-label">Client</label>
                        <select class="form-control" v-model="form.client_id"
                            :class="{ 'is-invalid': submitted && !form.client_id }">
                            <option value="">Select Client</option>
                            <option v-for="client in clients" :key="client.id" :value="client.id">
                                {{ client.name }}
                            </option>
                        </select>
                        <div class="invalid-feedback">Please select a client.</div>
                    </BCol>

                    <!-- Service Name -->
                    <BCol lg="6">
                        <label class="form-label">Service Name</label>
                        <input type="text" class="form-control" placeholder="Enter Service Name"
                            v-model="form.service_name" :class="{ 'is-invalid': submitted && !form.service_name }" />
                        <div class="invalid-feedback">Please enter service name.</div>
                    </BCol>

                    <!-- Quantity -->
                    <BCol lg="6">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" placeholder="Enter Quantity" v-model="form.quantity"
                            :class="{ 'is-invalid': submitted && !form.quantity }" />
                        <div class="invalid-feedback">Please enter quantity.</div>
                    </BCol>

                    <!-- Unit Price -->
                    <BCol lg="6">
                        <label class="form-label">Unit Price</label>
                        <input type="number" class="form-control" placeholder="Enter Unit Price"
                            v-model="form.unit_price" :class="{ 'is-invalid': submitted && !form.unit_price }" />
                        <div class="invalid-feedback">Please enter unit price.</div>
                    </BCol>

                    <!-- tax_value -->
                    <BCol lg="6">
                        <label class="form-label">tax_value</label>
                        <input type="number" class="form-control" placeholder="Enter tax_value"
                            v-model="form.tax_value" />
                    </BCol>
                    <!-- Service Description -->
                    <BCol lg="12">
                        <label class="form-label">Service Description</label>
                        <textarea class="form-control" placeholder="Enter Description"
                            v-model="form.service_description"></textarea>
                    </BCol>
                </BRow>

                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton type="button" variant="light" @click="toggleCreateModal = false">
                        Close
                    </BButton>

                    <BButton type="submit" variant="success" @click="handleSubmit">
                        Invoices Request
                    </BButton>
                </div>
            </BFrom>
        </BModal>

        <!-- invoices edit modal -->
        <BModal v-model="toggleEditModal" id="showmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle invoicesModal" class="v-modal-custom" centered size="lg"
            :title="'Edit Invoices'">
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- Client -->
                    <BCol lg="6">
                        <label class="form-label">Client</label>
                        <select class="form-control" v-model="editForm.client_id"
                            :class="{ 'is-invalid': submitted && !editForm.client_id }">
                            <option value="">Select Client</option>
                            <option v-for="client in clients" :key="client.id" :value="client.id">
                                {{ client.name }}
                            </option>
                        </select>
                        <div class="invalid-feedback">Please select a client.</div>
                    </BCol>

                    <!-- Service Name -->
                    <BCol lg="6">
                        <label class="form-label">Service Name</label>
                        <input type="text" class="form-control" placeholder="Enter Service Name"
                            v-model="editForm.service_name"
                            :class="{ 'is-invalid': submitted && !editForm.service_name }" />
                        <div class="invalid-feedback">Please enter service name.</div>
                    </BCol>

                    <!-- Quantity -->
                    <BCol lg="6">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" placeholder="Enter Quantity"
                            v-model="editForm.quantity" :class="{ 'is-invalid': submitted && !editForm.quantity }" />
                        <div class="invalid-feedback">Please enter quantity.</div>
                    </BCol>

                    <!-- Unit Price -->
                    <BCol lg="6">
                        <label class="form-label">Unit Price</label>
                        <input type="number" class="form-control" placeholder="Enter Unit Price"
                            v-model="editForm.unit_price"
                            :class="{ 'is-invalid': submitted && !editForm.unit_price }" />
                        <div class="invalid-feedback">Please enter unit price.</div>
                    </BCol>

                    <!-- tax_value -->
                    <BCol lg="6">
                        <label class="form-label">tax_value</label>
                        <input type="number" class="form-control" placeholder="Enter tax_value"
                            v-model="editForm.tax_value" />
                    </BCol>
                    <!-- Service Description -->
                    <BCol lg="12">
                        <label class="form-label">Service Description</label>
                        <textarea class="form-control" placeholder="Enter Description"
                            v-model="editForm.service_description"></textarea>
                    </BCol>
                </BRow>

                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton type="button" variant="light" @click="toggleEditModal = false">
                        Close
                    </BButton>

                    <BButton type="submit" variant="success" @click="handleUpdateSubmit">
                        Update Invoices
                    </BButton>
                </div>
            </BFrom>
        </BModal>

        <!-- invoices edit modal -->
        <BModal v-model="togglePayModal" id="showmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle invoicesModal" class="v-modal-custom" centered size="lg"
            :title="'Pay Invoice'">
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- Payment Type -->
                    <BCol lg="6">
                        <label class="form-label">Payment Type</label>
                        <select class="form-control" v-model="pay.payment_type"
                            :class="{ 'is-invalid': submitted && !pay.payment_type }">
                            <option value="">Select Payment Type</option>
                            <option value="full">Full</option>
                            <option value="partial">Partial</option>
                        </select>
                        <div class="invalid-feedback">Please select a payment type.</div>
                    </BCol>

                    <!-- Payment Method -->
                    <BCol lg="6">
                        <label class="form-label">Payment Method</label>
                        <select class="form-control" v-model="pay.payment_method"
                            :class="{ 'is-invalid': submitted && !pay.payment_method }">
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash</option>
                            <option value="bank">Bank</option>
                            <option value="credit_card">Credit Card</option>
                        </select>
                        <div class="invalid-feedback">Please select a Payment Method.</div>
                    </BCol>

                    <!-- partial amount -->
                    <BCol v-if="pay.payment_type == 'full'" lg="6">
                        <label class="form-label">Full Amount</label>
                        <p class="form-control">{{ pay.total }}</p>
                    </BCol>
                    <!-- partial amount -->
                    <BCol v-if="pay.payment_type == 'partial'" lg="6">
                        <label class="form-label">Partial Amount</label>
                        <p class="form-control">{{ pay.partial_amount }}</p>
                    </BCol>
                </BRow>

                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton type="button" variant="light" @click="togglePayModal = false">
                        Close
                    </BButton>

                    <BButton type="submit" variant="success" @click="handlePaymentSubmit">
                        Pay Now
                    </BButton>
                </div>
            </BFrom>
        </BModal>

    </Layout>
</template>

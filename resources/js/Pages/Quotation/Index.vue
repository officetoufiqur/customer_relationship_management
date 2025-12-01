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

const props = defineProps({
    quotation: Array,
    clients: Array,
});

// ========== base form ==========
const baseForm = ref({
    id: "",
    client_id: "",
    quotation_number: "",
    service_name: "",
    service_description: "",
    quantity: "",
    unit_price: "",
    tax: "",
});

const form = useForm({ ...baseForm.value });
const editForm = useForm({ ...baseForm.value });

// ========== show all data ==========
const tableHeaders = [
    { key: "id", label: "ID", sortable: true },
    { key: "quotation_number", label: "Quotation Number", sortable: true },
    { key: "service_name", label: "Service Name", sortable: true },
    { key: "service_description", label: "Service Description", sortable: true },
    { key: "quantity", label: "Quantity", sortable: true },
    { key: "unit_price", label: "Unit Price", sortable: true },
    { key: "tax", label: "Tax", sortable: true },
    { key: "actions", label: "Actions", sortable: false },
];

const searchQuery = ref("");

const filteredData = computed(() => {
    let data = props.quotation ?? [];

    if (searchQuery.value) {
        const s = searchQuery.value.toLowerCase();

        data = data.filter((c) => {
            return (
                String(c.quotation_number ?? "").toLowerCase().includes(s) ||
                String(c.service_name ?? "").toLowerCase().includes(s) ||
                String(c.service_description ?? "").toLowerCase().includes(s) ||
                String(c.quantity ?? "").toLowerCase().includes(s) ||
                String(c.unit_price ?? "").toLowerCase().includes(s) ||
                String(c.tax ?? "").toLowerCase().includes(s)
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

const quotationCreateModal = () => {
    toggleCreateModal.value = true;
};

const handleSubmit = async () => {
    if (!form.client_id  || !form.service_name || !form.service_description || !form.quantity || !form.unit_price || !form.tax) {
        Swal.fire(
            "Validation Error",
            "Please fill all required fields",
            "warning"
        );
        return;
    }

    form.post("/quotation/store", {
        onSuccess: () => {
            Swal.fire("Created!", "Quotation added successfully", "success");
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

const editModal = (quotation) => {
    toggleEditModal.value = true;

    editForm.id = quotation.id;
    editForm.client_id = quotation.client_id;
    editForm.quotation_number = quotation.quotation_number;
    editForm.service_name = quotation.service_name;
    editForm.service_description = quotation.service_description;
    editForm.quantity = quotation.quantity;
    editForm.unit_price = quotation.unit_price;
    editForm.tax = quotation.tax;
};

const handleUpdateSubmit = () => {
    editForm.post(`/quotation/update/${editForm.id}`, {
        onSuccess: () => {
            Swal.fire("Updated!", "quotation updated successfully", "success");
            toggleEditModal.value = false;
            editForm.reset();
        },
    });
};

// ============== delete ==============
const deleteData = (quotation) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(`/quotation/destroy/${quotation.id}`, {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "Quotation deleted successfully",
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

const viewModal = (quotation) => {
    toggleViewModal.value = true;
    editForm.id = quotation.id;
    editForm.client_id = quotation.client_id;
    editForm.quotation_number = quotation.quotation_number;
    editForm.service_name = quotation.service_name;
    editForm.service_description = quotation.service_description;
    editForm.quantity = quotation.quantity;
    editForm.unit_price = quotation.unit_price;
    editForm.tax = quotation.tax;
    editForm.total_amount = quotation.total_amount;
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
        <PageHeader title="List View" pageTitle="Quotation" />

        <BRow>
            <BCol lg="12">
                <BCard no-body>
                    <!-- HEADER -->
                    <BCardHeader class="border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                All Quotation
                            </h5>
                            <div class="d-flex gap-2">
                                <BButton v-if="can('quotation_create')" variant="danger" @click="quotationCreateModal">
                                    <i class="ri-add-line me-1"></i>
                                    Create Quotation
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
                                    <tr v-for="(quotation, i) in resultQuery" :key="i">
                                        <td>
                                            <input class="form-check-input" type="checkbox" />
                                        </td>

                                        <td>{{ quotation.id }}</td>
                                        <!-- <td>{{ quotation.client.name }}</td> -->
                                        <td>{{ quotation.quotation_number }}</td>
                                        <td>{{ quotation.service_name }}</td>
                                        <td>{{ quotation.service_description }}</td>
                                        <td>{{ quotation.quantity }}</td>
                                        <td>{{ quotation.unit_price }}</td>
                                        <td>{{ quotation.tax }}</td>
                                        <td class="d-flex gap-2">
                                            <BButton v-if="can('quotation_edit')" variant="success px-3"
                                                @click="editModal(quotation)">
                                                Edit
                                            </BButton>

                                            <BButton v-if="can('quotation_delete')" variant="danger px-3"
                                                @click="deleteData(quotation)">
                                                Delete
                                            </BButton>

                                            <BButton variant="info px-3" @click="viewModal(quotation)">
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

        <!-- quotation create modal -->
        <BModal v-model="toggleCreateModal" id="showmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle quotationModal" class="v-modal-custom" centered size="lg"
            :title="'Add Quotation'">
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
                            v-model="form.service_name"
                            :class="{ 'is-invalid': submitted && !form.service_name }" />
                        <div class="invalid-feedback">Please enter service name.</div>
                    </BCol>

                    <!-- Quantity -->
                    <BCol lg="6">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" placeholder="Enter Quantity"
                            v-model="form.quantity"
                            :class="{ 'is-invalid': submitted && !form.quantity }" />
                        <div class="invalid-feedback">Please enter quantity.</div>
                    </BCol>

                    <!-- Unit Price -->
                    <BCol lg="6">
                        <label class="form-label">Unit Price</label>
                        <input type="number" class="form-control" placeholder="Enter Unit Price"
                            v-model="form.unit_price"
                            :class="{ 'is-invalid': submitted && !form.unit_price }" />
                        <div class="invalid-feedback">Please enter unit price.</div>
                    </BCol>

                    <!-- Tax -->
                    <BCol lg="6">
                        <label class="form-label">Tax</label>
                        <input type="number" class="form-control" placeholder="Enter Tax" v-model="form.tax" />
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
                        Quotation Request
                    </BButton>
                </div>
            </BFrom>
        </BModal>

        <!-- quotation edit modal -->
        <BModal v-model="toggleEditModal" id="showmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle quotationModal" class="v-modal-custom" centered size="lg"
            :title="'Edit quotation'">
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
                            v-model="editForm.quantity"
                            :class="{ 'is-invalid': submitted && !editForm.quantity }" />
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

                    <!-- Tax -->
                    <BCol lg="6">
                        <label class="form-label">Tax</label>
                        <input type="number" class="form-control" placeholder="Enter Tax" v-model="editForm.tax" />
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
                        Update quotation
                    </BButton>
                </div>
            </BFrom>
        </BModal>

        <!-- quotation view modal -->
        <BModal v-model="toggleViewModal" id="showmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle quotationModal" class="v-modal-custom" centered size="lg"
            :title="'View quotation Information'">
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="">
                    <!-- client Name -->
                    <BCol lg="6">
                        <label class="form-label">client Name</label>
                        <p class="form-control">{{ editForm.client_id }}</p>
                    </BCol>

                    <!-- Service Name -->
                    <BCol lg="6">
                        <label class="form-label">Service Name</label>
                        <p class="form-control">{{ editForm.service_name }}</p>
                    </BCol>

                    <!-- Quantity -->
                    <BCol lg="6">
                        <label class="form-label">Quantity</label>
                        <p class="form-control">{{ editForm.quantity }}</p>
                    </BCol>

                    <!-- Unit Price -->
                    <BCol lg="6">
                        <label class="form-label">Unit Price</label>
                        <p class="form-control">{{ editForm.unit_price }}</p>
                    </BCol>

                    <!-- Tax -->
                    <BCol lg="6">
                        <label class="form-label">Tax</label>
                        <p class="form-control">{{ editForm.tax }}</p>
                    </BCol>
                    <!-- total_amount -->
                    <BCol lg="6">
                        <label class="form-label">Total Amount</label>
                        <p class="form-control">{{ editForm.total_amount }}</p>
                    </BCol>
                    <!-- Service Description -->
                    <BCol lg="12">
                        <label class="form-label">Service Description</label>
                        <p class="form-control">{{ editForm.service_description }}</p>
                    </BCol>
                </BRow>
            </BFrom>
        </BModal>
    </Layout>
</template>

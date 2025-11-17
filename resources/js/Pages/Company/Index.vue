<script setup>
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, computed, watch, onMounted } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const props = defineProps({
    companys: Array,
});

// ========== base form ==========
const baseForm = ref({
    id: "",
    name: "",
    registration_number: "",
    license_type: "",
    expiry_date: "",
    services_provided: "",
    renewal_status: "",
    employee_name: "",
    passport_number: "",
    permit_expiry: "",
    permit_type: "",
});

const form = useForm({ ...baseForm.value });
const editForm = useForm({ ...baseForm.value });

// ========== show all data ==========
const tableHeaders = [
    { key: "id", label: "ID", sortable: true },
    { key: "name", label: "Name", sortable: true },
    { key: "services_provided", label: "Services Provided", sortable: true },
    { key: "renewal_status", label: "Renewal Status", sortable: true },
    { key: "actions", label: "Actions", sortable: false },
];

const column = ref([{}]);
const searchQuery = ref("");

const filteredData = computed(() => {
    let data = props.companys ?? [];

    // search filter
    if (searchQuery.value) {
        const s = searchQuery.value.toLowerCase();
        data = data.filter((c) => {
            return (
                (c.name || "").toLowerCase().includes(s) ||
                (c.registration_number || "").toLowerCase().includes(s) ||
                (c.license_type || "").toLowerCase().includes(s) ||
                (c.expiry_date || "").toLowerCase().includes(s) ||
                (c.services_provided || "").toLowerCase().includes(s) ||
                (c.renewal_status || "").toLowerCase().includes(s)
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

const companyCreateModal = () => {
    toggleCreateModal.value = true;
};

const handleSubmit = async () => {
    if (!form.name || !form.registration_number || !form.license_type) {
        Swal.fire(
            "Validation Error",
            "Please fill all required fields",
            "warning"
        );
        return;
    }

    form.post("/companys/store", {
        onSuccess: () => {
            Swal.fire("Created!", "Company added successfully", "success");
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
const editModal = (company) => {
    toggleEditModal.value = true;

    editForm.id = company.id;
    editForm.name = company.name;
    editForm.registration_number = company.registration_number;
    editForm.license_type = company.license_type;
    editForm.expiry_date = company.expiry_date;
    editForm.services_provided = company.services_provided;
    editForm.renewal_status = company.renewal_status;
    editForm.employee_name = company.employees.employee_name;
    editForm.passport_number = company.employees.passport_number;
    editForm.permit_expiry = company.employees.permit_expiry;
    editForm.permit_type = company.employees.permit_type;
};

const handleUpdateSubmit = () => {
    editForm.post(`/company/update/${editForm.id}`, {
        onSuccess: () => {
            Swal.fire("Updated!", "company updated successfully", "success");
            toggleEditModal.value = false;
            editForm.reset();
        },
    });
};

// ============== delete ==============
const deleteData = (company) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(`/company/destroy/${company.id}`, {
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

const viewModal = (company) => {
    toggleViewModal.value = true;
    editForm.id = company.id;
    editForm.name = company.name;
    editForm.registration_number = company.registration_number;
    editForm.license_type = company.license_type;
    editForm.expiry_date = company.expiry_date;
    editForm.services_provided = company.services_provided;
    editForm.renewal_status = company.renewal_status;
    editForm.employee_name = company.employees.employee_name;
    editForm.passport_number = company.employees.passport_number;
    editForm.permit_expiry = company.employees.permit_expiry;
    editForm.permit_type = company.employees.permit_type;
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
        <PageHeader title="List View" pageTitle="companys" />

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
                                <BButton
                                    variant="danger"
                                    @click="companyCreateModal"
                                >
                                    <i class="ri-add-line me-1"></i>
                                    Create Company
                                </BButton>
                            </div>
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
                                        v-for="(company, i) in resultQuery"
                                        :key="i"
                                    >
                                        <td>
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                            />
                                        </td>

                                        <td>{{ company.id }}</td>
                                        <td>{{ company.name }}</td>
                                        <td>{{ company.services_provided }}</td>
                                        <td>{{ company.renewal_status }}</td>

                                        <td class="d-flex gap-2">
                                            <BButton
                                                variant="success px-3"
                                                @click="editModal(company)"
                                            >
                                                Edit
                                            </BButton>

                                            <BButton
                                                variant="danger px-3"
                                                @click="deleteData(company)"
                                            >
                                                Delete
                                            </BButton>

                                            <BButton
                                                variant="info px-3"
                                                @click="viewModal(company)"
                                            >
                                                View
                                            </BButton>
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

        <!-- company create modal -->
        <BModal
            v-model="toggleCreateModal"
            id="showmodal"
            modal-class="zoomIn"
            hide-footer
            header-class="p-3 bg-info-subtle companyModal"
            class="v-modal-custom"
            centered
            size="lg"
            :title="'Add Company'"
        >
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- Company Name -->
                    <BCol lg="6">
                        <label class="form-label">Company Name</label>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Company Name"
                            v-model="form.name"
                            :class="{ 'is-invalid': submitted && !form.name }"
                        />
                        <div class="invalid-feedback">
                            Please enter company name.
                        </div>
                    </BCol>

                    <!-- Registration Number -->
                    <BCol lg="6">
                        <label class="form-label">Registration Number</label>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Registration Number"
                            v-model="form.registration_number"
                            :class="{
                                'is-invalid':
                                    submitted && !form.registration_number,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter registration number.
                        </div>
                    </BCol>

                    <!-- License Type -->
                    <BCol lg="6">
                        <label class="form-label">License Type</label>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="License Type"
                            v-model="form.license_type"
                            :class="{
                                'is-invalid': submitted && !form.license_type,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter license type.
                        </div>
                    </BCol>

                    <!-- Expiry Date -->
                    <BCol lg="6">
                        <label class="form-label">Expiry Date</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="form.expiry_date"
                            :class="{
                                'is-invalid': submitted && !form.expiry_date,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please select expiry date.
                        </div>
                    </BCol>

                    <!-- Services Provided -->
                    <BCol lg="6">
                        <label class="form-label">Services Provided</label>
                        <select
                            class="form-control"
                            v-model="form.services_provided"
                            :class="{
                                'is-invalid':
                                    submitted && !form.services_provided,
                            }"
                        >
                            <option value="">Select Service</option>
                            <option value="acounting">Acounting</option>
                            <option value="vat_filing">Vat Filing</option>
                            <option value="commercial_address">
                                Commercial Address
                            </option>
                            <option value="bahraini_partner_service">
                                Bahraini Partner Service
                            </option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter service information.
                        </div>
                    </BCol>

                    <!-- Renewal Status -->
                    <BCol lg="6">
                        <label class="form-label">Renewal Status</label>
                        <select
                            class="form-control"
                            v-model="form.renewal_status"
                            :class="{
                                'is-invalid': submitted && !form.renewal_status,
                            }"
                        >
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="renewed">Renewed</option>
                            <option value="expired">Expired</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select renewal status.
                        </div>
                    </BCol>

                    <!-- Employee Name -->
                    <BCol lg="6">
                        <label class="form-label">Employee Name</label>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Employee Name"
                            v-model="form.employee_name"
                            :class="{
                                'is-invalid': submitted && !form.employee_name,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter employee name.
                        </div>
                    </BCol>

                    <!-- Passport Number -->
                    <BCol lg="6">
                        <label class="form-label">Passport Number</label>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Passport Number"
                            v-model="form.passport_number"
                            :class="{
                                'is-invalid':
                                    submitted && !form.passport_number,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter passport number.
                        </div>
                    </BCol>

                    <!-- Permit Expiry -->
                    <BCol lg="6">
                        <label class="form-label">Permit Expiry Date</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="form.permit_expiry"
                            :class="{
                                'is-invalid': submitted && !form.permit_expiry,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please select permit expiry.
                        </div>
                    </BCol>

                    <!-- Permit Type -->
                    <BCol lg="6">
                        <label class="form-label">Permit Type</label>
                        <select
                            class="form-control"
                            v-model="form.permit_type"
                            :class="{
                                'is-invalid': submitted && !form.permit_type,
                            }"
                        >
                            <option value="">Select Permit Type</option>
                            <option value="work_permit">Work Permit</option>
                            <option value="visitor_permit">
                                Visitor Permit
                            </option>
                            <option value="student_visa">Student Visa</option>
                            <option value="other_permit">Other Permit</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter permit type.
                        </div>
                    </BCol>
                </BRow>

                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton
                        type="button"
                        variant="light"
                        @click="toggleCreateModal = false"
                    >
                        Close
                    </BButton>

                    <BButton
                        type="submit"
                        variant="success"
                        @click="handleSubmit"
                    >
                        Add Company
                    </BButton>
                </div>
            </BFrom>
        </BModal>

        <!-- company edit modal -->
        <BModal
            v-model="toggleEditModal"
            id="showmodal"
            modal-class="zoomIn"
            hide-footer
            header-class="p-3 bg-info-subtle companyModal"
            class="v-modal-custom"
            centered
            size="lg"
            :title="'Add Company'"
        >
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- Company Name -->
                    <BCol lg="6">
                        <label class="form-label">Company Name</label>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Company Name"
                            v-model="editForm.name"
                            :class="{
                                'is-invalid': submitted && !editForm.name,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter company name.
                        </div>
                    </BCol>

                    <!-- Registration Number -->
                    <BCol lg="6">
                        <label class="form-label">Registration Number</label>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Registration Number"
                            v-model="editForm.registration_number"
                            :class="{
                                'is-invalid':
                                    submitted && !editForm.registration_number,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter registration number.
                        </div>
                    </BCol>

                    <!-- License Type -->
                    <BCol lg="6">
                        <label class="form-label">License Type</label>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="License Type"
                            v-model="editForm.license_type"
                            :class="{
                                'is-invalid':
                                    submitted && !editForm.license_type,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter license type.
                        </div>
                    </BCol>

                    <!-- Expiry Date -->
                    <BCol lg="6">
                        <label class="form-label">Expiry Date</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="editForm.expiry_date"
                            :class="{
                                'is-invalid':
                                    submitted && !editForm.expiry_date,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please select expiry date.
                        </div>
                    </BCol>

                    <!-- Services Provided -->
                    <BCol lg="6">
                        <label class="form-label">Services Provided</label>
                        <select
                            class="form-control"
                            v-model="editForm.services_provided"
                            :class="{
                                'is-invalid':
                                    submitted && !editForm.services_provided,
                            }"
                        >
                            <option value="">Select Service</option>
                            <option value="acounting">Acounting</option>
                            <option value="vat_filing">Vat Filing</option>
                            <option value="commercial_address">
                                Commercial Address
                            </option>
                            <option value="bahraini_partner_service">
                                Bahraini Partner Service
                            </option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter service information.
                        </div>
                    </BCol>

                    <!-- Renewal Status -->
                    <BCol lg="6">
                        <label class="form-label">Renewal Status</label>
                        <select
                            class="form-control"
                            v-model="editForm.renewal_status"
                            :class="{
                                'is-invalid':
                                    submitted && !editForm.renewal_status,
                            }"
                        >
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="renewed">Renewed</option>
                            <option value="expired">Expired</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select renewal status.
                        </div>
                    </BCol>

                    <!-- Employee Name -->
                    <BCol lg="6">
                        <label class="form-label">Employee Name</label>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Employee Name"
                            v-model="editForm.employee_name"
                            :class="{
                                'is-invalid':
                                    submitted && !editForm.employee_name,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter employee name.
                        </div>
                    </BCol>

                    <!-- Passport Number -->
                    <BCol lg="6">
                        <label class="form-label">Passport Number</label>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Passport Number"
                            v-model="editForm.passport_number"
                            :class="{
                                'is-invalid':
                                    submitted && !editForm.passport_number,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter passport number.
                        </div>
                    </BCol>

                    <!-- Permit Expiry -->
                    <BCol lg="6">
                        <label class="form-label">Permit Expiry Date</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="editForm.permit_expiry"
                            :class="{
                                'is-invalid':
                                    submitted && !editForm.permit_expiry,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please select permit expiry.
                        </div>
                    </BCol>

                    <!-- Permit Type -->
                    <BCol lg="6">
                        <label class="form-label">Permit Type</label>
                        <select
                            class="form-control"
                            v-model="editForm.permit_type"
                            :class="{
                                'is-invalid':
                                    submitted && !editForm.permit_type,
                            }"
                        >
                            <option value="">Select Permit Type</option>
                            <option value="work_permit">Work Permit</option>
                            <option value="visitor_permit">
                                Visitor Permit
                            </option>
                            <option value="student_visa">Student Visa</option>
                            <option value="other_permit">Other Permit</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter permit type.
                        </div>
                    </BCol>
                </BRow>

                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton
                        type="button"
                        variant="light"
                        @click="toggleEditModal = false"
                    >
                        Close
                    </BButton>

                    <BButton
                        type="submit"
                        variant="success"
                        @click="handleUpdateSubmit"
                    >
                        Add Company
                    </BButton>
                </div>
            </BFrom>
        </BModal>

        <!-- company edit modal -->
        <BModal
            v-model="toggleViewModal"
            id="showmodal"
            modal-class="zoomIn"
            hide-footer
            header-class="p-3 bg-info-subtle companyModal"
            class="v-modal-custom"
            centered
            size="lg"
            :title="'View Company Information'"
        >
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- Company Name -->
                    <BCol lg="6">
                        <label class="form-label">Company Name</label>
                        <p class="form-control">{{ editForm.name }}</p>
                    </BCol>

                    <!-- Registration Number -->
                    <BCol lg="6">
                        <label class="form-label">Registration Number</label>
                        <p class="form-control">
                            {{ editForm.registration_number }}
                        </p>
                    </BCol>

                    <!-- License Type -->
                    <BCol lg="6">
                        <label class="form-label">License Type</label>
                        <p class="form-control">{{ editForm.license_type }}</p>
                    </BCol>

                    <!-- Expiry Date -->
                    <BCol lg="6">
                        <label class="form-label">Expiry Date</label>
                        <p class="form-control">{{ editForm.expiry_date }}</p>
                    </BCol>

                    <!-- Services Provided -->
                    <BCol lg="6">
                        <label class="form-label">Services Provided</label>
                        <p class="form-control">
                            {{ editForm.services_provided }}
                        </p>
                    </BCol>

                    <!-- Renewal Status -->
                    <BCol lg="6">
                        <label class="form-label">Renewal Status</label>
                        <p class="form-control">
                            {{ editForm.renewal_status }}
                        </p>
                    </BCol>

                    <!-- Employee Name -->
                    <BCol lg="6">
                        <label class="form-label">Employee Name</label>
                        <p class="form-control">{{ editForm.employee_name }}</p>
                    </BCol>

                    <!-- Passport Number -->
                    <BCol lg="6">
                        <label class="form-label">Passport Number</label>
                        <p class="form-control">
                            {{ editForm.passport_number }}
                        </p>
                    </BCol>

                    <!-- Permit Expiry -->
                    <BCol lg="6">
                        <label class="form-label">Permit Expiry Date</label>
                        <p class="form-control">{{ editForm.permit_expiry }}</p>
                    </BCol>

                    <!-- Permit Type -->
                    <BCol lg="6">
                        <label class="form-label">Permit Type</label>
                        <p class="form-control">{{ editForm.permit_type }}</p>
                    </BCol>
                </BRow>
            </BFrom>
        </BModal>
    </Layout>
</template>

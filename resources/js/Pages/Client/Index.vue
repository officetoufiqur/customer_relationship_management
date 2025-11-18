<script setup>
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, computed, watch, onMounted } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import $ from "jquery";

const props = defineProps({
    clients: Array,
});

// ========== base form ==========
const baseForm = ref({
    id: "",
    name: "",
    phone: "",
    email: "",
    whatsapp: "",
    source_of_lead: "",
    service_type: "",
    follow_up_status: "",
    client_interaction: "",
    project_cost: "",
    documents: "",
    quotation_sent_status: "",
    follow_date: "",
});

const form = useForm({ ...baseForm.value });
const editForm = useForm({ ...baseForm.value });

// ========== show all data ==========
const tableHeaders = [
    { key: "id", label: "ID", sortable: true },
    { key: "name", label: "Name", sortable: true },
    { key: "phone", label: "Phone", sortable: true },
    { key: "email", label: "Email", sortable: true },
    { key: "whatsapp", label: "Whatsapp", sortable: true },
    { key: "source_of_lead", label: "Source Of Lead", sortable: true },
    { key: "project_cost", label: "Project Cost", sortable: true },
    { key: "follow_date", label: "Follow Up Date", sortable: true },
    { key: "follow_up_status", label: "Follow Up Status", sortable: true },
    { key: "actions", label: "Actions", sortable: false },
];

const searchQuery = ref("");

const filteredData = computed(() => {
    let data = props.clients ?? [];

    // search filter
    if (searchQuery.value) {
        const s = searchQuery.value.toLowerCase();
        data = data.filter((c) => {
            return (
                (c.name || "").toLowerCase().includes(s) ||
                (c.email || "").toLowerCase().includes(s) ||
                (c.phone || "").toLowerCase().includes(s) ||
                (c.whatsapp || "").toLowerCase().includes(s) ||
                (c.follow_date || "").toLowerCase().includes(s)
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

const clientCreateModal = () => {
    toggleCreateModal.value = true;
};

const handleDocuments = (e) => {
    const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
    form.documents = file;
};

const handleSubmit = async () => {
    if (!form.name || !form.email || !form.phone) {
        Swal.fire(
            "Validation Error",
            "Please fill all required fields",
            "warning"
        );
        return;
    }

    form.post("/clients/store", {
        onSuccess: () => {
            Swal.fire("Created!", "client added successfully", "success");
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
const handleEditFileUpload = (e) => {
    editForm.documents = e.target.files[0];
};

const editModal = (client) => {
    toggleEditModal.value = true;

    editForm.id = client.id;
    editForm.name = client.name
    editForm.phone = client.phone
    editForm.email = client.email
    editForm.whatsapp = client.whatsapp
    editForm.source_of_lead = client.source_of_lead
    editForm.service_type = client.service_type
    editForm.follow_up_status = client.follow_up_status
    editForm.client_interaction = client.client_interaction
    editForm.project_cost = client.project_cost
    editForm.documents = client.documents
    editForm.quotation_sent_status = client.quotation_sent_status
    editForm.follow_date = client.follow_date
};

const handleUpdateSubmit = () => {
    editForm.post(`/client/update/${editForm.id}`, {
        onSuccess: () => {
            Swal.fire("Updated!", "client updated successfully", "success");
            toggleEditModal.value = false;
            editForm.reset();
        },
    });
};

// ============== delete ==============
const deleteData = (client) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(`/client/destroy/${client.id}`, {
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

const viewModal = (client) => {
    toggleViewModal.value = true;
    editForm.id = client.id;
    editForm.name = client.name
    editForm.phone = client.phone
    editForm.email = client.email
    editForm.whatsapp = client.whatsapp
    editForm.source_of_lead = client.source_of_lead
    editForm.service_type = client.service_type
    editForm.follow_up_status = client.follow_up_status
    editForm.client_interaction = client.client_interaction
    editForm.project_cost = client.project_cost
    editForm.documents = client.documents
    editForm.quotation_sent_status = client.quotation_sent_status
    editForm.follow_date = client.follow_date
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

// ===== dropify =====
const initDropify = () => {
    $(".dropify").dropify({
        messages: {
            default: "Drag and drop a file here or click",
            replace: "Drag and drop or click to replace",
            remove: "Remove",
            error: "Oops, something went wrong.",
        },
    });
};

const editDropify = () => {
    $(".imageFile").dropify({
        defaultFile: editForm.documents || "",
        messages: {
            default: "Drag and drop a file here or click",
            replace: "Drag and drop or click to replace",
            remove: "Remove",
            error: "Oops, something went wrong.",
        },
    });
};

onMounted(() => {
    initDropify();
    editDropify();
});

// Reset page when search changes
watch(searchQuery, () => {
    page.value = 1;
});



</script>

<template>
    <Layout>
        <PageHeader title="List View" pageTitle="clients" />

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
                                    @click="clientCreateModal"
                                >
                                    <i class="ri-add-line me-1"></i>
                                    Create client
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
                                        <td>{{ client.name }}</td>
                                        <td>{{ client.phone }}</td>
                                        <td>{{ client.email }}</td>
                                        <td>{{ client.whatsapp }}</td>
                                        <td>{{ client.source_of_lead }}</td>
                                        <td>{{ client.project_cost }}</td>
                                        <td>{{ client.follow_date }}</td>
                                        <td>{{ client.follow_up_status }}</td>

                                        <td class="d-flex gap-2">
                                            <BButton
                                                variant="success px-3"
                                                @click="editModal(client)"
                                            >
                                                Edit
                                            </BButton>

                                            <BButton
                                                variant="danger px-3"
                                                @click="deleteData(client)"
                                            >
                                                Delete
                                            </BButton>

                                            <BButton
                                                variant="info px-3"
                                                @click="viewModal(client)"
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

        <!-- client create modal -->
        <BModal
            v-model="toggleCreateModal"
            id="showmodal"
            modal-class="zoomIn"
            hide-footer
            header-class="p-3 bg-info-subtle clientModal"
            class="v-modal-custom"
            centered
            size="lg"
            :title="'Add client'"
        >
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- client Name -->
                    <BCol lg="6">
                        <label class="form-label">Client Name</label>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="client name"
                            v-model="form.name"
                            :class="{ 'is-invalid': submitted && !form.name }"
                        />
                        <div class="invalid-feedback">
                            Please enter client name.
                        </div>
                    </BCol>

                    <!-- Phone -->
                    <BCol lg="6">
                        <label class="form-label">Phone</label>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Phone"
                            v-model="form.phone"
                            :class="{
                                'is-invalid':
                                    submitted && !form.phone,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter Phone.
                        </div>
                    </BCol>

                    <!-- Email -->
                    <BCol lg="6">
                        <label class="form-label">Email</label>
                        <input
                            type="email"
                            class="form-control"
                            placeholder="Email"
                            v-model="form.email"
                            :class="{
                                'is-invalid': submitted && !form.email,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter Email.
                        </div>
                    </BCol>

                    <!-- whatsapp -->
                    <BCol lg="6">
                        <label class="form-label">Whatsapp</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="form.whatsapp"
                            :class="{
                                'is-invalid': submitted && !form.whatsapp,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please select whatsapp.
                        </div>
                    </BCol>

                    <!-- Source Of Lead -->
                    <BCol lg="6">
                        <label class="form-label">Source Of Lead</label>
                        <select
                            class="form-control"
                            v-model="form.source_of_lead"
                            :class="{
                                'is-invalid':
                                    submitted && !form.source_of_lead,
                            }"
                        >
                            <option value="">Select source of lead</option>
                            <option value="referral">Referral</option>
                            <option value="social">Social</option>
                            <option value="media">Media</option>
                            <option value="website">Website</option>
                            <option value="walk_in">Walk In</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter Source Of Lead.
                        </div>
                    </BCol>

                    <!-- service type -->
                    <BCol lg="6">
                        <label class="form-label">Service Type</label>
                        <select
                            class="form-control"
                            v-model="form.service_type"
                            :class="{
                                'is-invalid': submitted && !form.service_type,
                            }"
                        >
                            <option value="">Select Status</option>
                            <option value="company_formation">Company Formation</option>
                            <option value="accounting">Accounting</option>
                            <option value="visa_processing">Visa Processing</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select service type.
                        </div>
                    </BCol>

                    <!-- follow up status -->
                    <BCol lg="6">
                        <label class="form-label">Follow Up Status</label>
                        <select
                            class="form-control"
                            v-model="form.follow_up_status"
                            :class="{
                                'is-invalid': submitted && !form.follow_up_status,
                            }"
                        >
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="in_discussion">In Discussion</option>
                            <option value="approved">Approved</option>
                            <option value="lost">Lost</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select follow up status.
                        </div>
                    </BCol>

                    <!-- client interaction -->
                    <BCol lg="6">
                        <label class="form-label">Client Interaction</label>
                        <select
                            class="form-control"
                            v-model="form.client_interaction"
                            :class="{
                                'is-invalid': submitted && !form.client_interaction,
                            }"
                        >
                            <option value="">Select client interaction</option>
                            <option value="calls">Calls</option>
                            <option value="emails">Emails</option>
                            <option value="messages">Messages</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select client interaction.
                        </div>
                    </BCol>

                    <!-- project cost -->
                    <BCol lg="6">
                        <label class="form-label">Project Cost</label>
                        <input
                            type="number"
                            class="form-control"
                            placeholder="project cost"
                            v-model="form.project_cost"
                            :class="{
                                'is-invalid': submitted && !form.project_cost,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter project cost.
                        </div>
                    </BCol>

                    <!-- follow date -->
                    <BCol lg="6">
                        <label class="form-label">Follow Up Date</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="form.follow_date"
                            :class="{
                                'is-invalid': submitted && !form.follow_date,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please select follow date.
                        </div>
                    </BCol>

                     <!-- documents -->
                    <BCol lg="12">
                        <label class="form-label">Quotation Documents</label>
                         <input
                            type="file"
                            id="documents"
                            class="dropify"
                            data-height="150"
                            @change="handleDocuments($event)"
                        />
                        <div class="invalid-feedback">
                            Please enter documents.
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
                        Add client
                    </BButton>
                </div>
            </BFrom>
        </BModal>

        <!-- client edit modal -->
        <BModal
            v-model="toggleEditModal"
            id="showmodal"
            modal-class="zoomIn"
            hide-footer
            header-class="p-3 bg-info-subtle clientModal"
            class="v-modal-custom"
            centered
            size="lg"
            :title="'Add client'"
        >
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- client Name -->
                    <BCol lg="6">
                        <label class="form-label">Client Name</label>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="client name"
                            v-model="editForm.name"
                            :class="{ 'is-invalid': submitted && !editForm.name }"
                        />
                        <div class="invalid-feedback">
                            Please enter client name.
                        </div>
                    </BCol>

                    <!-- Phone -->
                    <BCol lg="6">
                        <label class="form-label">Phone</label>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Phone"
                            v-model="editForm.phone"
                            :class="{
                                'is-invalid':
                                    submitted && !editForm.phone,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter Phone.
                        </div>
                    </BCol>

                    <!-- Email -->
                    <BCol lg="6">
                        <label class="form-label">Email</label>
                        <input
                            type="email"
                            class="form-control"
                            placeholder="Email"
                            v-model="editForm.email"
                            :class="{
                                'is-invalid': submitted && !editForm.email,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter Email.
                        </div>
                    </BCol>

                    <!-- whatsapp -->
                    <BCol lg="6">
                        <label class="form-label">Whatsapp</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="editForm.whatsapp"
                            :class="{
                                'is-invalid': submitted && !editForm.whatsapp,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please select whatsapp.
                        </div>
                    </BCol>

                    <!-- Source Of Lead -->
                    <BCol lg="6">
                        <label class="form-label">Source Of Lead</label>
                        <select
                            class="form-control"
                            v-model="editForm.source_of_lead"
                            :class="{
                                'is-invalid':
                                    submitted && !editForm.source_of_lead,
                            }"
                        >
                            <option value="">Select source of lead</option>
                            <option value="referral">Referral</option>
                            <option value="social">Social</option>
                            <option value="media">Media</option>
                            <option value="website">Website</option>
                            <option value="walk_in">Walk In</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter Source Of Lead.
                        </div>
                    </BCol>

                    <!-- service type -->
                    <BCol lg="6">
                        <label class="form-label">Service Type</label>
                        <select
                            class="form-control"
                            v-model="editForm.service_type"
                            :class="{
                                'is-invalid': submitted && !editForm.service_type,
                            }"
                        >
                            <option value="">Select Status</option>
                            <option value="company_formation">Company Formation</option>
                            <option value="accounting">Accounting</option>
                            <option value="visa_processing">Visa Processing</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select service type.
                        </div>
                    </BCol>

                    <!-- follow up status -->
                    <BCol lg="6">
                        <label class="form-label">Follow Up Status</label>
                        <select
                            class="form-control"
                            v-model="editForm.follow_up_status"
                            :class="{
                                'is-invalid': submitted && !editForm.follow_up_status,
                            }"
                        >
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="in_discussion">In Discussion</option>
                            <option value="approved">Approved</option>
                            <option value="lost">Lost</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select follow up status.
                        </div>
                    </BCol>

                    <!-- client interaction -->
                    <BCol lg="6">
                        <label class="form-label">Client Interaction</label>
                        <select
                            class="form-control"
                            v-model="editForm.client_interaction"
                            :class="{
                                'is-invalid': submitted && !editForm.client_interaction,
                            }"
                        >
                            <option value="">Select client interaction</option>
                            <option value="calls">Calls</option>
                            <option value="emails">Emails</option>
                            <option value="messages">Messages</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select client interaction.
                        </div>
                    </BCol>

                    <!-- project cost -->
                    <BCol lg="6">
                        <label class="form-label">Project Cost</label>
                        <input
                            type="number"
                            class="form-control"
                            placeholder="project cost"
                            v-model="editForm.project_cost"
                            :class="{
                                'is-invalid': submitted && !editForm.project_cost,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter project cost.
                        </div>
                    </BCol>

                    <!-- follow date -->
                    <BCol lg="6">
                        <label class="form-label">Follow Up Date</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="editForm.follow_date"
                            :class="{
                                'is-invalid': submitted && !editForm.follow_date,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please select follow date.
                        </div>
                    </BCol>

                     <!-- documents -->
                    <BCol lg="12">
                        <label class="form-label">Quotation Documents</label>
                         <input
                            type="file"
                            id="documents"
                            class="imageFile"
                            data-height="150"
                            @change="handleEditFileUpload"
                        />
                        <div class="invalid-feedback">
                            Please enter documents.
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
                        Update client
                    </BButton>
                </div>
            </BFrom>
        </BModal>

        <!-- client edit modal -->
        <BModal
            v-model="toggleViewModal"
            id="showmodal"
            modal-class="zoomIn"
            hide-footer
            header-class="p-3 bg-info-subtle clientModal"
            class="v-modal-custom"
            centered
            size="lg"
            :title="'View client Information'"
        >
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="">
                    <!-- client Name -->
                    <BCol lg="6">
                        <label class="form-label">Client Name</label>
                        <p class="form-control">{{ editForm.name }}</p>
                    </BCol>

                    <!-- Phone -->
                    <BCol lg="6">
                        <label class="form-label">Phone</label>
                        <p class="form-control">{{ editForm.phone }}</p>
                    </BCol>

                    <!-- Email -->
                    <BCol lg="6">
                        <label class="form-label">Email</label>
                        <p class="form-control">{{ editForm.email }}</p>
                    </BCol>

                    <!-- whatsapp -->
                    <BCol lg="6">
                        <label class="form-label">Whatsapp</label>
                        <p class="form-control">{{ editForm.whatsapp }}</p>
                    </BCol>

                    <!-- Source Of Lead -->
                    <BCol lg="6">
                        <label class="form-label">Source Of Lead</label>
                        <p class="form-control">{{ editForm.source_of_lead }}</p>
                    </BCol>

                    <!-- service type -->
                    <BCol lg="6">
                        <label class="form-label">Service Type</label>
                        <p class="form-control">{{ editForm.service_type }}</p>
                    </BCol>

                    <!-- follow up status -->
                    <BCol lg="6">
                        <label class="form-label">Follow Up Status</label>
                        <p class="form-control">{{ editForm.follow_up_status }}</p>
                    </BCol>

                    <!-- client interaction -->
                    <BCol lg="6">
                        <label class="form-label">Client Interaction</label>
                        <p class="form-control">{{ editForm.client_interaction }}</p>
                    </BCol>

                    <!-- project cost -->
                    <BCol lg="6">
                        <label class="form-label">Project Cost</label>
                        <p class="form-control">{{ editForm.project_cost }}</p>
                    </BCol>

                    <!-- follow date -->
                    <BCol lg="6">
                        <label class="form-label">Follow Up Date</label>
                        <p class="form-control">{{ editForm.follow_date }}</p>
                    </BCol>

                     <!-- documents -->
                    <BCol lg="12">
                        <label class="form-label">Quotation Documents</label>
                         <img :src="editForm.documents" alt="" style="width: 100%; height: auto; object-fit: contain;">
                    </BCol>

                </BRow>
            </BFrom>
        </BModal>
    </Layout>
</template>

<style>
.dropify-wrapper .dropify-preview .dropify-render img {
    width: 100% !important;
    height: auto !important;
    object-fit: contain;
}

.dropify-wrapper .dropify-message p {
    font-size: 16px;
    text-align: center;
}
</style>
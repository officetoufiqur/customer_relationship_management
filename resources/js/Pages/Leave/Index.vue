<script setup>
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, reactive, computed, onMounted, watch, nextTick } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import $ from "jquery";

const props = defineProps({
    leaves: Array,
});

const leaveData = props.leaves;
//======= create leave modal =======
const leaveListModal = ref(false);
const dataEdit = ref(false);
const submitted = ref(false);

const toggleModal = () => {
    dataEdit.value = false;
    leaveListModal.value = true;
};

// form models
const form = useForm({
    user_id: "",
    leave_type: "",
    reason: "",
    status: "",
    start_date: "",
    end_date: "",
    is_medical: "",
    medical_excuse_file: null,
});

const editForm = useForm({
    leave_type: "",
    reason: "",
    start_date: "",
    end_date: "",
    is_medical: "",
    medical_excuse_file: null,
});

// handle file upload for both create and edit forms
const handleFileUpload = (e, isEdit = false) => {
    const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
    form.medical_excuse_file = file;
};

const handleEditFileUpload = (e) => {
    editForm.medical_excuse_file = e.target.files[0];
};

const handleUpdateSubmit = () => {
    editForm.post(`/leave/update/${dataEdit.value.id}`, {
        onSuccess: () => {
            console.log("leave updated successfully");
            leaveEditListModal.value = false;
            editForm.reset();
        },
    });
};
const handleSubmit = () => {
    form.post("/leave/store", {
        onSuccess: () => {
            console.log("leave added successfully");
            leaveListModal.value = false;
            form.reset();
        },
    });
};

// ===== edit leave modal =====
const leaveEditListModal = ref(false);

const closeModal = () => {
    leaveEditListModal.value = false;
    leaveListModal.value = false;
    leaveViewModal.value = false;
};

const toggleEditModal = (leave) => {
    dataEdit.value = leave;
    editForm.user_id = leave.user_id || "";
    editForm.leave_type = leave.leave_type || "";
    editForm.reason = leave.reason || "";
    editForm.status = leave.status || "";
    editForm.start_date = leave.start_date || "";
    editForm.end_date = leave.end_date || "";
    editForm.is_medical = leave.is_medical == 1 || leave.is_medical === true;
    editForm.medical_excuse_file = leave.medical_excuse_file || null;
    leaveEditListModal.value = true;
};

const handleStatusUpdateSubmit = () => {
    viewForm.put(`/leave/update/status/${dataEdit.value.id}`, {
        onSuccess: () => {
            leaveViewModal.value = false;
        },
    });
};

// ===== View leave modal =====
const leaveViewModal = ref(false);

const viewForm = useForm({
    status: "",
});

const toggleViewModal = (leave) => {
     dataEdit.value = leave;
     editForm.user_id = leave.user_id || "";
     editForm.leave_type = leave.leave_type || "";
     editForm.reason = leave.reason || "";
     editForm.status = leave.status || "";
     editForm.start_date = leave.start_date || "";
     editForm.end_date = leave.end_date || "";
     editForm.is_medical = leave.is_medical == 1 || leave.is_medical === true;
     editForm.medical_excuse_file = leave.medical_excuse_file || null;
     leaveViewModal.value = true;
 };

// ===== PAGINATION + SORTING =====
const search = ref("");
const sortKey = ref("id");
const sortOrder = ref("asc");
const page = ref(1);
const perPage = ref(5);

const filteredleaves = computed(() =>
    props.leaves.filter((leave) =>
        leave.leave_type.toLowerCase().includes(search.value.toLowerCase())
    )
);

const sortedleaves = computed(() => {
    return [...filteredleaves.value].sort((a, b) => {
        const valA = a[sortKey.value]?.toString().toLowerCase();
        const valB = b[sortKey.value]?.toString().toLowerCase();

        if (valA < valB) return sortOrder.value === "asc" ? -1 : 1;
        if (valA > valB) return sortOrder.value === "asc" ? 1 : -1;
        return 0;
    });
});

const totalPages = computed(() =>
    Math.ceil(sortedleaves.value.length / perPage.value)
);

const paginatedleaves = computed(() => {
    const start = (page.value - 1) * perPage.value;
    const end = start + perPage.value;
    return sortedleaves.value.slice(start, end);
});

const sortBy = (key) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
    } else {
        sortKey.value = key;
        sortOrder.value = "asc";
    }
};

const pages = computed(() => {
    const arr = [];
    for (let i = 1; i <= totalPages.value; i++) {
        arr.push(i);
    }
    return arr;
});

// ===== dropify =====
watch(
    () => form.is_medical,
    (newVal) => {
        if (newVal) {
            setTimeout(() => initDropify(), 100);
            setTimeout(() => editDropify(), 100);
        }
    }
);

watch(
    () => editForm.is_medical,
    (newVal) => {
        if (newVal) {
            setTimeout(() => initDropify(), 100);
            setTimeout(() => editDropify(), 100);
        }
    }
);

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
    $(".image").dropify({
        defaultFile: editForm.medical_excuse_file || "",
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
</script>

<template>
    <Layout>
        <PageHeader title="All Leave" pageTitle="Leave" />

        <BRow>
            <BCol lg="12">
                <BCard no-body id="leavesList">
                    <BCardHeader class="border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                All Leave
                            </h5>
                            <div class="flex-shrink-0">
                                <div class="d-flex flex-wrap gap-2">
                                    <BButton
                                        variant="soft-danger"
                                        class="me-1"
                                        id="remove-actions"
                                    >
                                        <i class="ri-delete-bin-2-line"></i>
                                    </BButton>
                                    <BButton
                                        variant="danger"
                                        class="add-btn"
                                        @click="toggleModal"
                                    >
                                        <i
                                            class="ri-add-line align-bottom me-1"
                                        ></i>
                                        Create Leave
                                    </BButton>
                                </div>
                            </div>
                        </div>
                    </BCardHeader>

                    <BCardBody
                        class="border border-dashed border-end-0 border-start-0"
                    >
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
                                        <input
                                            type="text"
                                            class="form-control search bg-light border-light"
                                            placeholder="Search for leaves..."
                                            v-model="search"
                                        />
                                        <i
                                            class="ri-search-line search-icon"
                                        ></i>
                                    </div>
                                </BCol>
                            </BRow>
                        </BForm>
                    </BCardBody>

                    <BCardBody>
                        <div class="table-responsive table-card mb-4">
                            <table
                                class="table align-middle table-nowrap mb-0"
                                id="leavesTable"
                            >
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th
                                            @click="sortBy('id')"
                                            style="cursor: pointer"
                                        >
                                            ID
                                            <i
                                                :class="[
                                                    'ms-1',
                                                    sortKey === 'id'
                                                        ? sortOrder === 'asc'
                                                            ? 'bx bxs-sort-alt'
                                                            : 'bx bxs-sort-alt'
                                                        : '',
                                                ]"
                                            ></i>
                                        </th>
                                        <th
                                            @click="sortBy('name')"
                                            style="cursor: pointer"
                                        >
                                            Name
                                            <i
                                                :class="[
                                                    'ms-1',
                                                    sortKey === 'name'
                                                        ? sortOrder === 'asc'
                                                            ? 'bx bxs-sort-alt'
                                                            : 'bx bxs-sort-alt'
                                                        : '',
                                                ]"
                                            ></i>
                                        </th>
                                        <th
                                            @click="sortBy('leave_type')"
                                            style="cursor: pointer"
                                        >
                                            Leave Type
                                            <i
                                                :class="[
                                                    'ms-1',
                                                    sortKey === 'leave_type'
                                                        ? sortOrder === 'asc'
                                                            ? 'bx bxs-sort-alt'
                                                            : 'bx bxs-sort-alt'
                                                        : '',
                                                ]"
                                            ></i>
                                        </th>
                                        <th
                                            @click="sortBy('start_date')"
                                            style="cursor: pointer"
                                        >
                                            Start Date
                                            <i
                                                :class="[
                                                    'ms-1',
                                                    sortKey === 'start_date'
                                                        ? sortOrder === 'asc'
                                                            ? 'bx bxs-sort-alt'
                                                            : 'bx bxs-sort-alt'
                                                        : '',
                                                ]"
                                            ></i>
                                        </th>
                                        <th
                                            @click="sortBy('end_date')"
                                            style="cursor: pointer"
                                        >
                                            End Date
                                            <i
                                                :class="[
                                                    'ms-1',
                                                    sortKey === 'end_date'
                                                        ? sortOrder === 'asc'
                                                            ? 'bx bxs-sort-alt'
                                                            : 'bx bxs-sort-alt'
                                                        : '',
                                                ]"
                                            ></i>
                                        </th>
                                        <th
                                            @click="sortBy('status')"
                                            style="cursor: pointer"
                                        >
                                            Status
                                            <i
                                                :class="[
                                                    'ms-1',
                                                    sortKey === 'status'
                                                        ? sortOrder === 'asc'
                                                            ? 'bx bxs-sort-alt'
                                                            : 'bx bxs-sort-alt'
                                                        : '',
                                                ]"
                                            ></i>
                                        </th>
                                        <th
                                            @click="sortBy('reason')"
                                            style="cursor: pointer"
                                        >
                                            Reason
                                            <i
                                                :class="[
                                                    'ms-1',
                                                    sortKey === 'reason'
                                                        ? sortOrder === 'asc'
                                                            ? 'bx bxs-sort-alt'
                                                            : 'bx bxs-sort-alt'
                                                        : '',
                                                ]"
                                            ></i>
                                        </th>
                                        <th style="cursor: pointer">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
                                    <tr
                                        v-for="(
                                            leave, index
                                        ) in paginatedleaves"
                                        :key="index"
                                    >
                                        <td>{{ leave.id }}</td>
                                        <td>{{ leave.user.name }}</td>
                                        <td>{{ leave.leave_type }}</td>
                                        <td>
                                            {{
                                                new Date(
                                                    leave.start_date
                                                ).toLocaleDateString("en-US", {
                                                    year: "numeric",
                                                    month: "short",
                                                    day: "numeric",
                                                })
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                new Date(
                                                    leave.end_date
                                                ).toLocaleDateString("en-US", {
                                                    year: "numeric",
                                                    month: "short",
                                                    day: "numeric",
                                                })
                                            }}
                                        </td>
                                        <td>
                                            <BButton v-if="leave.status == 'pending'"
                                                variant=" px-3 py-1"
                                                class="add-btn" style="background-color: #F3F6F9; color: #878A99; font-weight: 500;">
                                                {{ leave.status }}
                                            </BButton>
                                            <BButton v-if="leave.status == 'approved'"
                                                variant=" px-3 py-1"
                                                class="add-btn" style="background-color: #DFF2E3; color: #28A987; font-weight: 500;">
                                                {{ leave.status }}
                                            </BButton>
                                            <BButton v-if="leave.status == 'reject'"
                                                variant="px-3 py-1"
                                                class="add-btn" style="background-color: #FCF2F2; color: #CA2026; font-weight: 500;">
                                                Rejected
                                            </BButton>
                                        </td>
                                        <td>{{ leave.reason.length > 20 ? leave.reason.slice(0, 20) + '...' : leave.reason }}</td>
                                        <td class="d-flex gap-2">
                                            <BButton
                                                variant="danger px-3"
                                                class="add-btn"
                                                @click="toggleEditModal(leave)"
                                            >
                                                Edit
                                            </BButton>
                                            <BButton
                                                variant="info px-3"
                                                class="add-btn"
                                                @click="toggleViewModal(leave)"
                                            >
                                                View
                                            </BButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div
                                class="noresult"
                                v-if="paginatedleaves.length < 1"
                            >
                                <div class="text-center p-4">
                                    <h5 class="mt-2">Sorry! No Result Found</h5>
                                    <p class="text-muted mb-0">
                                        Try adjusting your search or filters.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-end">
                            <div class="pagination-wrap hstack gap-2">
                                <BLink
                                    class="page-item pagination-prev"
                                    href="#"
                                    :disabled="page <= 1"
                                    @click.prevent="
                                        page = Math.max(1, page - 1)
                                    "
                                >
                                    Previous
                                </BLink>
                                <ul class="pagination listjs-pagination mb-0">
                                    <li
                                        v-for="(pageNumber, index) in pages"
                                        :key="index"
                                        :class="{ active: pageNumber === page }"
                                        @click="page = pageNumber"
                                    >
                                        <BLink class="page" href="#">{{
                                            pageNumber
                                        }}</BLink>
                                    </li>
                                </ul>
                                <BLink
                                    class="page-item pagination-next"
                                    href="#"
                                    :disabled="page >= totalPages"
                                    @click.prevent="
                                        page = Math.min(totalPages, page + 1)
                                    "
                                >
                                    Next
                                </BLink>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>

        <!-- leave list modal -->
        <BModal
            v-model="leaveListModal"
            id="showmodal"
            modal-class="zoomIn"
            hide-footer
            header-class="p-3 bg-info-subtle leaveModal"
            class="v-modal-custom"
            centered
            size="lg"
            :title="dataEdit ? 'Edit leave' : 'Add leave'"
        >
            <BForm id="leaveForm" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- Leave Type -->
                    <BCol lg="6">
                        <label for="leave_type" class="form-label"
                            >Leave Type</label
                        >
                        <select
                            id="leave_type"
                            class="form-select shadow-none"
                            v-model="form.leave_type"
                        >
                            <option value="">Select Type</option>
                            <option value="annual">Annual</option>
                            <option value="sick">Sick</option>
                            <option value="emergency">Emergency</option>
                        </select>
                        <div
                            class="invalid-feedback"
                            v-if="submitted && !form.leave_type"
                        >
                            Please select a leave type.
                        </div>
                    </BCol>

                    <!-- Start Date -->
                    <BCol lg="6">
                        <label for="start_date" class="form-label"
                            >Start Date</label
                        >
                        <input
                            type="date"
                            id="start_date"
                            class="form-control"
                            v-model="form.start_date"
                            :class="{
                                'is-invalid': submitted && !form.start_date,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please select start date.
                        </div>
                    </BCol>

                    <!-- End Date -->
                    <BCol lg="6">
                        <label for="end_date" class="form-label"
                            >End Date</label
                        >
                        <input
                            type="date"
                            id="end_date"
                            class="form-control"
                            v-model="form.end_date"
                            :class="{
                                'is-invalid': submitted && !form.end_date,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please select end date.
                        </div>
                    </BCol>

                    <!-- Is Medical -->
                    <BCol lg="6">
                        <label class="form-label d-block"
                            >Is Medical Leave?</label
                        >
                        <div class="form-check form-switch">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="is_medical"
                                v-model="form.is_medical"
                            />
                            <label class="form-check-label" for="is_medical">
                                {{ form.is_medical ? "Yes" : "No" }}
                            </label>
                        </div>
                    </BCol>

                    <!-- Medical File Upload -->
                    <BCol lg="12" v-if="form.is_medical">
                        <label for="medical_excuse_file" class="form-label"
                            >Medical Excuse File</label
                        >
                        <input
                            type="file"
                            id="medical_excuse_file"
                            class="dropify"
                            data-height="150"
                            @change="handleFileUpload($event, false)"
                        />
                    </BCol>

                    <!-- Reason -->
                    <BCol lg="12">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea
                            id="reason"
                            class="form-control"
                            rows="3"
                            placeholder="Enter reason for leave"
                            v-model="form.reason"
                        ></textarea>
                    </BCol>
                </BRow>

                <!-- Modal Footer -->
                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton type="button" variant="light" @click="closeModal">
                        Close
                    </BButton>
                    <BButton
                        type="submit"
                        variant="success"
                        @click="handleSubmit"
                    >
                        {{ dataEdit ? "Update" : "Add Leave" }}
                    </BButton>
                </div>
            </BForm>
        </BModal>

        <!-- Edit leave modal -->
        <BModal
            v-model="leaveEditListModal"
            id="editmodal"
            modal-class="zoomIn"
            hide-footer
            header-class="p-3 bg-info-subtle leaveModal"
            class="v-modal-custom"
            centered
            size="lg"
            :title="'Edit leave'"
        >
            <BForm id="editform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- Leave Type -->
                    <BCol lg="6">
                        <label for="leave_type" class="form-label"
                            >Leave Type</label
                        >
                        <select
                            id="leave_type"
                            class="form-select shadow-none"
                            v-model="editForm.leave_type"
                        >
                            <option value="">Select Type</option>
                            <option value="annual">Annual</option>
                            <option value="sick">Sick</option>
                            <option value="emergency">Emergency</option>
                        </select>
                        <div
                            class="invalid-feedback"
                            v-if="submitted && !form.leave_type"
                        >
                            Please select a leave type.
                        </div>
                    </BCol>

                    <!-- Start Date -->
                    <BCol lg="6">
                        <label for="start_date" class="form-label"
                            >Start Date</label
                        >
                        <input
                            type="date"
                            id="start_date"
                            class="form-control"
                            v-model="editForm.start_date"
                            :class="{
                                'is-invalid': submitted && !form.start_date,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please select start date.
                        </div>
                    </BCol>

                    <!-- End Date -->
                    <BCol lg="6">
                        <label for="end_date" class="form-label"
                            >End Date</label
                        >
                        <input
                            type="date"
                            id="end_date"
                            class="form-control"
                            v-model="editForm.end_date"
                            :class="{
                                'is-invalid': submitted && !form.end_date,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please select end date.
                        </div>
                    </BCol>

                    <!-- Is Medical -->
                    <BCol lg="6">
                        <label class="form-label d-block"
                            >Is Medical Leave?</label
                        >
                        <div class="form-check form-switch">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="is_medical"
                                v-model="editForm.is_medical"
                            />
                            <label class="form-check-label" for="is_medical">
                                {{ editForm.is_medical ? "Yes" : "No" }}
                            </label>
                        </div>
                    </BCol>

                    <!-- Medical File Upload -->
                    <BCol lg="12" v-if="editForm.is_medical">
                        <label for="medical_excuse_file" class="form-label"
                            >Medical Excuse File</label
                        >
                        <input
                            type="file"
                            id="medical_excuse_file"
                            class="image"
                            data-height="150"
                            @change="handleEditFileUpload($event, false)"
                        />
                    </BCol>

                    <!-- Reason -->
                    <BCol lg="12">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea
                            id="reason"
                            class="form-control"
                            rows="3"
                            placeholder="Enter reason for leave"
                            v-model="editForm.reason"
                        ></textarea>
                    </BCol>
                </BRow>

                <!-- Modal Footer -->
                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton type="button" variant="light" @click="closeModal">
                        Close
                    </BButton>
                    <BButton
                        type="submit"
                        variant="success"
                        @click="handleUpdateSubmit"
                    >
                        {{ dataEdit ? "Update" : "Add Leave" }}
                    </BButton>
                </div>
            </BForm>
        </BModal>

        <!-- View leave modal -->
        <BModal
            v-model="leaveViewModal"
            id="editmodal"
            modal-class="zoomIn"
            hide-footer
            header-class="p-3 bg-info-subtle leaveModal"
            class="v-modal-custom"
            centered
            size="lg"
            :title="'View leave'"
        >
            <BRow class="g-3">
                <!-- Leave Type -->
                <BCol lg="6">
                    <label for="leave_type" class="form-label"
                        >Leave Type</label
                    >
                    <div class="form-control">
                        {{ editForm.leave_type }}
                    </div>
                </BCol>

                <!-- Start Date -->
                <BCol lg="6">
                    <label for="start_date" class="form-label"
                        >Start Date</label
                    >
                    <div class="form-control">
                        {{ editForm.start_date }}
                    </div>
                </BCol>

                <!-- End Date -->
                <BCol lg="6">
                    <label for="end_date" class="form-label">End Date</label>
                    <div class="form-control">
                        {{ editForm.end_date }}
                    </div>
                </BCol>

                <!-- Is Medical -->
                <BCol lg="6">
                    <label class="form-label d-block">Is Medical Leave?</label>
                    <div class="form-control">
                        {{ editForm.is_medical ? "Yes" : "No" }}
                    </div>
                </BCol>

                <!-- Medical File Upload -->
                <BCol lg="12" v-if="editForm.is_medical">
                    <label for="medical_excuse_file" class="form-label"
                        >Medical Excuse File</label
                    >
                    <div class="form-control text-center">
                        <img
                            :src="editForm.medical_excuse_file"
                            alt=""
                            class="w-25 rounded"
                        />
                    </div>
                </BCol>

                <!-- Reason -->
                <BCol lg="12">
                    <label for="reason" class="form-label">Reason</label>
                    <div class="form-control">
                        {{ editForm.reason }}
                    </div>
                </BCol>
            </BRow>

            <BForm id="editform" class="tablelist-form mt-3" autocomplete="off">
                <BCol lg="6">
                    <label for="status" class="form-label">Status</label>
                    <select
                        id="leave_type"
                        class="form-select shadow-none cursor-pointer"
                        v-model="viewForm.status"
                    >
                        <option selected>Select Type</option>
                        <option value="approved">Approved</option>
                        <option value="reject">Rejected</option>
                    </select>
                    <div
                        class="invalid-feedback"
                        v-if="submitted && !form.status"
                    >
                        Please select a leave type.
                    </div>
                </BCol>

                <!-- Modal Footer -->
                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton type="button" variant="light" @click="closeModal">
                        Close
                    </BButton>
                    <BButton
                        type="submit"
                        variant="success"
                        @click="handleStatusUpdateSubmit"
                    >
                        {{ dataEdit ? "Update Status" : "Add Leave" }}
                    </BButton>
                </div>
            </BForm>
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

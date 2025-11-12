<script setup>
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, reactive, computed } from "vue";
import { router, useForm } from "@inertiajs/vue3";

const props = defineProps({
    leaves: Array,
});

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
    employe_id: "",
    leave_type: "",
    reason: "",
    status: "",
    start_date: "",
    end_date: "",
    is_medical: "",
    medical_excuse_fil: "",
});

const editForm = useForm({
    employe_id: "",
    leave_type: "",
    reason: "",
    status: "",
    start_date: "",
    end_date: "",
    is_medical: "",
    medical_excuse_fil: "",
});

const handleSubmit = () => {
    if (dataEdit.value) {
        editForm.put(`/leave/update/${dataEdit.value.id}`, {
            onSuccess: () => {
                console.log("leave updated successfully");
                leaveEditListModal.value = false;
                editForm.reset();
            },
        });
    } else {
        form.post("/leave/store", {
            onSuccess: () => {
                console.log("leave added successfully");
                leaveListModal.value = false;
                form.reset();
            },
        });
    }
};

// ===== edit leave modal =====
const leaveEditListModal = ref(false);

const toggleEditModal = (leave) => {
    dataEdit.value = leave;
    editForm.employe_id = leave.employe_id || "";
    editForm.leave_type = leave.leave_type || "";
    editForm.reason = leave.reason || "";
    editForm.status = leave.status || "";
    editForm.start_date = leave.start_date || "";
    editForm.end_date = leave.end_date || "";
    editForm.is_medical = leave.is_medical || "";
    editForm.medical_excuse_fil = leave.medical_excuse_fil || "";
    leaveEditListModal.value = true;
};

// ===== PAGINATION + SORTING =====
const search = ref("");
const sortKey = ref("id");
const sortOrder = ref("asc");
const page = ref(1);
const perPage = ref(5);

const filteredleaves = computed(() =>
    props.leaves.filter(
        (leave) =>
            leave.name.toLowerCase().includes(search.value.toLowerCase()) ||
            leave.email.toLowerCase().includes(search.value.toLowerCase())
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

const viewEmployee = (leave) => {
    router.visit(`/employee/profile/${leave.id}`);
};
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
                            <BRow class="g-3">
                                <BCol xxl="2" sm="6">
                                    <select
                                        v-model="perPage"
                                        class="form-select shadow-none"
                                    >
                                        <option :value="5">5 per page</option>
                                        <option :value="10">10 per page</option>
                                        <option :value="20">20 per page</option>
                                    </select>
                                </BCol>
                                <BCol xxl="5" sm="12">
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
                                        <th scope="col" style="width: 40px">
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="checkAll"
                                                />
                                            </div>
                                        </th>
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
                                            @click="sortBy('email')"
                                            style="cursor: pointer"
                                        >
                                            Email
                                            <i
                                                :class="[
                                                    'ms-1',
                                                    sortKey === 'email'
                                                        ? sortOrder === 'asc'
                                                            ? 'bx bxs-sort-alt'
                                                            : 'bx bxs-sort-alt'
                                                        : '',
                                                ]"
                                            ></i>
                                        </th>
                                        <th
                                            @click="sortBy('position')"
                                            style="cursor: pointer"
                                        >
                                            Position
                                            <i
                                                :class="[
                                                    'ms-1',
                                                    sortKey === 'position'
                                                        ? sortOrder === 'asc'
                                                            ? 'bx bxs-sort-alt'
                                                            : 'bx bxs-sort-alt'
                                                        : '',
                                                ]"
                                            ></i>
                                        </th>
                                        <th
                                            @click="sortBy('department')"
                                            style="cursor: pointer"
                                        >
                                            Department
                                            <i
                                                :class="[
                                                    'ms-1',
                                                    sortKey === 'department'
                                                        ? sortOrder === 'asc'
                                                            ? 'bx bxs-sort-alt'
                                                            : 'bx bxs-sort-alt'
                                                        : '',
                                                ]"
                                            ></i>
                                        </th>
                                        <th
                                            @click="sortBy('salary')"
                                            style="cursor: pointer"
                                        >
                                            Salary
                                            <i
                                                :class="[
                                                    'ms-1',
                                                    sortKey === 'salary'
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
                                        <th scope="row">
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                />
                                            </div>
                                        </th>
                                        <td>{{ leave.id }}</td>
                                        <td>{{ leave.name }}</td>
                                        <td>{{ leave.email }}</td>
                                        <td>{{ leave.position }}</td>
                                        <td>{{ leave.department }}</td>
                                        <td>{{ leave.salary }}</td>
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
                                                size="sm"
                                                @click="viewEmployee(leave)"
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
                            class="form-control"
                            @change="handleFileUpload"
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
                    <!-- ID Number -->
                    <BCol lg="6">
                        <label for="edit-id-number" class="form-label"
                            >ID Number</label
                        >
                        <input
                            type="text"
                            id="edit-id-number"
                            class="form-control"
                            placeholder="Enter employee ID number"
                            v-model="editForm.id_number"
                        />
                    </BCol>

                    <!-- Position -->
                    <BCol lg="6">
                        <label for="edit-position" class="form-label"
                            >Position</label
                        >
                        <input
                            type="text"
                            id="edit-position"
                            class="form-control"
                            placeholder="Enter position"
                            v-model="editForm.position"
                        />
                    </BCol>

                    <!-- Department -->
                    <BCol lg="6">
                        <label for="edit-department" class="form-label"
                            >Department</label
                        >
                        <input
                            type="text"
                            id="edit-department"
                            class="form-control"
                            placeholder="Enter department"
                            v-model="editForm.department"
                        />
                    </BCol>

                    <!-- Employment Status -->
                    <BCol lg="6">
                        <label for="edit-employ-status" class="form-label"
                            >Employment Status</label
                        >
                        <select
                            id="edit-employ-status"
                            class="form-select shadow-none"
                            v-model="editForm.employ_status"
                        >
                            <option value="">Select status</option>
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Intern">Intern</option>
                        </select>
                    </BCol>

                    <!-- Salary -->
                    <BCol lg="6">
                        <label for="edit-salary" class="form-label"
                            >Salary</label
                        >
                        <input
                            type="number"
                            id="edit-salary"
                            class="form-control"
                            placeholder="Enter salary amount"
                            v-model="editForm.salary"
                        />
                    </BCol>

                    <!-- Allowances -->
                    <BCol lg="6">
                        <label for="edit-allowances" class="form-label"
                            >Allowances</label
                        >
                        <input
                            type="number"
                            id="edit-allowances"
                            class="form-control"
                            placeholder="Enter allowances amount"
                            v-model="editForm.allowances"
                        />
                    </BCol>

                    <!-- Annual Leave Balance -->
                    <BCol lg="6">
                        <label for="edit-annual-leave" class="form-label"
                            >Annual Leave Balance</label
                        >
                        <input
                            type="number"
                            id="edit-annual-leave"
                            class="form-control"
                            placeholder="Enter annual leave days"
                            v-model="editForm.annual_leave_balance"
                        />
                    </BCol>

                    <!-- Sick Leave Balance -->
                    <BCol lg="6">
                        <label for="edit-sick-leave" class="form-label"
                            >Sick Leave Balance</label
                        >
                        <input
                            type="number"
                            id="edit-sick-leave"
                            class="form-control"
                            placeholder="Enter sick leave days"
                            v-model="editForm.sick_leave_balance"
                        />
                    </BCol>
                </BRow>

                <!-- Modal Footer -->
                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton
                        type="button"
                        variant="light"
                        @click="leaveEditListModal = false"
                        id="closemodal"
                    >
                        Close
                    </BButton>
                    <BButton
                        type="submit"
                        variant="success"
                        id="edit-btn"
                        @click="handleSubmit"
                    >
                        Update
                    </BButton>
                </div>
            </BForm>
        </BModal>
    </Layout>
</template>

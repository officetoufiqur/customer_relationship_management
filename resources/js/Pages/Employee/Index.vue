<script setup>
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, reactive, computed } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const props = defineProps({
    users: Array,
    roles: Array,
});

//======= create user modal =======
const userListModal = ref(false);
const dataEdit = ref(false);
const submitted = ref(false);

const toggleModal = () => {
    dataEdit.value = false;
    userListModal.value = true;
};

// form models
const form = useForm({
    name: "",
    email: "",
    password: "",
    id_number: "",
    position: "",
    department: "",
    employ_status: "",
    salary: "",
    allowances: "",
    deductions: "",
    annual_leave_balance: "",
    sick_leave_balance: "",
    roles: [],
});

const editForm = useForm({
    name: "",
    email: "",
    password: "",
    id_number: "",
    position: "",
    department: "",
    employ_status: "",
    salary: "",
    allowances: "",
    deductions: "",
    annual_leave_balance: "",
    sick_leave_balance: "",
    roles: [],
});

const handleSubmit = () => {
    if (dataEdit.value) {
        // Update existing user
        editForm.put(`/employees/update/${dataEdit.value.id}`, {
            onSuccess: () => {
                Swal.fire("Updated!", "Employee updated successfully", "success");
                console.log("User updated successfully");
                userEditListModal.value = false;
                editForm.reset();
            },
        });
    } else {
        // Create new user
        form.post("/employees/store", {
            onSuccess: () => {
                Swal.fire("Created!", "Employee added successfully", "success");
                console.log("User added successfully");
                userListModal.value = false;
                form.reset();
            },
        });
    }
};

// ===== edit user modal =====
const userEditListModal = ref(false);

const toggleEditModal = (user) => {
    dataEdit.value = user;
    editForm.id_number = user.id_number || "";
    editForm.position = user.position || "";
    editForm.department = user.department || "";
    editForm.employ_status = user.employ_status || "";
    editForm.salary = user.salary || "";
    editForm.allowances = user.allowances || "";
    editForm.deductions = user.deductions || "";
    editForm.annual_leave_balance = user.annual_leave_balance || "";
    editForm.sick_leave_balance = user.sick_leave_balance || "";
    editForm.roles = user.roles.map((r) => r.id);
    userEditListModal.value = true;
};

// ===== PAGINATION + SORTING =====
const search = ref("");
const sortKey = ref("id");
const sortOrder = ref("asc");
const page = ref(1);
const perPage = ref(5);

const filteredUsers = computed(() =>
    props.users.filter(
        (user) =>
            user.name.toLowerCase().includes(search.value.toLowerCase()) ||
            user.email.toLowerCase().includes(search.value.toLowerCase())
    )
);

const sortedUsers = computed(() => {
    return [...filteredUsers.value].sort((a, b) => {
        const valA = a[sortKey.value]?.toString().toLowerCase();
        const valB = b[sortKey.value]?.toString().toLowerCase();

        if (valA < valB) return sortOrder.value === "asc" ? -1 : 1;
        if (valA > valB) return sortOrder.value === "asc" ? 1 : -1;
        return 0;
    });
});

const totalPages = computed(() =>
    Math.ceil(sortedUsers.value.length / perPage.value)
);

const paginatedUsers = computed(() => {
    const start = (page.value - 1) * perPage.value;
    const end = start + perPage.value;
    return sortedUsers.value.slice(start, end);
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

const viewEmployee = (user) => {
    router.visit(`/employee/profile/${user.id}`);
};
</script>

<template>
    <Layout>
        <PageHeader title="All Users" pageTitle="Users" />

        <BRow>
            <BCol lg="12">
                <BCard no-body id="usersList">
                    <BCardHeader class="border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                All users
                            </h5>
                            <div class="flex-shrink-0">
                                <div class="d-flex flex-wrap gap-2">
                                    <BButton variant="soft-danger" class="me-1" id="remove-actions">
                                        <i class="ri-delete-bin-2-line"></i>
                                    </BButton>
                                    <BButton variant="danger" class="add-btn" @click="toggleModal">
                                        <i class="ri-add-line align-bottom me-1"></i>
                                        Create user
                                    </BButton>
                                </div>
                            </div>
                        </div>
                    </BCardHeader>

                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <BForm>
                            <BRow class="g-3 justify-content-between">
                                <BCol xxl="2" sm="12" class="d-flex align-items-center gap-2">
                                    <select v-model="perPage"  class="form-select shadow-none w-auto cursor-pointer"
                                        style="background-color: #f3f6f9">
                                        <option :value="5">5</option>
                                        <option :value="10">10</option>
                                        <option :value="20">20</option>
                                    </select>
                                    <span>Entries per page</span>
                                </BCol>
                                <BCol xxl="4" sm="12">
                                    <div class="search-box">
                                        <input type="text" class="form-control search bg-light border-light"
                                            placeholder="Search for users..." v-model="search" />
                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                </BCol>
                            </BRow>
                        </BForm>
                    </BCardBody>

                    <BCardBody>
                        <div class="table-responsive table-card mb-4">
                            <table class="table align-middle table-nowrap mb-0" id="usersTable">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th scope="col" style="width: 40px">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="checkAll" />
                                            </div>
                                        </th>
                                        <th @click="sortBy('id')" style="cursor: pointer">
                                            ID
                                            <i :class="[
                                                'ms-1',
                                                sortKey === 'id'
                                                    ? sortOrder === 'asc'
                                                        ? 'bx bxs-sort-alt'
                                                        : 'bx bxs-sort-alt'
                                                    : '',
                                            ]"></i>
                                        </th>
                                        <th @click="sortBy('name')" style="cursor: pointer">
                                            Name
                                            <i :class="[
                                                'ms-1',
                                                sortKey === 'name'
                                                    ? sortOrder === 'asc'
                                                        ? 'bx bxs-sort-alt'
                                                        : 'bx bxs-sort-alt'
                                                    : '',
                                            ]"></i>
                                        </th>
                                        <th @click="sortBy('email')" style="cursor: pointer">
                                            Email
                                            <i :class="[
                                                'ms-1',
                                                sortKey === 'email'
                                                    ? sortOrder === 'asc'
                                                        ? 'bx bxs-sort-alt'
                                                        : 'bx bxs-sort-alt'
                                                    : '',
                                            ]"></i>
                                        </th>
                                        <th @click="sortBy('position')" style="cursor: pointer">
                                            Position
                                            <i :class="[
                                                'ms-1',
                                                sortKey === 'position'
                                                    ? sortOrder === 'asc'
                                                        ? 'bx bxs-sort-alt'
                                                        : 'bx bxs-sort-alt'
                                                    : '',
                                            ]"></i>
                                        </th>
                                        <th @click="sortBy('department')" style="cursor: pointer">
                                            Department
                                            <i :class="[
                                                'ms-1',
                                                sortKey === 'department'
                                                    ? sortOrder === 'asc'
                                                        ? 'bx bxs-sort-alt'
                                                        : 'bx bxs-sort-alt'
                                                    : '',
                                            ]"></i>
                                        </th>
                                        <th @click="sortBy('salary')" style="cursor: pointer">
                                            Salary
                                            <i :class="[
                                                'ms-1',
                                                sortKey === 'salary'
                                                    ? sortOrder === 'asc'
                                                        ? 'bx bxs-sort-alt'
                                                        : 'bx bxs-sort-alt'
                                                    : '',
                                            ]"></i>
                                        </th>
                                        <th style="cursor: pointer">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
                                    <tr v-for="(user, index) in paginatedUsers" :key="index">
                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" />
                                            </div>
                                        </th>
                                        <td>{{ user.id }}</td>
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.email }}</td>
                                        <td>{{ user.position }}</td>
                                        <td>{{ user.department }}</td>
                                        <td>{{ user.salary }}</td>
                                        <td class="d-flex gap-2">
                                            <BButton variant="danger px-3" class="add-btn"
                                                @click="toggleEditModal(user)">
                                                Edit
                                            </BButton>
                                            <BButton variant="info px-3" size="sm" @click="viewEmployee(user)">
                                                View
                                            </BButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="noresult" v-if="paginatedUsers.length < 1">
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
                                <BLink class="page-item pagination-prev" href="#" :disabled="page <= 1" @click.prevent="
                                    page = Math.max(1, page - 1)
                                    ">
                                    Previous
                                </BLink>
                                <ul class="pagination listjs-pagination mb-0">
                                    <li v-for="(pageNumber, index) in pages" :key="index"
                                        :class="{ active: pageNumber === page }" @click="page = pageNumber">
                                        <BLink class="page" href="#">{{
                                            pageNumber
                                        }}</BLink>
                                    </li>
                                </ul>
                                <BLink class="page-item pagination-next" href="#" :disabled="page >= totalPages"
                                    @click.prevent="
                                        page = Math.min(totalPages, page + 1)
                                        ">
                                    Next
                                </BLink>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>

        <!-- user list modal -->
        <BModal v-model="userListModal" id="showmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle userModal" class="v-modal-custom" centered size="lg"
            :title="dataEdit ? 'Edit user' : 'Add user'">
            <BForm id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- Name -->
                    <BCol lg="6">
                        <label for="name-field" class="form-label">Full Name</label>
                        <input type="text" id="name-field" class="form-control" placeholder="Enter full name"
                            v-model="form.name" :class="{ 'is-invalid': submitted && !form.name }" />
                        <div class="invalid-feedback">Please enter a name.</div>
                    </BCol>

                    <!-- Email -->
                    <BCol lg="6">
                        <label for="email-field" class="form-label">Email</label>
                        <input type="email" id="email-field" class="form-control" placeholder="Enter email address"
                            v-model="form.email" :class="{ 'is-invalid': submitted && !form.email }" />
                        <div class="invalid-feedback">
                            Please enter an email.
                        </div>
                    </BCol>

                    <!-- Password -->
                    <BCol lg="6">
                        <label for="password-field" class="form-label">Password</label>
                        <input type="password" id="password-field" class="form-control" placeholder="Enter password"
                            v-model="form.password" :class="{
                                'is-invalid': submitted && !form.password,
                            }" />
                        <div class="invalid-feedback">
                            Please enter a password.
                        </div>
                    </BCol>

                    <!-- ID Number -->
                    <BCol lg="6">
                        <label for="id-number" class="form-label">ID Number</label>
                        <input type="text" id="id-number" class="form-control" placeholder="Enter employee ID number"
                            v-model="form.id_number" />
                    </BCol>

                    <!-- Position -->
                    <BCol lg="6">
                        <label for="position" class="form-label">Position</label>
                        <input type="text" id="position" class="form-control" placeholder="Enter position"
                            v-model="form.position" />
                    </BCol>

                    <!-- Department -->
                    <BCol lg="6">
                        <label for="department" class="form-label">Department</label>
                        <input type="text" id="department" class="form-control" placeholder="Enter department"
                            v-model="form.department" />
                    </BCol>

                    <!-- Employment Status -->
                    <BCol lg="6">
                        <label for="employ-status" class="form-label">Employment Status</label>
                        <select id="employ-status" class="form-select shadow-none" v-model="form.employ_status">
                            <option value="">Select status</option>
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Intern">Intern</option>
                        </select>
                    </BCol>

                    <!-- Salary -->
                    <BCol lg="6">
                        <label for="salary" class="form-label">Salary</label>
                        <input type="number" id="salary" class="form-control" placeholder="Enter salary amount"
                            v-model="form.salary" />
                    </BCol>

                    <!-- Allowances -->
                    <BCol lg="6">
                        <label for="allowances" class="form-label">Allowances</label>
                        <input type="number" id="allowances" class="form-control" placeholder="Enter allowances amount"
                            v-model="form.allowances" />
                    </BCol>

                    <!-- Deductions -->
                    <!-- <BCol lg="6">
                        <label for="deductions" class="form-label"
                            >Deductions</label
                        >
                        <input
                            type="number"
                            id="deductions"
                            class="form-control"
                            placeholder="Enter deductions amount"
                            v-model="form.deductions"
                        />
                    </BCol> -->

                    <!-- Annual Leave Balance -->
                    <BCol lg="6">
                        <label for="annual-leave" class="form-label">Annual Leave Balance</label>
                        <input type="number" id="annual-leave" class="form-control"
                            placeholder="Enter annual leave days" v-model="form.annual_leave_balance" />
                    </BCol>

                    <!-- Sick Leave Balance -->
                    <BCol lg="12">
                        <label for="sick-leave" class="form-label">Sick Leave Balance</label>
                        <input type="number" id="sick-leave" class="form-control" placeholder="Enter sick leave days"
                            v-model="form.sick_leave_balance" />
                    </BCol>

                    <!-- Roles -->
                    <BCol lg="6">
                        <label for="sick-leave" class="form-label">Roles</label>
                        <div class="row g-2">
                            <label class="col-md-4 d-flex align-items-center gap-2" v-for="role in props.roles" :key="role.id">
                                <input type="checkbox" :value="role.id" v-model="form.roles" class="form-check-input cursor-pointer" />
                                <span class="text-gray-700 cursor-pointer">{{ role.name }}</span>
                            </label>
                        </div>
                        <div class="invalid-feedback text-red-500 mt-1">
                            Please enter roles.
                        </div>
                    </BCol>

                </BRow>

                <!-- Modal Footer -->
                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton type="button" variant="light" @click="userListModal = false" id="closemodal">
                        Close
                    </BButton>
                    <BButton type="submit" variant="success" id="add-btn" @click="handleSubmit">
                        {{ dataEdit ? "Update" : "Add user" }}
                    </BButton>
                </div>
            </BForm>
        </BModal>

        <!-- Edit user modal -->
        <BModal v-model="userEditListModal" id="editmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle userModal" class="v-modal-custom" centered size="lg" :title="'Edit user'">
            <BForm id="editform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- ID Number -->
                    <BCol lg="6">
                        <label for="edit-id-number" class="form-label">ID Number</label>
                        <input type="text" id="edit-id-number" class="form-control"
                            placeholder="Enter employee ID number" v-model="editForm.id_number" />
                    </BCol>

                    <!-- Position -->
                    <BCol lg="6">
                        <label for="edit-position" class="form-label">Position</label>
                        <input type="text" id="edit-position" class="form-control" placeholder="Enter position"
                            v-model="editForm.position" />
                    </BCol>

                    <!-- Department -->
                    <BCol lg="6">
                        <label for="edit-department" class="form-label">Department</label>
                        <input type="text" id="edit-department" class="form-control" placeholder="Enter department"
                            v-model="editForm.department" />
                    </BCol>

                    <!-- Employment Status -->
                    <BCol lg="6">
                        <label for="edit-employ-status" class="form-label">Employment Status</label>
                        <select id="edit-employ-status" class="form-select shadow-none"
                            v-model="editForm.employ_status">
                            <option value="">Select status</option>
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Intern">Intern</option>
                        </select>
                    </BCol>

                    <!-- Salary -->
                    <BCol lg="6">
                        <label for="edit-salary" class="form-label">Salary</label>
                        <input type="number" id="edit-salary" class="form-control" placeholder="Enter salary amount"
                            v-model="editForm.salary" />
                    </BCol>

                    <!-- Allowances -->
                    <BCol lg="6">
                        <label for="edit-allowances" class="form-label">Allowances</label>
                        <input type="number" id="edit-allowances" class="form-control"
                            placeholder="Enter allowances amount" v-model="editForm.allowances" />
                    </BCol>

                    <!-- Annual Leave Balance -->
                    <BCol lg="6">
                        <label for="edit-annual-leave" class="form-label">Annual Leave Balance</label>
                        <input type="number" id="edit-annual-leave" class="form-control"
                            placeholder="Enter annual leave days" v-model="editForm.annual_leave_balance" />
                    </BCol>

                    <!-- Sick Leave Balance -->
                    <BCol lg="6">
                        <label for="edit-sick-leave" class="form-label">Sick Leave Balance</label>
                        <input type="number" id="edit-sick-leave" class="form-control"
                            placeholder="Enter sick leave days" v-model="editForm.sick_leave_balance" />
                    </BCol>

                    <!-- Roles -->
                    <BCol lg="6">
                        <label for="sick-leave" class="form-label">Roles</label>
                        <div class="row g-2">
                            <label class="col-md-4 d-flex align-items-center gap-2" v-for="role in props.roles" :key="role.id">
                                <input type="checkbox" :value="role.id" v-model="editForm.roles" class="form-check-input cursor-pointer" />
                                <span class="text-gray-700 cursor-pointer">{{ role.name }}</span>
                            </label>
                        </div>
                        <div class="invalid-feedback text-red-500 mt-1">
                            Please enter roles.
                        </div>
                    </BCol>
                </BRow>

                <!-- Modal Footer -->
                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton type="button" variant="light" @click="userEditListModal = false" id="closemodal">
                        Close
                    </BButton>
                    <BButton type="submit" variant="success" id="edit-btn" @click="handleSubmit">
                        Update
                    </BButton>
                </div>
            </BForm>
        </BModal>
    </Layout>
</template>

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
    roles: Array,
});

// ========== base form ==========
const baseForm = ref({
    id: "",
    name: "",
});

const form = useForm({ ...baseForm.value });
const editForm = useForm({ ...baseForm.value });

// ========== show all data ==========
const tableHeaders = [
    { key: "id", label: "ID", sortable: true },
    { key: "name", label: "Name", sortable: true },
    { key: "actions", label: "Actions", sortable: false },
];

const searchQuery = ref("");

const filteredData = computed(() => {
    let data = props.roles ?? [];

    if (searchQuery.value) {
        const s = searchQuery.value.toLowerCase();

        data = data.filter((c) => {
            return (
                String(c.name ?? "").toLowerCase().includes(s)
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

const roleCreateModal = () => {
    toggleCreateModal.value = true;
};

const handleSubmit = async () => {
    if (!form.name) {
        Swal.fire(
            "Validation Error",
            "Please fill all required fields",
            "warning"
        );
        return;
    }

    form.post("/role/store", {
        onSuccess: () => {
            Swal.fire("Created!", "role added successfully", "success");
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

const editModal = (role) => {
    toggleEditModal.value = true;

    editForm.id = role.id;
    editForm.name = role.name;
};

const handleUpdateSubmit = () => {
    editForm.post(`/role/update/${editForm.id}`, {
        onSuccess: () => {
            Swal.fire("Updated!", "role updated successfully", "success");
            toggleEditModal.value = false;
            editForm.reset();
        },
    });
};

// ============== delete ==============
const deleteData = (role) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(`/role/destroy/${role.id}`, {
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
        <PageHeader title="List View" pageTitle="Roles" />

        <BRow>
            <BCol lg="12">
                <BCard no-body>
                    <!-- HEADER -->
                    <BCardHeader class="border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                All Roles
                            </h5>
                            <div class="d-flex gap-2">
                                <BButton variant="danger" @click="roleCreateModal">
                                    <i class="ri-add-line me-1"></i>
                                    Create Roles
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
                                        <th style="width: 2px">
                                            <input class="form-check-input" type="checkbox" />
                                        </th>
                                        <th v-for="header in tableHeaders" :key="header.key" :data-sort="header.key"
                                            :style="{
                                                width: header.width || '100px',
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
                                    <tr v-for="(role, i) in resultQuery" :key="i">
                                        <td>
                                            <input class="form-check-input" type="checkbox" />
                                        </td>

                                        <td>{{ role.id }}</td>
                                        <td>{{ role.name }}</td>
                                        <td class="d-flex gap-2">
                                            <BButton variant="success px-3" @click="editModal(role)">
                                                Edit
                                            </BButton>
                                            <BButton variant="danger px-3" @click="deleteData(role)">
                                                Delete
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

        <!-- role create modal -->
        <BModal v-model="toggleCreateModal" id="showmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle roleModal" class="v-modal-custom" centered size="lg"
            :title="'Add role'">
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- name -->
                    <BCol lg="12">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" placeholder="Enter name" v-model="form.name"
                            :class="{
                                'is-invalid': submitted && !form.name,
                            }" />
                        <div class="invalid-feedback">
                            Please enter name.
                        </div>
                    </BCol>
                </BRow>

                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton type="button" variant="light" @click="toggleCreateModal = false">
                        Close
                    </BButton>

                    <BButton type="submit" variant="success" @click="handleSubmit">
                        role Create
                    </BButton>
                </div>
            </BFrom>
        </BModal>

        <!-- role edit modal -->
        <BModal v-model="toggleEditModal" id="showmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle roleModal" class="v-modal-custom" centered size="lg"
            :title="'Edit role'">
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- name -->
                    <BCol lg="12">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" placeholder="Enter name" v-model="editForm.name"
                            :class="{
                                'is-invalid': submitted && !editForm.name,
                            }" />
                        <div class="invalid-feedback">
                            Please enter name.
                        </div>
                    </BCol>
                </BRow>

                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton type="button" variant="light" @click="toggleEditModal = false">
                        Close
                    </BButton>

                    <BButton type="submit" variant="success" @click="handleUpdateSubmit">
                        Update role
                    </BButton>
                </div>
            </BFrom>
        </BModal>
    </Layout>
</template>

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
});

// ========== base form ==========
const baseForm = ref({
    id: "",
    company_id: "",
    amount: "",
    recipient_name: "",
    payment_method: "",
    client_paid: "",
    reason: ""
});

const form = useForm({ ...baseForm.value });
const editForm = useForm({ ...baseForm.value });

// ========== show all data ==========
const tableHeaders = [
    { key: "id", label: "ID", sortable: true },
    { key: "company_name", label: "Company Name", sortable: true },
    { key: "recipient_name", label: "Recipient Name", sortable: true },
    { key: "payment_method", label: "Payment Method", sortable: true },
    { key: "amount", label: "Amount", sortable: true },
    { key: "client_paid", label: "Client Paid", sortable: true },
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


// ============== Create ==============
const toggleCreateModal = ref(false);

const expenseCreateModal = () => {
    toggleCreateModal.value = true;
};

const handleSubmit = async () => {
    if (!form.company_name || form.amount || !form.company_id || !form.payment_method || !form.amount || !form.client_paid) {
        Swal.fire(
            "Validation Error",
            "Please fill all required fields",
            "warning"
        );
        return;
    }

    form.post("/expense/store", {
        onSuccess: () => {
            Swal.fire("Created!", "Expense added successfully", "success");
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

const editModal = (expense) => {
    toggleEditModal.value = true;

    editForm.id = expense.id;
    editForm.company_name = expense.company_name;
    editForm.recipient_name = expense.recipient_name;
    editForm.payment_method = expense.payment_method;
    editForm.amount = expense.amount;
    editForm.client_paid = expense.client_paid;
    editForm.reason = expense.reason;
};

const handleUpdateSubmit = () => {
    editForm.post(`/expense/update/${editForm.id}`, {
        onSuccess: () => {
            Swal.fire("Updated!", "Expense updated successfully", "success");
            toggleEditModal.value = false;
            editForm.reset();
        },
    });
};

// ============== delete ==============
const deleteData = (expense) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(`/expense/destroy/${expense.id}`, {
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

const viewModal = (expense) => {
    toggleViewModal.value = true;
    editForm.id = expense.id;
    editForm.company_name = expense.company_name;
    editForm.recipient_name = expense.recipient_name;
    editForm.payment_method = expense.payment_method;
    editForm.amount = expense.amount;
    editForm.client_paid = expense.client_paid;
    editForm.reason = expense.reason;
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
        <PageHeader title="List View" pageTitle="Expenses" />

        <BRow>
            <BCol lg="12">
                <BCard no-body>
                    <!-- HEADER -->
                    <BCardHeader class="border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                All Expense
                            </h5>
                            <div class="d-flex gap-2">
                                <BButton variant="danger" @click="expenseCreateModal">
                                    <i class="ri-add-line me-1"></i>
                                    Create Expense Request
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
                                        <td class="d-flex gap-2">
                                            <BButton variant="success px-3" @click="editModal(expense)">
                                                Edit
                                            </BButton>

                                            <BButton variant="danger px-3" @click="deleteData(expense)">
                                                Delete
                                            </BButton>

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

        <!-- expense create modal -->
        <BModal v-model="toggleCreateModal" id="showmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle expenseModal" class="v-modal-custom" centered size="lg"
            :title="'Add Expense'">
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- company Name -->
                    <BCol lg="6">
                        <label class="form-label">Company Name</label>
                        <select class="form-control" v-model="form.company_id" :class="{
                            'is-invalid': submitted && !form.company_id,
                        }">
                            <option selected>Select company Name</option>
                            <option v-for="company in companys" :key="company.id" :value="company.id">{{ company.name }}
                            </option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter company Name.
                        </div>
                    </BCol>

                    <!-- amount -->
                    <BCol lg="6">
                        <label class="form-label">Amount</label>
                        <input type="number" class="form-control" placeholder="Enter Amount" v-model="form.amount"
                            :class="{
                                'is-invalid': submitted && !form.amount,
                            }" />
                        <div class="invalid-feedback">
                            Please enter amount.
                        </div>
                    </BCol>

                    <!-- recipient Name -->
                    <BCol lg="6">
                        <label class="form-label">Recipient Name</label>
                        <select class="form-control" v-model="form.recipient_name" :class="{
                            'is-invalid': submitted && !form.recipient_name,
                        }">
                            <option value="">Select recipient Name</option>
                            <option value="vendor">Vendor</option>
                            <option value="government">Government</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter recipient Name.
                        </div>
                    </BCol>

                    <!-- payment_method -->
                    <BCol lg="6">
                        <label class="form-label">Payment Method</label>
                        <select class="form-control" v-model="form.payment_method" :class="{
                            'is-invalid': submitted && !form.payment_method,
                        }">
                            <option value="">Select payment method</option>
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer</option>
                            <option value="card">Card</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter payment method.
                        </div>
                    </BCol>

                    <!-- client_paid -->
                    <BCol lg="12">
                        <label class="form-label">Client Paid</label>
                        <select class="form-control" v-model="form.client_paid" :class="{
                            'is-invalid': submitted && !form.client_paid,
                        }">
                            <option value="">Select client paid</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter client paid.
                        </div>
                    </BCol>

                    <!-- Reason -->
                    <BCol lg="12">
                        <label class="form-label">Reason (Optional)</label>
                        <textarea name="" id="" cols="30" rows="5" class="form-control" v-model="form.reason"
                            placeholder="Enter your reason" :class="{
                                'is-invalid': submitted && !form.reason,
                            }"></textarea>
                        <div class="invalid-feedback">Please enter Reason.</div>
                    </BCol>
                </BRow>

                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton type="button" variant="light" @click="toggleCreateModal = false">
                        Close
                    </BButton>

                    <BButton type="submit" variant="success" @click="handleSubmit">
                        Expense Request
                    </BButton>
                </div>
            </BFrom>
        </BModal>

        <!-- expense edit modal -->
        <BModal v-model="toggleEditModal" id="showmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle expenseModal" class="v-modal-custom" centered size="lg"
            :title="'Edit expense'">
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- company Name -->
                    <BCol lg="6">
                        <label class="form-label">Company Name</label>
                        <select class="form-control" v-model="editForm.company_name" :class="{
                            'is-invalid': submitted && !editForm.company_name,
                        }">
                            <option selected>Select company Name</option>
                            <option v-for="company in companys" :key="company.id" :value="company.name">{{ company.name
                            }}
                            </option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter company Name.
                        </div>
                    </BCol>

                    <!-- amount -->
                    <BCol lg="6">
                        <label class="form-label">Amount</label>
                        <input type="number" class="form-control" placeholder="Enter Amount" v-model="editForm.amount"
                            :class="{
                                'is-invalid': submitted && !editForm.amount,
                            }" />
                        <div class="invalid-feedback">
                            Please enter amount.
                        </div>
                    </BCol>

                    <!-- recipient Name -->
                    <BCol lg="6">
                        <label class="form-label">Recipient Name</label>
                        <select class="form-control" v-model="editForm.recipient_name" :class="{
                            'is-invalid': submitted && !editForm.recipient_name,
                        }">
                            <option value="">Select recipient Name</option>
                            <option value="vendor">Vendor</option>
                            <option value="government">Government</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter recipient Name.
                        </div>
                    </BCol>

                    <!-- payment_method -->
                    <BCol lg="6">
                        <label class="form-label">Payment Method</label>
                        <select class="form-control" v-model="editForm.payment_method" :class="{
                            'is-invalid': submitted && !editForm.payment_method,
                        }">
                            <option value="">Select payment method</option>
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer</option>
                            <option value="card">Card</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter payment method.
                        </div>
                    </BCol>

                    <!-- client_paid -->
                    <BCol lg="12">
                        <label class="form-label">Client Paid</label>
                        <select class="form-control" v-model="editForm.client_paid" :class="{
                            'is-invalid': submitted && !editForm.client_paid,
                        }">
                            <option value="">Select client paid</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter client paid.
                        </div>
                    </BCol>

                    <!-- Reason -->
                    <BCol lg="12">
                        <label class="form-label">Reason (Optional)</label>
                        <textarea name="" id="" cols="30" rows="5" class="form-control" v-model="editForm.reason"
                            placeholder="Enter your reason" :class="{
                                'is-invalid': submitted && !editForm.reason,
                            }"></textarea>
                        <div class="invalid-feedback">Please enter Reason.</div>
                    </BCol>
                </BRow>

                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton type="button" variant="light" @click="toggleEditModal = false">
                        Close
                    </BButton>

                    <BButton type="submit" variant="success" @click="handleUpdateSubmit">
                        Update Expense
                    </BButton>
                </div>
            </BFrom>
        </BModal>

        <!-- expense view modal -->
        <BModal v-model="toggleViewModal" id="showmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle expenseModal" class="v-modal-custom" centered size="lg"
            :title="'View Expense Information'">
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="">
                    <!-- company Name -->
                    <BCol lg="6">
                        <label class="form-label">Company Name</label>
                        <p class="form-control">{{ editForm.company_name }}</p>
                    </BCol>

                    <!-- amount -->
                    <BCol lg="6">
                        <label class="form-label">Amount</label>
                        <p class="form-control">{{ editForm.amount }}</p>
                    </BCol>

                    <!-- recipient Name -->
                    <BCol lg="6">
                        <label class="form-label">Recipient Name</label>
                        <p class="form-control">{{ editForm.recipient_name }}</p>
                    </BCol>

                    <!-- payment_method -->
                    <BCol lg="6">
                        <label class="form-label">Payment Method</label>
                        <p class="form-control">{{ editForm.payment_method }}</p>
                    </BCol>

                    <!-- client_paid -->
                    <BCol lg="12">
                        <label class="form-label">Client Paid</label>
                        <p class="form-control">{{ editForm.client_paid }}</p>
                    </BCol>

                    <!-- Reason -->
                    <BCol lg="12">
                        <label class="form-label">Reason (Optional)</label>
                        <p class="form-control">{{ editForm.reason }}</p>
                    </BCol>
                </BRow>
            </BFrom>
        </BModal>
    </Layout>
</template>

<script setup>
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, computed, watch, onMounted, nextTick } from "vue";
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import { CountTo } from "vue3-count-to";
import Swal from "sweetalert2";
import $ from "jquery";
import { reactive } from "vue";
import { can } from "@/helpers/can";

const props = defineProps({
    balance: Array,
    flash: Object,
});

// ========== base form ==========
const form = useForm({
    id: "",
    type: "",
    opening_balance: "",
});

// ========== show all data ==========
const tableHeaders = [
    { key: "id", label: "ID", sortable: true },
    { key: "name", label: "Name", sortable: true },
    { key: "type", label: "Type", sortable: true },
    { key: "opening_balance", label: "Opening Balance", sortable: true },
    { key: "current_balance", label: "Current Balance", sortable: true }
];

const searchQuery = ref("");

const filteredData = computed(() => {
    let data = props.balance ?? [];

    if (searchQuery.value) {
        const s = searchQuery.value.toLowerCase();

        data = data.filter((c) => {
            return (
                String(c.name ?? "").toLowerCase().includes(s) ||
                String(c.type ?? "").toLowerCase().includes(s) ||
                String(c.current_balance ?? "").toLowerCase().includes(s) ||
                String(c.opening_balance ?? "").toLowerCase().includes(s)
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

const balanceCreateModal = () => {
    toggleCreateModal.value = true;
};

const handleSubmit = async () => {
    if (!form.type || !form.opening_balance) {
        Swal.fire(
            "Validation Error",
            "Please fill all required fields",
            "warning"
        );
        return;
    }

    form.post("/balance/store", {
        onSuccess: () => {
            Swal.fire("Created!", "Balance added successfully", "success");
            toggleCreateModal.value = false;
            form.reset();
        },
        onError: (errors) => {
            console.error(errors);
            Swal.fire("Error", "Creation failed", "error");
        },
    });
};

// ============== Create ==============
const transferForm = useForm({
    transfer_amount: "",
})
const toggleTransferModal = ref(false);

const balancetransferModal = () => {
    toggleTransferModal.value = true;
};

const handleTransferSubmit = async () => {
    if (!transferForm.transfer_amount) {
        Swal.fire(
            "Validation Error",
            "Please fill all required fields",
            "warning"
        );
        return;
    }

    transferForm.post("/balance/transfer", {
        onSuccess: () => {
            Swal.fire("Transferred!", "Balance transferred successfully", "success");
            toggleTransferModal.value = false;
            transferForm.reset();
        },
        onError: (errors) => {
            console.error(errors);
            Swal.fire("Error", errors.transfer_amount || "Transfer failed", "error");
        },
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
        <PageHeader title="List View" pageTitle="Balance" />
        <BRow>
            <BCol lg="12">
                <BCard no-body>
                    <!-- HEADER -->
                    <BCardHeader class="border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                All Balance
                            </h5>
                            <div class="d-flex gap-3">
                                <div class="d-flex gap-2">
                                    <BButton v-if="can('balance_create')" variant="warning"
                                        @click="balancetransferModal">
                                        <i class="ri-add-line me-1"></i>
                                        Transfer Money
                                    </BButton>
                                </div>
                                <div v-if="(!balance || balance.length <= 3)" class="d-flex gap-2">
                                    <BButton v-if="can('balance_create')" variant="danger" @click="balanceCreateModal">
                                        <i class="ri-add-line me-1"></i>
                                        Create Opening Balances
                                    </BButton>
                                </div>
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
                                    <tr v-for="(balance, i) in resultQuery" :key="i">
                                        <td>{{ balance.id }}</td>
                                        <td>{{ balance.name }}</td>
                                        <td>{{ balance.type }}</td>
                                        <td>{{ balance.opening_balance }}</td>
                                        <td>{{ balance.current_balance }}</td>
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

        <!-- balance create modal -->
        <BModal v-model="toggleCreateModal" id="showmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle balanceModal" class="v-modal-custom" centered size="lg"
            :title="'Add balance'">
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- Method Name -->
                    <BCol lg="6">
                        <label class="form-label">Method Name</label>
                        <select class="form-control" v-model="form.type" :class="{
                            'is-invalid': submitted && !form.type,
                        }">
                            <option value="">Select Method Name</option>
                            <option value="bank">Bank</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="cash">Cash</option>
                        </select>
                        <div class="invalid-feedback">
                            Please enter Method Name.
                        </div>
                    </BCol>

                    <!-- opening_balance -->
                    <BCol lg="6">
                        <label class="form-label">Opening Balance</label>
                        <input type="number" class="form-control" placeholder="Enter opening balance"
                            v-model="form.opening_balance" :class="{
                                'is-invalid': submitted && !form.opening_balance,
                            }" />
                        <div class="invalid-feedback">
                            Please enter opening_balance.
                        </div>
                    </BCol>
                </BRow>

                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton type="button" variant="light" @click="toggleCreateModal = false">
                        Close
                    </BButton>

                    <BButton type="submit" variant="success" @click="handleSubmit">
                        Create
                    </BButton>
                </div>
            </BFrom>
        </BModal>

        <!-- balance transfer modal -->
        <BModal v-model="toggleTransferModal" id="showmodal" modal-class="zoomIn" hide-footer
            header-class="p-3 bg-info-subtle balanceModal" class="v-modal-custom" centered size="lg"
            :title="'Transfer balance'">
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <p>From Bank to Credit Card</p>
                <BRow class="g-3">
                    <!-- transfer_amount -->
                    <BCol lg="6">
                        <label class="form-label">Transfer Balance</label>
                        <input type="number" class="form-control" placeholder="Enter transfer balance"
                            v-model="transferForm.transfer_amount" :class="{
                                'is-invalid': submitted && !transferForm.transfer_amount,
                            }" />
                        <div class="invalid-feedback">
                            Please enter transfer_amount.
                        </div>
                    </BCol>
                </BRow>

                <span v-if="props.flash.error" class="text-danger mt-5">
                    {{ props.flash.error }}
                </span>

                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton type="button" variant="light" @click="toggleTransferModal = false">
                        Close
                    </BButton>

                    <BButton type="submit" variant="success" @click="handleTransferSubmit">
                        Transfer Amount
                    </BButton>
                </div>
            </BFrom>
        </BModal>
    </Layout>
</template>

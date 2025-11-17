<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { Link, Head, useForm } from "@inertiajs/vue3";
import { CountTo } from "vue3-count-to";
import "@vueform/multiselect/themes/default.css";
import "flatpickr/dist/flatpickr.css";

import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Swal from "sweetalert2";
import axios from "axios";
import animationData from "@/Components/widgets/msoeawqm.json";
import animationData1 from "@/Components/widgets/gsqxdxog.json";
import Lottie from "@/Components/widgets/lottie.vue";
import simplebar from "simplebar-vue";

const props = defineProps({
    companys: Array,
});

// Data
const companyListModal = ref(false);
const companyEditModal = ref(false);
const deleteModal = ref(false);
const submitted = ref(false);
const dataEdit = ref(false);
const allcompany = ref([]);
const page = ref(1);
const pages = ref([]);
const searchQuery = ref(null);
const filterdate = ref(null);
const filtervalue = ref("All");

// Flatpickr config
const timeConfig = {
    enableTime: false,
    dateFormat: "Y-m-d",
};

// Lottie animations
const defaultOptions = { animationData };

// Event object (company Form Data)
const event = ref({
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

const baseForm = {
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
};

// Form state (used for both create & update)
const form = useForm({ ...baseForm });
const editForm = useForm({ ...baseForm });

// Computed properties
const displayedPosts = computed(() => {
    return paginate(props.companys);
});

const resultQuery = computed(() => {
    if (searchQuery.value) {
        const search = searchQuery.value.toLowerCase();
        return displayedPosts.value.filter((data) => {
            return (
                data.name.toLowerCase().includes(search) ||
                data.registration_number.toLowerCase().includes(search) ||
                data.license_type.toLowerCase().includes(search) ||
                data.expiry_date.toLowerCase().includes(search) ||
                data.services_provided.toLowerCase().includes(search) ||
                data.renewal_status.toLowerCase().includes(search) ||
                data.employee_name.toLowerCase().includes(search)
            );
        });
    }
    return displayedPosts.value;
});

// Watch
watch(allcompany, () => {
    setPages();
});

// Lifecycle
onMounted(() => {
    setPages();
    fetchcompanys();
});

// Methods
const handleAttachment = (eventData) => {
    event.value.attachment = Array.from(eventData.target.files);
};

const handleEditFileUpload = (e) => {
    editForm.attachment = Array.from(e.target.files);
};

// 🔹 Fetch all companys from backend (Laravel API)
const fetchcompanys = async () => {
    try {
        const res = await axios.get("/companys");
        allcompany.value = res.data;
    } catch (error) {
        console.error(error);
    }
};

// 🔹 Create or Update company
const handleSubmit = async () => {
    submitted.value = true;

    if (
        !event.value.name ||
        !event.value.registration_number ||
        !event.value.license_type
    ) {
        Swal.fire(
            "Validation Error",
            "Please fill all required fields",
            "warning"
        );
        return;
    }

    form.id = event.value.id;
    form.name = event.value.name;
    form.registration_number = event.value.registration_number;
    form.license_type = event.value.license_type;
    form.expiry_date = event.value.expiry_date;
    form.services_provided = event.value.services_provided;
    form.renewal_status = event.value.renewal_status;
    form.employee_name = event.value.employee_name;
    form.passport_number = event.value.passport_number;
    form.permit_expiry = event.value.permit_expiry;
    form.permit_type = event.value.permit_type;

    form.post("/companys/store", {
        onSuccess: (res) => {
            Swal.fire("Created!", "company added successfully", "success");
            companyListModal.value = false;
            form.reset();
        },
        onError: (errors) => {
            console.error(errors);
            Swal.fire("Error", "Creation failed", "error");
        },
    });
};

// 🔹 Open Add Modal
const toggleModal = () => {
    companyListModal.value = true;
    dataEdit.value = false;
};

// 🔹 Edit company
const editDetails = (company) => {
    companyEditModal.value = true;

    editForm.id = company.id;
    editForm.name = company.name;
    editForm.registration_number = company.registration_number;
    editForm.license_type = company.license_type;
    editForm.expiry_date = company.expiry_date;
    editForm.services_provided = company.services_provided;
    editForm.renewal_status = company.renewal_status;
    editForm.employee_name = company.employee_name;
    editForm.passport_number = company.passport_number;
    editForm.permit_expiry = company.permit_expiry;
    editForm.permit_type = company.permit_type;
};

const handleUpdateSubmit = () => {
    editForm.post(`/company/update/${editForm.id}`, {
        onSuccess: () => {
            Swal.fire("Updated!", "company updated successfully", "success");
            companyEditModal.value = false;
            editForm.reset();
        },
    });
};

// 🔹 Delete company
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
            form.delete(`/companys/destroy/${company.id}`, {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "company deleted successfully",
                        "success"
                    );
                    deleteModal.value = false;
                },
                onError: (error) => {
                    console.error(error);
                    Swal.fire("Error", "Something went wrong", "error");
                },
            });
        }
    });
};

// 🔹 Pagination
const perPage = ref(5);
const sortKey = ref("id");
const sortOrder = ref("asc");

const setPages = () => {
    let numberOfPages = Math.ceil(props.companys.length / perPage.value);
    pages.value = [];
    for (let i = 1; i <= numberOfPages; i++) pages.value.push(i);
};

// 🔹 Paginate companys
const paginate = (companys) => {
    let pageNum = page.value;
    let from = (pageNum - 1) * perPage.value;
    let to = pageNum * perPage.value;
    return companys.slice(from, to);
};
</script>

<template>
    <Layout>
        <PageHeader title="List View" pageTitle="companys" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="companysList">
                    <BCardHeader class="border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                All companys
                            </h5>
                            <div class="flex-shrink-0">
                                <div class="d-flex flex-wrap gap-2">
                                    <BButton
                                        variant="soft-danger"
                                        class="me-1"
                                        id="remove-actions"
                                        @click="deleteMultiple"
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
                                        Create company
                                    </BButton>
                                </div>
                            </div>
                        </div>
                    </BCardHeader>
                    <BCardBody
                        class="border border-dashed border-end-0 border-start-0"
                    >
                        <BFrom>
                            <BRow class="g-3 justify-content-between">
                                <BCol
                                    xxl="2"
                                    sm="6"
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
                                            type="text"
                                            class="form-control search bg-light border"
                                            placeholder="Search for companys or something..."
                                            v-model="searchQuery"
                                        />
                                        <i
                                            class="ri-search-line search-icon"
                                        ></i>
                                    </div>
                                </BCol>
                            </BRow>
                        </BFrom>
                    </BCardBody>
                    <BCardBody>
                        <div class="table-responsive table-card mb-4">
                            <table
                                class="table align-middle table-nowrap mb-0"
                                id="companysTable"
                            >
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th scope="col" style="width: 40px">
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="checkAll"
                                                    value="option"
                                                />
                                            </div>
                                        </th>
                                        <th
                                            class="sort"
                                            data-sort="id"
                                            @click="onSort('id')"
                                        >
                                            ID
                                        </th>
                                        <th
                                            class="sort"
                                            data-sort="title"
                                            @click="onSort('title')"
                                        >
                                            Title
                                        </th>
                                        <th
                                            class="sort"
                                            data-sort="companys_name"
                                            @click="onSort('company')"
                                        >
                                            Description
                                        </th>
                                        <th
                                            class="sort"
                                            data-sort="assignedto"
                                            @click="onSort('subItem')"
                                        >
                                            Assigned To
                                        </th>
                                        <th
                                            class="sort"
                                            data-sort="due_date"
                                            @click="onSort('dueDate')"
                                        >
                                            Deadline
                                        </th>
                                        <th
                                            class="sort"
                                            data-sort="status"
                                            @click="onSort('status')"
                                        >
                                            Status
                                        </th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
                                    <tr
                                        v-for="(company, index) of resultQuery"
                                        :key="index"
                                    >
                                        <th scope="row">
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="chk_child"
                                                    value="option1"
                                                />
                                            </div>
                                        </th>
                                        <td class="id">
                                            <Link
                                                href="/apps/companys-details"
                                                class="fw-medium link-primary"
                                                >{{ company.id }}
                                            </Link>
                                        </td>
                                        <td class="project_name">
                                            <Link
                                                href="/apps/projects-overview"
                                                class="fw-medium link-primary"
                                                >{{ company.title }}
                                            </Link>
                                        </td>

                                        <td class="client_name">
                                            {{ company.description }}
                                        </td>

                                        <td class="due_date">
                                            <div class="avatar-group">
                                                <a
                                                    v-for="user in company.company_users"
                                                    :key="user.id"
                                                    href="javascript:void(0);"
                                                    class="avatar-group-item"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    :title="
                                                        user.assigned_to.name
                                                    "
                                                >
                                                    <!-- If user has image -->
                                                    <b-img
                                                        v-if="
                                                            user.assigned_to
                                                                .avatar
                                                        "
                                                        :src="
                                                            user.assigned_to
                                                                .avatar
                                                        "
                                                        alt=""
                                                        class="rounded-circle avatar-sm"
                                                    ></b-img>

                                                    <!-- If avatar not available show first letter -->
                                                    <div
                                                        v-else
                                                        class="avatar-sm"
                                                    >
                                                        <div
                                                            class="avatar-title rounded-circle bg-light text-primary"
                                                        >
                                                            {{
                                                                user.assigned_to.name.charAt(
                                                                    0
                                                                )
                                                            }}
                                                        </div>
                                                    </div>
                                                </a>

                                                <!-- Extra Users (More than 3 example) -->
                                                <a
                                                    v-if="
                                                        company.company_users
                                                            .length > 3
                                                    "
                                                    href="javascript:void(0);"
                                                    class="avatar-group-item"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="more"
                                                >
                                                    <div class="avatar-sm">
                                                        <div
                                                            class="avatar-title rounded-circle"
                                                        >
                                                            {{
                                                                company
                                                                    .company_users
                                                                    .length - 3
                                                            }}+
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </td>

                                        <td class="due_date">
                                            {{ company.deadline }}
                                        </td>
                                        <td class="status">
                                            <span
                                                class="badge text-uppercase"
                                                :class="{
                                                    'bg-secondary-subtle text-secondary':
                                                        company.status ==
                                                        'in_progress',
                                                    'bg-info-subtle text-info':
                                                        company.status ==
                                                        'delayed',
                                                    'bg-success-subtle text-success':
                                                        company.status ==
                                                        'completed',
                                                    'bg-warning-subtle text-warning':
                                                        company.status ==
                                                        'pending',
                                                }"
                                                >{{ company.status }}</span
                                            >
                                        </td>
                                        <td class="d-flex gap-2">
                                            <BButton
                                                variant="success px-3"
                                                class="add-btn"
                                                @click="editDetails(company)"
                                            >
                                                Edit
                                            </BButton>
                                            <BButton
                                                variant="danger px-3"
                                                class="add-btn"
                                                @click="deleteData(company)"
                                            >
                                                Delete
                                            </BButton>
                                            <Link
                                                :href="`/company/view/${company.id}`"
                                                class="btn btn-info"
                                            >
                                                View
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="noresult" v-if="resultQuery.length < 1">
                                <div class="text-center">
                                    <lottie
                                        colors="primary:#121331,secondary:#08a88a"
                                        :options="defaultOptions"
                                        :height="75"
                                        :width="75"
                                    />
                                    <h5 class="mt-2">Sorry! No Result Found</h5>
                                    <p class="text-muted mb-0">
                                        We've searched more than 200k+ companys
                                        We did not find any companys for you
                                        search.
                                    </p>
                                </div>
                            </div>
                        </div>

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
            v-model="companyListModal"
            id="showmodal"
            modal-class="zoomIn"
            hide-footer
            header-class="p-3 bg-info-subtle companyModal"
            class="v-modal-custom"
            centered
            size="lg"
            :title="dataEdit ? 'Edit Company' : 'Add Company'"
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
                            v-model="event.name"
                            :class="{ 'is-invalid': submitted && !event.name }"
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
                            v-model="event.registration_number"
                            :class="{
                                'is-invalid':
                                    submitted && !event.registration_number,
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
                            v-model="event.license_type"
                            :class="{
                                'is-invalid': submitted && !event.license_type,
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
                            v-model="event.expiry_date"
                            :class="{
                                'is-invalid': submitted && !event.expiry_date,
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
                            v-model="event.services_provided"
                            :class="{
                                'is-invalid':
                                    submitted && !event.services_provided,
                            }"
                        >
                            <option value="">Select Service</option>
                            <option value="acounting">Acounting</option>
                            <option value="vat_filing">Vat Filing</option>
                            <option value="commercial_address">Commercial Address</option>
                            <option value="bahraini_partner_service">Bahraini Partner Service</option>
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
                            v-model="event.renewal_status"
                            :class="{
                                'is-invalid':
                                    submitted && !event.renewal_status,
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
                            v-model="event.employee_name"
                            :class="{
                                'is-invalid': submitted && !event.employee_name,
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
                            v-model="event.passport_number"
                            :class="{
                                'is-invalid':
                                    submitted && !event.passport_number,
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
                            v-model="event.permit_expiry"
                            :class="{
                                'is-invalid': submitted && !event.permit_expiry,
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
                            v-model="event.permit_type"
                            :class="{
                                'is-invalid':
                                    submitted && !event.permit_type,
                            }"
                        >
                            <option value="">Select Permit Type</option>
                            <option value="work_permit">Work Permit</option>
                            <option value="visitor_permit">Visitor Permit</option>
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
                        @click="companyListModal = false"
                    >
                        Close
                    </BButton>

                    <BButton
                        type="submit"
                        variant="success"
                        @click="handleSubmit"
                    >
                        {{ dataEdit ? "Update" : "Add Company" }}
                    </BButton>
                </div>
            </BFrom>
        </BModal>

        <!-- company edit modal -->
        <BModal
            v-model="companyEditModal"
            id="showmodal"
            modal-class="zoomIn"
            hide-footer
            header-class="p-3 bg-info-subtle companyModal"
            class="v-modal-custom"
            centered
            size="lg"
            :title="dataEdit ? 'Edit company' : 'Add company'"
        >
            <BFrom id="editform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <BCol lg="12">
                        <label for="projectName-field" class="form-label"
                            >Title</label
                        >
                        <input
                            type="text"
                            id="projectName"
                            class="form-control"
                            placeholder="Title"
                            v-model="editForm.title"
                            :class="{
                                'is-invalid': submitted && !editForm.title,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter a title.
                        </div>
                    </BCol>
                    <BCol lg="12">
                        <div>
                            <label
                                for="companysDescription-field"
                                class="form-label"
                                >Description</label
                            >
                            <input
                                type="text"
                                id="companysDescription"
                                class="form-control"
                                placeholder="Description"
                                v-model="editForm.description"
                                :class="{
                                    'is-invalid':
                                        submitted && !editForm.description,
                                }"
                            />
                            <div class="invalid-feedback">
                                Please enter a Description.
                            </div>
                        </div>
                    </BCol>
                    <BCol lg="12">
                        <div class="mb-2">
                            <p for="attachment-field" class="form-label">
                                Attachment
                            </p>
                            <img
                                :src="editForm.attachment"
                                alt=""
                                style="width: 15%; border-radius: 5px"
                            />
                        </div>
                        <input
                            type="file"
                            id="attachment-field"
                            class="form-control"
                            placeholder="Attachment"
                            @change="handleEditFileUpload"
                            :class="{
                                'is-invalid':
                                    submitted && !editForm.attachment.length,
                            }"
                            multiple
                        />
                        <div class="invalid-feedback">
                            Please select at least one attachment.
                        </div>
                    </BCol>
                    <BCol lg="12">
                        <label for="createName-field" class="form-label"
                            >Deadline</label
                        >
                        <input
                            type="date"
                            id="createName"
                            class="form-control"
                            placeholder="Deadline"
                            v-model="editForm.deadline"
                            :class="{
                                'is-invalid': submitted && !editForm.deadline,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter a Deadline.
                        </div>
                    </BCol>
                    <BCol lg="12">
                        <label class="form-label">Assigned To</label>
                        <simplebar data-simplebar style="height: 120px">
                            <ul class="list-unstyled vstack gap-2 mb-0">
                                <li v-for="user in users" :key="user.id">
                                    <div
                                        class="form-check d-flex align-items-center"
                                    >
                                        <input
                                            class="form-check-input me-3"
                                            type="checkbox"
                                            :value="user.id"
                                            :id="'user-' + user.id"
                                            v-model="editForm.assigned_to"
                                        />
                                        <label
                                            class="form-check-label d-flex align-items-center"
                                            :for="'user-' + user.id"
                                        >
                                            <span class="flex-shrink-0">
                                                <img
                                                    :src="
                                                        user.profile_photo_url ||
                                                        'https://ui-avatars.com/api/?name=' +
                                                            user.name
                                                    "
                                                    alt=""
                                                    class="avatar-xxs rounded-circle"
                                                />
                                            </span>
                                            <span class="flex-grow-1 ms-2">{{
                                                user.name
                                            }}</span>
                                        </label>
                                    </div>
                                </li>
                            </ul>
                        </simplebar>
                        <div
                            class="invalid-feedback"
                            v-if="submitted && !editForm.assigned_to.length"
                        >
                            Please select at least one assignee.
                        </div>
                    </BCol>
                </BRow>

                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton
                        type="button"
                        variant="light"
                        @click="companyEditModal = false"
                        id="closemodal"
                    >
                        Close
                    </BButton>
                    <BButton
                        type="submit"
                        variant="success"
                        id="add-btn"
                        @click="handleUpdateSubmit"
                    >
                        Update
                    </BButton>
                </div>
            </BFrom>
        </BModal>
    </Layout>
</template>

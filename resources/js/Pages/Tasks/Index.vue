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
    users: Array,
    tasks: Array,
});

// Data
const taskListModal = ref(false);
const taskEditModal = ref(false);
const deleteModal = ref(false);
const submitted = ref(false);
const dataEdit = ref(false);
const allTask = ref([]);
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

// Event object (Task Form Data)
const event = ref({
    id: "",
    title: "",
    description: "",
    attachment: [],
    assigned_to: [],
    deadline: "",
});

const baseForm = {
    id: "",
    title: "",
    description: "",
    attachment: [],
    assigned_to: [],
    deadline: "",
};

// Form state (used for both create & update)
const form = useForm({ ...baseForm });
const editForm = useForm({ ...baseForm });

// Computed properties
const displayedPosts = computed(() => {
    return paginate(props.tasks);
});

const resultQuery = computed(() => {
    if (searchQuery.value) {
        const search = searchQuery.value.toLowerCase();
        return displayedPosts.value.filter((data) => {
            return (
                data.title.toLowerCase().includes(search) ||
                data.description.toLowerCase().includes(search) ||
                data.deadline.toLowerCase().includes(search) ||
                data.status.toLowerCase().includes(search)
            );
        });
    }
    return displayedPosts.value;
});

// Watch
watch(allTask, () => {
    setPages();
});

// Lifecycle
onMounted(() => {
    setPages();
    fetchTasks();
});

// Methods
const handleAttachment = (eventData) => {
    event.value.attachment = Array.from(eventData.target.files);
};

const handleEditFileUpload = (e) => {
    editForm.attachment = Array.from(e.target.files);
};

// 🔹 Fetch all tasks from backend (Laravel API)
const fetchTasks = async () => {
    try {
        const res = await axios.get("/tasks");
        allTask.value = res.data;
    } catch (error) {
        console.error(error);
    }
};

// 🔹 Create or Update Task
const handleSubmit = async () => {
    submitted.value = true;

    if (
        !event.value.title ||
        !event.value.deadline ||
        !event.value.attachment.length
    ) {
        Swal.fire(
            "Validation Error",
            "Please fill all required fields",
            "warning"
        );
        return;
    }

    form.id = event.value.id;
    form.title = event.value.title;
    form.description = event.value.description;
    form.attachment = event.value.attachment;
    form.assigned_to = event.value.assigned_to;
    form.deadline = event.value.deadline;

    form.post("/tasks/store", {
        onSuccess: (res) => {
            Swal.fire("Created!", "Task added successfully", "success");
            taskListModal.value = false;
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
    taskListModal.value = true;
    dataEdit.value = false;
};

// 🔹 Edit Task
const editDetails = (task) => {
    taskEditModal.value = true;

    editForm.id = task.id;
    editForm.title = task.title;
    editForm.description = task.description;
    editForm.attachment = task.attachment;
    editForm.assigned_to = task.task_users.map((u) => u.assigned_to.id);
    editForm.deadline = task.deadline;
};

const handleUpdateSubmit = () => {
    editForm.post(`/task/update/${editForm.id}`, {
        onSuccess: () => {
            Swal.fire("Updated!", "Task updated successfully", "success");
            taskEditModal.value = false;
            editForm.reset();
        },
    });
};

// 🔹 Delete Task
const deleteData = (task) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(`/tasks/destroy/${task.id}`, {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "Task deleted successfully",
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
    let numberOfPages = Math.ceil(props.tasks.length / perPage.value);
    pages.value = [];
    for (let i = 1; i <= numberOfPages; i++) pages.value.push(i);
};

// 🔹 Paginate tasks
const paginate = (tasks) => {
    let pageNum = page.value;
    let from = (pageNum - 1) * perPage.value;
    let to = pageNum * perPage.value;
    return tasks.slice(from, to);
};

</script>

<template>
    <Layout>
        <PageHeader title="List View" pageTitle="Tasks" />
        <BRow>
            <BCol xxl="4" sm="6">
                <BCard no-body class="card-animate">
                    <BCardBody>
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="fw-medium text-muted mb-0">
                                    Total Tasks
                                </p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    <count-to
                                        :startVal="0"
                                        :endVal="234"
                                        :duration="5000"
                                    ></count-to
                                    >k
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span
                                        class="avatar-title bg-info-subtle text-info rounded-circle fs-4"
                                    >
                                        <i class="ri-ticket-2-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
            <BCol xxl="4" sm="6">
                <BCard no-body class="card-animate">
                    <BCardBody>
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="fw-medium text-muted mb-0">
                                    Pending Tasks
                                </p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    <count-to
                                        :startVal="0"
                                        :endVal="64"
                                        :duration="5000"
                                    ></count-to
                                    >k
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span
                                        class="avatar-title bg-warning-subtle text-warning rounded-circle fs-4"
                                    >
                                        <i class="mdi mdi-timer-sand"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
            <BCol xxl="4" sm="6">
                <BCard no-body class="card-animate">
                    <BCardBody>
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="fw-medium text-muted mb-0">
                                    Completed Tasks
                                </p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    <count-to
                                        :startVal="0"
                                        :endVal="116"
                                        :duration="5000"
                                    ></count-to
                                    >K
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span
                                        class="avatar-title bg-success-subtle text-success rounded-circle fs-4"
                                    >
                                        <i class="ri-checkbox-circle-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>

        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                    <BCardHeader class="border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                All Tasks
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
                                        Create Task
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
                                            placeholder="Search for tasks or something..."
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
                                id="tasksTable"
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
                                            data-sort="tasks_name"
                                            @click="onSort('task')"
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
                                        v-for="(task, index) of resultQuery"
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
                                                href="/apps/tasks-details"
                                                class="fw-medium link-primary"
                                                >{{ task.id }}
                                            </Link>
                                        </td>
                                        <td class="project_name">
                                            <Link
                                                href="/apps/projects-overview"
                                                class="fw-medium link-primary"
                                                >{{ task.title }}
                                            </Link>
                                        </td>

                                        <td class="client_name">
                                            {{ task.description }}
                                        </td>

                                        <td
                                            class="due_date"
                                            v-for="user in task.task_users"
                                            :key="user.id"
                                        >
                                            {{ user.assigned_to.name }}
                                        </td>
                                        <td class="due_date">
                                            {{ task.deadline }}
                                        </td>
                                        <td class="status">
                                            <span
                                                class="badge text-uppercase"
                                                :class="{
                                                    'bg-secondary-subtle text-secondary':
                                                        task.status ==
                                                        'in_progress',
                                                    'bg-info-subtle text-info':
                                                        task.status ==
                                                        'delayed',
                                                    'bg-success-subtle text-success':
                                                        task.status ==
                                                        'completed',
                                                    'bg-warning-subtle text-warning':
                                                        task.status ==
                                                        'pending',
                                                }"
                                                >{{ task.status }}</span
                                            >
                                        </td>
                                        <td class="d-flex gap-2">
                                            <BButton
                                                variant="success px-3"
                                                class="add-btn"
                                                @click="editDetails(task)"
                                            >
                                                Edit
                                            </BButton>
                                            <BButton
                                                variant="danger px-3"
                                                class="add-btn"
                                                @click="deleteData(task)"
                                            >
                                                Delete
                                            </BButton>
                                            <Link :href="`/task/view/${task.id}`" class="btn btn-info">
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
                                        We've searched more than 200k+ tasks We
                                        did not find any tasks for you search.
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

        <!-- task create modal -->
        <BModal
            v-model="taskListModal"
            id="showmodal"
            modal-class="zoomIn"
            hide-footer
            header-class="p-3 bg-info-subtle taskModal"
            class="v-modal-custom"
            centered
            size="lg"
            :title="dataEdit ? 'Edit Task' : 'Add Task'"
        >
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
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
                            v-model="event.title"
                            :class="{
                                'is-invalid': submitted && !event.title,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter a title.
                        </div>
                    </BCol>
                    <BCol lg="12">
                        <div>
                            <label
                                for="tasksDescription-field"
                                class="form-label"
                                >Description</label
                            >
                            <input
                                type="text"
                                id="tasksDescription"
                                class="form-control"
                                placeholder="Description"
                                v-model="event.description"
                                :class="{
                                    'is-invalid':
                                        submitted && !event.description,
                                }"
                            />
                            <div class="invalid-feedback">
                                Please enter a Description.
                            </div>
                        </div>
                    </BCol>
                    <BCol lg="12">
                        <label for="attachment-field" class="form-label"
                            >Attachment</label
                        >
                        <input
                            type="file"
                            id="attachment-field"
                            class="form-control"
                            placeholder="Attachment"
                            @change="handleAttachment"
                            :class="{
                                'is-invalid':
                                    submitted && !event.attachment.length,
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
                            v-model="event.deadline"
                            :class="{
                                'is-invalid': submitted && !event.deadline,
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
                                            v-model="event.assigned_to"
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
                            v-if="submitted && !event.assigned_to.length"
                        >
                            Please select at least one assignee.
                        </div>
                    </BCol>
                </BRow>

                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton
                        type="button"
                        variant="light"
                        @click="taskListModal = false"
                        id="closemodal"
                    >
                        Close
                    </BButton>
                    <BButton
                        type="submit"
                        variant="success"
                        id="add-btn"
                        @click="handleSubmit"
                    >
                        {{ dataEdit ? "Update" : "Add Task" }}
                    </BButton>
                </div>
            </BFrom>
        </BModal>

        <!-- task edit modal -->
        <BModal
            v-model="taskEditModal"
            id="showmodal"
            modal-class="zoomIn"
            hide-footer
            header-class="p-3 bg-info-subtle taskModal"
            class="v-modal-custom"
            centered
            size="lg"
            :title="dataEdit ? 'Edit Task' : 'Add Task'"
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
                                for="tasksDescription-field"
                                class="form-label"
                                >Description</label
                            >
                            <input
                                type="text"
                                id="tasksDescription"
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
                            <p for="attachment-field" class="form-label"
                                >Attachment</p
                            >
                            <img
                                :src="editForm.attachment"
                                alt=""
                                style="width: 15%; border-radius: 5px;"
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
                        @click="taskEditModal = false"
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

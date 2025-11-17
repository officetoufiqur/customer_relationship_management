<script setup>
import { ref } from "vue";
import { Link, Head, useForm } from "@inertiajs/vue3";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";

import animationData from "@/Components/widgets/kbtmbyzy.json";
import Lottie from "@/Components/widgets/lottie.vue";
import simplebar from "simplebar-vue";
import VueCountdown from "@chenfengyuan/vue-countdown";
import { computed } from "vue";
import Swal from "sweetalert2";

// ----------- Reactive State -------------------
const modalShow = ref(false);
const value = ref(null);

const defaultOptions = {
    animationData: animationData,
};

const props = defineProps({
    task: Object,
});

// ---- Calculate Remaining Time ----
const remainingTime = computed(() => {
    const deadlineTime = new Date(props.task.deadline).getTime();
    const now = Date.now();
    const diff = deadlineTime - now;

    return diff > 0 ? diff : 0;
});

// const optionData = ref("delay");

const form = useForm({
    task_id: "",
    user_id: "",
    delay_reason: "",
    transferred_note: "",
    transferred_to: [],
    optionData: "delay",
});

const editReassignModal = ref(false);

const editReassign = (task, userId) => {
    editReassignModal.value = true;

    form.task_id = task.id;
    form.user_id = userId;

    form.delay_reason = task.delay_reason;
    form.transferred_note = task.transferred_note;
    form.transferred_to = [];
    form.option = form.optionData; 
};


const handleUpdateSubmit = () => {
    form.post(`/task/reassign/update/${form.task_id}`, {
        onSuccess: () => {
            Swal.fire(
                "Updated!",
                "Employee Reassign or delay updated successfully",
                "success"
            );
            editReassignModal.value = false;
            form.reset();
        },
    });
};
</script>

<template>
    <Layout>
        <PageHeader title="Task Details" pageTitle="Tasks" />
        <BRow>
            <BCol xxl="3">
                <BCard no-body>
                    <BCardBody class="text-center">
                        <h6 class="card-title mb-3 flex-grow-1 text-start">
                            Time Tracking
                        </h6>
                        <div class="mb-2">
                            <lottie
                                colors="primary:#405189,secondary:#02a8b5"
                                :options="defaultOptions"
                                :height="90"
                                :width="90"
                            />
                        </div>
                        <h6 class="card-title mb-3 flex-grow-1">
                            Time Remaining:
                        </h6>
                        <VueCountdown
                            :time="remainingTime"
                            v-slot="{ days, hours, minutes, seconds }"
                        >
                            <h5>
                                {{ days }} days, {{ hours }} hours,
                                {{ minutes }} minutes
                            </h5>
                        </VueCountdown>
                    </BCardBody>
                </BCard>
                <BCard no-body class="mb-3">
                    <BCardBody>
                        <div class="table-card">
                            <table class="table mb-0">
                                <tbody>
                                    <tr>
                                        <td class="fw-medium">Tasks Title</td>
                                        <td>{{ task.title }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">Status</td>
                                        <td>
                                            <BBadge
                                                variant="secondary-subtle"
                                                class="bg-secondary-subtle text-secondary"
                                            >
                                                {{ task.status }}</BBadge
                                            >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">Due Date</td>
                                        <td>{{ task.deadline }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body class="mb-3">
                    <BCardBody>
                        <div class="d-flex mb-3">
                            <h6 class="card-title mb-0 flex-grow-1">
                                Assigned To
                            </h6>
                        </div>
                        <ul class="list-unstyled vstack gap-3 mb-0">
                            <li v-for="item in task.users" :key="item.id">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <img
                                            v-if="item.employee?.image"
                                            :src="item.employee?.image"
                                            alt=""
                                            class="avatar-xs rounded-circle"
                                        />
                                        <img
                                            v-else
                                            src="/assets/image/avatar.jpg"
                                            alt=""
                                            class="avatar-xs rounded-circle"
                                        />
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-1">{{ item.name }}</h6>
                                        <p class="text-muted mb-0">
                                            {{ item.employee?.position }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <BDropdown
                                            variant="link"
                                            toggle-class="btn btn-icon btn-sm fs-16 text-muted arrow-none"
                                            menu-class="dropdown-menu-end"
                                        >
                                            <template #button-content>
                                                <i
                                                    class="ri-more-fill fs-17"
                                                ></i>
                                            </template>
                                            <BDropdownItem>
                                                <Link
                                                    :href="`/employee/profile/${item.id}`"
                                                >
                                                    <i
                                                        class="ri-eye-fill me-2 align-bottom text-muted"
                                                    ></i>
                                                    View
                                                </Link>
                                            </BDropdownItem>
                                            <BDropdownItem>
                                                <i
                                                    class="ri-delete-bin-5-fill me-2 align-bottom text-muted"
                                                ></i>
                                                Delete
                                            </BDropdownItem>
                                        </BDropdown>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardBody>
                        <h5 class="card-title mb-3">Attachments</h5>
                        <div class="vstack gap-2">
                            <div class="border rounded border-dashed p-2">
                                <div
                                    v-for="item in task.attachments"
                                    :key="item.id"
                                    class="d-flex align-items-center mb-2"
                                >
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-sm">
                                            <div
                                                class="avatar-title bg-light text-secondary rounded fs-24"
                                            >
                                                <i
                                                    class="ri-folder-zip-line"
                                                ></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="fs-13 mb-1">
                                            <BLink
                                                href="javascript:void(0);"
                                                class="text-body text-truncate d-block"
                                                >{{
                                                    item.path.split("/").pop()
                                                }}</BLink
                                            >
                                        </h5>
                                        <div>{{ item.size }}</div>
                                    </div>
                                    <div class="flex-shrink-0 ms-2">
                                        <div class="d-flex gap-1">
                                            <BButton
                                                variant="link"
                                                size="sm"
                                                class="btn-icon text-muted fs-18"
                                            >
                                                <a :href="item" download>
                                                    <i
                                                        class="ri-download-2-line"
                                                    ></i>
                                                </a>
                                            </BButton>
                                            <BDropdown
                                                variant="link"
                                                toggle-class="btn btn-icon text-muted btn-sm fs-18 arrow-none"
                                                menu-class="dropdown-menu-end"
                                            >
                                                <template #button-content>
                                                    <i class="ri-more-fill"></i>
                                                </template>
                                                <BDropdownItem
                                                    ><i
                                                        class="ri-pencil-fill align-bottom me-2 text-muted"
                                                    ></i>
                                                    Rename
                                                </BDropdownItem>
                                                <BDropdownItem
                                                    ><i
                                                        class="ri-delete-bin-fill align-bottom me-2 text-muted"
                                                    ></i>
                                                    Delete
                                                </BDropdownItem>
                                            </BDropdown>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="mt-2 text-center">
                                <BButton type="button" variant="success"
                                    >View more</BButton
                                >
                            </div> -->
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
            <BCol xxl="9">
                <BCard no-body>
                    <BCardBody>
                        <div class="text-muted">
                            <h6 class="mb-3 fw-semibold text-uppercase">
                                Summary
                            </h6>
                            <p>{{ task.description }}</p>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardBody>
                        <h5 class="card-title mb-3">Assigned All Employees</h5>
                        <table class="table table-nowrap">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Position</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in task.users" :key="item.id">
                                    <th scope="row">
                                        {{ item.id }}
                                    </th>
                                    <td>{{ item.name }}</td>
                                    <td>{{ item.employee?.position || "N/A" }}</td>
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
                                    <td>
                                        <BButton
                                            variant="success px-3"
                                            class="add-btn"
                                            @click="editReassign(task, item.id)"
                                        >
                                            Delay/Reassign
                                        </BButton>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>

        <!-- task edit modal -->
        <BModal
            v-model="editReassignModal"
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
                            >Select Option</label
                        >
                        <select
                            v-model="form.optionData"
                            class="form-select shadow-none cursor-pointer"
                        >
                            <option selected>Select Option</option>
                            <option value="delay">Delay</option>
                            <option value="reassign">Reassign</option>
                        </select>
                    </BCol>
                    <BCol v-if="form.optionData == 'delay'" lg="12">
                        <label for="projectName-field" class="form-label"
                            >Delay Reason</label
                        >
                        <textarea
                            id="projectName"
                            class="form-control"
                            placeholder="delay reason"
                            v-model="form.delay_reason"
                            :class="{
                                'is-invalid': submitted && !form.delay_reason,
                            }"
                        ></textarea>
                        <div class="invalid-feedback">
                            Please enter a title.
                        </div>
                    </BCol>
                    <div v-if="form.optionData == 'reassign'">
                        <BCol lg="12">
                            <label for="projectName-field" class="form-label"
                                >Transferred Notes</label
                            >
                            <textarea
                                id="projectName"
                                class="form-control"
                                placeholder="transferred notes"
                                v-model="form.transferred_note"
                                :class="{
                                    'is-invalid':
                                        submitted && !form.transferred_note,
                                }"
                            ></textarea>
                            <div class="invalid-feedback">
                                Please enter a title.
                            </div>
                        </BCol>
                        <BCol lg="12" class="mt-3">
                            <label class="form-label">Transferred To</label>
                            <simplebar data-simplebar style="height: 120px">
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li
                                        v-for="user in task.users"
                                        :key="user.id"
                                    >
                                        <div
                                            class="form-check d-flex align-items-center"
                                        >
                                            <input
                                                class="form-check-input me-2"
                                                type="checkbox"
                                                :value="user.id"
                                                :id="'user-' + user.id"
                                                v-model="form.transferred_to"
                                            />
                                            <label
                                                class="form-check-label d-flex align-items-center"
                                                :for="'user-' + user.id"
                                            >
                                                <span class="flex-grow-1">{{
                                                    user.name
                                                }}</span>
                                            </label>
                                        </div>
                                    </li>
                                </ul>
                            </simplebar>
                            <div
                                class="invalid-feedback"
                                v-if="submitted && !form.transferred_to.length"
                            >
                                Please select at least one assignee.
                            </div>
                        </BCol>
                    </div>
                </BRow>

                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton
                        type="button"
                        variant="light"
                        @click="editReassignModal = false"
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

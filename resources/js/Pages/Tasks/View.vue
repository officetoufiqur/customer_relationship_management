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

const form = useForm({
    id: '',
    delay_reason: '',
    transferred_notes: '',
    transferred_to: '',
})
const editReassignModal = ref(false);
const editReassign = (task) => {
    editReassignModal.value = true;
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
                            <div class="flex-shrink-0">
                                <BButton
                                    type="button"
                                    variant="soft-danger"
                                    size="sm"
                                    @click="modalShow = !modalShow"
                                >
                                    <i
                                        class="ri-share-line me-1 align-bottom"
                                    ></i>
                                    Assigned Member
                                </BButton>
                            </div>
                        </div>
                        <ul class="list-unstyled vstack gap-3 mb-0">
                            <li v-for="item in task.users" :key="item.id">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <img
                                            v-if="item.employee.image"
                                            :src="item.employee.image"
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
                                            {{ item.employee.position }}
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
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in task.users" :key="item.id">
                                    <th scope="row">
                                        {{ item.id }}
                                    </th>
                                    <td>{{ item.name }}</td>
                                    <td>{{ item.employee.position }}</td>
                                    <td>
                                        <BButton
                                            variant="success px-3"
                                            class="add-btn"
                                            @click="editReassign(task)"
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
                            >Delay Reason</label
                        >
                        <input
                            type="text"
                            id="projectName"
                            class="form-control"
                            placeholder="delay reason"
                            v-model="form.delay_reason"
                            :class="{
                                'is-invalid': submitted && !form.delay_reason,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter a title.
                        </div>
                    </BCol>
                    <BCol lg="12">
                        <label for="projectName-field" class="form-label"
                            >Transferred Notes</label
                        >
                        <input
                            type="text"
                            id="projectName"
                            class="form-control"
                            placeholder="transferred notes"
                            v-model="form.delay_reason"
                            :class="{
                                'is-invalid': submitted && !form.delay_reason,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter a title.
                        </div>
                    </BCol>
                    <BCol lg="12">
                        <label class="form-label">Transferred To</label>
                        <simplebar data-simplebar style="height: 120px">
                            <ul class="list-unstyled vstack gap-2 mb-0">
                                <li v-for="user in task.users" :key="user.id">
                                    <div
                                        class="form-check d-flex align-items-center"
                                    >
                                        <input
                                            class="form-check-input me-3"
                                            type="checkbox"
                                            :value="user.id"
                                            :id="'user-' + user.id"
                                            v-model="form.transferred_to"
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
                            v-if="submitted && !form.transferred_to.length"
                        >
                            Please select at least one assignee.
                        </div>
                    </BCol>
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



        <BModal
            v-model="modalShow"
            hide-footer
            title="Team Members"
            body-class="p-4"
            content-class="border-0"
            header-class="p-3 ps-4 bg-success-subtle"
            class="v-modal-custom"
            centered
        >
            <div class="search-box mb-3">
                <input
                    type="text"
                    class="form-control bg-light border-light"
                    placeholder="Search here..."
                />
                <i class="ri-search-line search-icon"></i>
            </div>

            <div class="mb-4 d-flex align-items-center">
                <div class="me-2">
                    <h5 class="mb-0 fs-13">Members :</h5>
                </div>
                <div class="avatar-group justify-content-center">
                    <BLink
                        href="javascript: void(0);"
                        class="avatar-group-item"
                        v-b-tooltip.hover
                        title="Tonya Noble"
                    >
                        <div class="avatar-xs">
                            <img
                                src="@assets/images/users/avatar-10.jpg"
                                alt=""
                                class="rounded-circle img-fluid"
                            />
                        </div>
                    </BLink>
                    <BLink
                        href="javascript: void(0);"
                        class="avatar-group-item"
                        v-b-tooltip.hover
                        title="Thomas Taylor"
                    >
                        <div class="avatar-xs">
                            <img
                                src="@assets/images/users/avatar-8.jpg"
                                alt=""
                                class="rounded-circle img-fluid"
                            />
                        </div>
                    </BLink>
                    <BLink
                        href="javascript: void(0);"
                        class="avatar-group-item"
                        v-b-tooltip.hover
                        title="Nancy Martino"
                    >
                        <div class="avatar-xs">
                            <img
                                src="@assets/images/users/avatar-2.jpg"
                                alt=""
                                class="rounded-circle img-fluid"
                            />
                        </div>
                    </BLink>
                </div>
            </div>
            <div class="mx-n4 px-4" data-simplebar style="max-height: 225px">
                <div class="vstack gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-xs flex-shrink-0 me-3">
                            <img
                                src="@assets/images/users/avatar-2.jpg"
                                alt=""
                                class="img-fluid rounded-circle"
                            />
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fs-13 mb-0">
                                <BLink
                                    href="javascript:void(0);"
                                    class="text-body d-block"
                                    >Nancy Martino
                                </BLink>
                            </h5>
                        </div>
                        <div class="flex-shrink-0">
                            <BButton type="button" variant="light" size="sm"
                                >Add</BButton
                            >
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="avatar-xs flex-shrink-0 me-3">
                            <div
                                class="avatar-title bg-danger-subtle text-danger rounded-circle"
                            >
                                HB
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fs-13 mb-0">
                                <BLink
                                    href="javascript:void(0);"
                                    class="text-body d-block"
                                    >Henry Baird
                                </BLink>
                            </h5>
                        </div>
                        <div class="flex-shrink-0">
                            <BButton type="button" variant="light" size="sm"
                                >Add</BButton
                            >
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="avatar-xs flex-shrink-0 me-3">
                            <img
                                src="@assets/images/users/avatar-3.jpg"
                                alt=""
                                class="img-fluid rounded-circle"
                            />
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fs-13 mb-0">
                                <BLink
                                    href="javascript:void(0);"
                                    class="text-body d-block"
                                    >Frank Hook
                                </BLink>
                            </h5>
                        </div>
                        <div class="flex-shrink-0">
                            <BButton type="button" variant="light" size="sm"
                                >Add</BButton
                            >
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="avatar-xs flex-shrink-0 me-3">
                            <img
                                src="@assets/images/users/avatar-4.jpg"
                                alt=""
                                class="img-fluid rounded-circle"
                            />
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fs-13 mb-0">
                                <BLink
                                    href="javascript:void(0);"
                                    class="text-body d-block"
                                    >Jennifer Carter
                                </BLink>
                            </h5>
                        </div>
                        <div class="flex-shrink-0">
                            <BButton type="button" variant="light" size="sm"
                                >Add</BButton
                            >
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="avatar-xs flex-shrink-0 me-3">
                            <div
                                class="avatar-title bg-success-subtle text-success rounded-circle"
                            >
                                AC
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fs-13 mb-0">
                                <BLink
                                    href="javascript:void(0);"
                                    class="text-body d-block"
                                    >Alexis Clarke
                                </BLink>
                            </h5>
                        </div>
                        <div class="flex-shrink-0">
                            <BButton type="button" variant="light" size="sm"
                                >Add</BButton
                            >
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="avatar-xs flex-shrink-0 me-3">
                            <img
                                src="@assets/images/users/avatar-7.jpg"
                                alt=""
                                class="img-fluid rounded-circle"
                            />
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fs-13 mb-0">
                                <BLink
                                    href="javascript:void(0);"
                                    class="text-body d-block"
                                    >Joseph Parker
                                </BLink>
                            </h5>
                        </div>
                        <div class="flex-shrink-0">
                            <BButton type="button" variant="light" size="sm"
                                >Add</BButton
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer v-modal-footer">
                <BButton
                    type="button"
                    variant="light"
                    class="w-xs"
                    @click="modalShow = false"
                >
                    Cancel</BButton
                >
                <BButton type="button" variant="success" class="w-xs"
                    >Assigned</BButton
                >
            </div>
        </BModal>
    </Layout>
</template>

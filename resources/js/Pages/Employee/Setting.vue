<script>
import { Link, Head, useForm } from "@inertiajs/vue3";
import Layout from "@/Layouts/main.vue";

import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";

export default {
    props: {
        employee: Object,
    },
    data() {
        return {
            value: ["javascript"],
            date: null,
            value1: null,
            value2: null,
        };
    },
    components: {
        Layout,
        Multiselect,
        flatPickr,
        Link,
        Head,
    },
    setup(props) {
        const form = useForm({
            name: props.employee?.name ?? '',
            email: props.employee?.email ?? '',
        });

        return {
            form,
        };
    },
};
</script>

<template>
    <Layout>
        <Head title="Profile Setting" />
        <div class="position-relative mx-n4 mt-n4">
            <div class="profile-wid-bg profile-setting-img">
                <div class="profile-wid-bg">
                    <template v-if="employee && employee.cover_photo">
                        <img
                            :src="employee.cover_photo"
                            :alt="employee.name"
                            class="img-thumbnail rounded-circle"
                        />
                    </template>
                    <template v-else>
                        <img
                            src="@assets/images/profile-bg.jpg"
                            alt=""
                            class="profile-wid-img"
                        />
                    </template>
                </div>
                <div class="overlay-content">
                    <div class="text-end p-3">
                        <div
                            class="p-0 ms-auto rounded-circle profile-photo-edit"
                        >
                            <input
                                id="profile-foreground-img-file-input"
                                type="file"
                                class="profile-foreground-img-file-input"
                            />
                            <label
                                for="profile-foreground-img-file-input"
                                class="profile-photo-edit btn btn-light"
                            >
                                <i
                                    class="ri-image-edit-line align-bottom me-1"
                                ></i>
                                Change Cover
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <BRow>
            <BCol xxl="3">
                <BCard no-body class="mt-n5">
                    <BCardBody class="p-4">
                        <div class="text-center">
                            <div
                                class="profile-user position-relative d-inline-block mx-auto mb-4"
                            >
                                <div class="avatar-lg">
                                    <template v-if="employee && employee.image">
                                        <img
                                            :src="employee.image"
                                            :alt="employee.name"
                                            class="img-thumbnail rounded-circle"
                                        />
                                    </template>
                                    <template v-else>
                                        <div
                                            class="img-thumbnail rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                            style="
                                                width: 90px;
                                                height: 90px;
                                                font-size: 32px;
                                            "
                                        >
                                            {{
                                                employee.name
                                                    .charAt(0)
                                                    .toUpperCase()
                                            }}
                                        </div>
                                    </template>
                                </div>
                                <div
                                    class="avatar-xs p-0 rounded-circle profile-photo-edit"
                                >
                                    <input
                                        id="profile-img-file-input"
                                        type="file"
                                        class="profile-img-file-input"
                                    />
                                    <label
                                        for="profile-img-file-input"
                                        class="profile-photo-edit avatar-xs"
                                    >
                                        <span
                                            class="avatar-title rounded-circle bg-light text-body"
                                        >
                                            <i class="ri-camera-fill"></i>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <h5 class="fs-16 mb-1">{{ employee.name }}</h5>
                            <p class="text-muted mb-0">
                                {{ employee.designation }}
                            </p>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body>
                    <BCardBody>
                        <div class="d-flex align-items-center mb-4">
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-0">Portfolio</h5>
                            </div>
                            <div class="flex-shrink-0">
                                <BLink
                                    href="javascript:void(0);"
                                    class="badge bg-light text-primary fs-12"
                                    ><i
                                        class="ri-add-fill align-bottom me-1"
                                    ></i>
                                    Add</BLink
                                >
                            </div>
                        </div>
                        <div class="mb-3 d-flex">
                            <div class="avatar-xs d-block flex-shrink-0 me-3">
                                <span
                                    class="avatar-title rounded-circle fs-16 bg-body text-body"
                                >
                                    <i class="ri-github-fill"></i>
                                </span>
                            </div>
                            <input
                                type="email"
                                class="form-control"
                                id="gitUsername"
                                placeholder="Username"
                                value="@daveadame"
                            />
                        </div>
                        <div class="mb-3 d-flex">
                            <div class="avatar-xs d-block flex-shrink-0 me-3">
                                <span
                                    class="avatar-title rounded-circle fs-16 bg-primary"
                                >
                                    <i class="ri-global-fill"></i>
                                </span>
                            </div>
                            <input
                                type="text"
                                class="form-control"
                                id="websiteInput"
                                placeholder="www.example.com"
                                value="www.velzon.com"
                            />
                        </div>
                        <div class="mb-3 d-flex">
                            <div class="avatar-xs d-block flex-shrink-0 me-3">
                                <span
                                    class="avatar-title rounded-circle fs-16 bg-success"
                                >
                                    <i class="ri-dribbble-fill"></i>
                                </span>
                            </div>
                            <input
                                type="text"
                                class="form-control"
                                id="dribbleName"
                                placeholder="Username"
                                value="@dave_adame"
                            />
                        </div>
                        <div class="d-flex">
                            <div class="avatar-xs d-block flex-shrink-0 me-3">
                                <span
                                    class="avatar-title rounded-circle fs-16 bg-danger"
                                >
                                    <i class="ri-pinterest-fill"></i>
                                </span>
                            </div>
                            <input
                                type="text"
                                class="form-control"
                                id="pinterestName"
                                placeholder="Username"
                                value="Advance Dave"
                            />
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
            <BCol xxl="9">
                <BCard no-body class="mt-xxl-n5">
                    <BCardBody class="p-4 pt-2">
                        <BTabs
                            nav-class="nav-tabs-custom rounded border-bottom-0"
                        >
                            <BTab
                                class="nav-item"
                                title="Personal Details"
                                active
                            >
                                <form action="javascript:void(0);">
                                    <BRow class="pt-4">
                                        <BCol lg="6">
                                            <div class="mb-3">
                                                <label
                                                    for="firstnameInput"
                                                    class="form-label"
                                                    >Name</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="firstnameInput"
                                                    placeholder="Enter your firstname"
                                                    v-model="form.name"
                                                />
                                            </div>
                                        </BCol>
                                         <BCol lg="6">
                                            <div class="mb-3">
                                                <label
                                                    for="emailInput"
                                                    class="form-label"
                                                    >Email Address</label
                                                >
                                                <input
                                                    type="email"
                                                    class="form-control"
                                                    id="emailInput"
                                                    placeholder="Enter your email"
                                                    v-model="form.email"
                                                />
                                            </div>
                                        </BCol>
                                        <BCol lg="6">
                                            <div class="mb-3">
                                                <label
                                                    for="position"
                                                    class="form-label"
                                                    >Position</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="position"
                                                    placeholder="Enter your position"
                                                    v-model="form.position"
                                                />
                                            </div>
                                        </BCol>
                                        <BCol lg="6">
                                            <div class="mb-3">
                                                <label
                                                    for="department"
                                                    class="form-label"
                                                    >Department</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="department"
                                                    placeholder="Enter your department"
                                                    v-model="form.department"
                                                />
                                            </div>
                                        </BCol>
                                       
                                        <BCol lg="6">
                                            <div class="mb-3">
                                                <label
                                                    for="location"
                                                    class="form-label"
                                                    >Location</label
                                                >

                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="location"
                                                    placeholder="Enter your location"
                                                    v-model="form.location"
                                                />
                                            </div>
                                        </BCol>
                                        <BCol lg="6">
                                            <div class="mb-3">
                                                <label
                                                    for="skillsInput"
                                                    class="form-label"
                                                    >Skills</label
                                                >
                                                <Multiselect
                                                    v-model="value"
                                                    id="skillsinput"
                                                    mode="tags"
                                                    :close-on-select="false"
                                                    :searchable="true"
                                                    :create-option="true"
                                                    :options="[
                                                        {
                                                            value: 'illustrator',
                                                            label: 'Illustrator',
                                                        },
                                                        {
                                                            value: 'photoshop',
                                                            label: 'Photoshop',
                                                        },
                                                        {
                                                            value: 'css',
                                                            label: 'CSS',
                                                        },
                                                        {
                                                            value: 'html',
                                                            label: 'HTML',
                                                        },
                                                        {
                                                            value: 'javascript',
                                                            label: 'Javascript',
                                                        },
                                                        {
                                                            value: 'python',
                                                            label: 'Python',
                                                        },
                                                        {
                                                            value: 'php',
                                                            label: 'PHP',
                                                        },
                                                    ]"
                                                />
                                            </div>
                                        </BCol>
                                        <BCol lg="12">
                                            <div class="mb-3 pb-2">
                                                <label
                                                    for="exampleFormControlTextarea"
                                                    class="form-label"
                                                    >Description</label
                                                >
                                                <textarea
                                                    class="form-control"
                                                    id="exampleFormControlTextarea"
                                                    placeholder="Enter your description"
                                                    rows="3"
                                                    v-model="form.about"
                                                ></textarea
                                                >
                                            </div>
                                        </BCol>
                                        <BCol lg="12">
                                            <div
                                                class="hstack gap-2 justify-content-end"
                                            >
                                                <BButton
                                                    type="submit"
                                                    variant="primary"
                                                >
                                                    Updates
                                                </BButton>
                                                <BButton
                                                    type="button"
                                                    variant="soft-success"
                                                >
                                                    Cancel
                                                </BButton>
                                            </div>
                                        </BCol>
                                    </BRow>
                                </form>
                            </BTab>
                            <BTab title="Change Password">
                                <form action="javascript:void(0);">
                                    <BRow class="g-2 pt-4">
                                      <div class="w-full space-y-3">
                                        <BCol lg="12">
                                            <div>
                                                <label
                                                    for="oldpasswordInput"
                                                    class="form-label"
                                                    >Old Password*</label
                                                >
                                                <input
                                                    type="password"
                                                    class="form-control"
                                                    id="oldpasswordInput"
                                                    placeholder="Enter current password"
                                                />
                                            </div>
                                        </BCol>
                                        <BCol lg="12">
                                            <div>
                                                <label
                                                    for="newpasswordInput"
                                                    class="form-label"
                                                    >New Password*</label
                                                >
                                                <input
                                                    type="password"
                                                    class="form-control"
                                                    id="newpasswordInput"
                                                    placeholder="Enter new password"
                                                />
                                            </div>
                                        </BCol>
                                        <BCol lg="12">
                                            <div>
                                                <label
                                                    for="confirmpasswordInput"
                                                    class="form-label"
                                                    >Confirm Password*</label
                                                >
                                                <input
                                                    type="password"
                                                    class="form-control"
                                                    id="confirmpasswordInput"
                                                    placeholder="Confirm password"
                                                />
                                            </div>
                                        </BCol>
                                        <BCol lg="12">
                                            <div class="mb-3">
                                                <BLink
                                                    href="javascript:void(0);"
                                                    class="link-primary text-decoration-underline"
                                                    >Forgot Password ?</BLink
                                                >
                                            </div>
                                        </BCol>
                                        </div>
                                        <BCol lg="12">
                                            <div class="text-end">
                                                <BButton
                                                    type="submit"
                                                    variant="success"
                                                >
                                                    Change Password
                                                </BButton>
                                            </div>
                                        </BCol>
                                    </BRow>
                                </form>
                            </BTab>
                        </BTabs>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>

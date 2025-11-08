<script>
import { Link, Head } from "@inertiajs/vue3";
import Layout from "@/Layouts/main.vue";

import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay, Navigation } from "swiper/modules";

import "swiper/css";
import "swiper/css/autoplay";
import "swiper/css/navigation";

export default {
    props: {
        employee: Object,
    },
    data() {
        return {
            Autoplay,
            Navigation,
        };
    },
    components: {
        Layout,
        Swiper,
        SwiperSlide,
        Head,
        Link,
    },
};
</script>

<template>
    <Layout>
        <Head title="Simple Profile" />
        <div class="profile-foreground position-relative mx-n4 mt-n4">
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
        </div>
        <div class="pt-4 mb-4 mb-lg-3 pb-lg-4 profile-wrapper">
            <BRow class="g-4">
                <BCol cols="auto">
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
                                    width: 80px;
                                    height: 80px;
                                    font-size: 32px;
                                "
                            >
                                {{ employee.name.charAt(0).toUpperCase() }}
                            </div>
                        </template>
                    </div>
                </BCol>
                <BCol>
                    <div class="p-2">
                        <h3 class="text-white mb-1">{{ employee.name }}</h3>
                        <p class="text-white text-opacity-75">
                            {{ employee.deductions }}
                        </p>
                        <div class="hstack text-white-50 gap-1">
                            <div class="me-2">
                                <i
                                    class="ri-map-pin-user-line me-1 text-white text-opacity-75 fs-16 align-middle"
                                ></i
                                >California, United States
                            </div>
                            <div>
                                <i
                                    class="ri-building-line me-1 text-white text-opacity-75 fs-16 align-middle"
                                ></i
                                >Themesbrand
                            </div>
                        </div>
                    </div>
                </BCol>
            </BRow>
        </div>

        <BRow>
            <BCol lg="12">
                <div>
                    <div
                        class="d-flex profile-wrapper"
                        style="position: absolute; right: 13px; top: 26px"
                    >
                        <div class="d-flex justify-content-end">
                            <Link
                                href="/pages/profile-setting"
                                class="btn btn-success"
                                ><i class="ri-edit-box-line align-bottom"></i>
                                Edit Profile</Link
                            >
                        </div>
                    </div>
                    <BTabs
                        nav-class=" animation-nav profile-nav gap-2 gap-lg-3  pt-4 text-muted"
                        pills
                    >
                        <!-- overview -->
                        <BTab title="Overview" class="nav-item pt-4">
                            <template #title>
                                <i
                                    class="ri-airplay-fill d-inline-block d-md-none"
                                ></i>
                                <span class="d-none d-md-inline-block"
                                    >Overview</span
                                >
                            </template>
                            <BRow>
                                <BCol xxl="3">
                                    <BCard no-body>
                                        <BCardBody>
                                            <h5 class="card-title mb-3">
                                                Info
                                            </h5>
                                            <div class="table-responsive">
                                                <table
                                                    class="table table-borderless mb-0"
                                                >
                                                    <tbody>
                                                        <tr>
                                                            <th
                                                                class="ps-0"
                                                                scope="row"
                                                            >
                                                                Full Name :
                                                            </th>
                                                            <td
                                                                class="text-muted"
                                                            >
                                                                Anna Adame
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th
                                                                class="ps-0"
                                                                scope="row"
                                                            >
                                                                Mobile :
                                                            </th>
                                                            <td
                                                                class="text-muted"
                                                            >
                                                                +(1) 987 6543
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th
                                                                class="ps-0"
                                                                scope="row"
                                                            >
                                                                E-mail :
                                                            </th>
                                                            <td
                                                                class="text-muted"
                                                            >
                                                                daveadame@velzon.com
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th
                                                                class="ps-0"
                                                                scope="row"
                                                            >
                                                                Location :
                                                            </th>
                                                            <td
                                                                class="text-muted"
                                                            >
                                                                California,
                                                                United States
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th
                                                                class="ps-0"
                                                                scope="row"
                                                            >
                                                                Joining Date
                                                            </th>
                                                            <td
                                                                class="text-muted"
                                                            >
                                                                24 Nov 2021
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </BCardBody>
                                    </BCard>

                                    <BCard no-body>
                                        <BCardBody>
                                            <h5 class="card-title mb-4">
                                                Portfolio
                                            </h5>
                                            <div class="d-flex flex-wrap gap-2">
                                                <div>
                                                    <BLink
                                                        href="javascript:void(0);"
                                                        class="avatar-xs d-block"
                                                    >
                                                        <span
                                                            class="avatar-title rounded-circle fs-16 bg-body text-body"
                                                        >
                                                            <i
                                                                class="ri-github-fill"
                                                            ></i>
                                                        </span>
                                                    </BLink>
                                                </div>
                                                <div>
                                                    <BLink
                                                        href="javascript:void(0);"
                                                        class="avatar-xs d-block"
                                                    >
                                                        <span
                                                            class="avatar-title rounded-circle fs-16 bg-primary"
                                                        >
                                                            <i
                                                                class="ri-global-fill"
                                                            ></i>
                                                        </span>
                                                    </BLink>
                                                </div>
                                                <div>
                                                    <BLink
                                                        href="javascript:void(0);"
                                                        class="avatar-xs d-block"
                                                    >
                                                        <span
                                                            class="avatar-title rounded-circle fs-16 bg-success"
                                                        >
                                                            <i
                                                                class="ri-dribbble-fill"
                                                            ></i>
                                                        </span>
                                                    </BLink>
                                                </div>
                                                <div>
                                                    <BLink
                                                        href="javascript:void(0);"
                                                        class="avatar-xs d-block"
                                                    >
                                                        <span
                                                            class="avatar-title rounded-circle fs-16 bg-danger"
                                                        >
                                                            <i
                                                                class="ri-pinterest-fill"
                                                            ></i>
                                                        </span>
                                                    </BLink>
                                                </div>
                                            </div>
                                        </BCardBody>
                                    </BCard>

                                    <BCard no-body>
                                        <BCardBody>
                                            <h5 class="card-title mb-4">
                                                Skills
                                            </h5>
                                            <div
                                                class="d-flex flex-wrap gap-2 fs-15"
                                            >
                                                <BLink
                                                    href="javascript:void(0);"
                                                    class="badge bg-primary-subtle text-primary"
                                                    >Photoshop</BLink
                                                >
                                                <BLink
                                                    href="javascript:void(0);"
                                                    class="badge bg-primary-subtle text-primary"
                                                    >illustrator</BLink
                                                >
                                                <BLink
                                                    href="javascript:void(0);"
                                                    class="badge bg-primary-subtle text-primary"
                                                    >HTML</BLink
                                                >
                                                <BLink
                                                    href="javascript:void(0);"
                                                    class="badge bg-primary-subtle text-primary"
                                                    >CSS</BLink
                                                >
                                                <BLink
                                                    href="javascript:void(0);"
                                                    class="badge bg-primary-subtle text-primary"
                                                    >Javascript</BLink
                                                >
                                                <BLink
                                                    href="javascript:void(0);"
                                                    class="badge bg-primary-subtle text-primary"
                                                    >Php</BLink
                                                >
                                                <BLink
                                                    href="javascript:void(0);"
                                                    class="badge bg-primary-subtle text-primary"
                                                    >Python</BLink
                                                >
                                            </div>
                                        </BCardBody>
                                    </BCard>
                                </BCol>
                                <BCol xxl="9">
                                    <BCard no-body>
                                        <BCardBody>
                                            <h5 class="card-title mb-3">
                                                About
                                            </h5>
                                            <p>
                                                Hi I'm Anna Adame, It will be as
                                                simple as Occidental; in fact,
                                                it will be Occidental. To an
                                                English person, it will seem
                                                like simplified English, as a
                                                skeptical Cambridge friend of
                                                mine told me what Occidental is
                                                European languages are members
                                                of the same family.
                                            </p>
                                            <p>
                                                You always want to make sure
                                                that your fonts work well
                                                together and try to limit the
                                                number of fonts you use to three
                                                or less. Experiment and play
                                                around with the fonts that you
                                                already have in the software
                                                you’re working with reputable
                                                font websites. This may be the
                                                most commonly encountered tip I
                                                received from the designers I
                                                spoke with. They highly
                                                encourage that you use different
                                                fonts in one design, but do not
                                                over-exaggerate and go
                                                overboard.
                                            </p>
                                            <BRow>
                                                <BCol cols="6" md="4">
                                                    <div class="d-flex mt-4">
                                                        <div
                                                            class="flex-shrink-0 avatar-xs align-self-center me-3"
                                                        >
                                                            <div
                                                                class="avatar-title bg-light rounded-circle fs-16 text-primary"
                                                            >
                                                                <i
                                                                    class="ri-user-2-fill"
                                                                ></i>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="flex-grow-1 overflow-hidden"
                                                        >
                                                            <p class="mb-1">
                                                                Designation :
                                                            </p>
                                                            <h6
                                                                class="text-truncate mb-0"
                                                            >
                                                                Lead Designer /
                                                                Developer
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </BCol>
                                                <BCol cols="6" md="4">
                                                    <div class="d-flex mt-4">
                                                        <div
                                                            class="flex-shrink-0 avatar-xs align-self-center me-3"
                                                        >
                                                            <div
                                                                class="avatar-title bg-light rounded-circle fs-16 text-primary"
                                                            >
                                                                <i
                                                                    class="ri-global-line"
                                                                ></i>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="flex-grow-1 overflow-hidden"
                                                        >
                                                            <p class="mb-1">
                                                                Website :
                                                            </p>
                                                            <BLink
                                                                href="#"
                                                                class="fw-semibold"
                                                                >www.velzon.com</BLink
                                                            >
                                                        </div>
                                                    </div>
                                                </BCol>
                                            </BRow>
                                        </BCardBody>
                                    </BCard>

                                    <BCard no-body>
                                        <BCardBody>
                                            <h5 class="card-title mb-3">
                                                Projects
                                            </h5>
                                            <div class="swiper project-swiper">
                                                <swiper
                                                    :slides-per-view="3"
                                                    :space-between="50"
                                                >
                                                    <swiper-slide>
                                                        <div
                                                            class="swiper-slide"
                                                        >
                                                            <BCard
                                                                no-body
                                                                class="profile-project-card shadow-none profile-project-success mb-0"
                                                            >
                                                                <BCardBody
                                                                    class="p-4"
                                                                >
                                                                    <div
                                                                        class="d-flex"
                                                                    >
                                                                        <div
                                                                            class="flex-grow-1 text-muted overflow-hidden"
                                                                        >
                                                                            <h5
                                                                                class="fs-14 text-truncate mb-1"
                                                                            >
                                                                                <BLink
                                                                                    href="#"
                                                                                    class="text-body"
                                                                                    >ABC
                                                                                    Project
                                                                                    Customization</BLink
                                                                                >
                                                                            </h5>
                                                                            <p
                                                                                class="text-muted text-truncate mb-0"
                                                                            >
                                                                                Last
                                                                                Update
                                                                                :
                                                                                <span
                                                                                    class="fw-semibold text-body"
                                                                                    >4
                                                                                    hr
                                                                                    Ago</span
                                                                                >
                                                                            </p>
                                                                        </div>
                                                                        <div
                                                                            class="flex-shrink-0 ms-2"
                                                                        >
                                                                            <BBadge
                                                                                variant="warning-subtle"
                                                                                class="bg-warning-subtle text-warning fs-10"
                                                                            >
                                                                                Inprogress
                                                                            </BBadge>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="d-flex mt-4"
                                                                    >
                                                                        <div
                                                                            class="flex-grow-1"
                                                                        >
                                                                            <div
                                                                                class="d-flex align-items-center gap-2"
                                                                            >
                                                                                <div>
                                                                                    <h5
                                                                                        class="fs-12 text-muted mb-0"
                                                                                    >
                                                                                        Members
                                                                                        :
                                                                                    </h5>
                                                                                </div>
                                                                                <div
                                                                                    class="avatar-group"
                                                                                >
                                                                                    <div
                                                                                        class="avatar-group-item"
                                                                                    >
                                                                                        <div
                                                                                            class="avatar-xs"
                                                                                        >
                                                                                            <img
                                                                                                src="@assets/images/users/avatar-4.jpg"
                                                                                                alt=""
                                                                                                class="rounded-circle img-fluid"
                                                                                            />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div
                                                                                        class="avatar-group-item"
                                                                                    >
                                                                                        <div
                                                                                            class="avatar-xs"
                                                                                        >
                                                                                            <img
                                                                                                src="@assets/images/users/avatar-5.jpg"
                                                                                                alt=""
                                                                                                class="rounded-circle img-fluid"
                                                                                            />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div
                                                                                        class="avatar-group-item"
                                                                                    >
                                                                                        <div
                                                                                            class="avatar-xs"
                                                                                        >
                                                                                            <div
                                                                                                class="avatar-title rounded-circle bg-light text-primary"
                                                                                            >
                                                                                                A
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div
                                                                                        class="avatar-group-item"
                                                                                    >
                                                                                        <div
                                                                                            class="avatar-xs"
                                                                                        >
                                                                                            <img
                                                                                                src="@assets/images/users/avatar-2.jpg"
                                                                                                alt=""
                                                                                                class="rounded-circle img-fluid"
                                                                                            />
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </BCardBody>
                                                            </BCard>
                                                        </div>
                                                    </swiper-slide>
                                                    <swiper-slide>
                                                        <div
                                                            class="swiper-slide"
                                                        >
                                                            <BCard
                                                                no-body
                                                                class="profile-project-card shadow-none profile-project-danger mb-0"
                                                            >
                                                                <BCardBody
                                                                    class="p-4"
                                                                >
                                                                    <div
                                                                        class="d-flex"
                                                                    >
                                                                        <div
                                                                            class="flex-grow-1 text-muted overflow-hidden"
                                                                        >
                                                                            <h5
                                                                                class="fs-14 text-truncate mb-1"
                                                                            >
                                                                                <BLink
                                                                                    href="#"
                                                                                    class="text-body"
                                                                                    >Client
                                                                                    -
                                                                                    John</BLink
                                                                                >
                                                                            </h5>
                                                                            <p
                                                                                class="text-muted text-truncate mb-0"
                                                                            >
                                                                                Last
                                                                                Update
                                                                                :
                                                                                <span
                                                                                    class="fw-semibold text-body"
                                                                                    >1
                                                                                    hr
                                                                                    Ago</span
                                                                                >
                                                                            </p>
                                                                        </div>
                                                                        <div
                                                                            class="flex-shrink-0 ms-2"
                                                                        >
                                                                            <BBadge
                                                                                variant="success-subtle"
                                                                                class="bg-success-subtle text-success fs-10"
                                                                            >
                                                                                Completed
                                                                            </BBadge>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="d-flex mt-4"
                                                                    >
                                                                        <div
                                                                            class="flex-grow-1"
                                                                        >
                                                                            <div
                                                                                class="d-flex align-items-center gap-2"
                                                                            >
                                                                                <div>
                                                                                    <h5
                                                                                        class="fs-12 text-muted mb-0"
                                                                                    >
                                                                                        Members
                                                                                        :
                                                                                    </h5>
                                                                                </div>
                                                                                <div
                                                                                    class="avatar-group"
                                                                                >
                                                                                    <div
                                                                                        class="avatar-group-item"
                                                                                    >
                                                                                        <div
                                                                                            class="avatar-xs"
                                                                                        >
                                                                                            <img
                                                                                                src="@assets/images/users/avatar-2.jpg"
                                                                                                alt=""
                                                                                                class="rounded-circle img-fluid"
                                                                                            />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div
                                                                                        class="avatar-group-item"
                                                                                    >
                                                                                        <div
                                                                                            class="avatar-xs"
                                                                                        >
                                                                                            <div
                                                                                                class="avatar-title rounded-circle bg-light text-primary"
                                                                                            >
                                                                                                C
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </BCardBody>
                                                            </BCard>
                                                        </div>
                                                    </swiper-slide>
                                                    <swiper-slide>
                                                        <div
                                                            class="swiper-slide"
                                                        >
                                                            <BCard
                                                                no-body
                                                                class="profile-project-card shadow-none profile-project-info mb-0"
                                                            >
                                                                <BCardBody
                                                                    class="p-4"
                                                                >
                                                                    <div
                                                                        class="d-flex"
                                                                    >
                                                                        <div
                                                                            class="flex-grow-1 text-muted overflow-hidden"
                                                                        >
                                                                            <h5
                                                                                class="fs-14 text-truncate mb-1"
                                                                            >
                                                                                <BLink
                                                                                    href="#"
                                                                                    class="text-body"
                                                                                    >Brand
                                                                                    logo
                                                                                    Design</BLink
                                                                                >
                                                                            </h5>
                                                                            <p
                                                                                class="text-muted text-truncate mb-0"
                                                                            >
                                                                                Last
                                                                                Update
                                                                                :
                                                                                <span
                                                                                    class="fw-semibold text-body"
                                                                                    >2
                                                                                    hr
                                                                                    Ago</span
                                                                                >
                                                                            </p>
                                                                        </div>
                                                                        <div
                                                                            class="flex-shrink-0 ms-2"
                                                                        >
                                                                            <BBadge
                                                                                variant="warning-subtle"
                                                                                class="bg-warning-subtle text-warning fs-10"
                                                                            >
                                                                                Inprogress
                                                                            </BBadge>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="d-flex mt-4"
                                                                    >
                                                                        <div
                                                                            class="flex-grow-1"
                                                                        >
                                                                            <div
                                                                                class="d-flex align-items-center gap-2"
                                                                            >
                                                                                <div>
                                                                                    <h5
                                                                                        class="fs-12 text-muted mb-0"
                                                                                    >
                                                                                        Members
                                                                                        :
                                                                                    </h5>
                                                                                </div>
                                                                                <div
                                                                                    class="avatar-group"
                                                                                >
                                                                                    <div
                                                                                        class="avatar-group-item"
                                                                                    >
                                                                                        <div
                                                                                            class="avatar-xs"
                                                                                        >
                                                                                            <img
                                                                                                src="@assets/images/users/avatar-5.jpg"
                                                                                                alt=""
                                                                                                class="rounded-circle img-fluid"
                                                                                            />
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </BCardBody>
                                                            </BCard>
                                                        </div>
                                                    </swiper-slide>

                                                    <swiper-slide>
                                                        <div
                                                            class="swiper-slide"
                                                        >
                                                            <BCard
                                                                no-body
                                                                class="profile-project-card shadow-none profile-project-danger mb-0"
                                                            >
                                                                <BCardBody
                                                                    class="p-4"
                                                                >
                                                                    <div
                                                                        class="d-flex"
                                                                    >
                                                                        <div
                                                                            class="flex-grow-1 text-muted overflow-hidden"
                                                                        >
                                                                            <h5
                                                                                class="fs-14 text-truncate mb-1"
                                                                            >
                                                                                <BLink
                                                                                    href="#"
                                                                                    class="text-body"
                                                                                    >Project
                                                                                    update</BLink
                                                                                >
                                                                            </h5>
                                                                            <p
                                                                                class="text-muted text-truncate mb-0"
                                                                            >
                                                                                Last
                                                                                Update
                                                                                :
                                                                                <span
                                                                                    class="fw-semibold text-body"
                                                                                    >4
                                                                                    hr
                                                                                    Ago</span
                                                                                >
                                                                            </p>
                                                                        </div>
                                                                        <div
                                                                            class="flex-shrink-0 ms-2"
                                                                        >
                                                                            <BBadge
                                                                                variant="success-subtle"
                                                                                class="bg-success-subtle text-success fs-10"
                                                                            >
                                                                                Completed
                                                                            </BBadge>
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        class="d-flex mt-4"
                                                                    >
                                                                        <div
                                                                            class="flex-grow-1"
                                                                        >
                                                                            <div
                                                                                class="d-flex align-items-center gap-2"
                                                                            >
                                                                                <div>
                                                                                    <h5
                                                                                        class="fs-12 text-muted mb-0"
                                                                                    >
                                                                                        Members
                                                                                        :
                                                                                    </h5>
                                                                                </div>
                                                                                <div
                                                                                    class="avatar-group"
                                                                                >
                                                                                    <div
                                                                                        class="avatar-group-item"
                                                                                    >
                                                                                        <div
                                                                                            class="avatar-xs"
                                                                                        >
                                                                                            <img
                                                                                                src="@assets/images/users/avatar-4.jpg"
                                                                                                alt=""
                                                                                                class="rounded-circle img-fluid"
                                                                                            />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div
                                                                                        class="avatar-group-item"
                                                                                    >
                                                                                        <div
                                                                                            class="avatar-xs"
                                                                                        >
                                                                                            <img
                                                                                                src="@assets/images/users/avatar-5.jpg"
                                                                                                alt=""
                                                                                                class="rounded-circle img-fluid"
                                                                                            />
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </BCardBody>
                                                            </BCard>
                                                        </div>
                                                    </swiper-slide>
                                                    <swiper-slide>
                                                        <div
                                                            class="swiper-slide"
                                                        >
                                                            <BCard
                                                                no-body
                                                                class="profile-project-card shadow-none profile-project-warning mb-0"
                                                            >
                                                                <BCardBody
                                                                    class="p-4"
                                                                >
                                                                    <div
                                                                        class="d-flex"
                                                                    >
                                                                        <div
                                                                            class="flex-grow-1 text-muted overflow-hidden"
                                                                        >
                                                                            <h5
                                                                                class="fs-14 text-truncate mb-1"
                                                                            >
                                                                                <BLink
                                                                                    href="#"
                                                                                    class="text-body"
                                                                                    >Chat
                                                                                    App</BLink
                                                                                >
                                                                            </h5>
                                                                            <p
                                                                                class="text-muted text-truncate mb-0"
                                                                            >
                                                                                Last
                                                                                Update
                                                                                :
                                                                                <span
                                                                                    class="fw-semibold text-body"
                                                                                    >1
                                                                                    hr
                                                                                    Ago</span
                                                                                >
                                                                            </p>
                                                                        </div>
                                                                        <div
                                                                            class="flex-shrink-0 ms-2"
                                                                        >
                                                                            <BBadge
                                                                                variant="warning-subtle"
                                                                                class="bg-warning-subtle text-warning fs-10"
                                                                            >
                                                                                Inprogress
                                                                            </BBadge>
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        class="d-flex mt-4"
                                                                    >
                                                                        <div
                                                                            class="flex-grow-1"
                                                                        >
                                                                            <div
                                                                                class="d-flex align-items-center gap-2"
                                                                            >
                                                                                <div>
                                                                                    <h5
                                                                                        class="fs-12 text-muted mb-0"
                                                                                    >
                                                                                        Members
                                                                                        :
                                                                                    </h5>
                                                                                </div>
                                                                                <div
                                                                                    class="avatar-group"
                                                                                >
                                                                                    <div
                                                                                        class="avatar-group-item"
                                                                                    >
                                                                                        <div
                                                                                            class="avatar-xs"
                                                                                        >
                                                                                            <img
                                                                                                src="@assets/images/users/avatar-4.jpg"
                                                                                                alt=""
                                                                                                class="rounded-circle img-fluid"
                                                                                            />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div
                                                                                        class="avatar-group-item"
                                                                                    >
                                                                                        <div
                                                                                            class="avatar-xs"
                                                                                        >
                                                                                            <img
                                                                                                src="@assets/images/users/avatar-5.jpg"
                                                                                                alt=""
                                                                                                class="rounded-circle img-fluid"
                                                                                            />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div
                                                                                        class="avatar-group-item"
                                                                                    >
                                                                                        <div
                                                                                            class="avatar-xs"
                                                                                        >
                                                                                            <div
                                                                                                class="avatar-title rounded-circle bg-light text-primary"
                                                                                            >
                                                                                                A
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </BCardBody>
                                                            </BCard>
                                                        </div>
                                                    </swiper-slide>
                                                </swiper>
                                            </div>
                                        </BCardBody>
                                    </BCard>
                                </BCol>
                            </BRow>
                        </BTab>

                        <!-- project -->
                        <BTab class="nav-item pt-4">
                            <template #title>
                                <i
                                    class="ri-price-tag-line d-inline-block d-md-none"
                                ></i>
                                <span class="d-none d-md-inline-block"
                                    >Projects</span
                                >
                            </template>
                            <BCard no-body>
                                <BCardBody>
                                    <BRow>
                                        <BCol xxl="3" sm="6">
                                            <BCard
                                                no-body
                                                class="profile-project-card shadow-none profile-project-warning"
                                            >
                                                <BCardBody class="p-4">
                                                    <div class="d-flex">
                                                        <div
                                                            class="flex-grow-1 text-muted overflow-hidden"
                                                        >
                                                            <h5
                                                                class="fs-14 text-truncate"
                                                            >
                                                                <BLink
                                                                    href="#"
                                                                    class="text-body"
                                                                    >Chat App
                                                                    Update</BLink
                                                                >
                                                            </h5>
                                                            <p
                                                                class="text-muted text-truncate mb-0"
                                                            >
                                                                Last Update :
                                                                <span
                                                                    class="fw-semibold text-body"
                                                                    >2 year
                                                                    Ago</span
                                                                >
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="flex-shrink-0 ms-2"
                                                        >
                                                            <BBadge
                                                                variant="warning-subtle"
                                                                class="bg-warning-subtle text-warning fs-10"
                                                            >
                                                                Inprogress
                                                            </BBadge>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex mt-4">
                                                        <div
                                                            class="flex-grow-1"
                                                        >
                                                            <div
                                                                class="d-flex align-items-center gap-2"
                                                            >
                                                                <div>
                                                                    <h5
                                                                        class="fs-12 text-muted mb-0"
                                                                    >
                                                                        Members
                                                                        :
                                                                    </h5>
                                                                </div>
                                                                <div
                                                                    class="avatar-group"
                                                                >
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-1.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-3.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <div
                                                                                class="avatar-title rounded-circle bg-light text-primary"
                                                                            >
                                                                                J
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </BCardBody>
                                            </BCard>
                                        </BCol>
                                        <BCol xxl="3" sm="6">
                                            <BCard
                                                no-body
                                                class="profile-project-card shadow-none profile-project-success"
                                            >
                                                <BCardBody class="p-4">
                                                    <div class="d-flex">
                                                        <div
                                                            class="flex-grow-1 text-muted overflow-hidden"
                                                        >
                                                            <h5
                                                                class="fs-14 text-truncate"
                                                            >
                                                                <BLink
                                                                    href="#"
                                                                    class="text-body"
                                                                    >ABC Project
                                                                    Customization</BLink
                                                                >
                                                            </h5>
                                                            <p
                                                                class="text-muted text-truncate mb-0"
                                                            >
                                                                Last Update :
                                                                <span
                                                                    class="fw-semibold text-body"
                                                                    >2 month
                                                                    Ago</span
                                                                >
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="flex-shrink-0 ms-2"
                                                        >
                                                            <BBadge
                                                                variant="primary-subtle"
                                                                class="bg-primary-subtle text-primary fs-10"
                                                            >
                                                                Progress
                                                            </BBadge>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex mt-4">
                                                        <div
                                                            class="flex-grow-1"
                                                        >
                                                            <div
                                                                class="d-flex align-items-center gap-2"
                                                            >
                                                                <div>
                                                                    <h5
                                                                        class="fs-12 text-muted mb-0"
                                                                    >
                                                                        Members
                                                                        :
                                                                    </h5>
                                                                </div>
                                                                <div
                                                                    class="avatar-group"
                                                                >
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-8.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-7.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-6.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <div
                                                                                class="avatar-title rounded-circle bg-primary"
                                                                            >
                                                                                2+
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </BCardBody>
                                            </BCard>
                                        </BCol>
                                        <BCol xxl="3" sm="6">
                                            <BCard
                                                no-body
                                                class="profile-project-card shadow-none profile-project-info"
                                            >
                                                <BCardBody class="p-4">
                                                    <div class="d-flex">
                                                        <div
                                                            class="flex-grow-1 text-muted overflow-hidden"
                                                        >
                                                            <h5
                                                                class="fs-14 text-truncate"
                                                            >
                                                                <BLink
                                                                    href="#"
                                                                    class="text-body"
                                                                    >Client -
                                                                    Frank
                                                                    Hook</BLink
                                                                >
                                                            </h5>
                                                            <p
                                                                class="text-muted text-truncate mb-0"
                                                            >
                                                                Last Update :
                                                                <span
                                                                    class="fw-semibold text-body"
                                                                    >1 hr
                                                                    Ago</span
                                                                >
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="flex-shrink-0 ms-2"
                                                        >
                                                            <BBadge
                                                                variant="info-subtle"
                                                                class="bg-info-subtle text-info fs-10"
                                                                >New</BBadge
                                                            >
                                                        </div>
                                                    </div>

                                                    <div class="d-flex mt-4">
                                                        <div
                                                            class="flex-grow-1"
                                                        >
                                                            <div
                                                                class="d-flex align-items-center gap-2"
                                                            >
                                                                <div>
                                                                    <h5
                                                                        class="fs-12 text-muted mb-0"
                                                                    >
                                                                        Members
                                                                        :
                                                                    </h5>
                                                                </div>
                                                                <div
                                                                    class="avatar-group"
                                                                >
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-4.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <div
                                                                                class="avatar-title rounded-circle bg-light text-primary"
                                                                            >
                                                                                M
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-3.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </BCardBody>
                                            </BCard>
                                        </BCol>
                                        <BCol xxl="3" sm="6">
                                            <BCard
                                                no-body
                                                class="profile-project-card shadow-none profile-project-primary"
                                            >
                                                <BCardBody class="p-4">
                                                    <div class="d-flex">
                                                        <div
                                                            class="flex-grow-1 text-muted overflow-hidden"
                                                        >
                                                            <h5
                                                                class="fs-14 text-truncate"
                                                            >
                                                                <BLink
                                                                    href="#"
                                                                    class="text-body"
                                                                    >Velzon
                                                                    Project</BLink
                                                                >
                                                            </h5>
                                                            <p
                                                                class="text-muted text-truncate mb-0"
                                                            >
                                                                Last Update :
                                                                <span
                                                                    class="fw-semibold text-body"
                                                                    >11 hr
                                                                    Ago</span
                                                                >
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="flex-shrink-0 ms-2"
                                                        >
                                                            <BBadge
                                                                variant="success-subtle"
                                                                class="bg-success-subtle text-success fs-10"
                                                            >
                                                                Completed
                                                            </BBadge>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex mt-4">
                                                        <div
                                                            class="flex-grow-1"
                                                        >
                                                            <div
                                                                class="d-flex align-items-center gap-2"
                                                            >
                                                                <div>
                                                                    <h5
                                                                        class="fs-12 text-muted mb-0"
                                                                    >
                                                                        Members
                                                                        :
                                                                    </h5>
                                                                </div>
                                                                <div
                                                                    class="avatar-group"
                                                                >
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-7.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-5.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </BCardBody>
                                            </BCard>
                                        </BCol>
                                        <BCol xxl="3" sm="6">
                                            <BCard
                                                no-body
                                                class="profile-project-card shadow-none profile-project-danger"
                                            >
                                                <BCardBody class="p-4">
                                                    <div class="d-flex">
                                                        <div
                                                            class="flex-grow-1 text-muted overflow-hidden"
                                                        >
                                                            <h5
                                                                class="fs-14 text-truncate"
                                                            >
                                                                <BLink
                                                                    href="#"
                                                                    class="text-body"
                                                                    >Brand Logo
                                                                    Design</BLink
                                                                >
                                                            </h5>
                                                            <p
                                                                class="text-muted text-truncate mb-0"
                                                            >
                                                                Last Update :
                                                                <span
                                                                    class="fw-semibold text-body"
                                                                    >10 min
                                                                    Ago</span
                                                                >
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="flex-shrink-0 ms-2"
                                                        >
                                                            <BBadge
                                                                variant="info-subtle"
                                                                class="bg-info-subtle text-info fs-10"
                                                                >New</BBadge
                                                            >
                                                        </div>
                                                    </div>

                                                    <div class="d-flex mt-4">
                                                        <div
                                                            class="flex-grow-1"
                                                        >
                                                            <div
                                                                class="d-flex align-items-center gap-2"
                                                            >
                                                                <div>
                                                                    <h5
                                                                        class="fs-12 text-muted mb-0"
                                                                    >
                                                                        Members
                                                                        :
                                                                    </h5>
                                                                </div>
                                                                <div
                                                                    class="avatar-group"
                                                                >
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-7.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-6.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <div
                                                                                class="avatar-title rounded-circle bg-light text-primary"
                                                                            >
                                                                                E
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </BCardBody>
                                            </BCard>
                                        </BCol>
                                        <BCol xxl="3" sm="6">
                                            <BCard
                                                no-body
                                                class="profile-project-card shadow-none profile-project-primary"
                                            >
                                                <BCardBody class="p-4">
                                                    <div class="d-flex">
                                                        <div
                                                            class="flex-grow-1 text-muted overflow-hidden"
                                                        >
                                                            <h5
                                                                class="fs-14 text-truncate"
                                                            >
                                                                <BLink
                                                                    href="#"
                                                                    class="text-body"
                                                                    >Chat
                                                                    App</BLink
                                                                >
                                                            </h5>
                                                            <p
                                                                class="text-muted text-truncate mb-0"
                                                            >
                                                                Last Update :
                                                                <span
                                                                    class="fw-semibold text-body"
                                                                    >8 hr
                                                                    Ago</span
                                                                >
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="flex-shrink-0 ms-2"
                                                        >
                                                            <BBadge
                                                                variant="warning-subtle"
                                                                class="bg-warning-subtle text-warning fs-10"
                                                            >
                                                                Inprogress
                                                            </BBadge>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex mt-4">
                                                        <div
                                                            class="flex-grow-1"
                                                        >
                                                            <div
                                                                class="d-flex align-items-center gap-2"
                                                            >
                                                                <div>
                                                                    <h5
                                                                        class="fs-12 text-muted mb-0"
                                                                    >
                                                                        Members
                                                                        :
                                                                    </h5>
                                                                </div>
                                                                <div
                                                                    class="avatar-group"
                                                                >
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <div
                                                                                class="avatar-title rounded-circle bg-light text-primary"
                                                                            >
                                                                                R
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-3.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-8.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </BCardBody>
                                            </BCard>
                                        </BCol>
                                        <BCol xxl="3" sm="6">
                                            <BCard
                                                no-body
                                                class="profile-project-card shadow-none profile-project-warning"
                                            >
                                                <BCardBody class="p-4">
                                                    <div class="d-flex">
                                                        <div
                                                            class="flex-grow-1 text-muted overflow-hidden"
                                                        >
                                                            <h5
                                                                class="fs-14 text-truncate"
                                                            >
                                                                <BLink
                                                                    href="#"
                                                                    class="text-body"
                                                                    >Project
                                                                    Update</BLink
                                                                >
                                                            </h5>
                                                            <p
                                                                class="text-muted text-truncate mb-0"
                                                            >
                                                                Last Update :
                                                                <span
                                                                    class="fw-semibold text-body"
                                                                    >48 min
                                                                    Ago</span
                                                                >
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="flex-shrink-0 ms-2"
                                                        >
                                                            <BBadge
                                                                variant="warning-subtle"
                                                                class="bg-warning-subtle text-warning fs-10"
                                                            >
                                                                Inprogress
                                                            </BBadge>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex mt-4">
                                                        <div
                                                            class="flex-grow-1"
                                                        >
                                                            <div
                                                                class="d-flex align-items-center gap-2"
                                                            >
                                                                <div>
                                                                    <h5
                                                                        class="fs-12 text-muted mb-0"
                                                                    >
                                                                        Members
                                                                        :
                                                                    </h5>
                                                                </div>
                                                                <div
                                                                    class="avatar-group"
                                                                >
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-6.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-5.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-4.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </BCardBody>
                                            </BCard>
                                        </BCol>
                                        <BCol xxl="3" sm="6">
                                            <BCard
                                                no-body
                                                class="profile-project-card shadow-none profile-project-success"
                                            >
                                                <BCardBody class="p-4">
                                                    <div class="d-flex">
                                                        <div
                                                            class="flex-grow-1 text-muted overflow-hidden"
                                                        >
                                                            <h5
                                                                class="fs-14 text-truncate"
                                                            >
                                                                <BLink
                                                                    href="#"
                                                                    class="text-body"
                                                                    >Client -
                                                                    Jennifer</BLink
                                                                >
                                                            </h5>
                                                            <p
                                                                class="text-muted text-truncate mb-0"
                                                            >
                                                                Last Update :
                                                                <span
                                                                    class="fw-semibold text-body"
                                                                    >30 min
                                                                    Ago</span
                                                                >
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="flex-shrink-0 ms-2"
                                                        >
                                                            <BBadge
                                                                variant="primary-subtle"
                                                                class="bg-primary-subtle text-primary fs-10"
                                                            >
                                                                Process
                                                            </BBadge>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex mt-4">
                                                        <div
                                                            class="flex-grow-1"
                                                        >
                                                            <div
                                                                class="d-flex align-items-center gap-2"
                                                            >
                                                                <div>
                                                                    <h5
                                                                        class="fs-12 text-muted mb-0"
                                                                    >
                                                                        Members
                                                                        :
                                                                    </h5>
                                                                </div>
                                                                <div
                                                                    class="avatar-group"
                                                                >
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-1.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </BCardBody>
                                            </BCard>
                                        </BCol>
                                        <BCol xxl="3" sm="6">
                                            <BCard
                                                no-body
                                                class="profile-project-card shadow-none mb-xxl-0 profile-project-info"
                                            >
                                                <BCardBody class="p-4">
                                                    <div class="d-flex">
                                                        <div
                                                            class="flex-grow-1 text-muted overflow-hidden"
                                                        >
                                                            <h5
                                                                class="fs-14 text-truncate"
                                                            >
                                                                <BLink
                                                                    href="#"
                                                                    class="text-body"
                                                                    >Business
                                                                    Template -
                                                                    UI/UX
                                                                    design</BLink
                                                                >
                                                            </h5>
                                                            <p
                                                                class="text-muted text-truncate mb-0"
                                                            >
                                                                Last Update :
                                                                <span
                                                                    class="fw-semibold text-body"
                                                                    >7 month
                                                                    Ago</span
                                                                >
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="flex-shrink-0 ms-2"
                                                        >
                                                            <BBadge
                                                                variant="success-subtle"
                                                                class="bg-success-subtle text-success fs-10"
                                                            >
                                                                Completed
                                                            </BBadge>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex mt-4">
                                                        <div
                                                            class="flex-grow-1"
                                                        >
                                                            <div
                                                                class="d-flex align-items-center gap-2"
                                                            >
                                                                <div>
                                                                    <h5
                                                                        class="fs-12 text-muted mb-0"
                                                                    >
                                                                        Members
                                                                        :
                                                                    </h5>
                                                                </div>
                                                                <div
                                                                    class="avatar-group"
                                                                >
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-2.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-3.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-4.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <div
                                                                                class="avatar-title rounded-circle bg-primary"
                                                                            >
                                                                                2+
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </BCardBody>
                                            </BCard>
                                        </BCol>
                                        <BCol xxl="3" sm="6">
                                            <BCard
                                                no-body
                                                class="profile-project-card shadow-none mb-xxl-0 profile-project-success"
                                            >
                                                <BCardBody class="p-4">
                                                    <div class="d-flex">
                                                        <div
                                                            class="flex-grow-1 text-muted overflow-hidden"
                                                        >
                                                            <h5
                                                                class="fs-14 text-truncate"
                                                            >
                                                                <BLink
                                                                    href="#"
                                                                    class="text-body"
                                                                    >Update
                                                                    Project</BLink
                                                                >
                                                            </h5>
                                                            <p
                                                                class="text-muted text-truncate mb-0"
                                                            >
                                                                Last Update :
                                                                <span
                                                                    class="fw-semibold text-body"
                                                                    >1 month
                                                                    Ago</span
                                                                >
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="flex-shrink-0 ms-2"
                                                        >
                                                            <BBadge
                                                                variant="info-subtle"
                                                                class="bg-info-subtle text-info fs-10"
                                                                >New</BBadge
                                                            >
                                                        </div>
                                                    </div>
                                                    <div class="d-flex mt-4">
                                                        <div
                                                            class="flex-grow-1"
                                                        >
                                                            <div
                                                                class="d-flex align-items-center gap-2"
                                                            >
                                                                <div>
                                                                    <h5
                                                                        class="fs-12 text-muted mb-0"
                                                                    >
                                                                        Members
                                                                        :
                                                                    </h5>
                                                                </div>
                                                                <div
                                                                    class="avatar-group"
                                                                >
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-7.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <div
                                                                                class="avatar-title rounded-circle bg-light text-primary"
                                                                            >
                                                                                A
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </BCardBody>
                                            </BCard>
                                        </BCol>
                                        <BCol xxl="3" sm="6">
                                            <BCard
                                                no-body
                                                class="profile-project-card shadow-none mb-sm-0 profile-project-danger"
                                            >
                                                <BCardBody class="p-4">
                                                    <div class="d-flex">
                                                        <div
                                                            class="flex-grow-1 text-muted overflow-hidden"
                                                        >
                                                            <h5
                                                                class="fs-14 text-truncate"
                                                            >
                                                                <BLink
                                                                    href="#"
                                                                    class="text-body"
                                                                    >Bank
                                                                    Management
                                                                    System</BLink
                                                                >
                                                            </h5>
                                                            <p
                                                                class="text-muted text-truncate mb-0"
                                                            >
                                                                Last Update :
                                                                <span
                                                                    class="fw-semibold text-body"
                                                                    >10 month
                                                                    Ago</span
                                                                >
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="flex-shrink-0 ms-2"
                                                        >
                                                            <BBadge
                                                                variant="success-subtle"
                                                                class="bg-success-subtle text-success fs-10"
                                                            >
                                                                Completed
                                                            </BBadge>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex mt-4">
                                                        <div
                                                            class="flex-grow-1"
                                                        >
                                                            <div
                                                                class="d-flex align-items-center gap-2"
                                                            >
                                                                <div>
                                                                    <h5
                                                                        class="fs-12 text-muted mb-0"
                                                                    >
                                                                        Members
                                                                        :
                                                                    </h5>
                                                                </div>
                                                                <div
                                                                    class="avatar-group"
                                                                >
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-7.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-6.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-5.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <div
                                                                                class="avatar-title rounded-circle bg-primary"
                                                                            >
                                                                                2+
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </BCardBody>
                                            </BCard>
                                        </BCol>
                                        <BCol xxl="3" sm="6">
                                            <BCard
                                                no-body
                                                class="profile-project-card shadow-none mb-0 profile-project-primary"
                                            >
                                                <BCardBody class="p-4">
                                                    <div class="d-flex">
                                                        <div
                                                            class="flex-grow-1 text-muted overflow-hidden"
                                                        >
                                                            <h5
                                                                class="fs-14 text-truncate"
                                                            >
                                                                <BLink
                                                                    href="#"
                                                                    class="text-body"
                                                                    >PSD to HTML
                                                                    Convert</BLink
                                                                >
                                                            </h5>
                                                            <p
                                                                class="text-muted text-truncate mb-0"
                                                            >
                                                                Last Update :
                                                                <span
                                                                    class="fw-semibold text-body"
                                                                    >29 min
                                                                    Ago</span
                                                                >
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="flex-shrink-0 ms-2"
                                                        >
                                                            <BBadge
                                                                variant="info-subtle"
                                                                class="bg-info-subtle text-info fs-10"
                                                                >New</BBadge
                                                            >
                                                        </div>
                                                    </div>
                                                    <div class="d-flex mt-4">
                                                        <div
                                                            class="flex-grow-1"
                                                        >
                                                            <div
                                                                class="d-flex align-items-center gap-2"
                                                            >
                                                                <div>
                                                                    <h5
                                                                        class="fs-12 text-muted mb-0"
                                                                    >
                                                                        Members
                                                                        :
                                                                    </h5>
                                                                </div>
                                                                <div
                                                                    class="avatar-group"
                                                                >
                                                                    <div
                                                                        class="avatar-group-item"
                                                                    >
                                                                        <div
                                                                            class="avatar-xs"
                                                                        >
                                                                            <img
                                                                                src="@assets/images/users/avatar-7.jpg"
                                                                                alt=""
                                                                                class="rounded-circle img-fluid"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </BCardBody>
                                            </BCard>
                                        </BCol>
                                        <BCol lg="12">
                                            <div class="mt-4">
                                                <ul
                                                    class="pagination pagination-separated justify-content-center mb-0"
                                                >
                                                    <li
                                                        class="page-item disabled"
                                                    >
                                                        <BLink
                                                            href="javascript:void(0);"
                                                            class="page-link"
                                                            ><i
                                                                class="mdi mdi-chevron-left"
                                                            ></i>
                                                        </BLink>
                                                    </li>
                                                    <li
                                                        class="page-item active"
                                                    >
                                                        <BLink
                                                            href="javascript:void(0);"
                                                            class="page-link"
                                                            >1</BLink
                                                        >
                                                    </li>
                                                    <li class="page-item">
                                                        <BLink
                                                            href="javascript:void(0);"
                                                            class="page-link"
                                                            >2</BLink
                                                        >
                                                    </li>
                                                    <li class="page-item">
                                                        <BLink
                                                            href="javascript:void(0);"
                                                            class="page-link"
                                                            >3</BLink
                                                        >
                                                    </li>
                                                    <li class="page-item">
                                                        <BLink
                                                            href="javascript:void(0);"
                                                            class="page-link"
                                                            >4</BLink
                                                        >
                                                    </li>
                                                    <li class="page-item">
                                                        <BLink
                                                            href="javascript:void(0);"
                                                            class="page-link"
                                                            >5</BLink
                                                        >
                                                    </li>
                                                    <li class="page-item">
                                                        <BLink
                                                            href="javascript:void(0);"
                                                            class="page-link"
                                                            ><i
                                                                class="mdi mdi-chevron-right"
                                                            ></i>
                                                        </BLink>
                                                    </li>
                                                </ul>
                                            </div>
                                        </BCol>
                                    </BRow>
                                </BCardBody>
                            </BCard>
                        </BTab>
                    </BTabs>
                </div>
            </BCol>
        </BRow>
    </Layout>
</template>

<style>
.tamp {
    position: absolute;
    top: 17px;
    right: 16px;
}
</style>

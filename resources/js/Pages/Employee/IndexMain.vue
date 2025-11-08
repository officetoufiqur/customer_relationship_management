<script>
import { Link, Head } from "@inertiajs/vue3";
import { CountTo } from "vue3-count-to";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";

import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Swal from "sweetalert2";
import axios from "axios";
import animationData from "@/Components/widgets/msoeawqm.json";
import animationData1 from "@/Components/widgets/gsqxdxog.json";
import Lottie from "@/Components/widgets/lottie.vue";
import simplebar from "simplebar-vue";
export default {
    data() {
        return {
            userListModal: false,
            date3: null,
            rangeDateconfig: {
                wrap: true, // set wrap to true only when using 'input-group'
                altFormat: "M j, Y",
                altInput: true,
                dateFormat: "d M, Y",
                mode: "range",
            },
            config: {
                wrap: true, // set wrap to true only when using 'input-group'
                altFormat: "M j, Y",
                altInput: true,
                dateFormat: "d M, Y",
            },
            timeConfig: {
                enableTime: false,
                dateFormat: "d M, Y",
            },
            filterdate: null,
            filterdate1: null,
            filtervalue: "All",
            filtervalue1: "All",
            filtersearchQuery1: null,
            date: null,
            users: [],
            searchQuery: null,
            page: 1,
            perPage: 8,
            pages: [],
            defaultOptions: {
                animationData: animationData,
            },
            defaultOptions1: {
                animationData: animationData1,
            },

            //
            submitted: false,

            dataEdit: false,
            deleteModal: false,
            event: {
                id: "",
                name: "",
                email: "",
            },
        };
    },
    components: {
        CountTo,
        Layout,
        PageHeader,
        lottie: Lottie,
        Multiselect,
        flatPickr,
        simplebar,
        Link,
        Head,
    },
    computed: {
        displayedPosts() {
            return this.paginate(this.users);
        },
        resultQuery() {
            // Defensive search + filters. Some API responses may not include all fields
            // so protect against undefined values to avoid throwing and breaking render.
            const posts = this.displayedPosts || [];

            // Search
            if (this.searchQuery) {
                const search = this.searchQuery.toLowerCase();
                return posts.filter((data) => {
                    const name = (data.name || "").toString().toLowerCase();
                    const email = (data.email || "").toString().toLowerCase();
                    const id = (data.id || data._id || data.userId || "")
                        .toString()
                        .toLowerCase();
                    return (
                        name.includes(search) ||
                        email.includes(search) ||
                        id.includes(search)
                    );
                });
            }

            // Date range filter
            if (this.filterdate) {
                const parts = this.filterdate.split(" to ");
                const date1 = parts[0] ? new Date(parts[0]) : null;
                const date2 = parts[1] ? new Date(parts[1]) : null;
                if (!date1 || !date2) return posts;
                return posts.filter((data) => {
                    if (!data.dueDate) return false;
                    const d = new Date(data.dueDate);
                    return d >= date1 && d <= date2;
                });
            }

            // Status filter
            if (this.filtervalue) {
                return posts.filter((data) => {
                    if (!this.filtervalue || this.filtervalue === "All")
                        return true;
                    return (data.status || "") === this.filtervalue;
                });
            }

            return posts;
        },
    },
    watch: {
        users() {
            this.setPages();
        },
    },
    created() {
        this.setPages();
    },
    filters: {
        trimWords(value) {
            return value.split(" ").splice(0, 20).join(" ") + "...";
        },
    },
    beforeMount() {
        // Fetch employees. Be defensive about response shape so we don't throw
        // and leave `users` empty if the API shape is different.
        axios
            .get("/api/employees")
            .then((res) => {
                this.users = [];
                const monthNames = [
                    "Jan",
                    "Feb",
                    "Mar",
                    "Apr",
                    "May",
                    "Jun",
                    "Jul",
                    "Aug",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dec",
                ];

                // Normalize response: try several common shapes
                const payload =
                    res && res.data ? res.data.data || res.data || res : [];
                const rows = Array.isArray(payload)
                    ? payload
                    : Array.isArray(payload.data)
                    ? payload.data
                    : [];

                rows.forEach((row) => {
                    try {
                        const mapped = {
                            // prefer common fields; fall back to alternatives
                            id: row.id ?? row._id ?? row.userId ?? "",
                            name:
                                row.name ??
                                row.full_name ??
                                (row.first_name
                                    ? `${row.first_name} ${row.last_name || ""}`
                                    : "") ??
                                "",
                            email: row.email ?? row.email_address ?? "",
                            dueDate:
                                row.dueDate ??
                                row.due_date ??
                                row.created_at ??
                                null,
                            status: row.status ?? row.state ?? null,
                            // keep raw row for anything else
                            _raw: row,
                        };

                        // Format dueDate if present and is a valid date
                        if (mapped.dueDate) {
                            const dd = new Date(mapped.dueDate);
                            if (!isNaN(dd.getTime())) {
                                mapped.dueDate =
                                    dd.getDate() +
                                    " " +
                                    monthNames[dd.getMonth()] +
                                    ", " +
                                    dd.getFullYear();
                            }
                        }

                        // Normalize subItem if exists
                        const sub =
                            row.subItem || row.sub_items || row.items || [];
                        mapped.subItem = Array.isArray(sub)
                            ? sub.map((imag) => ({
                                  ...imag,
                                  image_src:
                                      imag.image_src ??
                                      (imag.img
                                          ? "https://api-node.themesbrand.website/images/users/" +
                                            imag.img
                                          : undefined),
                              }))
                            : [];

                        this.users.push(mapped);
                    } catch (e) {
                        // Skip rows that unexpectedly fail, but don't block the whole list
                        console.error("Failed to normalize employee row", e);
                    }
                });
            })
            .catch((er) => {
                console.error("Failed to load employees", er);
            });
    },

    methods: {
        //
        handleSubmit() {
            if (this.dataEdit) {
                this.submitted = true;
                if (
                    this.submitted &&
                    this.event.project &&
                    this.event.user &&
                    this.event.creater &&
                    this.event.dueDate &&
                    this.event.status &&
                    this.event.priority
                ) {
                    this.userListModal = false;

                    axios
                        .patch(
                            `https://api-node.themesbrand.website/apps/user/${this.event._id}`,
                            this.event
                        )
                        .then((response) => {
                            const data = response.data.data;
                            this.users = this.users.map((item) =>
                                item._id.toString() == data._id.toString()
                                    ? { ...item, ...data }
                                    : item
                            );
                        })
                        .catch((er) => {
                            console.log(er);
                        });
                }
            } else {
                this.submitted = true;
                // [1] Use the correct validation fields: name, email, password
                if (
                    this.submitted &&
                    this.event.name &&
                    this.event.email &&
                    this.event.password
                ) {
                    this.userListModal = false;
                    axios
                        .post(`/api/employees/store`, this.event)
                        .then((response) => {
                            this.users.unshift(response.data.data);
                            Swal.fire(
                                "Success!",
                                "User created successfully.",
                                "success"
                            );
                        })
                        .catch((er) => {
                            console.error("API Store Error:", er);
                            Swal.fire(
                                "Error!",
                                "Failed to save user data.",
                                "error"
                            );
                        });
                }
            }
        },

        onSort(column) {
            this.direction = this.direction === "asc" ? "desc" : "asc";
            const sortedArray = [...this.users];
            sortedArray.sort((a, b) => {
                const res =
                    a[column] < b[column] ? -1 : a[column] > b[column] ? 1 : 0;
                return this.direction === "asc" ? res : -res;
            });
            this.users = sortedArray;
        },

        editDetails(data) {
            this.dataEdit = true;
            this.userListModal = true;
            this.event = { ...data };

            this.submitted = false;
        },

        toggleModal() {
            this.userListModal = true;
            this.dataEdit = false;
            this.event = {};

            this.submitted = false;
        },

        deleteModalToggle(data) {
            this.deleteModal = true;
            this.event._id = data._id;
        },

        deleteData() {
            if (this.event._id) {
                axios
                    .delete(
                        `https://api-node.themesbrand.website/apps/user/${this.event._id}`
                    )
                    .then((response) => {
                        if (response.data.status === "success") {
                            this.users = this.users.filter(
                                (item) => item._id != this.event._id
                            );
                        }
                    })
                    .catch((er) => {
                        console.log(er);
                    });

                this.deleteModal = false;
            }
        },
        //

        SearchData() {
            this.filterdate = this.filterdate1;
            this.filtervalue = this.filtervalue1;
        },

        deleteMultiple() {
            let ids_array = [];
            var items = document.getElementsByName("chk_child");
            items.forEach(function (ele) {
                if (ele.checked == true) {
                    var trNode = ele.parentNode.parentNode.parentNode;
                    var id = trNode.querySelector(".id a").innerHTML;
                    ids_array.push(id);
                }
            });
            if (typeof ids_array !== "undefined" && ids_array.length > 0) {
                if (confirm("Are you sure you want to delete this?")) {
                    var cusList = this.users;
                    ids_array.forEach(function (id) {
                        cusList = cusList.filter(function (users) {
                            return users.userId != id;
                        });
                    });
                    this.users = cusList;
                    document.getElementById("checkAll").checked = false;
                    var itemss = document.getElementsByName("chk_child");
                    itemss.forEach(function (ele) {
                        if (ele.checked == true) {
                            ele.checked = false;
                            ele.closest("tr").classList.remove("table-active");
                        }
                    });
                } else {
                    return false;
                }
            } else {
                Swal.fire({
                    title: "Please select at least one checkbox",
                    confirmButtonClass: "btn btn-info",
                    buttonsStyling: false,
                    showCloseButton: true,
                });
            }
        },

        setPages() {
            let numberOfPages = Math.ceil(this.users.length / this.perPage);
            this.pages = [];
            for (let index = 1; index <= numberOfPages; index++) {
                this.pages.push(index);
            }
        },
        paginate(users) {
            let page = this.page;
            let perPage = this.perPage;
            let from = page * perPage - perPage;
            let to = page * perPage;
            return users.slice(from, to);
        },
    },
    mounted() {
        var checkAll = document.getElementById("checkAll");
        if (checkAll) {
            checkAll.onclick = function () {
                var checkboxes = document.querySelectorAll(
                    '.form-check-all input[type="checkbox"]'
                );

                if (checkAll.checked == true) {
                    checkboxes.forEach(function (checkbox) {
                        checkbox.checked = true;
                        checkbox.closest("tr").classList.add("table-active");
                        document.getElementById(
                            "remove-actions"
                        ).style.display = "block";
                    });
                } else {
                    checkboxes.forEach(function (checkbox) {
                        checkbox.checked = false;
                        checkbox.closest("tr").classList.remove("table-active");
                        document.getElementById(
                            "remove-actions"
                        ).style.display = "none";
                    });
                }
            };
        }

        var checkboxes = document.querySelectorAll(
            "#usersTable .form-check-input"
        );
        Array.from(checkboxes).forEach(function (element) {
            element.addEventListener("change", function (event) {
                var checkedCount = document.querySelectorAll(
                    "#usersTable .form-check-input:checked"
                ).length;

                if (
                    event.target
                        .closest("tr")
                        .classList.contains("table-active")
                ) {
                    checkedCount > 0
                        ? (document.getElementById(
                              "remove-actions"
                          ).style.display = "block")
                        : (document.getElementById(
                              "remove-actions"
                          ).style.display = "none");
                } else {
                    checkedCount > 0
                        ? (document.getElementById(
                              "remove-actions"
                          ).style.display = "block")
                        : (document.getElementById(
                              "remove-actions"
                          ).style.display = "none");
                }
            });
        });
    },
};
</script>

<template>
    <Layout>
        <PageHeader title="List View" pageTitle="users" />

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
                                        Create user
                                    </BButton>
                                </div>
                            </div>
                        </div>
                    </BCardHeader>
                    <BCardBody
                        class="border border-dashed border-end-0 border-start-0"
                    >
                        <BFrom>
                            <BRow class="g-3">
                                <BCol xxl="5" sm="12">
                                    <div class="search-box">
                                        <input
                                            type="text"
                                            class="form-control search bg-light border-light"
                                            placeholder="Search for users or something..."
                                            v-model="searchQuery"
                                        />
                                        <i
                                            class="ri-search-line search-icon"
                                        ></i>
                                    </div>
                                </BCol>

                                <BCol xxl="3" sm="4">
                                    <flat-pickr
                                        v-model="filterdate1"
                                        placeholder="Select date"
                                        :config="rangeDateconfig"
                                        class="form-control bg-light border-light"
                                    ></flat-pickr>
                                </BCol>

                                <BCol xxl="3" sm="4">
                                    <div class="input-light">
                                        <Multiselect
                                            v-model="filtervalue1"
                                            :close-on-select="true"
                                            :searchable="true"
                                            :create-option="true"
                                            :options="[
                                                { value: 'All', label: 'All' },
                                                { value: 'New', label: 'New' },
                                                {
                                                    value: 'Pending',
                                                    label: 'Pending',
                                                },
                                                {
                                                    value: 'Inprogress',
                                                    label: 'Inprogress',
                                                },
                                                {
                                                    value: 'Completed',
                                                    label: 'Completed',
                                                },
                                            ]"
                                        />
                                    </div>
                                </BCol>
                                <BCol xxl="1" sm="4">
                                    <BButton
                                        type="button"
                                        variant="primary"
                                        class="w-100"
                                        @click="SearchData"
                                    >
                                        <i
                                            class="ri-equalizer-fill me-1 align-bottom"
                                        ></i>
                                        Filters
                                    </BButton>
                                </BCol>
                            </BRow>
                        </BFrom>
                    </BCardBody>
                    <BCardBody>
                        <div class="table-responsive table-card mb-4">
                            <table
                                class="table align-middle table-nowrap mb-0"
                                id="usersTable"
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
                                            data-sort="project_name"
                                            @click="onSort('name')"
                                        >
                                            Name
                                        </th>
                                        <th
                                            class="sort"
                                            data-sort="users_name"
                                            @click="onSort('email')"
                                        >
                                            Email
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
                                    <tr
                                        v-for="(user, index) of resultQuery"
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
                                            {{ user.id }}
                                        </td>
                                        <td class="client_name">
                                            {{ user.name }}
                                        </td>
                                        <td class="due_date">
                                            {{ user.email }}
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
                                        We've searched more than 200k+ users We
                                        did not find any users for you search.
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

        <!-- user list modal -->
        <BModal
            v-model="userListModal"
            id="showmodal"
            modal-class="zoomIn"
            hide-footer
            header-class="p-3 bg-info-subtle userModal"
            class="v-modal-custom"
            centered
            size="lg"
            :title="dataEdit ? 'Edit user' : 'Add user'"
        >
            <BFrom id="addform" class="tablelist-form" autocomplete="off">
                <BRow class="g-3">
                    <!-- <input type="hidden" id="id" name=""> -->
                    <BCol lg="12">
                        <label for="projectName-field" class="form-label"
                            >Project Name</label
                        >
                        <input
                            type="text"
                            id="projectName"
                            class="form-control"
                            placeholder="Project name"
                            v-model="event.name"
                            :class="{ 'is-invalid': submitted && !event.name }"
                        />
                        <div class="invalid-feedback">
                            Please enter a project name.
                        </div>
                    </BCol>
                    <BCol lg="12">
                        <div>
                            <label for="usersTitle-field" class="form-label"
                                >Title</label
                            >
                            <input
                                type="text"
                                id="usersTitle"
                                class="form-control"
                                placeholder="Title"
                                v-model="event.email"
                                :class="{
                                    'is-invalid': submitted && !event.email,
                                }"
                            />
                            <div class="invalid-feedback">
                                Please enter a title.
                            </div>
                        </div>
                    </BCol>
                    <BCol lg="12">
                        <label for="createName-field" class="form-label"
                            >Client Name</label
                        >
                        <input
                            type="text"
                            id="createName"
                            class="form-control"
                            placeholder="Client name"
                            v-model="event.password"
                            :class="{
                                'is-invalid': submitted && !event.password,
                            }"
                        />
                        <div class="invalid-feedback">
                            Please enter a client name.
                        </div>
                    </BCol>
                    <!-- <BCol lg="12">
            <label class="form-label">Assigned To</label>
            <simplebar data-simplebar style="height: 95px">
              <ul class="list-unstyled vstack gap-2 mb-0">
                <li>
                  <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-3" type="checkbox" value="" id="James Forbes" />
                    <label class="form-check-label d-flex align-items-center" for="James Forbes">
                      <span class="flex-shrink-0">
                        <img src="@assets/images/users/avatar-2.jpg" alt="" class="avatar-xxs rounded-circle" />
                      </span>
                      <span class="flex-grow-1 ms-2"> James Forbes </span>
                    </label>
                  </div>
                </li>
                <li>
                  <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-3" type="checkbox" value="" id="john-robles" />
                    <label class="form-check-label d-flex align-items-center" for="john-robles">
                      <span class="flex-shrink-0">
                        <img src="@assets/images/users/avatar-3.jpg" alt="" class="avatar-xxs rounded-circle" />
                      </span>
                      <span class="flex-grow-1 ms-2"> John Robles </span>
                    </label>
                  </div>
                </li>
                <li>
                  <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-3" type="checkbox" name="assignedTo[]" value="avatar-4.jpg"
                      id="mary-gant">
                    <label class="form-check-label d-flex align-items-center" for="mary-gant">
                      <span class="flex-shrink-0">
                        <img src="@assets/images/users/avatar-4.jpg" alt="" class="avatar-xxs rounded-circle">
                      </span>
                      <span class="flex-grow-1 ms-2">Mary Gant</span>
                    </label>
                  </div>
                </li>
                <li>
                  <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-3" type="checkbox" value="" id="curtis-saenz" />
                    <label class="form-check-label d-flex align-items-center" for="curtis-saenz">
                      <span class="flex-shrink-0">
                        <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-xxs rounded-circle" />
                      </span>
                      <span class="flex-grow-1 ms-2">
                        Curtis Saenz
                      </span>
                    </label>
                  </div>
                </li>
                <li>
                  <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-3" type="checkbox" name="assignedTo[]" value="avatar-5.jpg"
                      id="virgie-price">
                    <label class="form-check-label d-flex align-items-center" for="virgie-price">
                      <span class="flex-shrink-0">
                        <img src="@assets/images/users/avatar-5.jpg" alt="" class="avatar-xxs rounded-circle">
                      </span>
                      <span class="flex-grow-1 ms-2">Virgie Price</span>
                    </label>
                  </div>
                </li>
                <li>
                  <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-3" type="checkbox" value="" id="anthony-mills" />
                    <label class="form-check-label d-flex align-items-center" for="anthony-mills">
                      <span class="flex-shrink-0">
                        <img src="@assets/images/users/avatar-2.jpg" alt="" class="avatar-xxs rounded-circle" />
                      </span>
                      <span class="flex-grow-1 ms-2">
                        Anthony Mills
                      </span>
                    </label>
                  </div>
                </li>
                <li>
                  <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-3" type="checkbox" value="" id="marian-angel" />
                    <label class="form-check-label d-flex align-items-center" for="marian-angel">
                      <span class="flex-shrink-0">
                        <img src="@assets/images/users/avatar-6.jpg" alt="" class="avatar-xxs rounded-circle" />
                      </span>
                      <span class="flex-grow-1 ms-2">
                        Marian Angel
                      </span>
                    </label>
                  </div>
                </li>
                <li>
                  <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-3" type="checkbox" value="" id="johnnie-walton" />
                    <label class="form-check-label d-flex align-items-center" for="johnnie-walton">
                      <span class="flex-shrink-0">
                        <img src="@assets/images/users/avatar-7.jpg" alt="" class="avatar-xxs rounded-circle" />
                      </span>
                      <span class="flex-grow-1 ms-2">
                        Johnnie Walton
                      </span>
                    </label>
                  </div>
                </li>
                <li>
                  <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-3" type="checkbox" value="" id="donna-weston" />
                    <label class="form-check-label d-flex align-items-center" for="donna-weston">
                      <span class="flex-shrink-0">
                        <img src="@assets/images/users/avatar-8.jpg" alt="" class="avatar-xxs rounded-circle" />
                      </span>
                      <span class="flex-grow-1 ms-2">
                        Donna Weston
                      </span>
                    </label>
                  </div>
                </li>
                <li>
                  <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-3" type="checkbox" value="" id="diego-norris" />
                    <label class="form-check-label d-flex align-items-center" for="diego-norris">
                      <span class="flex-shrink-0">
                        <img src="@assets/images/users/avatar-10.jpg" alt="" class="avatar-xxs rounded-circle" />
                      </span>
                      <span class="flex-grow-1 ms-2"> Diego Norris </span>
                    </label>
                  </div>
                </li>
              </ul>
            </simplebar>
            <div class="invalid-feedback">Please select a Assignes name.</div>
          </BCol>
          <BCol lg="6">
            <label for="duedate-field" class="form-label">Due Date</label>
            <flat-pickr placeholder="Select date" :config="timeConfig" class="form-control flatpickr-input" id="date"
              v-model="event.dueDate" :class="{ 'is-invalid': submitted && !event.dueDate }"></flat-pickr>
            <div class="invalid-feedback">Please enter a date name.</div>
          </BCol>
          <BCol lg="6">
            <label for="ticket-status" class="form-label">Status</label>
            <Multiselect id="statusid" :close-on-select="true" :searchable="true" :create-option="true" :options="[
              { value: '', label: 'Status' },
              { value: 'New', label: 'New' },
              { value: 'Inprogress', label: 'Inprogress' },
              { value: 'Pending', label: 'Pending' },
              { value: 'Completed', label: 'Completed' },
            ]" v-model="event.status" :class="{ 'is-invalid': submitted && !event.status }" />
            <div class="invalid-feedback">Please select a status.</div>
          </BCol>
          <BCol lg="12">
            <label for="priority-field" class="form-label">Priority</label>
            <Multiselect id="priority" :close-on-select="true" :searchable="true" :create-option="true" :options="[
              { value: '', label: 'Priority' },
              { value: 'High', label: 'High' },
              { value: 'Medium', label: 'Medium' },
              { value: 'Low', label: 'Low' },
            ]" v-model="event.priority" :class="{ 'is-invalid': submitted && !event.priority }" />
            <div class="invalid-feedback">Please select a priority.</div>
          </BCol> -->
                </BRow>

                <div class="hstack gap-2 justify-content-end mt-3">
                    <BButton
                        type="button"
                        variant="light"
                        @click="userListModal = false"
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
                        {{ dataEdit ? "Update" : "Add user" }}
                    </BButton>
                </div>
            </BFrom>
        </BModal>

        <!-- delete modal -->
        <BModal
            v-model="deleteModal"
            modal-class="zoomIn"
            hide-footer
            no-close-on-backdrop
            centered
        >
            <div class="mt-2 text-center">
                <lottie
                    class="avatar-xl"
                    colors="primary:#f7b84b,secondary:#f06548"
                    :options="defaultOptions1"
                    :height="75"
                    :width="75"
                />
                <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                    <h4>Are you sure ?</h4>
                    <p class="text-muted mx-4 mb-0">
                        Are you sure you want to remove this record ?
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                <BButton
                    variant="light"
                    size="w-sm"
                    @click="deleteModal = false"
                    >Close</BButton
                >
                <BButton
                    variant="danger"
                    size="w-sm"
                    id="delete-record"
                    @click="deleteData"
                    >Yes, Delete It!</BButton
                >
            </div>
        </BModal>
    </Layout>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useStore } from "vuex";
import { Link } from "@inertiajs/vue3";
import simplebar from "simplebar-vue";

// Store
const store = useStore();

// Simplebar settings
const settings = ref({
    minScrollbarLength: 60,
});

// Computed for layout type
const layoutType = computed(() => store?.state?.layout?.layoutType || {});

// ---------- Methods ----------
const getSiblings = (elem) => {
    const siblings = [];
    let sibling = elem.parentNode.firstChild;
    while (sibling) {
        if (sibling.nodeType === 1 && sibling !== elem) siblings.push(sibling);
        sibling = sibling.nextSibling;
    }
    return siblings;
};

const onRoutechange = () => {
    setTimeout(() => {
        const currentPath = window.location.pathname;
        const navbar = document.querySelector("#navbar-nav");
        if (navbar) {
            const targetLink = navbar.querySelector(`[href="${currentPath}"]`);
            const currentPosition = targetLink?.offsetTop;
            if (currentPosition > document.documentElement.clientHeight) {
                const scrollWrapper = document.querySelector(
                    "#scrollbar .simplebar-content-wrapper"
                );
                if (scrollWrapper) scrollWrapper.scrollTop = currentPosition + 300;
            }
        }
    }, 500);
};

const initActiveMenu = () => {
    setTimeout(() => {
        const currentPath = window.location.pathname;
        const navbar = document.querySelector("#navbar-nav");
        if (!navbar) return;

        const a = navbar.querySelector(`[href="${currentPath}"]`);
        if (!a) return;

        a.classList.add("active");

        let parentCollapseDiv = a.closest(".collapse.menu-dropdown");
        if (parentCollapseDiv) {
            parentCollapseDiv.classList.add("show");
            const firstChild = parentCollapseDiv.parentElement.children[0];
            firstChild.classList.add("active");
            firstChild.setAttribute("aria-expanded", "true");

            const closestCollapse = parentCollapseDiv.parentElement.closest(".collapse.menu-dropdown");
            if (closestCollapse) {
                closestCollapse.classList.add("show");
                closestCollapse.previousElementSibling?.classList.add("active");

                const grandparent = closestCollapse.previousElementSibling?.parentElement.closest(".collapse");
                if (grandparent) {
                    grandparent.previousElementSibling?.classList.add("active");
                    grandparent.classList.add("show");
                }
            }
        }
    }, 0);
};

const setupCollapses = () => {
    const collapses = document.querySelectorAll(".navbar-nav .collapse");
    collapses.forEach((collapse) => {
        // show.bs.collapse
        collapse.addEventListener("show.bs.collapse", (e) => {
            e.stopPropagation();

            const closestCollapse = collapse.parentElement.closest(".collapse");
            if (closestCollapse) {
                closestCollapse.querySelectorAll(".collapse").forEach((siblingCollapse) => {
                    if (siblingCollapse.classList.contains("show")) {
                        siblingCollapse.classList.remove("show");
                        siblingCollapse.parentElement.firstChild.setAttribute("aria-expanded", "false");
                    }
                });
            } else {
                const siblings = getSiblings(collapse.parentElement);
                siblings.forEach((item) => {
                    if (item.childNodes.length > 2) {
                        item.firstElementChild.setAttribute("aria-expanded", "false");
                        item.firstElementChild.classList.remove("active");
                    }
                    const ids = item.querySelectorAll("*[id]");
                    ids.forEach((item1) => {
                        item1.classList.remove("show");
                        item1.parentElement.firstChild.setAttribute("aria-expanded", "false");
                        item1.parentElement.firstChild.classList.remove("active");

                        if (item1.childNodes.length > 2) {
                            const val = item1.querySelectorAll("ul li a");
                            val.forEach((subitem) => {
                                if (subitem.hasAttribute("aria-expanded"))
                                    subitem.setAttribute("aria-expanded", "false");
                            });
                        }
                    });
                });
            }
        });

        // hide.bs.collapse
        collapse.addEventListener("hide.bs.collapse", (e) => {
            e.stopPropagation();
            collapse.querySelectorAll(".collapse").forEach((childCollapse) => {
                childCollapse.classList.remove("show");
                childCollapse.parentElement.firstChild.setAttribute("aria-expanded", "false");
            });
        });
    });
};

// ---------- Lifecycle ----------
onMounted(() => {
    initActiveMenu();
    onRoutechange();
    if (document.querySelectorAll(".navbar-nav .collapse").length) {
        setupCollapses();
    }
});
</script>

<template>
    <BContainer fluid>
        <div id="two-column-menu"></div>

        <template v-if="layoutType === 'vertical' || layoutType === 'semibox'">
            <ul class="navbar-nav h-100" id="navbar-nav">
                <li class="menu-title">
                    <span data-key="t-menu"> {{ $t("t-menu") }}</span>
                </li>
                <li class="nav-item">
                    <Link class="nav-link menu-link" href="/">
                    <i class="bx bxs-dashboard"></i>
                    <span data-key="t-dashboards">{{ $t("t-dashboards") }}</span>
                    </Link>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarEmployee" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarEmployee">
                        <i class="bx bx-user-circle"></i>
                        <span data-key="t-employee">Employee Management</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarEmployee">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <Link href="/employees/list" class="nav-link" data-key="t-simple-page">
                                Employee List
                                </Link>
                            </li>
                            <li class="nav-item">
                                <Link href="/leave/list" class="nav-link" data-key="t-settings">
                                Leave List
                                </Link>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <Link class="nav-link menu-link" href="/tasks-list">
                    <i class="bx bx-list-ol"></i>
                    <span data-key="t-tasks">{{ $t("t-tasks") }}</span>
                    </Link>
                </li>

                <li class="nav-item">
                    <Link class="nav-link menu-link" href="/companies/list">
                    <i class="bx bx-building"></i>
                    <span data-key="t-tasks">Company</span>
                    </Link>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarclient" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarclient">
                        <i class="bx bxs-user-account"></i>
                        <span data-key="t-client">Client Management</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarclient">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <Link class="nav-link menu-link" href="/client/list">
                                <span data-key="t-clients">Clients</span>
                                </Link>
                            </li>
                            <li class="nav-item">
                                <Link href="/client/intractions/list" class="nav-link" data-key="t-settings">
                                Client Quotation
                                </Link>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <Link class="nav-link menu-link" href="/commercial/address">
                    <i class="bx bx-buildings"></i>
                    <span data-key="t-clients">Commercial Address</span>
                    </Link>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarexpense" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarexpense">
                        <i class="bx bx-money"></i>
                        <span data-key="t-expense">Expense Management</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarexpense">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <Link class="nav-link menu-link" href="/expense/list">
                                <span data-key="t-expenses">Expenses List</span>
                                </Link>
                            </li>
                            <li class="nav-item">
                                <Link class="nav-link menu-link" href="/expense/request">
                                <span data-key="t-expenses">Expenses Request</span>
                                </Link>
                            </li>
                            <li class="nav-item">
                                <Link class="nav-link menu-link" href="/financial/logs">
                                <span data-key="t-expenses">Financial Logs</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </template>
    </BContainer>
</template>

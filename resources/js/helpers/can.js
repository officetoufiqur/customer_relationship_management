import { usePage } from '@inertiajs/vue3';

// permission check
export function can(permission) {
    const page = usePage();
    const user = page.props.auth?.user; 
    const permissions = user?.permissions || [];
    return permissions.includes(permission);
}

// role check
export function hasRole(role) {
    const page = usePage();
    const user = page.props.auth?.user;
    const roles = user?.roles || [];
    return roles.includes(role);
}
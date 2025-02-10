<template>
    <v-container>
        <page-title-with-action-buttons :title="{ show: true, text: 'Administrators' }" :backButton="{ show: false }" />

        <v-col class="px-0">
            <div class="d-flex align-center">
                <Searchbar searchLabel="Search for an Admin" v-model="searchQuery" />
                <div class="d-flex align-center justify-end">
                    <AddAdminModal @admin-added="handleNewAdmin" />
                </div>
            </div>
        </v-col>

        <clickable-table-with-pagination :thead="tableHeaders" :items="formattedAdmins" :currentPage="currentPage"
            :totalPages="totalPages" @update-page="onUpdatePage" :disable-pagination="false">

            <template #role="{ item }">
                <div v-if="item.isEditMode" class="edit-mode-container">
                    <v-select v-model="selectedDisplayRole" :items="roles" :value="selectedDisplayRole"
                        item-title="role" item-value="id" density="compact" variant="outlined" hide-details
                        class="role-select mb-3">
                        <template v-slot:prepend-inner>
                            <v-icon size="small" color="primary">mdi-shield-account</v-icon>
                        </template>
                    </v-select>
                </div>
                <template v-else>
                    {{ item.role }}
                </template>
            </template>

            <template #actions="{ item }">
                <div class="d-flex" v-if="!item.isEditMode">
                    <div v-if="item.id !== admin.id">
                        <v-btn class="me-2" prepend-icon="mdi-pencil" variant="tonal" size="small" color="primary"
                            @click.stop="toggleEditMode(item)" height="35px"> Edit Role </v-btn>
                        <v-btn class="me-2" prepend-icon="mdi-delete" variant="tonal" size="small" color="primary"
                            @click.stop="showDeleteConfirmation(item.id)" height="35px">
                            Remove
                        </v-btn>
                    </div>
                    <change-password-modal @click.stop="resetPassword(item.id)" />
                </div>
                <div class="d-flex" v-if="item.isEditMode">
                    <v-btn prepend-icon="mdi-check" size="small" height="35px" color="success"
                        @click.stop="editRole(item)" class="action-btn me-3" variant="tonal">
                        Save
                    </v-btn>
                    <v-btn prepend-icon="mdi-close" size="small" height="35px" color="error"
                        @click.stop="cancelEdit(item)" class="action-btn" variant="tonal">
                        Cancel
                    </v-btn>
                </div>
            </template>
        </clickable-table-with-pagination>

        <default-modal v-model="showDeleteModal" title="Remove Admin" icon="mdi-alert" max-width="500"
            title-class="font-weight-bold text-error" confirm-text="Remove" cancel-text="Cancel" confirm-color="error"
            @confirm="confirmDelete" @close="cancelDelete">
            <p>Are you sure you want to delete this admin? This action cannot be undone.</p>
        </default-modal>
    </v-container>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import axios from "axios";
import { getAuth, logout } from "@/pages/auth/authService";

const { admin } = getAuth();
const admins = ref([]);
const roles = ref([
    { id: 1, role: 'admin' },
    { id: 2, role: 'viewer' }
]);
const searchQuery = ref("");

const selectedDisplayRole = ref(null);
const originalRole = ref(null);
const showDeleteModal = ref(false);
const adminToDelete = ref(null);
const currentPage = ref(1);
const totalPages = ref(1);
const perPage = ref(5);

const fetchAdmins = async (page = 1) => {
    const { token } = getAuth();
    try {
        const response = await axios.get("/api/admins", {
            params: {
                page,
                per_page: perPage.value
            },
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });

        admins.value = response.data.data.map((admin) => ({
            ...admin,
            isEditMode: false,
            role_id: admin.admin_roles?.id || null,
        }));

        currentPage.value = response.data.current_page;
        totalPages.value = response.data.last_page;
    } catch (error) {
        console.error('Error fetching Admins:', error);
        admins.value = [];
    }
};

const toggleEditMode = (item) => {
    selectedDisplayRole.value = item.role_id;
    originalRole.value = item.role_id;

    if (!item.isEditMode) {
        item.originalRoleId = item.role_id;
    }

    const adminToUpdate = admins.value.find(admin => admin.id === item.id);
    if (adminToUpdate) {
        admins.value.forEach(admin => {
            if (admin.id !== item.id) {
                admin.isEditMode = false;
            }
        });
        adminToUpdate.isEditMode = !adminToUpdate.isEditMode;
    }
};

watch(selectedDisplayRole, (newVal) => {
    if (selectedDisplayRole.value !== null) {
        const item = formattedAdmins.value.find(a => a.isEditMode);
        if (item) {
            item.role_id = newVal;
            item.role = roles.value.find(r => r.id === newVal)?.role;
        }
    }
});

const editRole = async (item) => {
    const { token } = getAuth();
    try {
        await axios.put(`/api/admins/${item.id}`, { role_id: item.role_id }, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        const adminToUpdate = admins.value.find(admin => admin.id === item.id);
        if (adminToUpdate) {
            adminToUpdate.isEditMode = false;
            adminToUpdate.role_id = item.role_id;
        }
        await fetchAdmins();
        window.$snackbar("Admin role updated successfully!", "success");
    } catch (error) {
        if (error.response?.status === 403) {
            window.$snackbar("Oops! You don't have access to perform this action!", "error");
        } else {
            window.$snackbar("Error updating role", "error");
        }
    }
};

const cancelEdit = (item) => {
    selectedDisplayRole.value = originalRole.value;
    item.role_id = originalRole.value;

    const adminToUpdate = admins.value.find(admin => admin.id === item.id);
    if (adminToUpdate) {
        adminToUpdate.isEditMode = false;
        adminToUpdate.role_id = item.originalRoleId;
    }
};

const showDeleteConfirmation = (id) => {
    adminToDelete.value = id;
    showDeleteModal.value = true;
};

const confirmDelete = async () => {
    if (adminToDelete.value) {
        await deleteAdmin(adminToDelete.value);
        showDeleteModal.value = false;
        adminToDelete.value = null;
    }
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    adminToDelete.value = null;
};

const deleteAdmin = async (adminId) => {
    const { token, admin } = getAuth();
    const adminToDelete = admins.value.find(admin => admin.id === adminId);

    if (admin.id === adminId) {
        window.$snackbar("You cannot delete your own account!", "error");
        return;
    }

    if (adminToDelete.role_id !== 2) {
        window.$snackbar(`Please change the role to 'Viewer' first before deleting`, `warning`);
        return;
    }

    try {
        await axios.delete(`/api/admins/${adminId}`, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });

        if (admin.id === adminId) {
            logout();
        }
        admins.value = admins.value.filter((admin) => admin.id !== adminId);
        window.$snackbar("Admin deleted successfully!", "success");
    } catch (error) {
        if (error.response && error.response.status === 403) {
            window.$snackbar("Oops! You don't have access to perform this action!", "error");
        } else {
            console.error("Error deleting admin", "error");
        }
    }
};

onMounted(() => {
    fetchAdmins();
});

const filteredAdmins = computed(() => {
    if (!searchQuery.value) {
        return admins.value;
    }
    return admins.value.filter((admin) =>
        (admin.last_name && admin.last_name.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
        (admin.first_name && admin.first_name.toLowerCase().includes(searchQuery.value.toLowerCase()))
    );
});

const formattedAdmins = computed(() => {
    return filteredAdmins.value.map(admin => ({
        id: admin.id,
        full_name: `${admin.first_name || ''} ${admin.last_name || ''}`.trim() || 'N/A',
        email: admin.email || 'N/A',
        role: admin.admin_roles?.role || 'No Role Assigned',
        actions: '',
        isEditMode: admin.isEditMode,
        role_id: admin.role_id,
        first_name: admin.first_name,
        last_name: admin.last_name
    }));
});

const handleNewAdmin = (newAdmin) => {
    const formattedAdmin = {
        ...newAdmin,
        isEditMode: false,
        role_id: newAdmin.role_id,
        admin_roles: {
            id: newAdmin.role_id,
            role: roles.value.find(r => r.id === newAdmin.role_id)?.role
        }
    };

    admins.value.unshift(formattedAdmin);
};

const tableHeaders = ref([
    { title: "Full Name", key: "full_name" },
    { title: "Email", key: "email" },
    { title: "Role", key: "role" },
    { title: "Actions", key: "actions" }
]);

const onUpdatePage = (page) => {
    currentPage.value = page;
    fetchAdmins(page);
};
</script>

<style scoped>
.clickable-row {
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.clickable-row:hover {
    background-color: #e0e0e0 !important;
}

.edit-mode-container {
    padding: 8px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.role-select {
    min-width: 200px;
    transition: transform 0.2s ease;
}

.role-select:hover {
    transform: translateY(-1px);
}

.action-btn {
    transition: all 0.2s ease;
    text-transform: none;
    font-weight: 500;
    letter-spacing: 0.5px;
}

.action-btn:hover {
    transform: translateY(-1px);
    opacity: 0.9;
}
</style>

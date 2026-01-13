<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dotaPubTracker } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dota Pub Tracker',
        href: dotaPubTracker().url,
    },
];

interface PubRecord {
    id: number;
    name: string;
    total_pubs: number;
    win: number;
    lose: number;
    match_date: string;
    created_at: string;
    updated_at: string;
}

interface PaginationData {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    data: PubRecord[];
}

const records = ref<PubRecord[]>([]);
const loading = ref(false);
const search = ref('');
const dateFrom = ref('');
const dateTo = ref('');
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);
const perPage = ref(20);

// Form fields
const form = ref({
    id: null as number | null,
    name: '',
    total_pubs: '',
    win: '',
    lose: '',
    match_date: '',
});

const showFormModal = ref(false);
const isEditing = ref(false);
const formErrors = ref<Record<string, string[]>>({});
const submitting = ref(false);

// Delete confirmation
const showDeleteConfirm = ref(false);
const deleteId = ref<number | null>(null);

// Calculate win rate
const calculateWinRate = (win: number, lose: number) => {
    const total = win + lose;
    if (total === 0) return '0%';
    return `${((win / total) * 100).toFixed(1)}%`;
};

const fetchRecords = async () => {
    loading.value = true;
    try {
        const params = new URLSearchParams({
            page: currentPage.value.toString(),
            per_page: perPage.value.toString(),
        });
        
        if (search.value) params.append('search', search.value);
        if (dateFrom.value) params.append('date_from', dateFrom.value);
        if (dateTo.value) params.append('date_to', dateTo.value);

        const response = await axios.get(`/api/dota-pub-records?${params}`);
        
        if (response.data.success) {
            const data: PaginationData = response.data.data;
            records.value = data.data;
            currentPage.value = data.current_page;
            lastPage.value = data.last_page;
            total.value = data.total;
        }
    } catch (error) {
        console.error('Error fetching records:', error);
    } finally {
        loading.value = false;
    }
};

const applyFilters = () => {
    currentPage.value = 1;
    fetchRecords();
};

const clearFilters = () => {
    search.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    currentPage.value = 1;
    fetchRecords();
};

const openCreateForm = () => {
    resetForm();
    isEditing.value = false;
    formErrors.value = {};
    showFormModal.value = true;
};

const openEditForm = (record: PubRecord) => {
    form.value = {
        id: record.id,
        name: record.name,
        total_pubs: record.total_pubs.toString(),
        win: record.win.toString(),
        lose: record.lose.toString(),
        match_date: record.match_date.split('T')[0], // Format date for input
    };
    isEditing.value = true;
    formErrors.value = {};
    showFormModal.value = true;
};

const resetForm = () => {
    form.value = {
        id: null,
        name: '',
        total_pubs: '',
        win: '',
        lose: '',
        match_date: '',
    };
};

const validateForm = () => {
    const errors: Record<string, string[]> = {};
    
    if (!form.value.name.trim()) {
        errors.name = ['Name is required'];
    }
    
    if (!form.value.total_pubs || parseInt(form.value.total_pubs) <= 0) {
        errors.total_pubs = ['Total pubs must be greater than 0'];
    }
    
    if (!form.value.win || parseInt(form.value.win) < 0) {
        errors.win = ['Win count cannot be negative'];
    }
    
    if (!form.value.lose || parseInt(form.value.lose) < 0) {
        errors.lose = ['Lose count cannot be negative'];
    }
    
    const win = parseInt(form.value.win) || 0;
    const lose = parseInt(form.value.lose) || 0;
    const totalPubs = parseInt(form.value.total_pubs) || 0;
    
    if (win + lose !== totalPubs) {
        errors.total_pubs = ['Win + Lose must equal Total Pubs'];
    }
    
    if (!form.value.match_date) {
        errors.match_date = ['Match date is required'];
    }
    
    formErrors.value = errors;
    return Object.keys(errors).length === 0;
};

const submitForm = async () => {
    if (!validateForm()) return;
    
    submitting.value = true;
    try {
        const payload = {
            name: form.value.name,
            total_pubs: parseInt(form.value.total_pubs),
            win: parseInt(form.value.win),
            lose: parseInt(form.value.lose),
            match_date: form.value.match_date,
        };
        
        let response;
        if (isEditing.value && form.value.id) {
            response = await axios.put(`/api/dota-pub-records/${form.value.id}`, payload);
        } else {
            response = await axios.post('/api/dota-pub-records', payload);
        }
        
        if (response.data.success) {
            showFormModal.value = false;
            resetForm();
            await fetchRecords();
        }
    } catch (error: any) {
        if (error.response?.data?.errors) {
            formErrors.value = error.response.data.errors;
        }
        console.error('Error saving record:', error);
    } finally {
        submitting.value = false;
    }
};

const confirmDelete = (id: number) => {
    deleteId.value = id;
    showDeleteConfirm.value = true;
};

const deleteRecord = async () => {
    if (!deleteId.value) return;
    
    try {
        const response = await axios.delete(`/api/dota-pub-records/${deleteId.value}`);
        
        if (response.data.success) {
            await fetchRecords();
            showDeleteConfirm.value = false;
            deleteId.value = null;
        }
    } catch (error) {
        console.error('Error deleting record:', error);
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const goToPage = (page: number) => {
    currentPage.value = page;
    fetchRecords();
};

const pageNumbers = computed(() => {
    const pages = [];
    const maxVisible = 5;
    let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
    const end = Math.min(lastPage.value, start + maxVisible - 1);
    
    if (end - start < maxVisible - 1) {
        start = Math.max(1, end - maxVisible + 1);
    }
    
    for (let i = start; i <= end; i++) {
        pages.push(i);
    }
    
    return pages;
});

onMounted(() => {
    fetchRecords();
});
</script>

<template>
    <Head title="Dota Pub Tracker" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Header with Create Button -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Dota Pub Tracker</h1>
                    <p class="text-gray-600">Track your Dota 2 pub match statistics</p>
                </div>
                <button
                    @click="openCreateForm"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add New Record
                </button>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search by name..."
                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        @keyup.enter="applyFilters"
                    />
                    <input
                        v-model="dateFrom"
                        type="date"
                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <input
                        v-model="dateTo"
                        type="date"
                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <div class="flex gap-2">
                        <button
                            @click="applyFilters"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                        >
                            Filter
                        </button>
                        <button
                            @click="clearFilters"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                        >
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pubs</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Win</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lose</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Win Rate</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="loading">
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    Loading...
                                </td>
                            </tr>
                            <tr v-else-if="records.length === 0">
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No records found
                                </td>
                            </tr>
                            <tr v-else v-for="record in records" :key="record.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ record.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ record.total_pubs }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium">{{ record.win }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">{{ record.lose }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                    {{ calculateWinRate(record.win, record.lose) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ formatDate(record.match_date) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                    <button
                                        @click="openEditForm(record)"
                                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="confirmDelete(record.id)"
                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="lastPage > 1" class="px-6 py-4 flex items-center justify-between border-t border-gray-200">
                    <div class="text-sm text-gray-700">
                        Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, total) }} of {{ total }} results
                    </div>
                    <div class="flex gap-2">
                        <button
                            @click="goToPage(currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Previous
                        </button>
                        <button
                            v-for="page in pageNumbers"
                            :key="page"
                            @click="goToPage(page)"
                            :class="[
                                'px-3 py-1 border rounded',
                                page === currentPage
                                    ? 'bg-blue-600 text-white'
                                    : 'hover:bg-gray-50'
                            ]"
                        >
                            {{ page }}
                        </button>
                        <button
                            @click="goToPage(currentPage + 1)"
                            :disabled="currentPage === lastPage"
                            class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showFormModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="showFormModal = false">
                    <Transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showFormModal" class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full max-h-[90vh] overflow-y-auto" @click.stop>
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">
                                        {{ isEditing ? 'Edit Record' : 'Add New Record' }}
                                    </h2>
                                    <p class="text-sm text-gray-500 mt-1">Track your Dota 2 pub match</p>
                                </div>
                                <button 
                                    @click="showFormModal = false" 
                                    class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            
                            <form @submit.prevent="submitForm" class="space-y-4">
                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': formErrors.name }"
                                        placeholder="Enter player name"
                                    />
                                    <p v-if="formErrors.name" class="mt-1 text-sm text-red-600">{{ formErrors.name[0] }}</p>
                                </div>

                                <!-- Total Pubs -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Pubs *</label>
                                    <input
                                        v-model="form.total_pubs"
                                        type="number"
                                        min="1"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': formErrors.total_pubs }"
                                        placeholder="Enter total number of pubs"
                                    />
                                    <p v-if="formErrors.total_pubs" class="mt-1 text-sm text-red-600">{{ formErrors.total_pubs[0] }}</p>
                                </div>

                                <!-- Win -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Win *</label>
                                    <input
                                        v-model="form.win"
                                        type="number"
                                        min="0"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': formErrors.win }"
                                        placeholder="Enter number of wins"
                                    />
                                    <p v-if="formErrors.win" class="mt-1 text-sm text-red-600">{{ formErrors.win[0] }}</p>
                                </div>

                                <!-- Lose -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Lose *</label>
                                    <input
                                        v-model="form.lose"
                                        type="number"
                                        min="0"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': formErrors.lose }"
                                        placeholder="Enter number of losses"
                                    />
                                    <p v-if="formErrors.lose" class="mt-1 text-sm text-red-600">{{ formErrors.lose[0] }}</p>
                                </div>

                                <!-- Match Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Match Date *</label>
                                    <input
                                        v-model="form.match_date"
                                        type="date"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': formErrors.match_date }"
                                    />
                                    <p v-if="formErrors.match_date" class="mt-1 text-sm text-red-600">{{ formErrors.match_date[0] }}</p>
                                </div>

                                <!-- Summary -->
                                <div v-if="form.win || form.lose" class="p-4 bg-gray-50 rounded-lg">
                                    <h3 class="text-sm font-medium text-gray-700 mb-2">Summary:</h3>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <div class="text-gray-600">Total Pubs:</div>
                                        <div class="font-medium">{{ parseInt(form.total_pubs) || 0 }}</div>
                                        <div class="text-gray-600">Win Rate:</div>
                                        <div class="font-medium">
                                            {{
                                                calculateWinRate(
                                                    parseInt(form.win) || 0,
                                                    parseInt(form.lose) || 0
                                                )
                                            }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="flex gap-3 pt-4 border-t border-gray-200">
                                    <button
                                        type="button"
                                        @click="showFormModal = false"
                                        class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        :disabled="submitting"
                                        class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <span v-if="submitting">Saving...</span>
                                        <span v-else>{{ isEditing ? 'Update' : 'Save' }}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>

        <!-- Delete Confirmation Modal -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showDeleteConfirm" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="showDeleteConfirm = false">
                    <Transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showDeleteConfirm" class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full" @click.stop>
                            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            
                            <h2 class="text-xl font-bold text-gray-900 text-center mb-2">Delete Record</h2>
                            <p class="text-gray-600 text-center mb-6">Are you sure you want to delete this record? This action cannot be undone.</p>
                            
                            <div class="flex gap-3">
                                <button
                                    @click="showDeleteConfirm = false"
                                    class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                                >
                                    Cancel
                                </button>
                                <button
                                    @click="deleteRecord"
                                    class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>
    </AppLayout>
</template>
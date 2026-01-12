<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { partnerEnquiries } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Partner Enquiries',
        href: partnerEnquiries().url,
    },
];

interface Enquiry {
    id: number;
    name: string;
    email: string;
    company: string;
    message: string;
    created_at: string;
}

interface PaginationData {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    data: Enquiry[];
}

const enquiries = ref<Enquiry[]>([]);
const loading = ref(false);
const search = ref('');
const dateFrom = ref('');
const dateTo = ref('');
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);
const perPage = ref(20);

const selectedEnquiry = ref<Enquiry | null>(null);
const showModal = ref(false);
const showDeleteConfirm = ref(false);
const deleteId = ref<number | null>(null);

const fetchEnquiries = async () => {
    loading.value = true;
    try {
        const params = new URLSearchParams({
            page: currentPage.value.toString(),
            per_page: perPage.value.toString(),
        });
        
        if (search.value) params.append('search', search.value);
        if (dateFrom.value) params.append('date_from', dateFrom.value);
        if (dateTo.value) params.append('date_to', dateTo.value);

        const response = await axios.get(`/api/partner-enquiries?${params}`);
        
        if (response.data.success) {
            const data: PaginationData = response.data.data;
            enquiries.value = data.data;
            currentPage.value = data.current_page;
            lastPage.value = data.last_page;
            total.value = data.total;
        }
    } catch (error) {
        console.error('Error fetching enquiries:', error);
    } finally {
        loading.value = false;
    }
};

const applyFilters = () => {
    currentPage.value = 1;
    fetchEnquiries();
};

const clearFilters = () => {
    search.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    currentPage.value = 1;
    fetchEnquiries();
};

const viewEnquiry = (enquiry: Enquiry) => {
    selectedEnquiry.value = enquiry;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedEnquiry.value = null;
};

const confirmDelete = (id: number) => {
    deleteId.value = id;
    showDeleteConfirm.value = true;
};

const deleteEnquiry = async () => {
    if (!deleteId.value) return;
    
    try {
        const response = await axios.delete(`/api/partner-enquiries/${deleteId.value}`);
        
        if (response.data.success) {
            await fetchEnquiries();
            showDeleteConfirm.value = false;
            deleteId.value = null;
        }
    } catch (error) {
        console.error('Error deleting enquiry:', error);
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const goToPage = (page: number) => {
    currentPage.value = page;
    fetchEnquiries();
};

const pageNumbers = computed(() => {
    const pages = [];
    const maxVisible = 5;
    let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
    let end = Math.min(lastPage.value, start + maxVisible - 1);
    
    if (end - start < maxVisible - 1) {
        start = Math.max(1, end - maxVisible + 1);
    }
    
    for (let i = start; i <= end; i++) {
        pages.push(i);
    }
    
    return pages;
});

onMounted(() => {
    fetchEnquiries();
});
</script>

<template>
    <Head title="Partner Enquiries" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Filters -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search name, email, company..."
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="loading">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Loading...
                                </td>
                            </tr>
                            <tr v-else-if="enquiries.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No enquiries found
                                </td>
                            </tr>
                            <tr v-else v-for="enquiry in enquiries" :key="enquiry.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ enquiry.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ enquiry.email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ enquiry.company }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ formatDate(enquiry.created_at) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                    <button
                                        @click="viewEnquiry(enquiry)"
                                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                                    >
                                        View
                                    </button>
                                    <button
                                        @click="confirmDelete(enquiry.id)"
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

        <!-- View Modal -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeModal">
                    <Transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showModal" class="bg-white rounded-xl shadow-2xl p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">Enquiry Details</h2>
                                    <p class="text-sm text-gray-500 mt-1">View complete enquiry information</p>
                                </div>
                                <button 
                                    @click="closeModal" 
                                    class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            
                            <div v-if="selectedEnquiry" class="space-y-5">
                                <div class="py-3 border-b border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Name</label>
                                    <p class="text-gray-900 font-medium">{{ selectedEnquiry.name }}</p>
                                </div>
                                
                                <div class="py-3 border-b border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Email</label>
                                    <p class="text-gray-900 font-medium">{{ selectedEnquiry.email }}</p>
                                </div>
                                
                                <div class="py-3 border-b border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Company</label>
                                    <p class="text-gray-900 font-medium">{{ selectedEnquiry.company }}</p>
                                </div>
                                
                                <div class="py-3 border-b border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Message</label>
                                    <p class="text-gray-900 whitespace-pre-wrap leading-relaxed">{{ selectedEnquiry.message }}</p>
                                </div>
                                
                                <div class="py-3">
                                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Submitted</label>
                                    <p class="text-gray-900 font-medium">{{ formatDate(selectedEnquiry.created_at) }}</p>
                                </div>
                            </div>
                            
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <button
                                    @click="closeModal"
                                    class="w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                                >
                                    Close
                                </button>
                            </div>
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
                            
                            <h2 class="text-xl font-bold text-gray-900 text-center mb-2">Delete Enquiry</h2>
                            <p class="text-gray-600 text-center mb-6">Are you sure you want to delete this enquiry? This action cannot be undone.</p>
                            
                            <div class="flex gap-3">
                                <button
                                    @click="showDeleteConfirm = false"
                                    class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                                >
                                    Cancel
                                </button>
                                <button
                                    @click="deleteEnquiry"
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
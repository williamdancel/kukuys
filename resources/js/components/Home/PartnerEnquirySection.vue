<template>
    <section id="partner" class="container mx-auto px-6 pb-16">
        <div class="text-center mb-12">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                <i class="fas fa-handshake text-green-400 mr-4"></i>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-green-400 to-emerald-300">
                    Partner with KUKUYS
                </span>
            </h2>
            <p class="text-gray-300 text-lg max-w-3xl mx-auto">
                Join forces with the fastest-growing streamer group
            </p>
        </div>

        <div class="gap-8 max-w-2xl mx-auto">
            <!-- Success Message -->
            <div v-if="successMessage" class="mb-6 p-4 bg-gradient-to-r from-green-600/20 to-emerald-500/20 border border-green-500 rounded-lg">
                <div class="flex items-center gap-3 text-green-300">
                    <i class="fas fa-check-circle text-xl"></i>
                    <span class="font-semibold">{{ successMessage }}</span>
                    <button @click="successMessage = ''" class="ml-auto text-green-400 hover:text-green-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Error Message -->
            <div v-if="errorMessage" class="mb-6 p-4 bg-gradient-to-r from-red-600/20 to-red-500/20 border border-red-500 rounded-lg">
                <div class="flex items-center gap-3 text-red-300">
                    <i class="fas fa-exclamation-circle text-xl"></i>
                    <span class="font-semibold">{{ errorMessage }}</span>
                    <button @click="errorMessage = ''" class="ml-auto text-red-400 hover:text-red-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-sm rounded-2xl p-8 border border-gray-700">
                <h3 class="text-2xl font-bold text-white mb-6">
                    <i class="fas fa-paper-plane text-emerald-400 mr-3"></i>Get in Touch
                </h3>
                <form @submit.prevent="submitPartnerForm" class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <input type="text" placeholder="Full Name" v-model="partnerForm.name" 
                                   :class="{'border-red-500': errors.name}"
                                   class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 transition-colors" 
                                   :disabled="isSubmitting">
                            <p v-if="errors.name" class="text-red-400 text-sm mt-1">{{ errors.name[0] }}</p>
                        </div>
                        <div>
                            <input type="email" placeholder="Email Address" v-model="partnerForm.email"
                                   :class="{'border-red-500': errors.email}"
                                   class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 transition-colors"
                                   :disabled="isSubmitting">
                            <p v-if="errors.email" class="text-red-400 text-sm mt-1">{{ errors.email[0] }}</p>
                        </div>
                    </div>
                    <div>
                        <input type="text" placeholder="Company Name" v-model="partnerForm.company"
                               :class="{'border-red-500': errors.company}"
                               class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 transition-colors"
                               :disabled="isSubmitting">
                        <p v-if="errors.company" class="text-red-500 text-sm mt-1">{{ errors.company[0] }}</p>
                    </div>
                    <div>
                        <textarea placeholder="Tell us about your partnership proposal" v-model="partnerForm.message" rows="4"
                                  :class="{'border-red-500': errors.message}"
                                  class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 transition-colors resize-none"
                                  :disabled="isSubmitting"></textarea>
                        <p v-if="errors.message" class="text-red-500 text-sm mt-1">{{ errors.message[0] }}</p>
                    </div>
                    <button type="submit" :disabled="isSubmitting" 
                            class="cursor-pointer w-full bg-gradient-to-r from-green-600 to-emerald-500 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-emerald-600 transition-all transform hover:scale-105 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-paper-plane'"></i>
                        {{ isSubmitting ? 'Submitting...' : 'Submit Enquiry' }}
                    </button>
                </form>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios, { AxiosError } from 'axios';

interface PartnerForm {
    name: string;
    email: string;
    company: string;
    message: string;
}

interface FormErrors {
    [key: string]: string[];
}

const partnerForm = ref<PartnerForm>({
    name: '',
    email: '',
    company: '',
    message: ''
});

const isSubmitting = ref<boolean>(false);
const successMessage = ref<string>('');
const errorMessage = ref<string>('');
const errors = ref<FormErrors>({});

onMounted(() => {
    // Set CSRF token
    const token = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement;
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    }
});

// Client-side validation
const validateForm = (): FormErrors => {
    const validationErrors: FormErrors = {};
    
    // Name validation
    if (!partnerForm.value.name.trim()) {
        validationErrors.name = ['Your name is required.'];
    } else if (partnerForm.value.name.length > 50) {
        validationErrors.name = ['Name must not exceed 50 characters.'];
    } else if (containsScript(partnerForm.value.name)) {
        validationErrors.name = ['Invalid characters in name.'];
    }
    
    // Email validation
    if (!partnerForm.value.email.trim()) {
        validationErrors.email = ['Email address is required.'];
    } else if (!isValidEmail(partnerForm.value.email)) {
        validationErrors.email = ['Please enter a valid email address.'];
    } else if (partnerForm.value.email.length > 50) {
        validationErrors.email = ['Email must not exceed 50 characters.'];
    }
    
    // Company validation
    if (!partnerForm.value.company.trim()) {
        validationErrors.company = ['Company name is required.'];
    } else if (partnerForm.value.company.length > 50) {
        validationErrors.company = ['Company name must not exceed 50 characters.'];
    } else if (containsScript(partnerForm.value.company)) {
        validationErrors.company = ['Invalid characters in company name.'];
    }
    
    // Message validation
    if (!partnerForm.value.message.trim()) {
        validationErrors.message = ['Please enter your partnership proposal.'];
    } else if (partnerForm.value.message.length > 2000) {
        validationErrors.message = ['Message must not exceed 2000 characters.'];
    } else if (containsScript(partnerForm.value.message)) {
        validationErrors.message = ['Invalid characters in message.'];
    }
    
    return validationErrors;
};

// Check if input contains script tags
const containsScript = (text: string): boolean => {
    const scriptPattern = /<script\b[^>]*>([\s\S]*?)<\/script>/gi;
    const onEventPattern = /on\w+\s*=/gi;
    const javascriptPattern = /javascript:/gi;
    
    return scriptPattern.test(text) || 
           onEventPattern.test(text) || 
           javascriptPattern.test(text);
};

// Validate email format
const isValidEmail = (email: string): boolean => {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(email);
};

// Sanitize input on client side too
const sanitizeInput = (input: string): string => {
    return input
        .replace(/<[^>]*>/g, '') // Remove HTML tags
        .replace(/javascript:/gi, '') // Remove javascript:
        .replace(/on\w+\s*=\s*("[^"]*"|'[^']*'|[^"'>\s]+)/gi, '') // Remove event handlers
        .trim();
};

const submitPartnerForm = async (): Promise<void> => {
    // Client-side validation first
    const validationErrors = validateForm();
    if (Object.keys(validationErrors).length > 0) {
        errors.value = validationErrors;
        errorMessage.value = 'Please check the form for errors.';
        return;
    }
    
    isSubmitting.value = true;
    successMessage.value = '';
    errorMessage.value = '';
    errors.value = {};
    
    try {
        // Sanitize data before sending
        const sanitizedData = {
            name: sanitizeInput(partnerForm.value.name),
            email: partnerForm.value.email.trim(),
            company: sanitizeInput(partnerForm.value.company),
            message: sanitizeInput(partnerForm.value.message)
        };
        
        const response = await axios.post('/partner-enquiries', sanitizedData);
        
        if (response.data.success) {
            successMessage.value = response.data.message || 'Thank you! Your partnership enquiry has been submitted successfully.';
            
            // Reset form
            partnerForm.value = {
                name: '',
                email: '',
                company: '',
                message: ''
            };
            
            // Auto-clear success message after 5 seconds
            setTimeout(() => {
                successMessage.value = '';
            }, 5000);
        }
    } catch (error) {
        const axiosError = error as AxiosError;
        
        if (axiosError.response && axiosError.response.status === 422) {
            // Validation errors from server
            const errorData = axiosError.response.data as { errors?: FormErrors; message?: string };
            errors.value = errorData.errors || {};
            errorMessage.value = errorData.message || 'Please check the form for errors below.';
        } else {
            // Generic error message
            errorMessage.value = 'Unable to submit your enquiry at the moment. Please try again later.';
        }
        
        // Auto-clear error message after 5 seconds
        setTimeout(() => {
            errorMessage.value = '';
        }, 5000);
    } finally {
        isSubmitting.value = false;
    }
};
</script>
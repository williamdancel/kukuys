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

<script>
import axios from 'axios';

export default {
    name: 'PartnerEnquirySection',
    data() {
        return {
            partnerForm: {
                name: '',
                email: '',
                company: '',
                message: ''
            },
            isSubmitting: false,
            successMessage: '',
            errorMessage: '',
            errors: {}
        }
    },
    mounted() {
        // Set CSRF token
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        }
    },
    methods: {
        // Client-side validation
        validateForm() {
            const errors = {};
            
            // Name validation
            if (!this.partnerForm.name.trim()) {
                errors.name = ['Your name is required.'];
            } else if (this.partnerForm.name.length > 50) {
                errors.name = ['Name must not exceed 50 characters.'];
            } else if (this.containsScript(this.partnerForm.name)) {
                errors.name = ['Invalid characters in name.'];
            }
            
            // Email validation
            if (!this.partnerForm.email.trim()) {
                errors.email = ['Email address is required.'];
            } else if (!this.isValidEmail(this.partnerForm.email)) {
                errors.email = ['Please enter a valid email address.'];
            } else if (this.partnerForm.email.length > 50) {
                errors.email = ['Email must not exceed 50 characters.'];
            }
            
            // Company validation
            if (!this.partnerForm.company.trim()) {
                errors.company = ['Company name is required.'];
            } else if (this.partnerForm.company.length > 50) {
                errors.company = ['Company name must not exceed 50 characters.'];
            } else if (this.containsScript(this.partnerForm.company)) {
                errors.company = ['Invalid characters in company name.'];
            }
            
            // Message validation
            if (!this.partnerForm.message.trim()) {
                errors.message = ['Please enter your partnership proposal.'];
            } else if (this.partnerForm.message.length > 2000) {
                errors.message = ['Message must not exceed 2000 characters.'];
            } else if (this.containsScript(this.partnerForm.message)) {
                errors.message = ['Invalid characters in message.'];
            }
            
            return errors;
        },
        
        // Check if input contains script tags
        containsScript(text) {
            const scriptPattern = /<script\b[^>]*>([\s\S]*?)<\/script>/gi;
            const onEventPattern = /on\w+\s*=/gi;
            const javascriptPattern = /javascript:/gi;
            
            return scriptPattern.test(text) || 
                   onEventPattern.test(text) || 
                   javascriptPattern.test(text);
        },
        
        // Validate email format
        isValidEmail(email) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailPattern.test(email);
        },
        
        // Sanitize input on client side too
        sanitizeInput(input) {
            return input
                .replace(/<[^>]*>/g, '') // Remove HTML tags
                .replace(/javascript:/gi, '') // Remove javascript:
                .replace(/on\w+\s*=\s*("[^"]*"|'[^']*'|[^"'>\s]+)/gi, '') // Remove event handlers
                .trim();
        },
        
        async submitPartnerForm() {
            // Client-side validation first
            const validationErrors = this.validateForm();
            if (Object.keys(validationErrors).length > 0) {
                this.errors = validationErrors;
                this.errorMessage = 'Please check the form for errors.';
                return;
            }
            
            this.isSubmitting = true;
            this.successMessage = '';
            this.errorMessage = '';
            this.errors = {};
            
            try {
                // Sanitize data before sending
                const sanitizedData = {
                    name: this.sanitizeInput(this.partnerForm.name),
                    email: this.partnerForm.email.trim(),
                    company: this.sanitizeInput(this.partnerForm.company),
                    message: this.sanitizeInput(this.partnerForm.message)
                };
                
                const response = await axios.post('/partner-enquiries', sanitizedData);
                
                if (response.data.success) {
                    this.successMessage = response.data.message || 'Thank you! Your partnership enquiry has been submitted successfully.';
                    
                    // Reset form
                    this.partnerForm = {
                        name: '',
                        email: '',
                        company: '',
                        message: ''
                    };
                    
                    // Auto-clear success message after 5 seconds
                    setTimeout(() => {
                        this.successMessage = '';
                    }, 5000);
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    // Validation errors from server
                    this.errors = error.response.data.errors || {};
                    this.errorMessage = error.response.data.message || 'Please check the form for errors below.';
                } else {
                    // Generic error message
                    this.errorMessage = 'Unable to submit your enquiry at the moment. Please try again later.';
                }
                
                // Auto-clear error message after 5 seconds
                setTimeout(() => {
                    this.errorMessage = '';
                }, 5000);
            } finally {
                this.isSubmitting = false;
            }
        }
    }
}
</script>
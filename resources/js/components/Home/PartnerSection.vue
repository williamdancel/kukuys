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

        <div class=" gap-8 max-w-2xl mx-auto">

            <!-- Contact Form -->
            <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-sm rounded-2xl p-8 border border-gray-700">
                <h3 class="text-2xl font-bold text-white mb-6">
                    <i class="fas fa-paper-plane text-emerald-400 mr-3"></i>Get in Touch
                </h3>
                <form @submit.prevent="submitPartnerForm" class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <input type="text" placeholder="Full Name" v-model="partnerForm.name" class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 transition-colors" required>
                        </div>
                        <div>
                            <input type="email" placeholder="Email Address" v-model="partnerForm.email" class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 transition-colors" required>
                        </div>
                    </div>
                    <div>
                        <input type="text" placeholder="Company Name" v-model="partnerForm.company" class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 transition-colors" required>
                    </div>
                    <div>
                        <textarea placeholder="Tell us about your partnership proposal" v-model="partnerForm.message" rows="4" class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 transition-colors resize-none" required></textarea>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-emerald-500 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-emerald-600 transition-all transform hover:scale-105 flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i>Submit Enquiry
                    </button>
                </form>
            </div>
        </div>
    </section>
</template>
<script>
    export default {
        name: 'PartnerSection',
        data() {
            return {
                partnerForm: {
                    name: '',
                    email: '',
                    company: '',
                    message: ''
                }
            }
        },
        methods: {
            submitPartnerForm() {
                // Handle form submission logic here
                alert(`Thank you, ${this.partnerForm.name}! Your partnership enquiry has been submitted.`);
                // Reset form
                this.partnerForm = {
                    name: '',
                    email: '',
                    company: '',
                    message: ''
                };
            }
        }
    }
</script>
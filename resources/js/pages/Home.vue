<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-900 to-green-900/30">
         <!-- Loading Overlay -->
        <div v-if="isLoading" class="fixed inset-0 z-[9999] bg-gray-900 flex flex-col items-center justify-center">
            <LoadingScreen />
        </div>
        
        <!-- Main Content -->
        <div v-else>
            <!-- Navbar -->
            <Navbar />
            <!-- Home Section -->
            <HomeSection />

            <!-- Kukuys Members Section -->
            <StreamerSection />
            

            <!-- Merch Section -->
            <section id="merch" class="container mx-auto px-6 pb-16">
                <div class="text-center mb-12">
                    <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                        <i class="fas fa-tshirt text-green-400 mr-4"></i>
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-green-400 to-emerald-300">
                            Official Merchandise
                        </span>
                    </h2>
                    <p class="text-gray-300 text-lg max-w-3xl mx-auto">
                        Show your support with exclusive KUKUYS gear
                    </p>
                </div>

                <MerchSection />
            </section>

            <!-- Follow Us Section -->
            <FollowUs />

            <!-- Partner Section -->
            <PartnerEnquirySection />

            <!-- Footer -->
            <FooterSection />
        </div>
        <!-- Back to Top Button -->
         <BackToTop />
    </div>
</template>

<script lang="ts">
import HomeSection from '@/components/Home/HomeSection.vue';
import LoadingScreen from '@/components/Home/LoadingScreen.vue';
import Navbar from '@/components/Home/Navbar.vue';
import StreamerSection from '@/components/Home/StreamerSection.vue';
import MerchSection from '@/components/Home/MerchSection.vue';
import FollowUs from '@/components/Home/FollowUs.vue';
import PartnerEnquirySection from '@/components/Home/PartnerEnquirySection.vue';
import FooterSection from '@/components/Home/FooterSection.vue';
import BackToTop from '@/components/Home/BackToTop.vue';
export default {
    name: 'HomeComponent',
    data() {
        return {
            isLoading: true,
            partnerForm: {
                name: '',
                email: '',
                company: '',
                message: ''
            }
        }
    },
    components: {
        LoadingScreen,
        Navbar,
        HomeSection,
        StreamerSection,
        MerchSection,
        FollowUs,
        PartnerEnquirySection,
        FooterSection,
        BackToTop
    },
    mounted() {
         setTimeout(() => {
            this.isLoading = false;
        }, 2000);
    },
    beforeUnmount() {
        this.isLoading = true;
    }
    
}
</script>

<style scoped>
/* Custom animations */
.hover\:scale-105:hover {
    transform: scale(1.05);
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 10px;
}

::-webkit-scrollbar-track {
    background: #0f172a;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #10b981, #059669);
    border-radius: 5px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #34d399, #10b981);
}

/* Pulse animation for live indicator */
@keyframes pulse-green {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse-green 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Sticky navigation enhancement */
nav {
    transition: all 0.3s ease;
}

nav.scrolled {
    background: rgba(15, 23, 42, 0.95);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

/* Smooth section transitions */
section {
    scroll-margin-top: 80px;
}

/* Line clamp for description */
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom glow effect for cards */
.group:hover .group-hover\:shadow-2xl {
    box-shadow: 0 25px 50px -12px rgba(34, 197, 94, 0.1);
}

/* Input focus styles */
input:focus, textarea:focus {
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
}

/* Smooth transitions for all interactive elements */
a, button, input, textarea {
    transition: all 0.3s ease;
}
</style>
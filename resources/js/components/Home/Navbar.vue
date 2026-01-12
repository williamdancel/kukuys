<template>
    <nav ref="navbar" class="sticky top-0 z-50 bg-gray-900/90 backdrop-blur-md border-b border-gray-800/50 transition-all duration-300">
        <div class="container mx-auto px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold text-white cursor-pointer" @click="scrollToTop">
                            <i class="fas fa-gamepad mr-2 text-green-400"></i>KUKU<span class="bg-clip-text text-transparent bg-gradient-to-r from-green-500 to-emerald-400">YS</span>
                        </div>
                        <div class="hidden md:flex space-x-8" style="font-size:12px;">
                            <a href="#home" class="text-gray-300 hover:text-green-400 transition-all duration-300 hover:scale-105 relative group" @click.prevent="scrollToSection('home')">
                                <i class="fas fa-home mr-2"></i><span>Home</span>
                                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-green-400 to-emerald-300 group-hover:w-full transition-all duration-300"></span>
                            </a>
                            <a href="#kukuys" class="text-gray-300 hover:text-green-400 transition-all duration-300 hover:scale-105 relative group" @click.prevent="scrollToSection('kukuys')">
                                <i class="fas fa-users mr-2"></i><span>Kukuys</span>
                                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-green-400 to-emerald-300 group-hover:w-full transition-all duration-300"></span>
                            </a>
                            <a href="#merch" class="text-gray-300 hover:text-green-400 transition-all duration-300 hover:scale-105 relative group" @click.prevent="scrollToSection('merch')">
                                <i class="fas fa-tshirt mr-2"></i><span>Merch</span>
                                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-green-400 to-emerald-300 group-hover:w-full transition-all duration-300"></span>
                            </a>
                            <a href="#follow" class="text-gray-300 hover:text-green-400 transition-all duration-300 hover:scale-105 relative group" @click.prevent="scrollToSection('follow')">
                                <i class="fas fa-heart mr-2"></i><span>Follow Us</span>
                                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-green-400 to-emerald-300 group-hover:w-full transition-all duration-300"></span>
                            </a>
                            <a href="#partner" class="text-gray-300 hover:text-green-400 transition-all duration-300 hover:scale-105 relative group" @click.prevent="scrollToSection('partner')">
                                <i class="fas fa-handshake mr-2"></i><span>Partner with Us</span>
                                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-green-400 to-emerald-300 group-hover:w-full transition-all duration-300"></span>
                            </a>
                        </div>
                        <button class="md:hidden text-white hover:text-green-400 transition-colors duration-300" @click="toggleMobileMenu">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Mobile Menu -->
                <div v-if="mobileMenuOpen" class="md:hidden bg-gray-900/95 backdrop-blur-lg border-t border-gray-800">
                    <div class="container mx-auto px-6 py-4 flex flex-col space-y-4">
                        <a href="#home" class="text-gray-300 hover:text-green-400 transition-all duration-300 py-2 flex items-center" @click.prevent="scrollToSection('home'); mobileMenuOpen = false">
                            <i class="fas fa-home w-6 mr-3"></i>Home
                        </a>
                        <a href="#kukuys" class="text-gray-300 hover:text-green-400 transition-all duration-300 py-2 flex items-center" @click.prevent="scrollToSection('kukuys'); mobileMenuOpen = false">
                            <i class="fas fa-users w-6 mr-3"></i>Kukuys
                        </a>
                        <a href="#merch" class="text-gray-300 hover:text-green-400 transition-all duration-300 py-2 flex items-center" @click.prevent="scrollToSection('merch'); mobileMenuOpen = false">
                            <i class="fas fa-tshirt w-6 mr-3"></i>Merch
                        </a>
                        <a href="#follow" class="text-gray-300 hover:text-green-400 transition-all duration-300 py-2 flex items-center" @click.prevent="scrollToSection('follow'); mobileMenuOpen = false">
                            <i class="fas fa-heart w-6 mr-3"></i>Follow Us
                        </a>
                        <a href="#partner" class="text-gray-300 hover:text-green-400 transition-all duration-300 py-2 flex items-center" @click.prevent="scrollToSection('partner'); mobileMenuOpen = false">
                            <i class="fas fa-handshake w-6 mr-3"></i>Partner with Us
                        </a>
                    </div>
                </div>
            </nav>
</template>
<script lang="ts">
    export default {
    name: 'Navbar',
    data() {
        return {
            mobileMenuOpen: false,
        };
    },
    methods: {
        scrollToSection(sectionId: string) {
            const section = document.getElementById(sectionId);
            if (section) {
                section.scrollIntoView({ behavior: 'smooth' });
            }
        },
        scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        toggleMobileMenu() {
            this.mobileMenuOpen = !this.mobileMenuOpen;
        },
    },
}
    
</script>
<template>
    <section id="home" class="container mx-auto px-6 py-16 pt-25 text-center">
        <div class="relative mb-8">
            <div class="absolute inset-0 bg-gradient-to-r from-green-500 via-emerald-400 to-green-600 blur-3xl opacity-20"></div>
                <h1 class="text-5xl md:text-7xl font-bold mb-6 relative z-10 text-white">
                    WELCOME TO<br>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-green-400 via-emerald-300 to-green-500">
                    KUKUYS
                </span>
                </h1>
            </div>
            <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-3xl mx-auto">
                Elite Dota 2 Pros & Top Streamers United. <br> Watch us dominate on Kick.
            </p>
            <div class="flex flex-col md:flex-row gap-4 justify-center mb-16">
                <button class="cursor-pointer group relative bg-gradient-to-r from-green-600 to-emerald-500 text-white px-8 py-4 rounded-lg font-bold text-lg 
                hover:from-green-700 hover:to-emerald-600 transition-all 
                transform hover:scale-105 shadow-lg shadow-green-500/25" @click="scrollToSection('kukuys')">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-500 to-emerald-400 opacity-0 group-hover:opacity-20 blur-xl transition-opacity"></div>
                    <span class="relative flex items-center justify-center gap-3">
                        <i class="fas fa-users"></i>Meet the Kukuys
                    </span>
                </button>
            </div>
            <!-- Features Section -->
            <div class="container mx-auto px-6 pb-30">
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Pro Players Card -->
                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-8 border border-gray-700 hover:border-red-500 transition-all hover:transform hover:scale-105">
                        <div class="text-red-500 mb-4">
                            <i class="fas fa-trophy fa-3x mx-auto"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Pro Players</h3>
                        <p class="text-gray-300">Tournament winners & Dota 2 legends</p>
                    </div>

                    <!-- Daily Streams Card -->
                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-8 border border-gray-700 hover:border-yellow-500 transition-all hover:transform hover:scale-105">
                        <div class="text-yellow-500 mb-4">
                            <i class="fas fa-play fa-3x mx-auto"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Daily Streams</h3>
                        <p class="text-gray-300">Live gameplay & entertainment 24/7</p>
                    </div>

                    <!-- Community Card -->
                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-8 border border-gray-700 hover:border-blue-500 transition-all hover:transform hover:scale-105">
                        <div class="text-blue-500 mb-4">
                            <i class="fas fa-users fa-3x mx-auto"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Community</h3>
                        <p class="text-gray-300">Join thousands of dedicated fans</p>
                    </div>
                </div>
            </div>
    </section>
</template>
<script>
    export default {
        name: 'HomeSection',
        data() {
            return {};
        },methods: {
            scrollToSection(sectionId) {
                const element = document.getElementById(sectionId);
                if (element) {
                    const offset = 80; // Adjust for sticky header
                    const elementPosition = element.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - offset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            },
        }
    }
</script>
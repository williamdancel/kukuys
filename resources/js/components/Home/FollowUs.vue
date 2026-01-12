<template>
    <section id="follow" class="container mx-auto px-6 pb-16">
        <div class="text-center mb-12">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                <i class="fas fa-heart text-green-400 mr-4"></i>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-green-400 to-emerald-300">
                    Follow Our Journey
                </span>
            </h2>
            <p class="text-gray-300 text-lg max-w-3xl mx-auto">
                    Connect with us across all platforms
                </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-6 max-w-4xl mx-auto">

        <!-- Social Card 1 -->
        <a href="https://www.facebook.com/kukuysontop" target="_blank" class="group">
            <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-sm rounded-2xl p-8 border border-gray-700 hover:border-blue-400 transition-all duration-300 hover:transform hover:scale-105 text-center">
                <div class="text-5xl text-blue-500 mb-4 group-hover:scale-110 transition-transform duration-300">
                    <i class="fab fa-facebook"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Facebook</h3>
                <p class="text-gray-300 mb-4">Get updates, announcements, and memes</p>
                    <div class="inline-flex items-center text-blue-400 group-hover:text-blue-300">
                        <span>Follow Us</span>
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
             </a>

            <!-- Social Card 2 -->
            <a href="https://discord.com/invite/rzuxZdEFxx" target="_blank" class="group">
                <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-sm rounded-2xl p-8 border border-gray-700 hover:border-indigo-400 transition-all duration-300 hover:transform hover:scale-105 text-center">
                    <div class="text-5xl text-indigo-500 mb-4 group-hover:scale-110 transition-transform duration-300">
                        <i class="fab fa-discord"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Discord</h3>
                    <p class="text-gray-300 mb-4">Join our community and chat with fans</p>
                    <div class="inline-flex items-center text-indigo-400 group-hover:text-indigo-300">
                        <span>Join 10K+ Members</span>
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>
        </div>
    </section>
</template>
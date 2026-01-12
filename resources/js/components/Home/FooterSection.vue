<template>
    <footer class="container mx-auto px-6 py-8 border-t border-gray-800 mt-16">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-2xl font-bold text-white mb-4 md:mb-0 cursor-pointer" @click="scrollToTop">
                <i class="fas fa-gamepad text-green-400 mr-2"></i>KUKU<span class="bg-clip-text text-transparent bg-gradient-to-r from-green-500 to-emerald-400">YS</span>
            </div>
            <div class="flex items-center gap-6 mb-4 md:mb-0">
                <a href="https://www.facebook.com/kukuysontop" target="_blank" class="text-gray-400 hover:text-green-400 transition transform hover:scale-110">
                    <i class="fab fa-facebook text-xl"></i>
                </a>
                <a href="https://discord.com/invite/rzuxZdEFxx" target="_blank" class="text-gray-400 hover:text-green-400 transition transform hover:scale-110">
                    <i class="fab fa-discord text-xl"></i>
                </a>
            </div>
            <div class="text-white">
                <i class="fas fa-copyright mr-2"></i>2026 KUKUYS. All rights reserved.
            </div>
        </div>
    </footer>
</template>
<script>
    export default {
        name: 'FooterSection',
        methods: {
            scrollToTop() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    };
</script>
<template>
    <button 
            v-show="showBackToTop"
            @click="scrollToTop"
            class="cursor-pointer w-15 fixed bottom-6 right-6 z-50 bg-gradient-to-r from-green-600 to-emerald-500 text-white p-4 rounded-full shadow-lg animate-bounce hover:animate-none"
            aria-label="Back to top"
        >
        <i class="fas fa-arrow-up"></i>
    </button>
</template>
<script lang="ts">
    export default {
        name: 'BackToTop',
        data() {
            return {
                showBackToTop: false
            }
        },
        mounted() {
            window.addEventListener('scroll', this.handleScroll);
        },
        beforeUnmount() {
            window.removeEventListener('scroll', this.handleScroll);
        },
        methods: {
            scrollToTop() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            handleScroll() {
                this.showBackToTop = window.scrollY > 300;
            }
        }
    };
</script>
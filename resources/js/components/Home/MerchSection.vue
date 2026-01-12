<template>
    <div class="grid md:grid-cols-3 gap-8">
        <!-- Merch Item -->
        <div v-for="item in merch" :key="item.id" class="group relative bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-sm rounded-2xl overflow-hidden border border-gray-700 hover:border-green-500 transition-all duration-500 hover:transform hover:scale-[1.02]">
            <!-- Image Container -->
            <div class="relative h-64 overflow-hidden bg-gradient-to-br from-green-600/20 to-emerald-500/20">
                <img 
                    v-if="item.image" 
                    :src="item.image" 
                    :alt="item.name"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                >
                <div v-else class="w-full h-full bg-gradient-to-br from-green-400 to-emerald-300 flex items-center justify-center">
                    <i class="fas fa-tshirt text-6xl text-gray-900"></i>
                </div>
            </div>
            
            <div class="p-6">
                <h3 class="text-2xl font-bold text-white mb-2">{{ item.name }}</h3>
                <p class="text-gray-300 mb-4">{{ item.description }}</p>
                <div class="flex items-center justify-between mb-6">
                    <div class="text-2xl font-bold text-white">
                        <i class="fas fa-tag text-green-400 mr-2"></i>{{ item.price }}
                    </div>
                </div>
                <a 
                    :href="'https://www.messenger.com/t/869659129560069'" 
                    target="_blank" 
                    class="flex-1 w-full bg-gradient-to-r from-green-600 to-emerald-500 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-emerald-600 transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-2 cursor-pointer"
                >
                    <i class="fab fa-facebook"></i>Chat to our Facebook Page
                </a>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    export default {
        name: 'MerchSection',
        data() {
            return {
                showMore: false,
                itemsPerLoad: 6,
                merch: [
                    {
                        id: 1,
                        name: 'KUKUYS Hoodie',
                        description: 'Comfortable hoodie with KUKUYS branding, perfect for casual wear or gaming sessions.',
                        price: "TBD",
                        image: 'images/kukuys_merch/merch-hoodie.jpg',
                    },{
                        id: 2,
                        name: 'KUKUYS Jersey',
                        description: 'Official Kukuys Jersey with moisture-wicking technology and custom embroidery.',
                        price: "TBD",
                        image: 'images/kukuys_merch/merch-black-tshirt.jpg',
                    },
                    {
                        id: 3,
                        name: 'KUKUYS Black T-Shirt',
                        description: 'Official Kukuys Black T-Shirt with moisture-wicking technology and custom embroidery.',
                        price: "TBD",
                        image: 'images/kukuys_merch/merch-black-tshirt2.jpg',
                    },
                    {
                        id: 4,
                        name: 'KUKUYS White T-Shirt',
                        description: 'Official Kukuys White T-Shirt with moisture-wicking technology and custom embroidery.',
                        price: "TBD",
                        image: 'images/kukuys_merch/merch-white-tshirt.jpg',
                    }
                ],
            };
        },
    };
</script>
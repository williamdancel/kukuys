<template>
    <section id="kukuys" class="container mx-auto px-6 pb-30 pt-5">
        <div class="text-center mb-12">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                <i class="fas fa-users text-green-400 mr-4"></i>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-green-400 to-emerald-300">
                    Meet The Kukuys
                </span>
            </h2>
            <p class="text-gray-300 text-lg max-w-3xl mx-auto">
                The best Dota 2 professionals and Kick streamers in the scene
            </p>
            
            <!-- Filter Buttons -->
            <div class="flex flex-wrap justify-center gap-3 mt-8">
                <button 
                    @click="filterType = 'all'"
                    :class="filterType === 'all' ? 'from-green-600 to-emerald-500' : 'from-gray-700 to-gray-800'"
                    class="bg-gradient-to-r text-white px-6 py-2 rounded-lg font-semibold transition-all transform hover:scale-105 flex items-center gap-2"
                >
                    <i class="fas fa-users"></i>
                    All Kukuys ({{ streamers.length }})
                </button>
                <button 
                    @click="filterType = 'live'"
                    :class="filterType === 'live' ? 'from-green-600 to-emerald-500' : 'from-gray-700 to-gray-800'"
                    class="bg-gradient-to-r text-white px-6 py-2 rounded-lg font-semibold transition-all transform hover:scale-105 flex items-center gap-2 relative"
                >
                    <i class="fas fa-circle text-xs animate-pulse"></i>
                    Live Now ({{ liveCount }})
                    <span v-if="isCheckingLiveStatus" class="absolute -top-1 -right-1">
                        <i class="fas fa-sync fa-spin text-xs"></i>
                    </span>
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-green-500 mb-4"></div>
            <p class="text-gray-300">Loading Kukuys...</p>
        </div>

        <!-- Streamers Grid -->
        <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div
                v-for="streamer in visibleStreamers"
                :key="streamer.id"
                :class="[
                    'group relative bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-sm rounded-2xl overflow-hidden border transition-all duration-500',
                    streamer.isLive
                    ? 'border-red-500 shadow-red-500/30 animate-live-glow'
                    : 'border-gray-700 hover:border-green-500'
                ]"
            >
                <div class="absolute bg-gradient-to-br from-green-500/5 via-transparent to-emerald-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative h-64 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-600/30 to-emerald-500/30"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/80 to-transparent"></div>
                    
                    <div v-if="streamer.isLive" class="absolute top-4 right-4 z-10">
                        <div class="flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-500 text-white px-3 py-1 rounded-full text-sm font-bold shadow-lg">
                            <span class="animate-pulse">🔴</span>
                            <span>
                                LIVE
                                <span v-if="streamer.viewers">
                                    ({{ formatNumber(streamer.viewers) }})
                                </span>
                            </span>
                        </div>
                    </div>
                    
                    <div class="absolute left-1/2 transform -translate-x-1/2 w-40 h-40 rounded-full border-4 border-gray-900 bg-gradient-to-br bg-white overflow-hidden group-hover:scale-110 transition-transform duration-500" style="top:50px;">
                        <img 
                            v-if="streamer.image" 
                            :src="streamer.image" 
                            :alt="streamer.name"
                            loading="lazy"
                            class="w-full h-full object-cover"
                        >
                        <div v-else class="w-full h-full bg-gradient-to-br from-green-400 to-emerald-300 flex items-center justify-center text-5xl font-bold text-gray-900">
                            <i :class="streamer.icon"></i>
                        </div>
                    </div>
                </div>

                <div class="pt-20 pb-6 px-6">
                    <div class="text-center mb-4 min-h-[111px]">
                        <h3 class="text-2xl font-bold text-white mb-3">
                            <i :class="streamer.nameIcon" class="mr-2"></i>{{ streamer.name }}
                        </h3>
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            <span 
                                v-for="tag in streamer.tags" 
                                :key="tag"
                                class="px-3 py-1 bg-gradient-to-r from-green-900/30 to-emerald-900/30 text-green-300 text-sm rounded-full border border-green-800/50"
                            >
                               {{ tag }}
                            </span>
                        </div>
                    </div>

                    <p class="text-gray-300 text-center mb-6 line-clamp-3">
                        <i class="fas fa-quote-left text-green-400 mr-2 opacity-50"></i>
                        {{ streamer.description }}
                    </p>

                    <div class="mb-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white">
                                <i class="fas fa-users text-green-400 mr-1"></i> {{ formatNumber(streamer.followers) }}
                            </div>
                            <div class="text-sm text-gray-400">Kick Followers</div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-center gap-3">
                        <a 
                            v-show="streamer.websiteLink"
                            :href="streamer.websiteLink" 
                            target="_blank" 
                            class="flex-1 w-full bg-gradient-to-r from-gray-800 to-gray-900 border border-gray-700 text-white py-3 rounded-lg font-semibold hover:border-green-500 hover:from-gray-900 hover:to-black transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i class="fas fa-globe"></i>
                            <span>Website</span>
                        </a>
                        <a 
                            :href="streamer.kickLink" 
                            target="_blank" 
                            class="flex-1 w-full bg-gradient-to-r from-green-600 to-emerald-500 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-emerald-600 transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i class="fab fa-kickstarter"></i>
                            <span class="hidden sm:inline">Kick Stream</span>
                            <span class="sm:hidden">Kick</span>
                        </a>
                        <a 
                            v-show="streamer.facebookLink"
                            :href="streamer.facebookLink" 
                            target="_blank" 
                            class="flex-1 w-full bg-gradient-to-r from-blue-800 to-blue-900 border border-gray-700 text-white py-3 rounded-lg font-semibold hover:border-green-500 hover:from-gray-900 hover:to-black transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i class="fa-brands fa-facebook-f"></i>
                            <span>Facebook</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="!isLoading" class="text-center mt-12">
            <button 
                v-if="showMoreButtonVisible"
                @click="toggleShowMore"
                class="cursor-pointer bg-gradient-to-r from-green-600 to-emerald-500 text-white px-8 py-4 rounded-lg font-bold text-lg hover:from-green-700 hover:to-emerald-600 transition-all transform hover:scale-105 shadow-lg shadow-green-500/25 flex items-center gap-3 mx-auto"
            >
                {{ showMore ? 'Show Less Kukuys' : 'See More Kukuys' }}
                <i class="fas" :class="showMore ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            
            <p class="text-gray-400 mt-4 text-sm">
                Showing {{ visibleStreamers.length }} of {{ filteredStreamers.length }} streamers
            </p>
        </div>

        <!-- Error State -->
        <div v-if="error" class="text-center py-12">
            <div class="text-red-400 mb-4">
                <i class="fas fa-exclamation-triangle text-4xl"></i>
            </div>
            <p class="text-gray-300 mb-4">Error loading streamers: {{ error }}</p>
            <button 
                @click="fetchStreamersFromAPI"
                class="bg-gradient-to-r from-green-600 to-emerald-500 text-white px-6 py-2 rounded-lg font-semibold hover:from-green-700 hover:to-emerald-600 transition-all transform hover:scale-105"
            >
                <i class="fas fa-sync mr-2"></i> Retry
            </button>
        </div>
    </section>
</template>

<script lang="ts">
import axios from 'axios';

interface Streamer {
    id: number;
    name: string;
    kickUsername: string;
    nameIcon: string;
    tags: string[];
    description: string;
    followers: number;
    kickLink: string;
    websiteLink: string;
    facebookLink: string;
    isLive: boolean;
    viewers?: number;
    icon: string;
    image: string;
}

export default {
    name: 'StreamerSection',
    data() {
        return {
            showMore: false,
            itemsPerLoad: 6,
            filterType: 'live' as 'all' | 'live',
            isCheckingLiveStatus: false,
            isLoading: true,
            error: null as string | null,
            liveCheckInterval: null as number | null,
            streamers: [] as Streamer[]
        }
    },
    computed: {
        filteredStreamers(): Streamer[] {
            if (this.filterType === 'live') {
                return [...this.streamers]
                    .filter(s => s.isLive)
                    .sort((a, b) => a.id - b.id);
            }
            return this.streamers;
        },
        visibleStreamers(): Streamer[] {
            if (this.showMore) {
                return this.filteredStreamers;
            }
            return this.filteredStreamers.slice(0, this.itemsPerLoad);
        },
        showMoreButtonVisible(): boolean {
            return this.filteredStreamers.length > this.itemsPerLoad;
        },
        liveCount(): number {
            return this.streamers.filter(s => s.isLive).length;
        }
    },
    methods: {
        formatNumber(num: number): string {
            if (num >= 1000) {
                return (num / 1000).toFixed(num % 1000 === 0 ? 0 : 1) + 'K';
            }
            return num.toString();
        },
        toggleShowMore() {
            this.showMore = !this.showMore;
            
            if (!this.showMore) {
                setTimeout(() => {
                    const element = document.getElementById('kukuys');
                    if (element) {
                        element.scrollIntoView({ behavior: 'smooth' });
                    }
                }, 100);
            }
        },
        async checkLiveStatus() {
            this.isCheckingLiveStatus = true;

            try {
                const checks = this.streamers.map(async (streamer) => {
                    try {
                        const res = await fetch(`https://kick.com/api/v1/channels/${streamer.kickUsername}`);
                        if (!res.ok) return { username: streamer.kickUsername, isLive: false };

                        const data = await res.json();
                        return {
                            
                            username: streamer.kickUsername,
                            isLive: data.livestream.is_live === true,
                            viewers: data.livestream.viewer_count || data.livestream.viewers || null
                        };
                    } catch {
                        return { username: streamer.kickUsername, isLive: false };
                    }
                });

                const results = await Promise.all(checks);

                results.forEach(result => {
                    const streamer = this.streamers.find(s => s.kickUsername === result.username);
                    if (streamer) {
                        streamer.isLive = result.isLive;
                        streamer.viewers = result.viewers;
                    }
                });

            } finally {
                this.isCheckingLiveStatus = false;
            }
        },
        async fetchStreamersFromAPI() {
            this.isLoading = true;
            this.error = null;
            
            try {
                const response = await axios.get('/api/streamers/detailed');
                const apiStreamers = response.data.streamers;
                
                // Map API data to our Streamer interface
                this.streamers = apiStreamers.map((streamer: any) => ({
                    id: streamer.id,
                    name: streamer.name,
                    kickUsername: streamer.kickUsername,
                    nameIcon: streamer.nameIcon || '',
                    tags: Array.isArray(streamer.tags) ? streamer.tags : [],
                    description: streamer.description || '',
                    followers: streamer.followers || 0,
                    kickLink: streamer.kickLink || `https://kick.com/${streamer.kickUsername}`,
                    websiteLink: streamer.websiteLink || '',
                    facebookLink: streamer.facebookLink || '',
                    isLive: streamer.isLive || false,
                    icon: streamer.icon || 'fab fa-kickstarter',
                    image: streamer.image || 'images/kukuys_streamer/default.jpg'
                }));
                
                // After loading streamers, check their live status
                await this.checkLiveStatus();
            } catch (error: any) {
                console.error('Error fetching streamers from API:', error);
                this.error = error.message || 'Failed to load streamers';
                
                // Fallback to hardcoded streamers if API fails
                this.fallbackToHardcodedStreamers();
            } finally {
                this.isLoading = false;
            }
        },
        fallbackToHardcodedStreamers() {
            // Keep as fallback in case API fails
            this.streamers = [
               
            ];
            
            // Check live status for fallback streamers
            this.checkLiveStatus();
        }
    },
    async mounted() {
        // Fetch streamers from PHP API
        await this.fetchStreamersFromAPI();
        
        // Check live status every 2 minutes
        this.liveCheckInterval = window.setInterval(() => {
            this.checkLiveStatus();
        }, 120000);
    },
    beforeUnmount() {
        if (this.liveCheckInterval) {
            clearInterval(this.liveCheckInterval);
        }
    }
}
</script>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #0f172a;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #10b981, #059669);
    border-radius: 4px;
}

.grid {
    transition: all 0.3s ease;
}

@keyframes liveGlow {
    0%, 100% {
        box-shadow: 0 0 0px rgba(239, 68, 68, 0.6);
    }
    50% {
        box-shadow: 0 0 25px rgba(239, 68, 68, 0.9);
    }
}

.animate-live-glow {
    animation: liveGlow 1.8s ease-in-out infinite;
}
</style>
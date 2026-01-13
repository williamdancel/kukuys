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
        </div>

        <!-- Streamers Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Dynamic Streamer Cards -->
            <div 
                v-for="streamer in visibleStreamers" 
                :key="streamer.id"
                class="group relative bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-sm rounded-2xl overflow-hidden border border-gray-700 hover:border-green-500 transition-all duration-500 hover:transform hover:scale-[1.02] hover:shadow-2xl hover:shadow-green-500/10"
            >
                <!-- Card Background Glow -->
                <div class="absolute bg-gradient-to-br from-green-500/5 via-transparent to-emerald-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <!-- Streamer Image -->
                <div class="relative h-64 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-600/30 to-emerald-500/30"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/80 to-transparent"></div>
                    
                    <!-- Live Status Badge -->
                    <div v-if="streamer.isLive" class="absolute top-4 right-4 z-10">
                        <div class="flex items-center gap-2 bg-gradient-to-r from-green-600 to-emerald-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                            <i class="fas fa-circle text-xs animate-pulse"></i>
                            <span>LIVE</span>
                        </div>
                    </div>
                    
                    <!-- Profile Image -->
                    <div class="absolute left-1/2 transform -translate-x-1/2 w-40 h-40 rounded-full border-4 border-gray-900 bg-gradient-to-br bg-white overflow-hidden group-hover:scale-110 transition-transform duration-500" style="top:50px;">
                        <!-- Profile Image with fallback icon -->
                        <img 
                            v-if="streamer.image" 
                            :src="streamer.image" 
                            :alt="streamer.name"
                            class="w-full h-full object-cover"
                        >
                        <div v-else class="w-full h-full bg-gradient-to-br from-green-400 to-emerald-300 flex items-center justify-center text-5xl font-bold text-gray-900">
                            <i :class="streamer.icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Streamer Info -->
                <div class="pt-20 pb-6 px-6">
                    <!-- Name and Role -->
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

                    <!-- Description -->
                    <p class="text-gray-300 text-center mb-6 line-clamp-3">
                        <i class="fas fa-quote-left text-green-400 mr-2 opacity-50"></i>
                        {{ streamer.description }}
                    </p>

                    <!-- Stats -->
                    <div class="mb-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white">
                                <i class="fas fa-users text-green-400 mr-1"></i> {{ formatNumber(streamer.followers) }}
                            </div>
                            <div class="text-sm text-gray-400">Kick Followers</div>
                        </div>
                    </div>

                    <!-- Links -->
                    <div class="flex justify-center gap-3">
                        <a 
                            :href="streamer.kickLink" 
                            target="_blank" 
                            class="flex-1 w-full bg-gradient-to-r from-green-600 to-emerald-500 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-emerald-600 transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i class="fab fa-kickstarter"></i>Kick Stream
                        </a>
                        <a 
                            v-show="streamer.websiteLink"
                            :href="streamer.websiteLink" 
                            target="_blank" 
                            class="flex-1 w-full bg-gradient-to-r from-gray-800 to-gray-900 border border-gray-700 text-white py-3 rounded-lg font-semibold hover:border-green-500 hover:from-gray-900 hover:to-black transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i class="fas fa-globe"></i>Website
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- See More / Show Less Button -->
        <div class="text-center mt-12">
            <button 
                v-if="showMoreButtonVisible"
                @click="toggleShowMore"
                class="cursor-pointer bg-gradient-to-r from-green-600 to-emerald-500 text-white px-8 py-4 rounded-lg font-bold text-lg hover:from-green-700 hover:to-emerald-600 transition-all transform hover:scale-105 shadow-lg shadow-green-500/25 flex items-center gap-3 mx-auto"
            >
                {{ showMore ? 'Show Less Kukuys' : 'See More Kukuys' }}
                <i class="fas" :class="showMore ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            
            <!-- Streamer Counter -->
            <p class="text-gray-400 mt-4 text-sm">
                Showing {{ visibleStreamers.length }} of {{ streamers.length }} streamers
            </p>
        </div>
    </section>
</template>

<script lang="ts">
export default {
    name: 'StreamerSection',
    data() {
        return {
            showMore: false,
            itemsPerLoad: 6,
            streamers: [
                {
                    id: 1,
                    name: 'Kuku',
                    nameIcon: '',
                    tags: ['GodFather of PH Dota 2','Professional Dota 2 Player', 'Position 5 | Hard Support', 'Top 7 TI', 'WESG Champion'],
                    description: 'Veteran Filipino Dota 2 captain known for elite leadership, strong shot-calling, and international success.',
                    followers: 69000,
                    kickLink: 'https://kick.com/kukudota2',
                    websiteLink: 'https://www.kukudota2.com/',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/kuku.jpg'
                },{
                    id: 2,
                    name: 'Gabbi',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'Position 1 | Carry', 'Major Champion', 'TI Player', 'WESG Champion'],
                    description: 'Filipino Dota 2 core player known for explosive teamfight impact and international success.',
                    followers: 80800,
                    kickLink: 'https://kick.com/gabbidoto',
                    websiteLink: 'https://www.gabbidoto.com/',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/gabbi.jpg'
                },{
                    id: 3,
                    name: 'Armel',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'Position 2 | Midlaner', 'Major Champion', 'TI Player', 'WESG Champion'],
                    description: 'Filipino Dota 2 mid player known for high-level mechanics and international success.',
                    followers: 66600,
                    kickLink: 'https://kick.com/armeldoto',
                    websiteLink: 'https://armeldota.com/',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/armel.png'
                },{
                    id: 4,
                    name: 'DJ',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'Position 4 | Soft Support', 'Top 4 TI'],
                    description: 'Veteran Filipino Dota 2 support known for clutch saves, high game sense, and international success.',
                    followers: 33900,
                    kickLink: 'https://kick.com/djdoto',
                    websiteLink: 'https://djdota.com/',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/dj.jpg'
                },{
                    id: 5,  
                    name: 'Yowe',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'Position 2 | Midlaner', 'IRL Streamer'],
                    description: 'Filipino Dota 2 midlaner recognized for fearless plays and game-changing impact in high-level matches.',
                    followers: 60800,
                    kickLink: 'https://kick.com/yowe',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/yowe.jpg'
                },{
                    id: 6,  
                    name: 'Palos',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'Position 1 | Carry', 'Riyadh Player'],
                    description: 'Filipino Dota 2 carry known for sharp mechanics, aggressive plays, and international experience.',
                    followers: 36100,
                    kickLink: 'https://kick.com/solapsapdota',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/palos.jpg'
                },{
                    id: 7,  
                    name: 'Kokz',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'Position 2 | Midlaner'],
                    description: 'Filipino Dota 2 rising star recognized for game-changing impact.',
                    followers: 39400,
                    kickLink: 'https://kick.com/kokzdota',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/kokz.png'
                },{
                    id: 8,  
                    name: 'Abat',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'Position 1 | Carry', 'Dota 2 Caster'],
                    description: 'Filipino Dota 2 rising star recognized for aggressive plays and clutch impact.',
                    followers: 41600,
                    kickLink: 'https://kick.com/abatdota',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/abat.jpg'
                },{
                    id: 9,  
                    name: 'Jwl',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'Position 5 | Hard Support', 'Position 4 | Soft Support'],
                    description: 'Filipino Dota 2 rising star recognized for skillful plays and versatile support roles.',
                    followers: 28600,
                    kickLink: 'https://kick.com/jwldota',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/jwl.jpg'
                },{
                    id: 10,  
                    name: 'Tino',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'Position 3 | Offlane', 'Riyadh Player'],
                    description: 'Filipino Dota 2 offlane player known for strong teamfight presence and consistent competitive impact.',
                    followers: 15500,
                    kickLink: 'https://kick.com/tinodota',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/tino.jpg'
                },{
                    id: 11,  
                    name: 'Karl',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'Position 2 | Midlaner','TI Player', 'ESL One Champion'],
                    description: 'Filipino Dota 2 midlaner known for sharp mechanics, consistent performance, and international experience.',
                    followers: 39100,
                    kickLink: 'https://kick.com/karldotaa',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/karl.jpg'
                },{
                    id: 12,  
                    name: 'JG',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'Position 3 | Offlaner'],
                    description: 'Filipino Dota 2 offlaner known for skillful plays and competitive exposure.',
                    followers: 18500,
                    kickLink: 'https://kick.com/jgdota',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/jg.png'
                },{
                    id: 13,  
                    name: 'Sep',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'Position 4 | Soft Support'],
                    description: 'Filipino Dota 2 soft support known for skillful plays and impactful teamfight presence.',
                    followers: 20000,
                    kickLink: 'https://kick.com/sepdoto',
                    websiteLink: 'https://www.sepdoto.com/',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/sep.png'
                },{
                    id: 14,  
                    name: 'Lewis',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'Position 2 | Midlaner'],
                    description: 'Filipino Dota 2 midlaner known for sharp mechanics and consistent performance.',
                    followers: 11100,
                    kickLink: 'https://kick.com/lewisdotaa',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/lewis.jpg'
                },{
                    id: 15,  
                    name: 'Natsumi',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'Riyadh Player', 'Carry for OG'],
                    description: 'Filipino Dota 2 carry known for sharp mechanics, strategic farming, and international experience.',
                    followers: 33600,
                    kickLink: 'https://kick.com/natsumidota',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/natsumi.jpg'
                },{
                    id: 16,  
                    name: 'Nikko',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'Riyadh Player', 'Offlane for OG'],
                    description: 'Filipino Dota 2 offlaner recognized for impactful teamfight presence and international experience.',
                    followers: 24200,
                    kickLink: 'https://kick.com/nikkodota2',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/nikko.jpg'
                },{
                    id: 17,  
                    name: 'Skem',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'TI Player', 'Riyadh Player', 'Hard Support for OG'],
                    description: 'Filipino Dota 2 support known for smart plays, solid vision control, and international experience.',
                    followers: 14700,
                    kickLink: 'https://kick.com/skemdota123',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/skem.jpg'
                },{
                    id: 18,
                    name: 'Tims',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'Major Champion', 'Top 7 TI', 'WESG Champion', 'Soft Support for OG'],
                    description: 'Filipino Dota 2 mid player known for high-level mechanics and international success.',
                    followers: 27200,
                    kickLink: 'https://kick.com/timsdota',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/tims.png'
                },{
                    id: 19,  
                    name: 'Jaunuel',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'Position 5 | Hard Support', 'TI Player', 'Riyadh Player', ],
                    description: 'Filipino Dota 2 support known for smart positioning, strong game sense, and international competition.',
                    followers: 29000,
                    kickLink: 'https://kick.com/jaunueldota',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/jaunuel.png'
                },{
                    id: 20,  
                    name: 'Jing',
                    nameIcon: '',
                    tags: ['Professional Dota 2 Player', 'Position 5 | Hard Support', 'TI Player'],
                    description: 'Filipino Dota 2  rising star support known for adaptability, and impactful performances.',
                    followers: 22700,
                    kickLink: 'https://kick.com/jingdota',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/jing.jpg'
                },{
                    id: 21,  
                    name: 'JtzCast',
                    nameIcon: '',
                    tags: ['Professional Caster' , 'Dota 2 Streamer', 'Ex Manager of Team Kukuys', 'Kick Streamer'],
                    description: 'Filipino Dota 2 caster known for insightful live commentary, and engaging audience interaction.',
                    followers: 18400,
                    kickLink: 'https://kick.com/jtzcast',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/jtz.jpg'
                },{
                    id: 22,  
                    name: 'Nevertheless',
                    nameIcon: '',
                    tags: ['Manager of Team Tekla' , 'Dota 2 Streamer', 'Kick Streamer'],
                    description: 'Content creator known for live commentary, and supporting Team Tekla\'s competitive journey.',
                    followers: 19100,
                    kickLink: 'https://kick.com/nevertheless',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/kyle.jpg'
                },{
                    id: 23,  
                    name: 'LashSegway28',
                    nameIcon: '',
                    tags: ['Kick Streamer', 'Dota 2 Streamer', 'Dota 2 Caster'],
                    description: 'Content creator known for live commentary and variety gameplays.',
                    followers: 18600,
                    kickLink: 'https://kick.com/lashsegway28',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/lash.jpg'
                },{
                    id: 24,  
                    name: 'JaboleroDota',
                    nameIcon: '',
                    tags: ['Kick Streamer', 'Dota 2 Streamer', 'IRL Streamer'],
                    description: 'Content creator known for Dota 2 Streamer and IRL Streamer.',
                    followers: 12100,
                    kickLink: 'https://kick.com/jabolerodota',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/jabolero.jpg?v=1'
                },{
                    id: 25,  
                    name: 'Hubrisss',
                    nameIcon: '',
                    tags: ['Kick Streamer', 'Variety Streamer', 'GTA RP Streamer'],
                    description: 'Content creator known for GTA RP Streamer, variety gameplays, and engaging streams',
                    followers: 23600,
                    kickLink: 'https://kick.com/hubrisss',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/hubris.png'
                },{
                    id: 26,  
                    name: 'Chupaeng',
                    nameIcon: '',
                    tags: ['Kick Streamer', 'Variety Streamer', 'GTA RP Streamer'],
                    description: 'Content creator known for GTA RP Streamer, variety gameplays, and engaging streams',
                    followers: 15300,
                    kickLink: 'https://kick.com/chupaeng',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/chupaeng.jpg'
                },{
                    id: 27,  
                    name: 'SunshineMelodyyy',
                    nameIcon: '',
                    tags: ['Kick Streamer', 'Variety Streamer', 'GTA RP Streamer'],
                    description: 'Content creator known for GTA RP Streamer, variety gameplays, and engaging streams',
                    followers: 18000,
                    kickLink: 'https://kick.com/sunshinemelodyyy',
                    websiteLink: 'https://sunshinemelody.com/',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/sunshine.jpg'
                },{
                    id: 28,  
                    name: 'Jawocolet',
                    nameIcon: '',
                    tags: ['Kick Streamer', 'Variety Streamer'],
                    description: 'Content creator known for variety gameplays, and engaging streams',
                    followers: 4700,
                    kickLink: 'https://kick.com/jawocolet',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/jawo.jpg'
                },{
                    id: 29,  
                    name: 'Joevy',
                    nameIcon: '',
                    tags: ['Dota 2 Streamer', 'Manager of Xctn'],
                    description: 'Dota 2 streamer and manager of Xctn, supporting Xctn competitive journey.',
                    followers: 11100,
                    kickLink: 'https://kick.com/joevydota2',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/joevy.png'
                },{
                    id: 30,  
                    name: 'RexhaScarlett',
                    nameIcon: '',
                    tags: ['Kick Streamer', 'Variety Streamer'],
                    description: 'Content creator known for variety gameplays, engaging streams, and community-driven content.',
                    followers: 5800,
                    kickLink: 'https://kick.com/rexhascarlett',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/rexha.png'
                },{
                    id: 31,  
                    name: 'Sherickab',
                    nameIcon: '',
                    tags: ['Kick Streamer', 'Variety Streamer'],
                    description: 'Content creator known for variety gameplays, engaging streams, and community-driven content.',
                    followers: 9400,
                    kickLink: 'https://kick.com/sherickab',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/sherickab.jpg'
                },{
                    id: 32,  
                    name: 'Moodyemel',
                    nameIcon: '',
                    tags: ['Kick Streamer', 'Variety Streamer'],
                    description: 'Content creator known for variety gameplays, engaging streams, and community-driven content.',
                    followers: 4100,
                    kickLink: 'https://kick.com/moodyemel',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/moodyemel.png'
                },{
                    id: 33,  
                    name: 'Aerein',
                    nameIcon: '',
                    tags: ['Kick Streamer', 'Variety Streamer'],
                    description: 'Content creator known for variety gameplays, engaging streams, and community-driven content.',
                    followers: 7400,
                    kickLink: 'https://kick.com/aerein',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/ela.png'
                },{
                    id: 34,  
                    name: 'PeachyBunny',
                    nameIcon: '',
                    tags: ['Kick Streamer', 'Variety Streamer'],
                    description: 'Content creator known for variety gameplays, engaging streams, and community-driven content.',
                    followers: 7900,
                    kickLink: 'https://kick.com/peachybunny',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/peachy.png'
                },{
                    id: 35,  
                    name: 'Rainzerlyn',
                    nameIcon: '',
                    tags: ['Professional Caster','Kick Streamer', 'Variety Streamer'],
                    description: 'Content creator known for shoutcasting, variety gameplays, engaging streams, and community-driven content.',
                    followers: 13600,
                    kickLink: 'https://kick.com/rainzerlyn',
                    websiteLink: '',
                    isLive: false,
                    icon: 'fab fa-kickstarter',
                    image: 'images/kukuys_streamer/rainzerlyn.png'
                }
                
            ]
        }
    },
    computed: {
        visibleStreamers() {
            if (this.showMore) {
                // Show all streamers
                return this.streamers;
            } else {
                // Show only first 6 streamers
                return this.streamers.slice(0, this.itemsPerLoad);
            }
        },
        showMoreButtonVisible() {
            // Show button only if there are more streamers than the initial display
            return this.streamers.length > this.itemsPerLoad;
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
            
            // Smooth scroll to section when showing more
            if (!this.showMore) {
                setTimeout(() => {
                    const element = document.getElementById('kukuys');
                    if (element) {
                        element.scrollIntoView({ behavior: 'smooth' });
                    }
                }, 100);
            }
        }
    }
}
</script>

<style scoped>
/* Line clamp for description */
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom scrollbar for the section if needed */
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

/* Smooth transition for showing more cards */
.grid {
    transition: all 0.3s ease;
}
</style>
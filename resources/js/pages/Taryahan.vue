<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { taryahan } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Taryahan',
        href: taryahan().url,
    },
];

// ========== ANIMATED COIN FLIP SECTION ==========
const coinIsFlipping = ref<boolean>(false);
const coinResult = ref<string>('');
const coinSide = ref<'heads' | 'tails'>('heads');
const currentCoinFace = ref<'heads' | 'tails'>('heads');
const flipCount = ref<number>(0);

// How-to-use modal state
const showHelpModal = ref(false);

// Game selection for match
const selectedGame = ref<'dota2' | 'cs2'>('cs2');

const flipCoin = () => {
    if (coinIsFlipping.value) return;
    
    coinIsFlipping.value = true;
    coinResult.value = '';
    flipCount.value += 1;
    
    // Determine actual result
    const actualResult = Math.random() > 0.5 ? 'heads' : 'tails';
    const win = actualResult === coinSide.value ? 'WIN' : 'LOSE';
    
    // Animate the coin flip (10 rotations)
    let rotationCount = 0;
    const maxRotations = 10;
    
    const flipAnimation = () => {
        if (rotationCount < maxRotations) {
            // Alternate between heads and tails for animation
            currentCoinFace.value = currentCoinFace.value === 'heads' ? 'tails' : 'heads';
            rotationCount++;
            setTimeout(flipAnimation, 100); // Speed of rotation
        } else {
            // Final result
            currentCoinFace.value = actualResult;
            coinIsFlipping.value = false;
            coinResult.value = `Result: ${actualResult.toUpperCase()} - You ${win}!`;
        }
    };
    
    flipAnimation();
};

// ========== MATCHMAKING SECTION ==========
interface Player {
    id: number;
    name: string;
    skill: number; // 1-10 scale
}

interface Team {
    id: number;
    name: string;
    players: Player[];
    captain: string;
}

interface MatchRecord {
    id: number;
    team_a_name: string;
    team_b_name: string;
    team_a_players: string[];
    team_b_players: string[];
    team_a_captain: string;
    team_b_captain: string;
    winner: string; // 'team_a' or 'team_b'
    match_date: string;
    created_at: string;
    game_type?: 'dota2' | 'cs2'; // Add game type
}

// Player inputs
const players = ref<Player[]>(Array.from({ length: 10 }, (_, i) => ({
    id: i + 1,
    name: '',
    skill: 5 // Default skill level
})));

// Fisher–Yates shuffle
const shuffleArray = <T>(array: T[]): T[] => {
    const arr = [...array];
    for (let i = arr.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [arr[i], arr[j]] = [arr[j], arr[i]];
    }
    return arr;
};

// Auto-balance teams based on skill
const autoBalanceTeams = () => {
    const validPlayers = players.value
        .filter(p => p.name.trim() !== '')
        .map(p => ({ ...p, skill: p.skill || 5 }));

    if (validPlayers.length < 10) {
        alert(`Need ${10 - validPlayers.length} more players to auto-balance!`);
        return;
    }

    // Group players by skill
    const skillGroups: Record<number, Player[]> = {};

    validPlayers.forEach(player => {
        if (!skillGroups[player.skill]) {
            skillGroups[player.skill] = [];
        }
        skillGroups[player.skill].push(player);
    });

    // Sort skills descending and shuffle players within the same skill
    const sortedPlayers: Player[] = Object.keys(skillGroups)
        .map(Number)
        .sort((a, b) => b - a)
        .flatMap(skill => shuffleArray(skillGroups[skill]));

    // Snake draft distribution
    const teamAPlayers: Player[] = [];
    const teamBPlayers: Player[] = [];

    sortedPlayers.forEach((player, index) => {
        (index % 2 === 0 ? teamAPlayers : teamBPlayers).push(player);
    });

    // Update UI
    teamA.value.players = teamAPlayers;
    teamB.value.players = teamBPlayers;

    // Auto-select captains (highest skill after shuffle)
    teamA.value.captain = teamAPlayers[0]?.name || '';
    teamB.value.captain = teamBPlayers[0]?.name || '';
};


// Teams
const teamA = ref<Team>({
    id: 1,
    name: 'Team A',
    players: [],
    captain: '',
});

const teamB = ref<Team>({
    id: 2,
    name: 'Team B',
    players: [],
    captain: '',
});

// Available players for selection
const availablePlayers = computed(() => {
    return players.value
        .filter(p => p.name.trim() !== '')
        .filter(p => !teamA.value.players.some(tp => tp.id === p.id) && 
                     !teamB.value.players.some(tp => tp.id === p.id));
});

// Move player to team
const moveToTeam = (player: Player, team: 'A' | 'B') => {
    if (team === 'A') {
        teamA.value.players.push(player);
    } else {
        teamB.value.players.push(player);
    }
};

// Remove player from team
const removeFromTeam = (player: Player, team: 'A' | 'B') => {
    if (team === 'A') {
        teamA.value.players = teamA.value.players.filter(p => p.id !== player.id);
    } else {
        teamB.value.players = teamB.value.players.filter(p => p.id !== player.id);
    }
};

// Calculate team skill average
const teamSkillAverage = (team: Team) => {
    if (team.players.length === 0) return 0;
    const total = team.players.reduce((sum, player) => sum + player.skill, 0);
    return (total / team.players.length).toFixed(1);
};

// ========== MATCH RECORDS SECTION ==========
const matchRecords = ref<MatchRecord[]>([]);
const loading = ref(false);
const search = ref('');
const dateFrom = ref('');
const dateTo = ref('');
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);
const perPage = ref(20);

// Update winner modal
const showUpdateWinnerModal = ref(false);
const selectedMatch = ref<MatchRecord | null>(null);
const selectedWinner = ref<'team_a' | 'team_b'>('team_a');

// Delete confirmation modal
const showDeleteConfirm = ref(false);
const deleteId = ref<number | null>(null);

// Save match
const saveMatch = async () => {
    // Validation
    if (teamA.value.players.length !== 5 || teamB.value.players.length !== 5) {
        alert('Both teams must have exactly 5 players!');
        return;
    }
    
    if (!teamA.value.captain || !teamB.value.captain) {
        alert('Please select captains for both teams!');
        return;
    }
    
    try {
        const matchData = {
            team_a_name: teamA.value.name,
            team_b_name: teamB.value.name,
            team_a_players: teamA.value.players.map(p => p.name),
            team_b_players: teamB.value.players.map(p => p.name),
            team_a_captain: teamA.value.captain,
            team_b_captain: teamB.value.captain,
            winner: null, // No winner initially - will be set later
            game_type: selectedGame.value,
            match_date: new Date().toISOString().split('T')[0]
        };
        
        const response = await axios.post('/api/taryahan/matches', matchData);
        
        if (response.data.success) {
            resetTeams();
            fetchMatchRecords();
        }
    } catch (error) {
        console.error('Error saving match:', error);
        alert('Failed to save match');
    }
};

// Reset teams
const resetTeams = () => {
    teamA.value = {
        id: 1,
        name: 'Team A',
        players: [],
        captain: '',
    };
    
    teamB.value = {
        id: 2,
        name: 'Team B',
        players: [],
        captain: '',
    };
    
    players.value = Array.from({ length: 10 }, (_, i) => ({
        id: i + 1,
        name: '',
        skill: 5
    }));
    
    // Reset game selection to default
    selectedGame.value = 'cs2';
};

// Fetch match records
const fetchMatchRecords = async () => {
    loading.value = true;
    try {
        const params = new URLSearchParams({
            page: currentPage.value.toString(),
            per_page: perPage.value.toString(),
        });
        
        if (search.value) params.append('search', search.value);
        if (dateFrom.value) params.append('date_from', dateFrom.value);
        if (dateTo.value) params.append('date_to', dateTo.value);

        const response = await axios.get(`/api/taryahan/matches?${params}`);
        
        if (response.data.success) {
            matchRecords.value = response.data.data.data;
            currentPage.value = response.data.data.current_page;
            lastPage.value = response.data.data.last_page;
            total.value = response.data.data.total;
        }
    } catch (error) {
        console.error('Error fetching match records:', error);
    } finally {
        loading.value = false;
    }
};

// Open update winner modal
const openUpdateWinner = (match: MatchRecord) => {
    selectedMatch.value = match;
    selectedWinner.value = match.winner as 'team_a' | 'team_b';
    showUpdateWinnerModal.value = true;
};

// Update winner
const updateWinner = async () => {
    if (!selectedMatch.value) return;
    
    try {
        const response = await axios.put(`/api/taryahan/matches/${selectedMatch.value.id}`, {
            winner: selectedWinner.value
        });
        
        if (response.data.success) {
            await fetchMatchRecords();
            showUpdateWinnerModal.value = false;
            selectedMatch.value = null;
        }
    } catch (error) {
        console.error('Error updating winner:', error);
        alert('Failed to update winner');
    }
};

// Delete match record
const confirmDelete = (id: number) => {
    deleteId.value = id;
    showDeleteConfirm.value = true;
};

const deleteMatchRecord = async () => {
    if (!deleteId.value) return;
    
    try {
        const response = await axios.delete(`/api/taryahan/matches/${deleteId.value}`);
        
        if (response.data.success) {
            await fetchMatchRecords();
            showDeleteConfirm.value = false;
            deleteId.value = null;
        }
    } catch (error) {
        console.error('Error deleting match record:', error);
    }
};

// Format date
const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

// Format game type display
const formatGameType = (gameType: string | undefined) => {
    if (!gameType) return 'Dota 2';
    return gameType === 'cs2' ? 'Counter-Strike 2' : 'Dota 2';
};

// Pagination
const goToPage = (page: number) => {
    currentPage.value = page;
    fetchMatchRecords();
};

const pageNumbers = computed(() => {
    const pages = [];
    const maxVisible = 5;
    let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
    const end = Math.min(lastPage.value, start + maxVisible - 1);
    
    if (end - start < maxVisible - 1) {
        start = Math.max(1, end - maxVisible + 1);
    }
    
    for (let i = start; i <= end; i++) {
        pages.push(i);
    }
    
    return pages;
});

onMounted(() => {
    fetchMatchRecords();
});
</script>

<template>
    <Head title="Taryahan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Header with Create Button -->
            <div class="flex justify-between items-center mt-6">
                <div></div>
                <!-- Help Modal Trigger -->
                <button
                    @click="showHelpModal = true"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    How to use Taryahan?
                </button>
                <div></div>
            </div>

        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- ========== ANIMATED COIN FLIP SECTION ========== -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Coin Flip</h2>
                <div class="flex flex-col items-center justify-center p-6 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl">
                    <div class="text-center mb-6">
                        <p class="text-gray-600 mb-6">Choose your side and flip the coin to decide teams!</p>
                        
                        <!-- Coin Selection -->
                        <div class="flex justify-center gap-6 mb-8">
                            <button
                                @click="coinSide = 'heads'"
                                :class="[
                                    'px-8 py-4 rounded-xl font-medium transition transform hover:scale-105',
                                    coinSide === 'heads' 
                                        ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg' 
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                ]"
                            >
                                <div class="flex flex-col items-center">
                                    <svg class="w-8 h-8 mb-2" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10" />
                                        <text x="12" y="16" text-anchor="middle" fill="white" font-size="10">H</text>
                                    </svg>
                                    <span>Heads</span>
                                </div>
                            </button>
                            <button
                                @click="coinSide = 'tails'"
                                :class="[
                                    'px-8 py-4 rounded-xl font-medium transition transform hover:scale-105',
                                    coinSide === 'tails' 
                                        ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-lg' 
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                ]"
                            >
                                <div class="flex flex-col items-center">
                                    <svg class="w-8 h-8 mb-2" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10" />
                                        <text x="12" y="16" text-anchor="middle" fill="white" font-size="10">T</text>
                                    </svg>
                                    <span>Tails</span>
                                </div>
                            </button>
                        </div>
                        
                        <!-- Animated Coin -->
                        <div class="relative mb-8">
                            <div class="relative w-32 h-32 mx-auto">
                                <!-- Coin Container -->
                                <div 
                                    :class="[
                                        'absolute inset-0 rounded-full transition-all duration-100 transform',
                                        coinIsFlipping ? 'animate-spin' : '',
                                        currentCoinFace === 'heads' 
                                            ? 'bg-gradient-to-r from-yellow-400 to-yellow-500' 
                                            : 'bg-gradient-to-r from-gray-400 to-gray-500'
                                    ]"
                                    style="box-shadow: 0 0 20px rgba(0,0,0,0.2);"
                                ></div>
                                
                                <!-- Coin Face -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div 
                                        :class="[
                                            'text-4xl font-bold',
                                            currentCoinFace === 'heads' ? 'text-white' : 'text-gray-800'
                                        ]"
                                    >
                                        {{ currentCoinFace === 'heads' ? 'H' : 'T' }}
                                    </div>
                                </div>
                                
                                <!-- Coin Edge -->
                                <div class="absolute inset-0 rounded-full border-4 border-gray-300 opacity-50"></div>
                            </div>
                        </div>
                        
                        <!-- Flip Button -->
                        <button
                            @click="flipCoin"
                            :disabled="coinIsFlipping"
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg font-medium hover:opacity-90 transition transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="coinIsFlipping" class="flex items-center gap-2">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Flipping...
                            </span>
                            <span v-else>Flip Coin!</span>
                        </button>
                    </div>
                    
                    <!-- Result Display -->
                    <div v-if="coinResult" class="mt-4 p-4 bg-white rounded-lg shadow-inner animate-pulse">
                        <p class="text-lg font-semibold" :class="coinResult.includes('WIN') ? 'text-green-600' : 'text-red-600'">
                            {{ coinResult }}
                        </p>
                    </div>
                    
                    <!-- Stats -->
                    <div class="mt-6 text-sm text-gray-500">
                        <p>Flips today: {{ flipCount }}</p>
                    </div>
                </div>
            </div>

            <!-- ========== MATCHMAKING SECTION ========== -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Matchmaking</h2>
                
                <!-- Game Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Game:</label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input
                                type="radio"
                                v-model="selectedGame"
                                value="dota2"
                                class="mr-2 text-blue-600 focus:ring-blue-500"
                            />
                            <span class="text-gray-700">Dota 2 (5v5)</span>
                        </label>
                        <label class="flex items-center">
                            <input
                                type="radio"
                                v-model="selectedGame"
                                value="cs2"
                                class="mr-2 text-orange-600 focus:ring-orange-500"
                            />
                            <span class="text-gray-700">Counter-Strike 2 (5v5)</span>
                        </label>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ selectedGame === 'dota2' 
                            ? 'Dota 2: MOBA game with 5 players per team' 
                            : 'Counter-Strike 2: Tactical FPS with 5 players per team' }}
                    </p>
                </div>
                
                <!-- Player Inputs (5v5) -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Enter 10 Players</h3>
                        <button
                            @click="autoBalanceTeams"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm"
                        >
                            Auto-Balance Teams
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Team A Column -->
                        <div>
                            <h4 class="font-medium text-gray-700 mb-3">Team A Players</h4>
                            <div class="space-y-3">
                                <div v-for="player in players.slice(0, 5)" :key="player.id" class="flex gap-3">
                                    <input
                                        v-model="player.name"
                                        type="text"
                                        placeholder="Player name"
                                        class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                    <select
                                        v-model="player.skill"
                                        class="w-24 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option v-for="i in 10" :key="i" :value="i">Skill {{ i }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Team B Column -->
                        <div>
                            <h4 class="font-medium text-gray-700 mb-3">Team B Players</h4>
                            <div class="space-y-3">
                                <div v-for="player in players.slice(5)" :key="player.id" class="flex gap-3">
                                    <input
                                        v-model="player.name"
                                        type="text"
                                        placeholder="Player name"
                                        class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                    <select
                                        v-model="player.skill"
                                        class="w-24 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option v-for="i in 10" :key="i" :value="i">Skill {{ i }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Selection -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Available Players -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-700 mb-3">Available Players</h3>
                        <div class="space-y-2">
                            <div v-for="player in availablePlayers" :key="player.id" 
                                 class="flex items-center justify-between p-2 bg-white rounded border hover:bg-gray-50 transition">
                                <div>
                                    <span class="font-medium">{{ player.name }}</span>
                                    <span class="text-sm text-gray-500 ml-2">(Skill: {{ player.skill }})</span>
                                </div>
                                <div class="flex gap-2">
                                    <button @click="moveToTeam(player, 'A')" 
                                            class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm hover:bg-blue-200 transition">
                                        Team A
                                    </button>
                                    <button @click="moveToTeam(player, 'B')" 
                                            class="px-3 py-1 bg-red-100 text-red-700 rounded text-sm hover:bg-red-200 transition">
                                        Team B
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Team A -->
                    <div class="bg-blue-50 p-4 rounded-lg border-2 border-blue-200">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-semibold text-blue-700">Team A</h3>
                            <div class="text-sm text-blue-600">
                                Avg Skill: {{ teamSkillAverage(teamA) }}
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Captain</label>
                            <select v-model="teamA.captain" 
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Captain</option>
                                <option v-for="player in teamA.players" :key="player.id" :value="player.name">
                                    {{ player.name }} (Skill: {{ player.skill }})
                                </option>
                            </select>
                        </div>
                        
                        <div class="space-y-2">
                            <div v-for="player in teamA.players" :key="player.id" 
                                 class="flex items-center justify-between p-2 bg-white rounded border hover:bg-blue-50 transition">
                                <div>
                                    <span class="font-medium">{{ player.name }}</span>
                                    <span class="text-sm text-gray-500 ml-2">(Skill: {{ player.skill }})</span>
                                </div>
                                <button @click="removeFromTeam(player, 'A')" 
                                        class="px-2 py-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition">
                                    ✕
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Team B -->
                    <div class="bg-red-50 p-4 rounded-lg border-2 border-red-200">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-semibold text-red-700">Team B</h3>
                            <div class="text-sm text-red-600">
                                Avg Skill: {{ teamSkillAverage(teamB) }}
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Captain</label>
                            <select v-model="teamB.captain" 
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="">Select Captain</option>
                                <option v-for="player in teamB.players" :key="player.id" :value="player.name">
                                    {{ player.name }} (Skill: {{ player.skill }})
                                </option>
                            </select>
                        </div>
                        
                        <div class="space-y-2">
                            <div v-for="player in teamB.players" :key="player.id" 
                                 class="flex items-center justify-between p-2 bg-white rounded border hover:bg-red-50 transition">
                                <div>
                                    <span class="font-medium">{{ player.name }}</span>
                                    <span class="text-sm text-gray-500 ml-2">(Skill: {{ player.skill }})</span>
                                </div>
                                <button @click="removeFromTeam(player, 'B')" 
                                        class="px-2 py-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition">
                                    ✕
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <button @click="resetTeams" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Reset Teams
                    </button>
                    <button @click="saveMatch" 
                            :disabled="teamA.players.length !== 5 || teamB.players.length !== 5"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Save {{ selectedGame === 'dota2' ? 'Dota 2' : 'Counter-Strike 2' }} Match
                    </button>
                </div>
            </div>

            <!-- ========== MATCH HISTORY TABLE ========== -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Match History</h2>
                    
                    <!-- Filters -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search by team name or player..."
                            class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @keyup.enter="fetchMatchRecords"
                        />
                        <input
                            v-model="dateFrom"
                            type="date"
                            class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <input
                            v-model="dateTo"
                            type="date"
                            class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <div class="flex gap-2">
                            <button
                                @click="fetchMatchRecords"
                                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                            >
                                Filter
                            </button>
                            <button
                                @click="search = ''; dateFrom = ''; dateTo = ''; fetchMatchRecords()"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                            >
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Game</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teams</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Captains</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Players</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Winner</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="loading">
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    Loading...
                                </td>
                            </tr>
                            <tr v-else-if="matchRecords.length === 0">
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No match records found
                                </td>
                            </tr>
                            <tr v-else v-for="match in matchRecords" :key="match.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[
                                        'px-2 py-1 rounded-full text-xs font-medium',
                                        match.game_type === 'cs2'
                                            ? 'bg-orange-100 text-orange-800'
                                            : 'bg-blue-100 text-blue-800'
                                    ]">
                                        {{ formatGameType(match.game_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ match.team_a_name }}</div>
                                        <div class="text-sm text-gray-600">vs</div>
                                        <div class="font-medium text-gray-900">{{ match.team_b_name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm">{{ match.team_a_captain }}</div>
                                        <div class="text-sm text-gray-500">vs</div>
                                        <div class="text-sm">{{ match.team_b_captain }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="mb-1">
                                            <span class="font-medium">Team A:</span>
                                            <span class="text-gray-600 ml-2">{{ match.team_a_players.join(', ') }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium">Team B:</span>
                                            <span class="text-gray-600 ml-2">{{ match.team_b_players.join(', ') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="match.winner" :class="[
                                        'px-2 py-1 rounded-full text-xs font-medium',
                                        match.winner === 'team_a' 
                                            ? 'bg-blue-100 text-blue-800' 
                                            : 'bg-red-100 text-red-800'
                                    ]">
                                        {{ match.winner === 'team_a' ? match.team_a_name : match.team_b_name }}
                                    </span>
                                    <span v-else class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Pending
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ formatDate(match.match_date) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                    <button
                                        @click="openUpdateWinner(match)"
                                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                                    >
                                        Update Winner
                                    </button>
                                    <button
                                        @click="confirmDelete(match.id)"
                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="lastPage > 1" class="px-6 py-4 flex items-center justify-between border-t border-gray-200">
                    <div class="text-sm text-gray-700">
                        Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, total) }} of {{ total }} results
                    </div>
                    <div class="flex gap-2">
                        <button
                            @click="goToPage(currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Previous
                        </button>
                        <button
                            v-for="page in pageNumbers"
                            :key="page"
                            @click="goToPage(page)"
                            :class="[
                                'px-3 py-1 border rounded',
                                page === currentPage
                                    ? 'bg-blue-600 text-white'
                                    : 'hover:bg-gray-50'
                            ]"
                        >
                            {{ page }}
                        </button>
                        <button
                            @click="goToPage(currentPage + 1)"
                            :disabled="currentPage === lastPage"
                            class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- How-to-use Modal -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showHelpModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="showHelpModal = false">
                    <Transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showHelpModal" class="bg-white rounded-xl shadow-2xl p-6 max-w-4xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">
                                        How to Use Taryahan
                                    </h2>
                                    <p class="text-sm text-gray-500 mt-1">A quick guide to organizing and tracking your Counter-Strike 2 and Dota 2</p>
                                </div>
                                <button 
                                    @click="showHelpModal = false" 
                                    class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Video Player -->
                            <div class="mb-6">
                                <div class="aspect-video bg-gray-900 rounded-lg overflow-hidden">
                                    <video 
                                        controls 
                                        class="w-full h-full"
                                    >
                                        <source src="/videos/taryahan-tutorial.mp4" type="video/mp4">
                                        <p class="p-4 text-white text-center">
                                            Your browser doesn't support HTML5 video. 
                                            <a href="/videos/taryahan-tutorial.mp4" class="text-blue-400 underline">Download the video</a> instead.
                                        </p>
                                    </video>
                                </div>
                                <p class="text-sm text-gray-500 mt-2 text-center">Taryahan Match Manager Tutorial Video</p>
                            </div>
                            
                            <!-- Quick Steps -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mb-3">
                                        <span class="text-blue-600 font-bold">1</span>
                                    </div>
                                    <h3 class="font-semibold text-gray-900 mb-2">Setup Match</h3>
                                    <p class="text-sm text-gray-600">Select your game (Dota 2 or CS2), enter 10 players with their skill levels, and use the coin flip to decide teams.</p>
                                </div>
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mb-3">
                                        <span class="text-green-600 font-bold">2</span>
                                    </div>
                                    <h3 class="font-semibold text-gray-900 mb-2">Balance Teams</h3>
                                    <p class="text-sm text-gray-600">Use Auto-Balance to create fair teams based on skill levels or manually assign players to teams.</p>
                                </div>
                                <div class="bg-purple-50 p-4 rounded-lg">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mb-3">
                                        <span class="text-purple-600 font-bold">3</span>
                                    </div>
                                    <h3 class="font-semibold text-gray-900 mb-2">Save & Track</h3>
                                    <p class="text-sm text-gray-600">Save your match setup and later update the winner. Track all your matches in the history table.</p>
                                </div>
                            </div>
                            
                            <!-- Tips Section -->
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Tips & Features</h3>
                                <ul class="space-y-2 text-gray-600">
                                    <li class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span><strong>Coin Flip:</strong> Use the animated coin flip to fairly decide which team gets first pick or side selection</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span><strong>Auto-Balance:</strong> Automatically creates balanced teams based on player skill ratings (1-10)</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span><strong>Multi-Game Support:</strong> Works for both Dota 2 and Counter-Strike 2 (both are 5v5 games)</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Modal Actions -->
                            <div class="flex gap-3 pt-6 border-t border-gray-200">
                                <button
                                    @click="showHelpModal = false"
                                    class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                                >
                                    Got it, thanks!
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>

        <!-- Update Winner Modal -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showUpdateWinnerModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="showUpdateWinnerModal = false">
                    <Transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showUpdateWinnerModal" class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full" @click.stop>
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">Update Winner</h2>
                                    <p class="text-sm text-gray-500 mt-1">Select the winning team</p>
                                </div>
                                <button 
                                    @click="showUpdateWinnerModal = false" 
                                    class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            
                            <div v-if="selectedMatch" class="space-y-4">
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <div class="text-sm text-gray-600 mb-2">Match Details:</div>
                                    <div class="font-medium">{{ selectedMatch.team_a_name }} vs {{ selectedMatch.team_b_name }}</div>
                                    <div class="text-sm text-gray-500">
                                        Game: {{ formatGameType(selectedMatch.game_type) }} | 
                                        Date: {{ formatDate(selectedMatch.match_date) }}
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Winner:</label>
                                    <div class="space-y-3">
                                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-blue-50 transition"
                                               :class="selectedWinner === 'team_a' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                            <input type="radio" v-model="selectedWinner" value="team_a" class="mr-3">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ selectedMatch.team_a_name }}</div>
                                                <div class="text-sm text-gray-500">Captain: {{ selectedMatch.team_a_captain }}</div>
                                            </div>
                                        </label>
                                        
                                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-red-50 transition"
                                               :class="selectedWinner === 'team_b' ? 'border-red-500 bg-red-50' : 'border-gray-200'">
                                            <input type="radio" v-model="selectedWinner" value="team_b" class="mr-3">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ selectedMatch.team_b_name }}</div>
                                                <div class="text-sm text-gray-500">Captain: {{ selectedMatch.team_b_captain }}</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex gap-3 mt-6 pt-4 border-t border-gray-200">
                                <button
                                    @click="showUpdateWinnerModal = false"
                                    class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                                >
                                    Cancel
                                </button>
                                <button
                                    @click="updateWinner"
                                    class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                                >
                                    Update Winner
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>

        <!-- Delete Confirmation Modal -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showDeleteConfirm" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="showDeleteConfirm = false">
                    <Transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showDeleteConfirm" class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full" @click.stop>
                            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            
                            <h2 class="text-xl font-bold text-gray-900 text-center mb-2">Delete Match Record</h2>
                            <p class="text-gray-600 text-center mb-6">Are you sure you want to delete this match record? This action cannot be undone.</p>
                            
                            <div class="flex gap-3">
                                <button
                                    @click="showDeleteConfirm = false"
                                    class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                                >
                                    Cancel
                                </button>
                                <button
                                    @click="deleteMatchRecord"
                                    class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>
    </AppLayout>
</template>
<template>
  <div class="h-full overflow-y-auto bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/50">
    <div class="max-w-7xl mx-auto p-6 space-y-6">
      
      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center h-96">
        <div class="text-center">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
          <p class="mt-4 text-slate-600">Loading your dashboard...</p>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="flex items-center justify-center h-96">
        <div class="text-center bg-red-50 rounded-xl p-8 max-w-md">
          <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <h3 class="text-lg font-semibold text-red-900 mb-2">Failed to Load Dashboard</h3>
          <p class="text-red-700 mb-4">{{ error }}</p>
          <button @click="loadStats" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
            Try Again
          </button>
        </div>
      </div>

      <!-- Dashboard Content -->
      <template v-else>
        <!-- Welcome Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-8 text-white shadow-xl">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold mb-2">Good {{ greeting }}, {{ userName }}! 👋</h1>
              <p class="text-blue-100 text-lg">Welcome back to your PMSDrive dashboard</p>
            </div>
            <div class="hidden md:block">
              <svg class="w-24 h-24 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Storage Used Card -->
          <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow border border-slate-200">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                </svg>
              </div>
              <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Storage</span>
            </div>
            <div>
              <p class="text-2xl font-bold text-slate-800">{{ formatFileSize(stats.storage_used) }}</p>
              <p class="text-sm text-slate-500 mt-1">of {{ formatFileSize(stats.storage_quota) }} used</p>
              <div class="mt-3 bg-slate-100 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-full transition-all duration-500" :style="{ width: storagePercentage + '%' }"></div>
              </div>
              <p class="text-xs text-slate-400 mt-1">{{ storagePercentage }}% capacity</p>
            </div>
          </div>

          <!-- Total Files Card -->
          <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow border border-slate-200">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/30">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
              </div>
              <span class="text-xs font-semibold text-green-600 bg-green-50 px-3 py-1 rounded-full">Files</span>
            </div>
            <div>
              <p class="text-2xl font-bold text-slate-800">{{ stats.total_files || 0 }}</p>
              <p class="text-sm text-slate-500 mt-1">Total files stored</p>
            </div>
          </div>

          <!-- Total Folders Card -->
          <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow border border-slate-200">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
              </div>
              <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-3 py-1 rounded-full">Folders</span>
            </div>
            <div>
              <p class="text-2xl font-bold text-slate-800">{{ stats.total_folders || 0 }}</p>
              <p class="text-sm text-slate-500 mt-1">Organized folders</p>
            </div>
          </div>

          <!-- Shared Files Card -->
          <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow border border-slate-200">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
              </div>
              <span class="text-xs font-semibold text-orange-600 bg-orange-50 px-3 py-1 rounded-full">Shared</span>
            </div>
            <div>
              <p class="text-2xl font-bold text-slate-800">{{ stats.shared_files || 0 }}</p>
              <p class="text-sm text-slate-500 mt-1">Files shared with you</p>
            </div>
          </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          <!-- Left Column - Charts and Recent Files (2/3 width) -->
          <div class="lg:col-span-2 space-y-6">
            
            <!-- File Type Distribution Chart -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-200">
              <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-800">File Type Distribution</h2>
                <div class="flex space-x-2">
                  <button class="px-3 py-1 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg">Overview</button>
                  <button class="px-3 py-1 text-sm font-medium text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg">Analytics</button>
                </div>
              </div>
              <div class="h-80 flex items-center justify-center">
                <Pie v-if="stats.file_types && stats.file_types.length > 0" :data="fileTypeChartData" :options="chartOptions" />
                <div v-else class="text-center text-slate-400">
                  <svg class="w-16 h-16 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  <p>No files uploaded yet</p>
                </div>
              </div>
            </div>

            <!-- Recent Files -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-200">
              <h2 class="text-xl font-bold text-slate-800 mb-4">Recent Files</h2>
              <div v-if="stats.recent_files && stats.recent_files.length > 0" class="space-y-3">
                <div v-for="file in stats.recent_files.slice(0, 5)" :key="file.id" 
                     class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 transition-colors group border border-transparent hover:border-slate-200">
                  <div class="flex items-center space-x-3 flex-1">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center">
                      <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                      </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="font-medium text-slate-800 truncate">{{ file.name }}</p>
                      <p class="text-xs text-slate-500">{{ formatFileSize(file.size) }} • {{ formatDate(file.created_at) }}</p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                      </svg>
                    </button>
                    <button class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-8 text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <p>No recent files</p>
              </div>
            </div>

          </div>

          <!-- Right Column - Status and Quick Actions (1/3 width) -->
          <div class="space-y-6">
            
            <!-- Status Summary Card -->
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 shadow-xl text-white relative overflow-hidden">
              <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-bold">Status Summary</h3>
                  <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                </div>
                <p class="text-3xl font-bold mb-2">All Systems OK</p>
                <p class="text-blue-100 text-sm">Your storage is secure and accessible</p>
              </div>
              <svg class="absolute bottom-0 right-0 w-32 h-32 opacity-10" viewBox="0 0 200 200" fill="currentColor">
                <path d="M45,-55.5C57.9,-45.2,67.5,-30.4,71.8,-14.2C76.1,2,75.1,19.5,67.8,33.9C60.5,48.3,46.9,59.6,31.4,65.8C15.9,72,-1.5,73.1,-17.7,68.8C-33.9,64.5,-48.9,54.8,-58.7,41.2C-68.5,27.6,-73.1,10.1,-71.8,-7.1C-70.5,-24.3,-63.3,-41.2,-51.2,-51.1C-39.1,-61,-22.1,-64,-5.8,-57.1C10.5,-50.2,32.1,-65.8,45,-55.5Z"/>
              </svg>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-200">
              <h3 class="text-lg font-bold text-slate-800 mb-4">Quick Stats</h3>
              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <span class="text-sm text-slate-600">Available Space</span>
                  <span class="text-sm font-semibold text-green-600">{{ formatFileSize(stats.storage_quota - stats.storage_used) }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-sm text-slate-600">Upload Limit</span>
                  <span class="text-sm font-semibold text-slate-800">100 MB per file</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-sm text-slate-600">Active Shares</span>
                  <span class="text-sm font-semibold text-blue-600">{{ stats.shared_files || 0 }}</span>
                </div>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-200">
              <h3 class="text-lg font-bold text-slate-800 mb-4">Quick Actions</h3>
              <div class="space-y-2">
                <button class="w-full px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg shadow-blue-500/30 flex items-center justify-center space-x-2 font-medium">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                  </svg>
                  <span>Upload Files</span>
                </button>
                <button class="w-full px-4 py-3 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition-all flex items-center justify-center space-x-2 font-medium">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                  </svg>
                  <span>New Folder</span>
                </button>
                <button class="w-full px-4 py-3 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition-all flex items-center justify-center space-x-2 font-medium">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                  </svg>
                  <span>Share Files</span>
                </button>
              </div>
            </div>

          </div>

        </div>
      </template>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend
} from 'chart.js';
import { Pie } from 'vue-chartjs';
import axios from 'axios';

ChartJS.register(ArcElement, Tooltip, Legend);

const loading = ref(true);
const error = ref(null);
const stats = ref({
  storage_used: 0,
  storage_quota: 10737418240, // 10GB default
  total_files: 0,
  total_folders: 0,
  shared_files: 0,
  file_types: [],
  recent_files: []
});

// Get user name from localStorage
const userName = computed(() => {
  try {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    return user.name || 'User';
  } catch {
    return 'User';
  }
});

const greeting = computed(() => {
  const hour = new Date().getHours();
  if (hour < 12) return 'Morning';
  if (hour < 18) return 'Afternoon';
  return 'Evening';
});

const storagePercentage = computed(() => {
  if (!stats.value.storage_quota) return 0;
  return Math.round((stats.value.storage_used / stats.value.storage_quota) * 100);
});

const fileTypeChartData = computed(() => {
  return {
    labels: stats.value.file_types.map(ft => ft.type),
    datasets: [{
      data: stats.value.file_types.map(ft => ft.count),
      backgroundColor: [
        'rgba(59, 130, 246, 0.8)',
        'rgba(99, 102, 241, 0.8)',
        'rgba(168, 85, 247, 0.8)',
        'rgba(236, 72, 153, 0.8)',
        'rgba(251, 146, 60, 0.8)',
        'rgba(34, 197, 94, 0.8)'
      ],
      borderColor: [
        'rgb(59, 130, 246)',
        'rgb(99, 102, 241)',
        'rgb(168, 85, 247)',
        'rgb(236, 72, 153)',
        'rgb(251, 146, 60)',
        'rgb(34, 197, 94)'
      ],
      borderWidth: 2
    }]
  };
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'right',
      labels: {
        padding: 15,
        font: {
          size: 12,
          family: "'Inter', sans-serif"
        },
        generateLabels: function(chart) {
          const data = chart.data;
          if (data.labels.length && data.datasets.length) {
            return data.labels.map((label, i) => {
              const value = data.datasets[0].data[i];
              return {
                text: label + ' (' + value + ')',
                fillStyle: data.datasets[0].backgroundColor[i],
                hidden: false,
                index: i
              };
            });
          }
          return [];
        }
      }
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          const label = context.label || '';
          const value = context.parsed || 0;
          return ' ' + label + ': ' + value + ' files';
        }
      }
    }
  }
};

const loadStats = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    // Ensure token is set
    const token = localStorage.getItem('token');
    if (token) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }
    
    const response = await axios.get('/api/storage/stats');
    stats.value = response.data;
  } catch (err) {
    console.error('Error loading stats:', err);
    error.value = err.response?.data?.message || 'Failed to load dashboard data. Please try again.';
  } finally {
    loading.value = false;
  }
};

const formatFileSize = (bytes) => {
  if (!bytes || bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
};

const formatDate = (dateString) => {
  if (!dateString) return 'Unknown';
  const date = new Date(dateString);
  const now = new Date();
  const diff = now - date;
  const days = Math.floor(diff / (1000 * 60 * 60 * 24));

  if (days === 0) return 'Today';
  if (days === 1) return 'Yesterday';
  if (days < 7) return `${days} days ago`;

  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
};

onMounted(() => {
  // Small delay to ensure axios headers are set
  setTimeout(() => {
    loadStats();
  }, 100);
});
</script>

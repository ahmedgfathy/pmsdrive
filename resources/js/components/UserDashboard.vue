<template>
  <div class="min-h-screen bg-slate-50">
    <div class="max-w-screen-2xl mx-auto px-6 lg:px-8 py-8">
      
      <!-- Welcome Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">
          Good {{ greeting }}, 
          <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">{{ user?.name || 'User' }}</span>
        </h1>
        <p class="text-slate-500 mt-2">Your storage performance summary this week</p>
      </div>

      <!-- Quick Actions -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-2">
          <button :class="[
            'px-4 py-2 text-sm font-semibold rounded-lg transition-all',
            activeTab === 'overview' ? 'text-blue-600 bg-blue-50' : 'text-slate-600 hover:bg-slate-100'
          ]" @click="activeTab = 'overview'">
            Overview
          </button>
          <button :class="[
            'px-4 py-2 text-sm font-semibold rounded-lg transition-all',
            activeTab === 'analytics' ? 'text-blue-600 bg-blue-50' : 'text-slate-600 hover:bg-slate-100'
          ]" @click="activeTab = 'analytics'">
            Analytics
          </button>
        </div>
        
        <div class="flex items-center space-x-3">
          <button @click="$emit('navigate', 'drive')" class="px-5 py-2.5 bg-white border border-slate-200 hover:border-slate-300 rounded-xl text-sm font-semibold text-slate-700 transition-all flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            <span>Upload</span>
          </button>
          <button class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-xl text-sm font-semibold text-white shadow-lg shadow-blue-500/25 transition-all flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Export Report</span>
          </button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Storage Used -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-md transition-shadow">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500 mb-1">Storage Used</p>
              <h3 class="text-3xl font-bold text-slate-900">{{ stats.formatted_storage_used || '0 B' }}</h3>
              <div class="flex items-center mt-2">
                <span :class="[
                  'text-sm font-semibold',
                  stats.storage_percentage > 80 ? 'text-red-500' : 'text-emerald-500'
                ]">
                  {{ stats.storage_percentage || 0 }}%
                </span>
                <span class="text-xs text-slate-400 ml-2">of {{ stats.formatted_storage_quota || '10 GB' }}</span>
              </div>
            </div>
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/25">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
              </svg>
            </div>
          </div>
          <!-- Mini Progress Bar -->
          <div class="mt-4 w-full bg-slate-100 rounded-full h-2">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-2 rounded-full transition-all duration-500" :style="`width: ${Math.min(stats.storage_percentage || 0, 100)}%`"></div>
          </div>
        </div>

        <!-- Total Files -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-md transition-shadow">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500 mb-1">Total Files</p>
              <h3 class="text-3xl font-bold text-slate-900">{{ stats.total_files || 0 }}</h3>
              <div class="flex items-center mt-2">
                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-semibold text-emerald-500 ml-1">+12.5%</span>
              </div>
            </div>
            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/25">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Folders -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-md transition-shadow">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500 mb-1">Total Folders</p>
              <h3 class="text-3xl font-bold text-slate-900">{{ stats.total_folders || 0 }}</h3>
              <div class="flex items-center mt-2">
                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-semibold text-emerald-500 ml-1">+8.3%</span>
              </div>
            </div>
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/25">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Shared Files -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-md transition-shadow">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500 mb-1">Shared Files</p>
              <h3 class="text-3xl font-bold text-slate-900">0</h3>
              <div class="flex items-center mt-2">
                <span class="text-sm font-semibold text-slate-400">Coming Soon</span>
              </div>
            </div>
            <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/25">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Charts (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
          
          <!-- File Type Distribution Chart -->
          <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h2 class="text-xl font-bold text-slate-900">Storage by File Type</h2>
                <p class="text-sm text-slate-500 mt-1">Distribution of your files across different types</p>
              </div>
              <div class="flex items-center space-x-2">
                <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                <span class="text-xs text-slate-600">This month</span>
              </div>
            </div>
            
            <div v-if="stats.file_type_stats && stats.file_type_stats.length > 0" class="h-80">
              <Pie :data="chartData" :options="chartOptions" />
            </div>
            <div v-else class="h-80 flex flex-col items-center justify-center text-slate-400">
              <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <p class="text-lg font-medium">No files uploaded yet</p>
              <p class="text-sm mt-1">Start uploading files to see distribution</p>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
            <h2 class="text-xl font-bold text-slate-900 mb-4">Recent Files</h2>
            
            <div v-if="stats.recent_files && stats.recent_files.length > 0" class="space-y-1">
              <div v-for="file in stats.recent_files" :key="file.id" class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                <div class="flex items-center space-x-3 flex-1 min-w-0">
                  <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 truncate group-hover:text-blue-600 transition-colors">{{ file.original_name }}</p>
                    <p class="text-xs text-slate-500">{{ formatFileSize(file.size) }} • {{ formatDate(file.created_at) }}</p>
                  </div>
                </div>
                <button class="opacity-0 group-hover:opacity-100 p-2 hover:bg-slate-100 rounded-lg transition-all">
                  <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                  </svg>
                </button>
              </div>
            </div>
            
            <div v-else class="h-48 flex flex-col items-center justify-center text-slate-400">
              <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              <p class="font-medium">No recent files</p>
            </div>
          </div>
        </div>

        <!-- Right Column: Status Cards (1/3 width) -->
        <div class="space-y-6">
          
          <!-- Status Summary Card -->
          <div class="bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl shadow-xl shadow-blue-500/25 p-6 text-white overflow-hidden relative">
            <div class="relative z-10">
              <p class="text-sm font-medium text-blue-100 mb-1">Storage Capacity</p>
              <h3 class="text-4xl font-bold mb-1">{{ stats.storage_percentage || 0 }}%</h3>
              <p class="text-sm text-blue-100">Used this month</p>
            </div>
            
            <!-- Decorative Wave -->
            <svg class="absolute bottom-0 right-0 w-48 h-48 opacity-20" viewBox="0 0 200 200" fill="none">
              <path d="M0 100C20 80 40 60 60 60C80 60 100 80 120 80C140 80 160 60 180 60L200 60L200 200L0 200Z" fill="white"/>
            </svg>
          </div>

          <!-- Quick Stats -->
          <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Quick Stats</h3>
            
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-semibold text-slate-900">{{ stats.total_files || 0 }}</p>
                    <p class="text-xs text-slate-500">Total Files</p>
                  </div>
                </div>
                <span class="text-xs font-semibold text-emerald-500">100%</span>
              </div>

              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-semibold text-slate-900">{{ stats.total_folders || 0 }}</p>
                    <p class="text-xs text-slate-500">Total Folders</p>
                  </div>
                </div>
                <span class="text-xs font-semibold text-emerald-500">100%</span>
              </div>

              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-semibold text-slate-900">Active</p>
                    <p class="text-xs text-slate-500">Account Status</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Actions Card -->
          <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Quick Actions</h3>
            
            <div class="space-y-2">
              <button @click="$emit('navigate', 'drive')" class="w-full flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-blue-300 hover:bg-blue-50 transition-all group">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                  </div>
                  <span class="text-sm font-semibold text-slate-700 group-hover:text-blue-600">My Drive</span>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
              </button>

              <button @click="$emit('navigate', 'shared')" class="w-full flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 transition-all group">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 bg-emerald-100 group-hover:bg-emerald-200 rounded-lg flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                  </div>
                  <span class="text-sm font-semibold text-slate-700 group-hover:text-emerald-600">Shared Files</span>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
              </button>

              <button v-if="user?.is_admin" @click="$emit('navigate', 'admin')" class="w-full flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-purple-300 hover:bg-purple-50 transition-all group">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 bg-purple-100 group-hover:bg-purple-200 rounded-lg flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                  </div>
                  <span class="text-sm font-semibold text-slate-700 group-hover:text-purple-600">Admin Panel</span>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { Pie } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import axios from 'axios';

ChartJS.register(ArcElement, Tooltip, Legend);

defineEmits(['navigate']);

const activeTab = ref('overview');
const stats = ref({});
const user = ref(JSON.parse(localStorage.getItem('user') || '{}'));

const greeting = computed(() => {
  const hour = new Date().getHours();
  if (hour < 12) return 'Morning';
  if (hour < 18) return 'Afternoon';
  return 'Evening';
});

const chartData = computed(() => {
  if (!stats.value.file_type_stats || stats.value.file_type_stats.length === 0) {
    return { labels: [], datasets: [] };
  }

  const colors = [
    'rgba(59, 130, 246, 0.8)',   // blue
    'rgba(139, 92, 246, 0.8)',   // purple
    'rgba(236, 72, 153, 0.8)',   // pink
    'rgba(34, 197, 94, 0.8)',    // green
    'rgba(251, 146, 60, 0.8)',   // orange
    'rgba(14, 165, 233, 0.8)',   // sky
    'rgba(168, 85, 247, 0.8)',   // violet
    'rgba(249, 115, 22, 0.8)',   // orange-600
    'rgba(6, 182, 212, 0.8)',    // cyan
    'rgba(132, 204, 22, 0.8)',   // lime
  ];

  return {
    labels: stats.value.file_type_stats.map(item => item.type),
    datasets: [{
      data: stats.value.file_type_stats.map(item => item.size),
      backgroundColor: colors,
      borderColor: 'white',
      borderWidth: 3,
      hoverOffset: 10
    }]
  };
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
      labels: {
        padding: 20,
        font: { size: 13, weight: '600', family: 'Inter, system-ui, sans-serif' },
        usePointStyle: true,
        pointStyle: 'circle'
      }
    },
    tooltip: {
      backgroundColor: 'rgba(15, 23, 42, 0.95)',
      titleFont: { size: 14, weight: 'bold' },
      bodyFont: { size: 13 },
      padding: 12,
      borderWidth: 1,
      borderColor: 'rgba(148, 163, 184, 0.2)',
      callbacks: {
        label: function(context) {
          const bytes = context.parsed;
          return ' ' + formatFileSize(bytes);
        }
      }
    }
  }
};

const loadStats = async () => {
  try {
    const response = await axios.get('/api/storage/stats');
    stats.value = response.data;
  } catch (error) {
    console.error('Error loading stats:', error);
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
  loadStats();
});
</script>

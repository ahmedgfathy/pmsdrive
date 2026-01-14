<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
      <!-- Logo/Header -->
      <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-800">Shared File</h1>
        <p class="text-slate-500 text-sm mt-2">Download the file shared with you</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-8">
        <svg class="animate-spin h-10 w-10 text-blue-600 mx-auto" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-slate-600 mt-4">Loading file...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-8">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-800 mb-2">Access Denied</h3>
        <p class="text-slate-600 text-sm">{{ error }}</p>
      </div>

      <!-- File Info -->
      <div v-else-if="fileInfo && !needsPassword" class="space-y-6">
        <!-- File Icon -->
        <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl p-6 text-center">
          <svg class="w-16 h-16 text-blue-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <h3 class="text-lg font-semibold text-slate-800 truncate">{{ fileInfo.name }}</h3>
          <p class="text-sm text-slate-500 mt-1">{{ formatFileSize(fileInfo.size) }}</p>
        </div>

        <!-- Download Button -->
        <button
          @click="downloadFile"
          :disabled="downloading"
          class="w-full px-6 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg shadow-blue-500/30 font-medium disabled:opacity-50 flex items-center justify-center space-x-2"
        >
          <svg v-if="!downloading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
          </svg>
          <svg v-else class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>{{ downloading ? 'Downloading...' : 'Download File' }}</span>
        </button>

        <!-- Expiration Info -->
        <div v-if="fileInfo.expires_at" class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
          <p class="text-xs text-yellow-800 text-center">
            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            This link expires on {{ formatDate(fileInfo.expires_at) }}
          </p>
        </div>
      </div>

      <!-- Password Required -->
      <div v-else-if="needsPassword" class="space-y-4">
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-center">
          <svg class="w-12 h-12 text-yellow-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
          <p class="text-sm text-yellow-800">This file is password protected</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Enter Password</label>
          <input
            v-model="password"
            type="password"
            placeholder="Enter password"
            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
            @keyup.enter="verifyPassword"
          >
        </div>

        <button
          @click="verifyPassword"
          :disabled="!password || loading"
          class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg shadow-blue-500/30 font-medium disabled:opacity-50"
        >
          {{ loading ? 'Verifying...' : 'Verify Password' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const loading = ref(true);
const downloading = ref(false);
const error = ref('');
const fileInfo = ref(null);
const needsPassword = ref(false);
const password = ref('');

const loadFileInfo = async () => {
  loading.value = true;
  error.value = '';
  
  try {
    const response = await axios.get(`/api/share/${route.params.token}`);
    fileInfo.value = response.data.file;
    needsPassword.value = response.data.requires_password;
  } catch (err) {
    if (err.response?.status === 404) {
      error.value = 'File not found or link has expired';
    } else if (err.response?.status === 401) {
      needsPassword.value = true;
    } else {
      error.value = err.response?.data?.message || 'Failed to load file';
    }
  } finally {
    loading.value = false;
  }
};

const verifyPassword = async () => {
  loading.value = true;
  error.value = '';
  
  try {
    const response = await axios.get(`/api/share/${route.params.token}`, {
      params: { password: password.value }
    });
    fileInfo.value = response.data.file;
    needsPassword.value = false;
  } catch (err) {
    error.value = 'Incorrect password';
  } finally {
    loading.value = false;
  }
};

const downloadFile = async () => {
  downloading.value = true;
  
  try {
    const response = await axios.get(`/api/share/${route.params.token}`, {
      responseType: 'blob',
      params: password.value ? { password: password.value } : {}
    });
    
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', fileInfo.value.name);
    document.body.appendChild(link);
    link.click();
    link.remove();
  } catch (err) {
    error.value = 'Failed to download file';
  } finally {
    downloading.value = false;
  }
};

const formatFileSize = (bytes) => {
  if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + ' GB';
  if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
  if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
  return bytes + ' B';
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

onMounted(() => {
  loadFileInfo();
});
</script>

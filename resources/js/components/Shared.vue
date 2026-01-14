<template>
  <div class="h-full overflow-y-auto bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/50">
    <div class="max-w-7xl mx-auto p-6 space-y-6">
      
      <!-- Header Section -->
      <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6">
        <div class="flex items-center justify-between">
          <!-- Title -->
          <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-slate-800">Shared Files</h1>
              <p class="text-sm text-slate-500">Files shared with you by other users</p>
            </div>
          </div>

          <!-- Stats -->
          <div class="flex items-center space-x-6">
            <div class="text-center">
              <p class="text-2xl font-bold text-slate-800">{{ sharedFiles.length }}</p>
              <p class="text-xs text-slate-500">Shared Files</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="bg-red-50 border border-red-200 rounded-2xl p-6 shadow-lg">
        <div class="flex items-start space-x-3">
          <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <div>
            <h4 class="text-red-900 font-semibold">Error</h4>
            <p class="text-red-700 text-sm mt-1">{{ error }}</p>
          </div>
          <button @click="error = ''" class="ml-auto text-red-400 hover:text-red-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="text-center">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-orange-500 border-t-transparent"></div>
          <p class="mt-4 text-slate-600">Loading shared files...</p>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="sharedFiles.length === 0" class="bg-white rounded-2xl shadow-lg border-2 border-dashed border-slate-300 p-16 text-center">
        <div class="max-w-md mx-auto">
          <div class="w-24 h-24 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-slate-800 mb-2">No Shared Files Yet</h3>
          <p class="text-slate-500 mb-6">When other users share files with you, they will appear here</p>
        </div>
      </div>

      <!-- Shared Files List -->
      <div v-else class="space-y-3">
        <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wide px-2">Shared With You</h3>
        <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
          <div class="divide-y divide-slate-100">
            <div 
              v-for="file in sharedFiles" 
              :key="'shared-' + file.id"
              @click="selectFile(file)"
              :class="[
                'flex items-center px-6 py-4 hover:bg-slate-50 cursor-pointer group transition-colors',
                selectedFile?.id === file.id ? 'bg-orange-50 hover:bg-orange-100' : ''
              ]"
            >
              <!-- File Icon -->
              <div class="flex-shrink-0">
                <div :class="[
                  'w-12 h-12 rounded-xl flex items-center justify-center',
                  getFileIconBg(file.mime_type)
                ]">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                  </svg>
                </div>
              </div>

              <!-- File Info -->
              <div class="ml-4 flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-800 truncate group-hover:text-orange-600 transition-colors">
                  {{ file.original_name }}
                </p>
                <div class="flex items-center space-x-3 mt-1">
                  <span class="text-xs text-slate-500">{{ formatFileSize(file.size) }}</span>
                  <span class="text-xs text-slate-400">•</span>
                  <span class="text-xs text-slate-500">{{ getFileType(file.mime_type) }}</span>
                  <span class="text-xs text-slate-400">•</span>
                  <span class="text-xs text-slate-500">Shared by {{ file.shared_by?.name || 'Unknown' }}</span>
                </div>
              </div>

              <!-- Shared Date -->
              <div class="hidden md:block ml-4 flex-shrink-0">
                <p class="text-xs text-slate-500">{{ formatDate(file.shared_at) }}</p>
              </div>

              <!-- Action Buttons -->
              <div class="ml-4 flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button 
                  @click.stop="downloadFile(file)" 
                  class="p-2 text-slate-400 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all"
                  title="Download"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                  </svg>
                </button>
                <button 
                  @click.stop="viewDetails(file)" 
                  class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                  title="Details"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const sharedFiles = ref([]);
const loading = ref(false);
const error = ref('');
const selectedFile = ref(null);

const loadSharedFiles = async () => {
  loading.value = true;
  error.value = '';

  try {
    // Ensure token is set
    const token = localStorage.getItem('token');
    if (token) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }

    // TODO: Replace with actual shared files endpoint when backend is ready
    // For now, showing empty state
    const response = await axios.get('/api/files/shared');
    sharedFiles.value = response.data;
  } catch (err) {
    if (err.response?.status === 404) {
      // Endpoint not implemented yet, show empty state
      sharedFiles.value = [];
    } else {
      error.value = err.response?.data?.message || 'Failed to load shared files';
      console.error(err);
    }
  } finally {
    loading.value = false;
  }
};

const downloadFile = async (file) => {
  try {
    const response = await axios.get(`/api/files/${file.id}/download`, {
      responseType: 'blob'
    });

    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', file.original_name);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch (err) {
    error.value = 'Failed to download file';
    console.error(err);
  }
};

const viewDetails = (file) => {
  // TODO: Implement file details modal
  console.log('View details for:', file);
  alert(`File: ${file.original_name}\nShared by: ${file.shared_by?.name || 'Unknown'}\nSize: ${formatFileSize(file.size)}`);
};

const selectFile = (file) => {
  selectedFile.value = file;
};

const getFileIconBg = (mimeType) => {
  if (!mimeType) return 'bg-gradient-to-br from-slate-500 to-slate-600';
  
  if (mimeType.startsWith('image/')) return 'bg-gradient-to-br from-purple-500 to-purple-600';
  if (mimeType.startsWith('video/')) return 'bg-gradient-to-br from-pink-500 to-pink-600';
  if (mimeType.startsWith('audio/')) return 'bg-gradient-to-br from-orange-500 to-orange-600';
  if (mimeType.includes('pdf')) return 'bg-gradient-to-br from-red-500 to-red-600';
  if (mimeType.includes('word') || mimeType.includes('document')) return 'bg-gradient-to-br from-blue-500 to-blue-600';
  if (mimeType.includes('excel') || mimeType.includes('sheet')) return 'bg-gradient-to-br from-green-500 to-green-600';
  if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) return 'bg-gradient-to-br from-amber-500 to-amber-600';
  if (mimeType.includes('zip') || mimeType.includes('rar') || mimeType.includes('archive')) return 'bg-gradient-to-br from-yellow-500 to-yellow-600';
  
  return 'bg-gradient-to-br from-slate-500 to-slate-600';
};

const getFileType = (mimeType) => {
  if (!mimeType) return 'Unknown';
  
  if (mimeType.startsWith('image/')) return 'Image';
  if (mimeType.startsWith('video/')) return 'Video';
  if (mimeType.startsWith('audio/')) return 'Audio';
  if (mimeType.includes('pdf')) return 'PDF';
  if (mimeType.includes('word') || mimeType.includes('document')) return 'Document';
  if (mimeType.includes('excel') || mimeType.includes('sheet')) return 'Spreadsheet';
  if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) return 'Presentation';
  if (mimeType.includes('zip') || mimeType.includes('rar')) return 'Archive';
  if (mimeType.includes('text/')) return 'Text';
  
  return mimeType.split('/')[0] || 'File';
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
  const hours = Math.floor(diff / (1000 * 60 * 60));
  
  if (hours < 24) {
    if (hours < 1) return 'Just now';
    return `${hours}h ago`;
  }
  
  const days = Math.floor(hours / 24);
  if (days < 7) return `${days}d ago`;
  
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

onMounted(() => {
  setTimeout(() => {
    loadSharedFiles();
  }, 100);
});
</script>

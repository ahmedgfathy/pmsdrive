<template>
  <div class="h-full overflow-y-auto bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/50">
    <div class="max-w-7xl mx-auto p-6 space-y-6">

      <!-- Header & Actions -->
      <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <!-- Title -->
          <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-slate-800">My Drive</h1>
              <p class="text-sm text-slate-500">Manage your folders and files in one place</p>
            </div>
          </div>

          <!-- Stats & Actions -->
          <div class="flex flex-col gap-3 md:flex-row md:items-center md:gap-6">
            <div class="flex items-center justify-center gap-6">
              <div class="text-center">
                <p class="text-2xl font-bold text-slate-800">{{ folders.length }}</p>
                <p class="text-xs text-slate-500">Folders</p>
              </div>
              <div class="w-px h-10 bg-slate-200 hidden md:block"></div>
              <div class="text-center">
                <p class="text-2xl font-bold text-slate-800">{{ files.length }}</p>
                <p class="text-xs text-slate-500">Files</p>
              </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
              <button
                @click="showNewFolderDialog = true"
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition-all flex items-center gap-2 border border-slate-200"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Folder
              </button>
              <label class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg font-medium cursor-pointer transition-all hover:shadow-lg hover:shadow-blue-500/30 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Upload Files
                <input type="file" multiple @change="handleFileUpload" class="hidden">
              </label>
            </div>
          </div>
        </div>

        <!-- Breadcrumbs -->
        <div class="flex flex-wrap items-center gap-2 text-sm text-slate-600">
          <button @click="navigateToRoot" class="flex items-center gap-2 text-slate-700 hover:text-blue-600 font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l7-7m0 0l7 7m-7-7v18" />
            </svg>
            My Drive
          </button>
          <template v-for="crumb in breadcrumbs" :key="crumb.id">
            <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <button @click="navigateToFolder(crumb.id)" class="text-slate-600 hover:text-blue-600 transition truncate max-w-xs">
              {{ crumb.name }}
            </button>
          </template>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="bg-red-50 border border-red-200 rounded-2xl p-6 shadow-md">
        <div class="flex items-start gap-3">
          <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <h4 class="text-red-900 font-semibold">Something went wrong</h4>
            <p class="text-sm text-red-700 mt-1">{{ error }}</p>
          </div>
          <button @click="clearError" class="ml-auto text-red-400 hover:text-red-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Upload Progress -->
      <div v-if="uploading" class="bg-blue-50 border border-blue-200 rounded-2xl p-6 shadow-md">
        <div class="flex items-center gap-3">
          <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-blue-500 border-t-transparent"></div>
          <div>
            <p class="text-sm font-medium text-blue-800">Uploading files...</p>
            <p class="text-xs text-blue-600">Feel free to continue exploring your drive.</p>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="text-center">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
          <p class="mt-4 text-slate-600">Loading your drive...</p>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="folders.length === 0 && files.length === 0" class="bg-white rounded-2xl shadow-lg border-2 border-dashed border-slate-300 p-16 text-center">
        <div class="max-w-md mx-auto">
          <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-200 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-slate-800 mb-2">Your drive is empty</h3>
          <p class="text-slate-500 mb-6">Create a new folder or upload files to get started with PMS Drive.</p>
          <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <button
              @click="showNewFolderDialog = true"
              class="px-5 py-3 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 font-medium transition"
            >
              Create Folder
            </button>
            <label class="px-5 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg font-medium cursor-pointer transition hover:shadow-lg hover:shadow-blue-500/30">
              Upload Files
              <input type="file" multiple @change="handleFileUpload" class="hidden">
            </label>
          </div>
        </div>
      </div>

      <!-- Files and Folders List -->
      <div v-else class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
        <div class="divide-y divide-slate-100">
          <!-- Folders -->
          <div
            v-for="folder in folders"
            :key="'folder-' + folder.id"
            @click="navigateToFolder(folder.id)"
            @contextmenu.prevent="showContextMenu($event, folder, 'folder')"
            class="flex items-center px-6 py-4 hover:bg-slate-50 cursor-pointer group transition-colors"
          >
            <div class="flex-shrink-0">
              <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center shadow-md shadow-blue-400/20">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
              </div>
            </div>
            <div class="ml-4 flex-1 min-w-0">
              <p class="text-sm font-semibold text-slate-800 truncate group-hover:text-blue-600 transition-colors">{{ folder.name }}</p>
              <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-slate-500">
                <span>{{ folder.files_count }} files</span>
                <span class="hidden sm:block">•</span>
                <span>{{ folder.children_count }} folders</span>
              </div>
            </div>
            <div class="ml-4 hidden md:block text-xs text-slate-500">
              {{ formatDate(folder.created_at) }}
            </div>
            <button
              @click.stop="showContextMenu($event, folder, 'folder')"
              class="ml-4 p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-opacity opacity-0 group-hover:opacity-100"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
              </svg>
            </button>
          </div>

          <!-- Files -->
          <div
            v-for="file in files"
            :key="'file-' + file.id"
            @click="selectFile(file)"
            @contextmenu.prevent="showContextMenu($event, file, 'file')"
            :class="[
              'flex items-center px-6 py-4 hover:bg-slate-50 cursor-pointer group transition-colors',
              selectedFile?.id === file.id ? 'bg-blue-50/70 ring-1 ring-blue-200' : ''
            ]"
          >
            <div class="flex-shrink-0">
              <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-slate-500 to-slate-600 flex items-center justify-center shadow-md shadow-slate-400/20">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
            </div>
            <div class="ml-4 flex-1 min-w-0">
              <p class="text-sm font-semibold text-slate-800 truncate group-hover:text-blue-600 transition-colors">{{ file.original_name }}</p>
              <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-slate-500">
                <span>{{ formatFileSize(file.size) }}</span>
                <span class="hidden sm:block">•</span>
                <span>{{ file.mime_type }}</span>
              </div>
            </div>
            <div class="ml-4 hidden md:block text-xs text-slate-500">
              {{ formatDate(file.created_at) }}
            </div>
            <div class="ml-4 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
              <button
                @click.stop="openShareModal(file)"
                class="p-2 text-orange-500 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition"
                title="Share"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
              </button>
              <button
                @click.stop="downloadFile(file)"
                class="p-2 text-blue-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                title="Download"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
              </button>
              <button
                @click.stop="deleteFile(file)"
                class="p-2 text-red-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                title="Delete"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- New Folder Dialog -->
      <div v-if="showNewFolderDialog" class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md">
          <h3 class="text-lg font-semibold text-slate-800 mb-4">Create New Folder</h3>
          <input
            v-model="newFolderName"
            type="text"
            placeholder="Folder name"
            class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
          >
          <div class="mt-6 flex justify-end gap-3">
            <button @click="showNewFolderDialog = false" class="px-4 py-2 text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition">Cancel</button>
            <button @click="createFolder" class="px-5 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg font-medium hover:shadow-lg hover:shadow-blue-500/30 transition">Create</button>
          </div>
        </div>
      </div>

      <!-- Share Modal -->
      <ShareModal
        v-if="showShareModal && fileToShare"
        :file="fileToShare"
        @close="closeShareModal"
        @success="handleShareSuccess"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import ShareModal from './ShareModal.vue';

const currentFolderId = ref(null);
const breadcrumbs = ref([]);
const folders = ref([]);
const files = ref([]);
const showShareModal = ref(false);
const fileToShare = ref(null);
const loading = ref(false);
const uploading = ref(false);
const error = ref('');
const showNewFolderDialog = ref(false);
const newFolderName = ref('');
const selectedFile = ref(null);

const loadContents = async () => {
  loading.value = true;
  error.value = '';
  
  try {
    const [foldersRes, filesRes] = await Promise.all([
      axios.get('/api/folders', { params: { parent_id: currentFolderId.value } }),
      axios.get('/api/files', { params: { folder_id: currentFolderId.value } })
    ]);
    
    folders.value = foldersRes.data;
    files.value = filesRes.data;
  } catch (err) {
    error.value = 'Failed to load contents';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const navigateToRoot = () => {
  currentFolderId.value = null;
  breadcrumbs.value = [];
  loadContents();
};

const navigateToFolder = async (folderId) => {
  currentFolderId.value = folderId;
  
  try {
    const response = await axios.get(`/api/folders/${folderId}/breadcrumb`);
    breadcrumbs.value = response.data;
  } catch (err) {
    console.error('Failed to load breadcrumb:', err);
  }
  
  loadContents();
};

const createFolder = async () => {
  if (!newFolderName.value.trim()) return;
  
  try {
    await axios.post('/api/folders', {
      name: newFolderName.value,
      parent_id: currentFolderId.value
    });
    
    showNewFolderDialog.value = false;
    newFolderName.value = '';
    loadContents();
  } catch (err) {
    error.value = 'Failed to create folder';
    console.error(err);
  }
};

const handleFileUpload = async (event) => {
  const filesToUpload = Array.from(event.target.files);
  if (filesToUpload.length === 0) return;
  
  uploading.value = true;
  error.value = '';
  
  const formData = new FormData();
  filesToUpload.forEach(file => {
    formData.append('files[]', file);
  });
  
  if (currentFolderId.value) {
    formData.append('folder_id', currentFolderId.value);
  }
  
  try {
    await axios.post('/api/files/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    
    event.target.value = ''; // Reset input
    loadContents();
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to upload files';
    console.error(err);
  } finally {
    uploading.value = false;
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
  } catch (err) {
    error.value = 'Failed to download file';
    console.error(err);
  }
};

const deleteFile = async (file) => {
  if (!confirm(`Delete "${file.original_name}"?`)) return;
  
  try {
    await axios.delete(`/api/files/${file.id}`);
    loadContents();
  } catch (err) {
    error.value = 'Failed to delete file';
    console.error(err);
  }
};

const clearError = () => {
  error.value = '';
};

const openShareModal = (file) => {
  fileToShare.value = file;
  showShareModal.value = true;
};

const closeShareModal = () => {
  showShareModal.value = false;
  fileToShare.value = null;
};

const handleShareSuccess = () => {
  // Optionally reload files to update share status
  loadContents();
};

const selectFile = (file) => {
  selectedFile.value = file;
};

const showContextMenu = (event, item, type) => {
  // Context menu implementation can be added here
  console.log('Context menu for', type, item);
};

const formatFileSize = (bytes) => {
  if (bytes >= 1073741824) {
    return (bytes / 1073741824).toFixed(2) + ' GB';
  } else if (bytes >= 1048576) {
    return (bytes / 1048576).toFixed(2) + ' MB';
  } else if (bytes >= 1024) {
    return (bytes / 1024).toFixed(2) + ' KB';
  }
  return bytes + ' B';
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

onMounted(() => {
  loadContents();
});
</script>

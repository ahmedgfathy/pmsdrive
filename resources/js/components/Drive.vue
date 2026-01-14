<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Top Bar -->
    <div class="bg-white border-b border-gray-200 px-4 py-3">
      <div class="flex items-center justify-between">
        <!-- Breadcrumb Navigation -->
        <div class="flex items-center space-x-2 flex-1">
          <button @click="navigateToRoot" class="text-blue-600 hover:text-blue-800 font-medium">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
          </button>
          <span v-for="(crumb, index) in breadcrumbs" :key="crumb.id" class="flex items-center">
            <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <button @click="navigateToFolder(crumb.id)" class="text-sm text-gray-700 hover:text-blue-600">
              {{ crumb.name }}
            </button>
          </span>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-2">
          <button @click="showNewFolderDialog = true" class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
            <svg class="h-4 w-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
            </svg>
            New Folder
          </button>
          <label class="px-3 py-2 bg-blue-600 text-white rounded-md text-sm font-medium cursor-pointer hover:bg-blue-700">
            <svg class="h-4 w-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            Upload Files
            <input type="file" multiple @change="handleFileUpload" class="hidden">
          </label>
        </div>
      </div>
    </div>

    <!-- File Explorer Area -->
    <div class="p-4">
      <!-- Upload Progress -->
      <div v-if="uploading" class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center">
          <svg class="animate-spin h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span class="text-sm text-blue-800">Uploading files...</span>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
        <p class="text-sm text-red-800">{{ error }}</p>
      </div>

      <!-- Empty State -->
      <div v-if="folders.length === 0 && files.length === 0 && !loading" class="bg-white rounded-lg shadow-sm border-2 border-dashed border-gray-300 p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No files or folders</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by creating a new folder or uploading files.</p>
      </div>

      <!-- Files and Folders Grid -->
      <div v-else class="bg-white rounded-lg shadow-sm">
        <div class="grid grid-cols-1 divide-y divide-gray-200">
          <!-- Folders -->
          <div v-for="folder in folders" :key="'folder-' + folder.id" 
               @click="navigateToFolder(folder.id)"
               @contextmenu.prevent="showContextMenu($event, folder, 'folder')"
               class="flex items-center px-4 py-3 hover:bg-gray-50 cursor-pointer group">
            <div class="flex-shrink-0">
              <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
              </svg>
            </div>
            <div class="ml-4 flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 truncate">{{ folder.name }}</p>
              <p class="text-xs text-gray-500">{{ folder.files_count }} files, {{ folder.children_count }} folders</p>
            </div>
            <div class="ml-4 flex-shrink-0 text-xs text-gray-500">
              {{ formatDate(folder.created_at) }}
            </div>
            <button @click.stop="showContextMenu($event, folder, 'folder')" class="ml-2 opacity-0 group-hover:opacity-100">
              <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
              </svg>
            </button>
          </div>

          <!-- Files -->
          <div v-for="file in files" :key="'file-' + file.id"
               @click="selectFile(file)"
               @contextmenu.prevent="showContextMenu($event, file, 'file')"
               :class="['flex items-center px-4 py-3 hover:bg-gray-50 cursor-pointer group', selectedFile?.id === file.id ? 'bg-blue-50' : '']">
            <div class="flex-shrink-0">
              <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <div class="ml-4 flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 truncate">{{ file.original_name }}</p>
              <p class="text-xs text-gray-500">{{ formatFileSize(file.size) }} • {{ file.mime_type }}</p>
            </div>
            <div class="ml-4 flex-shrink-0 text-xs text-gray-500">
              {{ formatDate(file.created_at) }}
            </div>
            <button @click.stop="downloadFile(file)" class="ml-2 opacity-0 group-hover:opacity-100" title="Download">
              <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
            </button>
            <button @click.stop="deleteFile(file)" class="ml-2 opacity-0 group-hover:opacity-100" title="Delete">
              <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- New Folder Dialog -->
    <div v-if="showNewFolderDialog" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-96">
        <h3 class="text-lg font-semibold mb-4">Create New Folder</h3>
        <input v-model="newFolderName" type="text" placeholder="Folder name" 
               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
        <div class="mt-4 flex justify-end space-x-2">
          <button @click="showNewFolderDialog = false" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Cancel</button>
          <button @click="createFolder" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Create</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const currentFolderId = ref(null);
const breadcrumbs = ref([]);
const folders = ref([]);
const files = ref([]);
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

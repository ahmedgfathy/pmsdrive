<template>
  <div class="min-h-screen">
    <!-- Header Section -->
    <div class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Admin Panel</h1>
            <p class="text-gray-600 mt-1">Manage users and system settings</p>
          </div>
          <button @click="$emit('back-to-dashboard')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition">
            ← Back to Dashboard
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Tabs -->
      <div class="mb-6">
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex space-x-8">
            <button @click="activeTab = 'pending'" :class="[activeTab === 'pending' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300','py-4 px-1 border-b-2 font-medium text-sm flex items-center space-x-2']">
              <span>Pending Approvals</span>
              <span v-if="pendingUsers.length" class="ml-2 bg-blue-100 text-blue-600 py-0.5 px-2.5 rounded-full text-xs font-semibold">{{ pendingUsers.length }}</span>
            </button>
            <button @click="activeTab = 'all'" :class="[activeTab === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300','py-4 px-1 border-b-2 font-medium text-sm']">
              All Users
            </button>
          </nav>
        </div>
      </div>

      <!-- Pending Users Tab -->
      <div v-if="activeTab === 'pending'" class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
          <h2 class="text-lg font-semibold text-gray-900">Users Pending Approval</h2>
        </div>
        <div v-if="loading" class="p-12 text-center">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
          <p class="mt-2 text-gray-500">Loading...</p>
        </div>
        <div v-else-if="pendingUsers.length === 0" class="p-12 text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <p class="mt-2 text-gray-500 font-medium">No pending approvals</p>
          <p class="text-sm text-gray-400">All users have been approved</p>
        </div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in pendingUsers" :key="user.id" class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-gray-400 to-gray-500 rounded-full flex items-center justify-center text-white font-bold">
                      {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                  <button @click="approveUser(user.id)" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium shadow-sm hover:shadow-md transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Approve
                  </button>
                  <button @click="showRejectModal(user)" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium shadow-sm hover:shadow-md transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reject
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- All Users Tab -->
      <div v-if="activeTab === 'all'" class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-white">
          <h2 class="text-lg font-semibold text-gray-900">All System Users</h2>
        </div>
        <div v-if="loading" class="p-12 text-center">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-purple-500 border-t-transparent"></div>
          <p class="mt-2 text-gray-500">Loading...</p>
        </div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in allUsers" :key="user.id" class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div :class="[user.is_admin ? 'from-purple-500 to-blue-500' : 'from-gray-400 to-gray-500','w-10 h-10 bg-gradient-to-br rounded-full flex items-center justify-center text-white font-bold']">
                      {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[user.approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800','px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full']">
                    {{ user.approved ? '✓ Approved' : '⏳ Pending' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[user.is_admin ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800','px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full']">
                    {{ user.is_admin ? '👑 Admin' : '👤 User' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Reject Confirmation Modal -->
    <ConfirmModal
      v-if="showingRejectModal"
      title="Reject User Registration"
      :message="`Are you sure you want to reject ${userToReject?.name}? This will permanently delete their account.`"
      confirm-text="Yes, Reject"
      cancel-text="Cancel"
      type="danger"
      @confirm="confirmReject"
      @cancel="showingRejectModal = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import ConfirmModal from './ConfirmModal.vue';
import { useToast } from '../composables/useToast';

const { success, error } = useToast();

defineEmits(['back-to-dashboard', 'logout']);

const activeTab = ref('pending');
const pendingUsers = ref([]);
const allUsers = ref([]);
const loading = ref(false);
const showingRejectModal = ref(false);
const userToReject = ref(null);

const loadPendingUsers = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/admin/users/pending');
    pendingUsers.value = response.data;
  } catch (err) {
    console.error('Error loading pending users:', err);
    error('Failed to load pending users');
  } finally {
    loading.value = false;
  }
};

const loadAllUsers = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/admin/users');
    allUsers.value = response.data;
  } catch (err) {
    console.error('Error loading all users:', err);
    error('Failed to load users');
  } finally {
    loading.value = false;
  }
};

const approveUser = async (userId) => {
  try {
    await axios.post(`/api/admin/users/${userId}/approve`);
    success('User approved successfully!');
    await loadPendingUsers();
    if (activeTab.value === 'all') await loadAllUsers();
  } catch (err) {
    error(err.response?.data?.message || 'Failed to approve user');
  }
};

const showRejectModal = (user) => {
  userToReject.value = user;
  showingRejectModal.value = true;
};

const confirmReject = async () => {
  if (!userToReject.value) return;
  
  try {
    await axios.delete(`/api/admin/users/${userToReject.value.id}/reject`);
    success('User rejected and removed successfully');
    showingRejectModal.value = false;
    userToReject.value = null;
    await loadPendingUsers();
    if (activeTab.value === 'all') await loadAllUsers();
  } catch (err) {
    error(err.response?.data?.message || 'Failed to reject user');
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

onMounted(() => {
  loadPendingUsers();
  loadAllUsers();
});
</script>

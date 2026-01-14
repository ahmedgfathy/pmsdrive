<template>
  <div class="min-h-screen flex flex-col">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center space-x-4">
            <img src="/assets/images/logo.svg" alt="PMS Logo" class="h-10" />
            <h1 class="text-2xl font-bold text-blue-600">PMS Drive - Admin Panel</h1>
          </div>
          <div class="flex items-center space-x-8">
            <button @click="$emit('back-to-dashboard')" class="text-gray-700 hover:text-blue-600 font-medium">
              Dashboard
            </button>
            <button @click="$emit('logout')" class="text-red-600 hover:text-red-800 font-medium">
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Tabs -->
      <div class="mb-6">
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex space-x-8">
            <button
              @click="activeTab = 'pending'"
              :class="[
                activeTab === 'pending'
                  ? 'border-blue-500 text-blue-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
              ]"
            >
              Pending Approvals
              <span v-if="pendingUsers.length" class="ml-2 bg-blue-100 text-blue-600 py-0.5 px-2.5 rounded-full text-xs">
                {{ pendingUsers.length }}
              </span>
            </button>
            <button
              @click="activeTab = 'all'"
              :class="[
                activeTab === 'all'
                  ? 'border-blue-500 text-blue-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
              ]"
            >
              All Users
            </button>
          </nav>
        </div>
      </div>

      <!-- Pending Users Table -->
      <div v-if="activeTab === 'pending'" class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-900">Users Pending Approval</h2>
        </div>
        <div v-if="loading" class="p-6 text-center text-gray-500">
          Loading...
        </div>
        <div v-else-if="pendingUsers.length === 0" class="p-6 text-center text-gray-500">
          No pending approvals
        </div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in pendingUsers" :key="user.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                  <button
                    @click="approveUser(user.id)"
                    class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 px-3 py-1 rounded transition"
                  >
                    Approve
                  </button>
                  <button
                    @click="rejectUser(user.id)"
                    class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded transition"
                  >
                    Reject
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- All Users Table -->
      <div v-if="activeTab === 'all'" class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-900">All Users</h2>
        </div>
        <div v-if="loading" class="p-6 text-center text-gray-500">
          Loading...
        </div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in allUsers" :key="user.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    user.approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800',
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
                  ]">
                    {{ user.approved ? 'Approved' : 'Pending' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    user.is_admin ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800',
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
                  ]">
                    {{ user.is_admin ? 'Admin' : 'User' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Footer from './Footer.vue';

const emit = defineEmits(['back-to-dashboard', 'logout']);

const activeTab = ref('pending');
const pendingUsers = ref([]);
const allUsers = ref([]);
const loading = ref(false);

const loadPendingUsers = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/admin/users/pending');
    pendingUsers.value = response.data;
  } catch (err) {
    console.error('Error loading pending users:', err);
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
  } finally {
    loading.value = false;
  }
};

const approveUser = async (userId) => {
  try {
    await axios.post(`/api/admin/users/${userId}/approve`);
    await loadPendingUsers();
    if (activeTab.value === 'all') await loadAllUsers();
  } catch (err) {
    alert('Error approving user: ' + (err.response?.data?.message || err.message));
  }
};

const rejectUser = async (userId) => {
  if (!confirm('Are you sure you want to reject and delete this user?')) return;
  
  try {
    await axios.delete(`/api/admin/users/${userId}/reject`);
    await loadPendingUsers();
    if (activeTab.value === 'all') await loadAllUsers();
  } catch (err) {
    alert('Error rejecting user: ' + (err.response?.data?.message || err.message));
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString();
};

onMounted(() => {
  loadPendingUsers();
  loadAllUsers();
});
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- Toast Container -->
    <div class="fixed top-4 right-4 z-50 space-y-2">
      <Toast
        v-for="toast in toasts"
        :key="toast.id"
        :message="toast.message"
        :type="toast.type"
        @close="removeToast(toast.id)"
      />
    </div>

    <!-- Not Authenticated Views -->
    <div v-if="!isAuthenticated">
      <Login v-if="currentView === 'login'" @login="handleLogin" @show-register="currentView = 'register'" />
      <Register v-else @show-login="currentView = 'login'" />
    </div>

    <!-- Authenticated Views -->
    <div v-else-if="user" class="flex flex-col h-screen">
      <!-- Navigation with Premium Design -->
      <nav class="bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 shadow-2xl border-b border-blue-800/30">
        <div class="max-w-7xl mx-auto px-6">
          <div class="flex items-center justify-between h-16">
            <!-- Logo & Brand -->
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/50">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                </svg>
              </div>
              <div>
                <h1 class="text-xl font-bold text-white tracking-tight">PMSDrive</h1>
                <p class="text-xs text-blue-300">Cloud Storage</p>
              </div>
            </div>

            <!-- Navigation Tabs with Pill Style -->
            <div class="flex items-center space-x-2 bg-slate-800/50 rounded-full p-1.5 backdrop-blur-sm border border-slate-700/50">
              <button
                v-for="tab in tabs"
                :key="tab.value"
                @click="changeTab(tab.value)"
                :class="[
                  'px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 flex items-center space-x-2',
                  activeTab === tab.value
                    ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/50 scale-105'
                    : 'text-slate-300 hover:text-white hover:bg-slate-700/50'
                ]"
              >
                <component :is="tab.icon" class="w-4 h-4" />
                <span>{{ tab.label }}</span>
              </button>
            </div>

            <!-- User Menu -->
            <div class="flex items-center space-x-4">
              <!-- Admin Button -->
              <button
                v-if="user.is_admin"
                @click="changeTab('admin')"
                :class="[
                  'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 flex items-center space-x-2',
                  activeTab === 'admin'
                    ? 'bg-gradient-to-r from-purple-500 to-pink-600 text-white shadow-lg shadow-purple-500/50'
                    : 'bg-purple-500/20 text-purple-300 hover:bg-purple-500/30 border border-purple-500/30'
                ]"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
                <span>Admin Panel</span>
              </button>

              <!-- User Info -->
              <div class="flex items-center space-x-3 px-4 py-2 bg-slate-800/50 rounded-full border border-slate-700/50 backdrop-blur-sm">
                <div class="w-9 h-9 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center text-white font-semibold shadow-lg shadow-blue-500/30">
                  {{ user.name.charAt(0).toUpperCase() }}
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-white">{{ user.name }}</p>
                  <p class="text-xs text-slate-400">{{ user.email }}</p>
                </div>
              </div>

              <!-- Logout Button -->
              <button
                @click="handleLogout"
                class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 hover:text-red-200 rounded-lg transition-all duration-300 flex items-center space-x-2 border border-red-500/30"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="font-medium">Logout</span>
              </button>
            </div>
          </div>
        </div>
      </nav>

      <!-- Main Content Area -->
      <div class="flex-1 overflow-hidden">
        <component :is="currentComponent" />
      </div>
    </div>

    <!-- ConfirmModal -->
    <ConfirmModal
      v-if="confirmModalVisible"
      :message="confirmMessage"
      @confirm="confirmCallback"
      @cancel="confirmModalVisible = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import Login from './components/Login.vue';
import Register from './components/Register.vue';
import UserDashboard from './components/UserDashboard.vue';
import Drive from './components/Drive.vue';
import Shared from './components/Shared.vue';
import AdminPanel from './components/AdminPanel.vue';
import Toast from './components/Toast.vue';
import ConfirmModal from './components/ConfirmModal.vue';
import { useToast } from './composables/useToast';

const { toasts, success, error, removeToast } = useToast();

const user = ref(null);
const isAuthenticated = ref(false);
const currentView = ref('login');
const activeTab = ref('dashboard');

// Watch activeTab and save to localStorage
watch(activeTab, (newTab) => {
  localStorage.setItem('activeTab', newTab);
});

const changeTab = (tabValue) => {
  activeTab.value = tabValue;
};

const handleLogin = async (userData) => {
  try {
    // CRITICAL: Get CSRF cookie BEFORE login request
    await axios.get('/sanctum/csrf-cookie');
    
    const response = await axios.post('/api/login', userData);
    localStorage.setItem('token', response.data.token);
    localStorage.setItem('user', JSON.stringify(response.data.user));
    axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;

    user.value = response.data.user;
    isAuthenticated.value = true;
    currentView.value = 'dashboard';

    success(`Welcome back, ${response.data.user.name}!`);
  } catch (err) {
    error(err.response?.data?.message || 'Login failed. Please check your credentials.');
  }
};

const handleLogout = async () => {
  try {
    await axios.post('/api/logout');
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    localStorage.removeItem('activeTab'); // Clear saved tab
    delete axios.defaults.headers.common['Authorization'];

    user.value = null;
    isAuthenticated.value = false;
    currentView.value = 'login';
    activeTab.value = 'dashboard';

    success('Logged out successfully');
  } catch (err) {
    error('Logout failed. Please try again.');
  }
};

const tabs = [
  {
    label: 'Dashboard',
    value: 'dashboard',
    icon: {
      template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>'
    }
  },
  {
    label: 'Drive',
    value: 'drive',
    icon: {
      template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>'
    }
  },
  {
    label: 'Shared',
    value: 'shared',
    icon: {
      template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>'
    }
  }
];

const currentComponent = computed(() => {
  switch (activeTab.value) {
    case 'dashboard': return UserDashboard;
    case 'drive': return Drive;
    case 'shared': return Shared;
    case 'admin': return AdminPanel;
    default: return UserDashboard;
  }
});

const confirmModalVisible = ref(false);
const confirmMessage = ref('');
const confirmCallback = ref(null);

onMounted(async () => {
  const token = localStorage.getItem('token');
  const storedUser = localStorage.getItem('user');

  if (token && storedUser) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    try {
      const response = await axios.get('/api/me');
      user.value = response.data;
      isAuthenticated.value = true;
      currentView.value = 'dashboard';
      
      // Restore last active tab
      const savedTab = localStorage.getItem('activeTab');
      if (savedTab && ['dashboard', 'drive', 'shared', 'admin'].includes(savedTab)) {
        activeTab.value = savedTab;
      }
    } catch (err) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      localStorage.removeItem('activeTab');
      delete axios.defaults.headers.common['Authorization'];
      currentView.value = 'login';
    }
  }
});
</script>

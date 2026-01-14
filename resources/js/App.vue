<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Toast Notifications -->
    <div class="fixed top-6 right-6 z-50 space-y-3">
      <Toast
        v-for="toast in toasts"
        :key="toast.id"
        :type="toast.type"
        :title="toast.title"
        :message="toast.message"
        :duration="toast.duration"
        @close="removeToast(toast.id)"
      />
    </div>

    <!-- Login/Register -->
    <template v-if="!isAuthenticated">
      <Login v-if="currentView === 'login'" @login="handleLogin" @show-register="currentView = 'register'" />
      <Register v-else @show-login="currentView = 'login'" />
    </template>

    <!-- Main App -->
    <template v-else>
      <!-- Premium Navigation -->
      <nav class="bg-white border-b border-slate-200/60 backdrop-blur-xl sticky top-0 z-40">
        <div class="max-w-screen-2xl mx-auto px-6 lg:px-8">
          <div class="flex items-center justify-between h-20">
            
            <!-- Logo & Brand -->
            <div class="flex items-center space-x-4">
              <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-2xl blur opacity-25 group-hover:opacity-40 transition"></div>
                <div class="relative w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/25">
                  <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                  </svg>
                </div>
              </div>
              <div>
                <h1 class="text-2xl font-bold bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 bg-clip-text text-transparent">
                  PMS<span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Drive</span>
                </h1>
                <p class="text-xs font-medium text-slate-500 tracking-wide">Enterprise File System</p>
              </div>
            </div>

            <!-- Navigation Pills -->
            <div class="hidden lg:flex items-center space-x-2 bg-slate-100/70 backdrop-blur-sm rounded-2xl p-1.5 border border-slate-200/50">
              <button @click="currentView = 'dashboard'" :class="[
                'group relative px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center space-x-2.5',
                currentView === 'dashboard' 
                  ? 'bg-white text-blue-600 shadow-lg shadow-blue-500/10' 
                  : 'text-slate-600 hover:text-slate-900 hover:bg-white/50'
              ]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 13a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1v-7z"/>
                </svg>
                <span>Dashboard</span>
              </button>
              
              <button @click="currentView = 'drive'" :class="[
                'group relative px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center space-x-2.5',
                currentView === 'drive' 
                  ? 'bg-white text-blue-600 shadow-lg shadow-blue-500/10' 
                  : 'text-slate-600 hover:text-slate-900 hover:bg-white/50'
              ]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
                <span>My Drive</span>
              </button>
              
              <button @click="currentView = 'shared'" :class="[
                'group relative px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center space-x-2.5',
                currentView === 'shared' 
                  ? 'bg-white text-blue-600 shadow-lg shadow-blue-500/10' 
                  : 'text-slate-600 hover:text-slate-900 hover:bg-white/50'
              ]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
                <span>Shared</span>
              </button>
            </div>

            <!-- User Menu -->
            <div class="flex items-center space-x-4">
              <div class="hidden md:flex items-center space-x-3 px-4 py-2.5 bg-slate-50 rounded-xl border border-slate-200/60">
                <div class="text-right">
                  <p class="text-sm font-semibold text-slate-900">{{ user.name }}</p>
                  <p class="text-xs text-slate-500 font-medium">{{ user.is_admin ? 'Administrator' : 'Team Member' }}</p>
                </div>
                <div class="relative group">
                  <div class="absolute -inset-0.5 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl blur opacity-40 group-hover:opacity-60 transition"></div>
                  <div class="relative w-11 h-11 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center text-white font-bold text-base shadow-lg">
                    {{ user.name.charAt(0).toUpperCase() }}
                  </div>
                </div>
              </div>
              
              <button v-if="user.is_admin" @click="currentView = 'admin'" class="relative group px-5 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white rounded-xl text-sm font-semibold shadow-lg shadow-purple-500/25 hover:shadow-xl hover:shadow-purple-500/30 transition-all duration-200 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Admin Panel</span>
              </button>
              
              <button @click="handleLogout" class="p-2.5 text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </nav>

      <!-- Content Area -->
      <main class="min-h-[calc(100vh-5rem)]">
        <UserDashboard v-if="currentView === 'dashboard'" @navigate="handleNavigate" />
        <Drive v-else-if="currentView === 'drive'" />
        <Shared v-else-if="currentView === 'shared'" @navigate="handleNavigate" />
        <AdminPanel v-else-if="currentView === 'admin'" @back-to-dashboard="currentView = 'dashboard'" @logout="handleLogout" />
      </main>

      <Footer />
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Login from './components/Login.vue';
import Register from './components/Register.vue';
import UserDashboard from './components/UserDashboard.vue';
import Drive from './components/Drive.vue';
import Shared from './components/Shared.vue';
import AdminPanel from './components/AdminPanel.vue';
import Footer from './components/Footer.vue';
import Toast from './components/Toast.vue';
import { useToast } from './composables/useToast';

const { toasts, removeToast, success, error } = useToast();

const currentView = ref('login');
const isAuthenticated = ref(false);
const user = ref(null);

const handleLogin = async (userData) => {
  try {
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
  } catch (err) {
    console.error('Logout error:', err);
  } finally {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    delete axios.defaults.headers.common['Authorization'];
    isAuthenticated.value = false;
    user.value = null;
    currentView.value = 'login';
    success('Logged out successfully');
  }
};

const handleNavigate = (view) => {
  currentView.value = view;
};

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
    } catch (err) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      delete axios.defaults.headers.common['Authorization'];
    }
  }
});
</script>

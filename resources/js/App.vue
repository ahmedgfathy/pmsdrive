<template>
  <div class="min-h-screen flex flex-col">
    <!-- Login Page -->
    <Login 
      v-if="currentView === 'login'" 
      @login="handleLogin" 
      @show-register="currentView = 'register'"
    />
    
    <!-- Register Page -->
    <Register 
      v-else-if="currentView === 'register'" 
      @show-login="currentView = 'login'"
    />

    <!-- Main Application (Authenticated) -->
    <template v-else>
      <!-- Top Navigation Bar -->
      <nav class="bg-white shadow-md">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <!-- Logo and Brand -->
            <div class="flex items-center space-x-4">
              <img src="/assets/images/logo.svg" alt="PMS Logo" class="h-10" />
              <h1 class="text-2xl font-bold text-blue-600">PMS Drive</h1>
            </div>
            
            <!-- Main Navigation Links -->
            <div class="flex items-center space-x-1">
              <button 
                @click="currentView = 'dashboard'" 
                :class="['px-4 py-2 rounded-md font-medium transition', currentView === 'dashboard' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100']"
              >
                <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
              </button>
              
              <button 
                @click="currentView = 'drive'" 
                :class="['px-4 py-2 rounded-md font-medium transition', currentView === 'drive' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100']"
              >
                <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
                Drive
              </button>
              
              <button 
                @click="currentView = 'shared'" 
                :class="['px-4 py-2 rounded-md font-medium transition', currentView === 'shared' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100']"
              >
                <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
                Shared
              </button>
            </div>

            <!-- User Menu -->
            <div class="flex items-center space-x-4">
              <span class="text-sm text-gray-600">{{ user?.name }}</span>
              <button v-if="user?.is_admin" @click="currentView = 'admin'" class="px-3 py-2 bg-purple-600 text-white rounded-md text-sm font-medium hover:bg-purple-700">
                Admin Panel
              </button>
              <button @click="handleLogout" class="px-3 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700">
                Logout
              </button>
            </div>
          </div>
        </div>
      </nav>

      <!-- Main Content Area -->
      <main class="flex-1">
        <!-- Dashboard View -->
        <UserDashboard 
          v-if="currentView === 'dashboard'" 
          :user="user"
          @navigate="handleNavigate"
        />
        
        <!-- Drive View -->
        <Drive v-else-if="currentView === 'drive'" />
        
        <!-- Shared View -->
        <Shared 
          v-else-if="currentView === 'shared'" 
          @navigate="handleNavigate"
        />
        
        <!-- Admin Panel -->
        <AdminPanel
          v-else-if="currentView === 'admin'"
          @back-to-dashboard="currentView = 'dashboard'"
          @logout="handleLogout"
        />
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
import AdminPanel from './components/AdminPanel.vue';
import UserDashboard from './components/UserDashboard.vue';
import Drive from './components/Drive.vue';
import Shared from './components/Shared.vue';
import Footer from './components/Footer.vue';

const currentView = ref('login');
const user = ref(null);

const handleLogin = (userData) => {
  user.value = userData;
  currentView.value = 'dashboard';
};

const handleNavigate = (view) => {
  currentView.value = view;
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
    user.value = null;
    currentView.value = 'login';
  }
};

// Check if user is already logged in
onMounted(async () => {
  const token = localStorage.getItem('token');
  const savedUser = localStorage.getItem('user');
  
  if (token && savedUser) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    try {
      const response = await axios.get('/api/me');
      user.value = response.data;
      currentView.value = 'dashboard';
    } catch (err) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      delete axios.defaults.headers.common['Authorization'];
    }
  }
});
</script>

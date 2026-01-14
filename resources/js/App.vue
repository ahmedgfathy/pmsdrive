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

    <!-- Admin Panel -->
    <AdminPanel
      v-else-if="currentView === 'admin'"
      @back-to-dashboard="currentView = 'dashboard'"
      @logout="handleLogout"
    />
    
    <!-- Dashboard (Authenticated) -->
    <template v-else-if="currentView === 'dashboard'">
      <!-- Navigation Bar -->
      <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex items-center space-x-4">
              <img src="/assets/images/logo.svg" alt="PMS Logo" class="h-10" />
              <h1 class="text-2xl font-bold text-blue-600">PMS Drive</h1>
            </div>
            <div class="flex items-center space-x-8">
              <span class="text-gray-600 text-sm">Welcome, {{ user?.name }}</span>
              <a href="#" class="text-gray-700 hover:text-blue-600 font-medium">Dashboard</a>
              <a href="#" class="text-gray-700 hover:text-blue-600 font-medium">Projects</a>
              <a href="#" class="text-gray-700 hover:text-blue-600 font-medium">Tasks</a>
              <button v-if="user?.is_admin" @click="currentView = 'admin'" class="text-purple-600 hover:text-purple-800 font-medium">
                Admin Panel
              </button>
              <button @click="handleLogout" class="text-red-600 hover:text-red-800 font-medium">Logout</button>
            </div>
          </div>
        </div>
      </nav>

      <!-- Main Content -->
      <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome to PMS Drive</h2>
          <p class="text-gray-600">
            Your Project Management System powered by Laravel {{ laravelVersion }} and Vue {{ vueVersion }}
          </p>
          <p v-if="user?.is_admin" class="mt-2 text-purple-600 font-semibold">
            ⭐ Administrator Access
          </p>
        </div>

        <!-- Feature Cards -->
        <div class="grid md:grid-cols-3 gap-6">
          <!-- Project Management Card -->
          <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-xl transition-shadow">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Project Management</h3>
            <p class="text-gray-600">Organize and track all your projects in one place with powerful tools.</p>
          </div>

          <!-- Task Tracking Card -->
          <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-xl transition-shadow">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Task Tracking</h3>
            <p class="text-gray-600">Keep track of tasks, deadlines, and progress with ease.</p>
          </div>

          <!-- Team Collaboration Card -->
          <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-xl transition-shadow">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Team Collaboration</h3>
            <p class="text-gray-600">Work together seamlessly with your team members.</p>
          </div>
        </div>

        <!-- Interactive Demo -->
        <div class="mt-6 bg-white rounded-lg shadow-md p-6">
          <h3 class="text-xl font-semibold text-gray-900 mb-4">Vue Reactivity Demo</h3>
          <div class="flex items-center space-x-4">
            <button 
              @click="incrementCounter" 
              class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg transition duration-200"
            >
              Click Me!
            </button>
            <span class="text-lg text-gray-700">Counter: <span class="font-bold text-blue-600">{{ counter }}</span></span>
          </div>
        </div>
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
import Footer from './components/Footer.vue';

const laravelVersion = '12.47.0';
const vueVersion = '3.x';
const counter = ref(0);
const currentView = ref('login');
const user = ref(null);

const incrementCounter = () => {
  counter.value++;
};

const handleLogin = (userData) => {
  user.value = userData;
  currentView.value = 'dashboard';
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

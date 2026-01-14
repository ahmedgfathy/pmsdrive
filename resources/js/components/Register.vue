<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center px-4">
    <div class="max-w-md w-full">
      <!-- Logo and Title -->
      <div class="text-center mb-8">
        <img src="/assets/images/logo.svg" alt="PMS Logo" class="h-16 mx-auto mb-4" />
        <h1 class="text-3xl font-bold text-gray-900">PMS Drive</h1>
        <p class="text-gray-600 mt-2">Create your account</p>
      </div>

      <!-- Registration Form -->
      <div class="bg-white rounded-lg shadow-xl p-8">
        <form @submit.prevent="handleRegister">
          <!-- Name Field -->
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
              Full Name
            </label>
            <input
              type="text"
              id="name"
              v-model="form.name"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
              placeholder="John Doe"
            />
          </div>

          <!-- Email Field -->
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Email Address
            </label>
            <input
              type="email"
              id="email"
              v-model="form.email"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
              placeholder="your.email@example.com"
            />
          </div>

          <!-- Password Field -->
          <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              Password
            </label>
            <input
              type="password"
              id="password"
              v-model="form.password"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
              placeholder="Minimum 8 characters"
            />
          </div>

          <!-- Confirm Password Field -->
          <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
              Confirm Password
            </label>
            <input
              type="password"
              id="password_confirmation"
              v-model="form.password_confirmation"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
              placeholder="Re-enter your password"
            />
          </div>

          <!-- Error Message -->
          <div v-if="error" class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ error }}
          </div>

          <!-- Success Message -->
          <div v-if="success" class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ success }}
          </div>

          <!-- Register Button -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition duration-200 shadow-md hover:shadow-lg disabled:opacity-50"
          >
            {{ loading ? 'Creating Account...' : 'Register' }}
          </button>
        </form>

        <!-- Links -->
        <div class="mt-6 text-center text-sm">
          <a @click="$emit('show-login')" href="#" class="text-blue-600 hover:text-blue-800 font-medium">
            Already have an account? Sign In
          </a>
          <div class="mt-2">
            <a href="mailto:ahmed.fathy@pms.eg" class="text-blue-600 hover:text-blue-800 font-medium">
              Need Support?
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const emit = defineEmits(['show-login']);

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
});

const error = ref('');
const success = ref('');
const loading = ref(false);

const handleRegister = async () => {
  error.value = '';
  success.value = '';
  loading.value = true;

  try {
    const response = await axios.post('/api/register', form.value);
    success.value = response.data.message;
    form.value = { name: '', email: '', password: '', password_confirmation: '' };
    
    setTimeout(() => {
      emit('show-login');
    }, 3000);
  } catch (err) {
    if (err.response?.data?.errors) {
      error.value = Object.values(err.response.data.errors).flat().join(', ');
    } else {
      error.value = err.response?.data?.message || 'Registration failed. Please try again.';
    }
  } finally {
    loading.value = false;
  }
};
</script>

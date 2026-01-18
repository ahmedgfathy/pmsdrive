<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center px-4">
    <div class="max-w-md w-full">
      <!-- Logo and Title -->
      <div class="text-center mb-8">
        <img src="/assets/images/logo.svg" alt="PMS Logo" class="h-16 mx-auto mb-4" />
        <h1 class="text-3xl font-bold text-gray-900">PMS Drive</h1>
        <p class="text-gray-600 mt-2">Sign in to your account</p>
      </div>

      <!-- Login Form -->
      <div class="bg-white rounded-lg shadow-xl p-8">
        <form @submit.prevent="handleLogin">
          <!-- Email Field -->
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Email Address
            </label>
            <input
              type="email"
              id="email"
              v-model="email"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
              placeholder="your.email@example.com"
            />
          </div>

          <!-- Password Field -->
          <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              Password
            </label>
            <input
              type="password"
              id="password"
              v-model="password"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
              placeholder="Enter your password"
            />
          </div>

          <!-- Error Message -->
          <div v-if="props.error" class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <span v-if="props.error.includes('System Admin')">
              {{ props.error.split('System Admin')[0] }}
              <a href="mailto:ahmed.fathy@pms.eg" class="font-semibold underline hover:text-red-900">
                System Admin: Ahmed Fathy
              </a>
            </span>
            <span v-else>{{ props.error }}</span>
          </div>

          <!-- Login Button -->
          <button
            type="submit"
            :disabled="props.loading"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition duration-200 shadow-md hover:shadow-lg disabled:opacity-50"
          >
            {{ props.loading ? 'Signing In...' : 'Sign In' }}
          </button>
        </form>

        <!-- Links -->
        <div class="mt-6 flex justify-between items-center text-sm">
          <a @click="$emit('show-register')" href="#" class="text-blue-600 hover:text-blue-800 font-medium">
            Register
          </a>
          <a href="mailto:ahmed.fathy@pms.eg" class="text-blue-600 hover:text-blue-800 font-medium">
            Support
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const emit = defineEmits(['login', 'show-register']);

const props = defineProps({
  loading: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  }
});

const email = ref('');
const password = ref('');

const handleLogin = () => {
  emit('login', {
    email: email.value.trim(),
    password: password.value
  });
};
</script>

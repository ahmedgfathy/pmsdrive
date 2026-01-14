<template>
  <footer class="bg-gray-800 text-white py-6 mt-auto">
    <div class="container mx-auto px-4">
      <div class="text-center">
        <p class="text-sm">
          © {{ currentYear }} PMS. All rights reserved. 
          <span class="mx-2">|</span>
          Developed by 
          <a 
            href="mailto:ahmed.fathy@pms.eg" 
            class="text-blue-400 hover:text-blue-300 font-medium transition"
          >
            Ahmed Fathy
          </a>
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue';

const currentYear = computed(() => new Date().getFullYear());
</script>

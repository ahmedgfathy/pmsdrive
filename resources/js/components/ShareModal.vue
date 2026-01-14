<template>
  <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform transition-all max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="p-6 border-b border-slate-200 sticky top-0 bg-white rounded-t-2xl">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-xl font-bold text-slate-800">Share File</h3>
              <p class="text-sm text-slate-500 truncate max-w-md">{{ file.original_name }}</p>
            </div>
          </div>
          <button @click="$emit('close')" class="text-slate-400 hover:text-slate-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Content -->
      <div class="p-6 space-y-6">
        
        <!-- Tab Navigation -->
        <div class="flex space-x-2 bg-slate-100 rounded-xl p-1">
          <button
            @click="activeTab = 'user'"
            :class="[
              'flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-all',
              activeTab === 'user' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900'
            ]"
          >
            Share with User
          </button>
          <button
            @click="activeTab = 'link'"
            :class="[
              'flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-all',
              activeTab === 'link' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900'
            ]"
          >
            Get Link
          </button>
          <button
            @click="activeTab = 'email'"
            :class="[
              'flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-all',
              activeTab === 'email' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900'
            ]"
          >
            Send by Email
          </button>
        </div>

        <!-- Share with User Tab -->
        <div v-if="activeTab === 'user'" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">User Email</label>
            <input
              v-model="userEmail"
              type="email"
              placeholder="user@example.com"
              class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
            >
          </div>
          
          <div v-if="shareSuccess" class="bg-green-50 border border-green-200 rounded-xl p-4">
            <p class="text-green-800 text-sm">{{ shareSuccess }}</p>
          </div>
          
          <div v-if="shareError" class="bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-red-800 text-sm">{{ shareError }}</p>
          </div>
          
          <button
            @click="shareWithUser"
            :disabled="!userEmail || loading"
            class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg shadow-blue-500/30 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loading ? 'Sharing...' : 'Share with User' }}
          </button>
        </div>

        <!-- Get Link Tab -->
        <div v-if="activeTab === 'link'" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Link expires in (days)</label>
            <input
              v-model.number="expiresInDays"
              type="number"
              min="1"
              max="30"
              placeholder="7"
              class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Password (optional)</label>
            <input
              v-model="linkPassword"
              type="text"
              placeholder="Leave empty for no password"
              class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
            >
          </div>
          
          <div v-if="generatedLink" class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <p class="text-sm text-slate-600 mb-2">Share this link:</p>
            <div class="flex items-center space-x-2">
              <input
                :value="generatedLink"
                readonly
                class="flex-1 px-3 py-2 bg-white border border-blue-300 rounded-lg text-sm"
              >
              <button
                @click="copyLink"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all text-sm font-medium"
              >
                {{ copied ? 'Copied!' : 'Copy' }}
              </button>
            </div>
          </div>
          
          <button
            @click="generateLink"
            :disabled="loading"
            class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg shadow-blue-500/30 font-medium disabled:opacity-50"
          >
            {{ loading ? 'Generating...' : 'Generate Link' }}
          </button>
        </div>

        <!-- Send by Email Tab -->
        <div v-if="activeTab === 'email'" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Recipient Email</label>
            <input
              v-model="recipientEmail"
              type="email"
              placeholder="recipient@example.com"
              class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Message (optional)</label>
            <textarea
              v-model="emailMessage"
              rows="3"
              placeholder="Add a message to the recipient..."
              class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
            ></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Link expires in (days)</label>
            <input
              v-model.number="expiresInDays"
              type="number"
              min="1"
              max="30"
              placeholder="7"
              class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
            >
          </div>
          
          <div v-if="emailSuccess" class="bg-green-50 border border-green-200 rounded-xl p-4">
            <p class="text-green-800 text-sm">{{ emailSuccess }}</p>
          </div>
          
          <div v-if="emailError" class="bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-red-800 text-sm">{{ emailError }}</p>
          </div>
          
          <button
            @click="sendByEmail"
            :disabled="!recipientEmail || loading"
            class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg shadow-blue-500/30 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loading ? 'Sending...' : 'Send Email' }}
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
  file: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close', 'success']);

const activeTab = ref('user');
const loading = ref(false);

// Share with user
const userEmail = ref('');
const shareSuccess = ref('');
const shareError = ref('');

// Generate link
const expiresInDays = ref(7);
const linkPassword = ref('');
const generatedLink = ref('');
const copied = ref(false);

// Send by email
const recipientEmail = ref('');
const emailMessage = ref('');
const emailSuccess = ref('');
const emailError = ref('');

const shareWithUser = async () => {
  loading.value = true;
  shareSuccess.value = '';
  shareError.value = '';

  try {
    const response = await axios.post(`/api/files/${props.file.id}/share/user`, {
      user_email: userEmail.value
    });

    shareSuccess.value = response.data.message;
    userEmail.value = '';
    
    setTimeout(() => {
      emit('success');
      emit('close');
    }, 1500);
  } catch (err) {
    shareError.value = err.response?.data?.message || 'Failed to share file';
  } finally {
    loading.value = false;
  }
};

const generateLink = async () => {
  loading.value = true;

  try {
    const response = await axios.post(`/api/files/${props.file.id}/share/link`, {
      expires_in_days: expiresInDays.value,
      password: linkPassword.value || null
    });

    generatedLink.value = response.data.link;
  } catch (err) {
    shareError.value = err.response?.data?.message || 'Failed to generate link';
  } finally {
    loading.value = false;
  }
};

const copyLink = () => {
  navigator.clipboard.writeText(generatedLink.value);
  copied.value = true;
  setTimeout(() => copied.value = false, 2000);
};

const sendByEmail = async () => {
  loading.value = true;
  emailSuccess.value = '';
  emailError.value = '';

  try {
    const response = await axios.post(`/api/files/${props.file.id}/share/email`, {
      email: recipientEmail.value,
      message: emailMessage.value,
      expires_in_days: expiresInDays.value
    });

    emailSuccess.value = `Email sent successfully to ${recipientEmail.value}!`;
    recipientEmail.value = '';
    emailMessage.value = '';
    
    setTimeout(() => {
      emit('success');
      emit('close');
    }, 2000);
  } catch (err) {
    emailError.value = err.response?.data?.message || 'Failed to send email';
  } finally {
    loading.value = false;
  }
};
</script>

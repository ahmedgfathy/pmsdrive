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
            <button @click="activeTab = 'rules'" :class="[activeTab === 'rules' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300','py-4 px-1 border-b-2 font-medium text-sm']">
              File Rules
            </button>
          </nav>
        </div>
      </div>

      <!-- Pending Users Tab -->
      <div v-if="activeTab === 'pending'" class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
          <h2 class="text-lg font-semibold text-gray-900">Users Pending Approval</h2>
        </div>
        <div v-if="pendingLoading" class="p-12 text-center">
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
        <div v-if="allLoading" class="p-12 text-center">
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Storage</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.formatted_storage_quota || '—' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button @click="openQuotaModal(user)" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-sm hover:shadow-md transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Set Quota
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- File Rules Tab -->
      <div v-if="activeTab === 'rules'" class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-white flex items-center justify-between">
          <h2 class="text-lg font-semibold text-gray-900">File Extension Rules</h2>
          <button @click="openRuleModal()" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium shadow-sm hover:shadow-md transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Rule
          </button>
        </div>
        <div class="px-6 py-5 border-b border-gray-100 bg-white">
          <div class="flex items-start justify-between flex-col gap-4 lg:flex-row lg:items-center">
            <div>
              <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">Popular Extensions</h3>
              <p class="text-sm text-gray-500 mt-1">Toggle common file types to quickly allow or block uploads without typing.</p>
            </div>
            <p class="text-xs text-gray-400">Tip: You can still manage custom extensions below.</p>
          </div>
          <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="item in popularExtensions"
              :key="item.extension"
              class="border border-gray-200 rounded-xl p-4 shadow-sm hover:border-indigo-300 transition"
            >
              <div class="flex items-start justify-between">
                <div>
                  <p class="text-base font-semibold text-gray-900">.{{ item.extension }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ item.label }}</p>
                </div>
                <div class="flex flex-col space-y-2 text-sm font-medium text-gray-600">
                  <label class="inline-flex items-center space-x-2 cursor-pointer">
                    <input
                      type="checkbox"
                      class="rounded text-green-600 focus:ring-green-500"
                      :checked="popularState[item.extension]?.status === 'allowed'"
                      :disabled="popularBusy[item.extension]"
                      @change="togglePopular(item.extension, 'allowed', $event.target.checked)"
                    >
                    <span>Allow</span>
                  </label>
                  <label class="inline-flex items-center space-x-2 cursor-pointer">
                    <input
                      type="checkbox"
                      class="rounded text-red-600 focus:ring-red-500"
                      :checked="popularState[item.extension]?.status === 'forbidden'"
                      :disabled="popularBusy[item.extension]"
                      @change="togglePopular(item.extension, 'forbidden', $event.target.checked)"
                    >
                    <span>Forbid</span>
                  </label>
                </div>
              </div>
              <div v-if="popularState[item.extension]?.status === 'forbidden'" class="mt-4">
                <label class="block text-xs font-semibold text-gray-600 mb-2">Forbidden message</label>
                <textarea
                  v-model="popularState[item.extension].message"
                  :disabled="popularBusy[item.extension]"
                  rows="3"
                  maxlength="255"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none text-sm"
                  placeholder="Explain why this type is blocked."
                  @blur="savePopularMessage(item.extension)"
                ></textarea>
                <p class="text-xs text-gray-400 mt-1">Shown to users when they upload .{{ item.extension }} files.</p>
              </div>
              <button
                v-if="popularState[item.extension]?.status !== 'none'"
                class="mt-4 text-xs text-gray-500 hover:text-gray-700 transition"
                :disabled="popularBusy[item.extension]"
                @click="clearPopular(item.extension)"
              >
                Clear selection
              </button>
            </div>
          </div>
        </div>
        <div v-if="rulesLoading" class="p-12 text-center">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-indigo-500 border-t-transparent"></div>
          <p class="mt-2 text-gray-500">Loading rules...</p>
        </div>
        <div v-else-if="fileRules.length === 0" class="p-12 text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v2a2 2 0 002 2h6a2 2 0 002-2v-2M9 7V5a2 2 0 012-2h6a2 2 0 012 2v2M9 7h10M9 17h10M9 12h10M5 7h.01M5 12h.01M5 17h.01" />
          </svg>
          <p class="mt-2 text-gray-500 font-medium">No rules configured yet</p>
          <p class="text-sm text-gray-400">Add file extensions to allow or forbid uploads.</p>
        </div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Extension</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="rule in fileRules" :key="rule.id" class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">.{{ rule.extension }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[rule.is_allowed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700','px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full']">
                    {{ rule.is_allowed ? 'Allowed' : 'Forbidden' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-pre-line text-sm text-gray-500">{{ rule.message || (rule.is_allowed ? '—' : 'No message provided') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                  <button @click="openRuleModal(rule)" class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" />
                    </svg>
                    Edit
                  </button>
                  <button @click="deleteRule(rule)" class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Remove
                  </button>
                </td>
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

    <!-- Quota Modal -->
    <div v-if="showQuotaModal" class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-semibold text-gray-900">Set Storage Quota</h3>
        <p class="text-sm text-gray-500 mt-1" v-if="quotaUser">
          Assign a storage limit for <span class="font-medium text-gray-800">{{ quotaUser.name }}</span>.
        </p>
        <div class="mt-5 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Quota (GB)</label>
            <input
              v-model="quotaForm.quotaGb"
              type="number"
              min="0.1"
              step="0.1"
              placeholder="e.g. 10"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
            >
          </div>
          <div class="flex justify-end space-x-3">
            <button @click="closeQuotaModal" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">Cancel</button>
            <button @click="saveQuota" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">Save</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Rule Modal -->
    <div v-if="showRuleModal" class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900">{{ editingRule ? 'Edit Rule' : 'Add File Rule' }}</h3>
        <p class="text-sm text-gray-500 mt-1">Choose whether this extension is allowed or forbidden for uploads.</p>
        <div class="mt-5 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Extension</label>
            <input
              v-model="ruleForm.extension"
              type="text"
              maxlength="20"
              placeholder="e.g. pdf"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select
              v-model="ruleForm.is_allowed"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
            >
              <option :value="true">Allowed</option>
              <option :value="false">Forbidden</option>
            </select>
          </div>
          <div v-if="!ruleForm.is_allowed">
            <label class="block text-sm font-medium text-gray-700 mb-2">Forbidden Message</label>
            <textarea
              v-model="ruleForm.message"
              rows="3"
              maxlength="255"
              placeholder="Explain why this file type is blocked."
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"
            ></textarea>
          </div>
          <div class="flex justify-end space-x-3">
            <button @click="closeRuleModal" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">Cancel</button>
            <button @click="saveRule" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">Save</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import axios from 'axios';
import ConfirmModal from './ConfirmModal.vue';
import { useToast } from '../composables/useToast';

const { success, error } = useToast();

const ensureCsrf = async () => {
  try {
    await axios.get('/sanctum/csrf-cookie');
  } catch (err) {
    console.error('Failed to initialize CSRF token:', err);
    throw err;
  }
};

defineEmits(['back-to-dashboard', 'logout']);

const activeTab = ref('pending');
const pendingUsers = ref([]);
const allUsers = ref([]);
const fileRules = ref([]);

const pendingLoading = ref(false);
const allLoading = ref(false);
const rulesLoading = ref(false);

const popularExtensions = [
  { extension: 'pdf', label: 'PDF documents' },
  { extension: 'docx', label: 'Word documents' },
  { extension: 'xls', label: 'Excel spreadsheets' },
  { extension: 'xlsx', label: 'Excel workbooks' },
  { extension: 'ppt', label: 'PowerPoint presentations' },
  { extension: 'pptx', label: 'PowerPoint presentations' },
  { extension: 'jpg', label: 'JPEG images' },
  { extension: 'jpeg', label: 'JPEG images' },
  { extension: 'png', label: 'PNG images' },
  { extension: 'gif', label: 'GIF images' },
  { extension: 'mp3', label: 'Audio files' },
  { extension: 'mp4', label: 'Video files' },
  { extension: 'zip', label: 'Compressed archives' },
  { extension: 'rar', label: 'Compressed archives' },
  { extension: 'txt', label: 'Text files' },
];

const popularState = reactive({});
const popularBusy = reactive({});

const defaultForbiddenMessage = (extension) => `Uploads for .${extension} files are not permitted.`;

popularExtensions.forEach(({ extension }) => {
  popularState[extension] = {
    status: 'none',
    message: defaultForbiddenMessage(extension),
  };
  popularBusy[extension] = false;
});

const showingRejectModal = ref(false);
const userToReject = ref(null);

const showQuotaModal = ref(false);
const quotaUser = ref(null);
const quotaForm = reactive({
  quotaGb: '',
});

const showRuleModal = ref(false);
const editingRule = ref(null);
const ruleForm = reactive({
  extension: '',
  is_allowed: true,
  message: '',
});

const findRuleByExtension = (extension) => fileRules.value.find((rule) => rule.extension === extension);

const sortFileRules = () => {
  fileRules.value.sort((a, b) => a.extension.localeCompare(b.extension));
};

const upsertRuleInList = (rule) => {
  const index = fileRules.value.findIndex((item) => item.id === rule.id);
  if (index !== -1) {
    fileRules.value.splice(index, 1, rule);
  } else {
    fileRules.value.push(rule);
  }
  sortFileRules();
};

const removeRuleFromList = (ruleId) => {
  const index = fileRules.value.findIndex((item) => item.id === ruleId);
  if (index !== -1) {
    fileRules.value.splice(index, 1);
  }
};

const syncPopularState = () => {
  popularExtensions.forEach(({ extension }) => {
    const currentRule = findRuleByExtension(extension);
    if (currentRule) {
      popularState[extension] = {
        status: currentRule.is_allowed ? 'allowed' : 'forbidden',
        message: currentRule.message || defaultForbiddenMessage(extension),
      };
    } else {
      popularState[extension] = {
        status: 'none',
        message: defaultForbiddenMessage(extension),
      };
    }
  });
};

const loadPendingUsers = async () => {
  pendingLoading.value = true;
  try {
    const response = await axios.get('/api/admin/users/pending');
    pendingUsers.value = response.data;
  } catch (err) {
    console.error('Error loading pending users:', err);
    error('Failed to load pending users');
  } finally {
    pendingLoading.value = false;
  }
};

const loadAllUsers = async () => {
  allLoading.value = true;
  try {
    const response = await axios.get('/api/admin/users');
    allUsers.value = response.data;
  } catch (err) {
    console.error('Error loading all users:', err);
    error('Failed to load users');
  } finally {
    allLoading.value = false;
  }
};

const loadFileRules = async () => {
  rulesLoading.value = true;
  try {
    const response = await axios.get('/api/admin/file-restrictions');
    fileRules.value = response.data;
    sortFileRules();
    syncPopularState();
  } catch (err) {
    console.error('Error loading file rules:', err);
    error('Failed to load file rules');
    syncPopularState();
  } finally {
    rulesLoading.value = false;
  }
};

const approveUser = async (userId) => {
  try {
    await ensureCsrf();
    await axios.post(`/api/admin/users/${userId}/approve`);
    success('User approved successfully!');
    await loadPendingUsers();
    await loadAllUsers();
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
    await ensureCsrf();
    await axios.delete(`/api/admin/users/${userToReject.value.id}/reject`);
    success('User rejected and removed successfully');
    showingRejectModal.value = false;
    userToReject.value = null;
    await loadPendingUsers();
    await loadAllUsers();
  } catch (err) {
    error(err.response?.data?.message || 'Failed to reject user');
  }
};

const openQuotaModal = (user) => {
  quotaUser.value = user;
  const quotaGb = user.storage_quota
    ? Math.round((user.storage_quota / 1073741824) * 100) / 100
    : 0;
  quotaForm.quotaGb = quotaGb || '';
  showQuotaModal.value = true;
};

const closeQuotaModal = () => {
  showQuotaModal.value = false;
  quotaUser.value = null;
  quotaForm.quotaGb = '';
};

const saveQuota = async () => {
  if (!quotaUser.value) return;

  const parsedQuota = parseFloat(quotaForm.quotaGb);
  if (Number.isNaN(parsedQuota) || parsedQuota <= 0) {
    error('Enter a valid quota in GB.');
    return;
  }

  try {
    await ensureCsrf();
    const response = await axios.put(`/api/admin/users/${quotaUser.value.id}/quota`, {
      storage_quota_gb: parsedQuota,
    });

    success('Storage quota updated');
    const index = allUsers.value.findIndex((u) => u.id === quotaUser.value.id);
    if (index !== -1) {
      allUsers.value[index] = {
        ...allUsers.value[index],
        storage_quota: response.data.storage_quota,
        formatted_storage_quota: response.data.formatted_storage_quota,
      };
    }
    closeQuotaModal();
  } catch (err) {
    error(err.response?.data?.message || 'Failed to update quota');
  }
};

const openRuleModal = (rule = null) => {
  editingRule.value = rule;
  ruleForm.extension = rule ? rule.extension : '';
  ruleForm.is_allowed = rule ? rule.is_allowed : true;
  ruleForm.message = rule ? rule.message || '' : '';
  showRuleModal.value = true;
};

const closeRuleModal = () => {
  showRuleModal.value = false;
  editingRule.value = null;
  ruleForm.extension = '';
  ruleForm.is_allowed = true;
  ruleForm.message = '';
};

const saveRule = async () => {
  const payload = {
    extension: ruleForm.extension.trim(),
    is_allowed: !!ruleForm.is_allowed,
    message: ruleForm.is_allowed ? null : ruleForm.message.trim() || null,
  };

  if (!payload.extension) {
    error('Extension is required.');
    return;
  }

  if (!payload.is_allowed && !payload.message) {
    error('Provide a message for forbidden files.');
    return;
  }

  try {
    await ensureCsrf();
    if (editingRule.value) {
      await axios.put(`/api/admin/file-restrictions/${editingRule.value.id}`, payload);
      success('Rule updated');
    } else {
      await axios.post('/api/admin/file-restrictions', payload);
      success('Rule added');
    }
    await loadFileRules();
    closeRuleModal();
  } catch (err) {
    error(err.response?.data?.message || 'Failed to save rule');
  }
};

const deleteRule = async (rule) => {
  if (!confirm(`Remove the rule for .${rule.extension}?`)) {
    return;
  }

  try {
    await ensureCsrf();
    await axios.delete(`/api/admin/file-restrictions/${rule.id}`);
    success('Rule removed');
    await loadFileRules();
  } catch (err) {
    error(err.response?.data?.message || 'Failed to remove rule');
  }
};

const applyPopularStatus = async (extension, status) => {
  if (!popularState[extension]) {
    popularState[extension] = {
      status: 'none',
      message: defaultForbiddenMessage(extension),
    };
  }

  const currentRule = findRuleByExtension(extension);

  try {
    popularBusy[extension] = true;
    await ensureCsrf();

    if (status === 'allowed') {
      const payload = {
        extension,
        is_allowed: true,
        message: null,
      };
      let rule;
      if (currentRule) {
        const response = await axios.put(`/api/admin/file-restrictions/${currentRule.id}`, payload);
        rule = response.data;
      } else {
        const response = await axios.post('/api/admin/file-restrictions', payload);
        rule = response.data;
      }
      upsertRuleInList(rule);
      popularState[extension] = {
        status: 'allowed',
        message: defaultForbiddenMessage(extension),
      };
      success(`.${extension} allowed`);
    } else if (status === 'forbidden') {
      const messageValue = (popularState[extension].message || '').trim() || defaultForbiddenMessage(extension);
      const payload = {
        extension,
        is_allowed: false,
        message: messageValue,
      };
      let rule;
      if (currentRule) {
        const response = await axios.put(`/api/admin/file-restrictions/${currentRule.id}`, payload);
        rule = response.data;
      } else {
        const response = await axios.post('/api/admin/file-restrictions', payload);
        rule = response.data;
      }
      upsertRuleInList(rule);
      popularState[extension] = {
        status: 'forbidden',
        message: rule.message || defaultForbiddenMessage(extension),
      };
      success(`.${extension} forbidden`);
    } else {
      if (currentRule) {
        await axios.delete(`/api/admin/file-restrictions/${currentRule.id}`);
        removeRuleFromList(currentRule.id);
        success(`Rule cleared for .${extension}`);
      }
      popularState[extension] = {
        status: 'none',
        message: defaultForbiddenMessage(extension),
      };
    }
  } catch (err) {
    error(err.response?.data?.message || 'Failed to update extension rule');
    syncPopularState();
  } finally {
    popularBusy[extension] = false;
  }
};

const togglePopular = async (extension, targetStatus, isChecked) => {
  const nextStatus = isChecked ? targetStatus : 'none';
  await applyPopularStatus(extension, nextStatus);
};

const savePopularMessage = async (extension) => {
  const currentRule = findRuleByExtension(extension);
  if (!currentRule) {
    return;
  }
  if (popularState[extension]?.status !== 'forbidden') {
    return;
  }

  const messageValue = (popularState[extension].message || '').trim() || defaultForbiddenMessage(extension);
  const currentMessage = currentRule.message || defaultForbiddenMessage(extension);
  if (messageValue === currentMessage) {
    return;
  }

  try {
    popularBusy[extension] = true;
    await ensureCsrf();
    const response = await axios.put(`/api/admin/file-restrictions/${currentRule.id}`, {
      message: messageValue,
    });
    upsertRuleInList(response.data);
    popularState[extension].message = response.data.message || defaultForbiddenMessage(extension);
    success(`Message updated for .${extension}`);
  } catch (err) {
    error(err.response?.data?.message || 'Failed to update message');
    syncPopularState();
  } finally {
    popularBusy[extension] = false;
  }
};

const clearPopular = async (extension) => {
  await applyPopularStatus(extension, 'none');
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

watch(activeTab, (tab) => {
  if (tab === 'pending') {
    loadPendingUsers();
  } else if (tab === 'all') {
    loadAllUsers();
  } else if (tab === 'rules') {
    loadFileRules();
  }
});

onMounted(() => {
  loadPendingUsers();
  loadAllUsers();
  loadFileRules();
});
</script>

import { ref } from 'vue';

const toasts = ref([]);
let nextId = 0;

export function useToast() {
  const addToast = (options) => {
    const id = nextId++;
    const toast = {
      id,
      type: options.type || 'info',
      title: options.title || '',
      message: options.message || '',
      duration: options.duration || 3000
    };
    
    toasts.value.push(toast);
    
    if (toast.duration > 0) {
      setTimeout(() => {
        removeToast(id);
      }, toast.duration);
    }
    
    return id;
  };

  const removeToast = (id) => {
    const index = toasts.value.findIndex(t => t.id === id);
    if (index > -1) {
      toasts.value.splice(index, 1);
    }
  };

  const success = (message, title = 'Success') => {
    return addToast({ type: 'success', title, message });
  };

  const error = (message, title = 'Error') => {
    return addToast({ type: 'error', title, message });
  };

  const warning = (message, title = 'Warning') => {
    return addToast({ type: 'warning', title, message });
  };

  const info = (message, title = 'Info') => {
    return addToast({ type: 'info', title, message });
  };

  return {
    toasts,
    addToast,
    removeToast,
    success,
    error,
    warning,
    info
  };
}

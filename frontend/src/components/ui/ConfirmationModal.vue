<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h2>{{ title }}</h2>
        <button class="close-btn" @click="$emit('close')">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="modal-body">
        <div class="icon-container" :class="type">
          <i class="fas" :class="iconClass"></i>
        </div>
        <p class="message">{{ message }}</p>
      </div>

      <div class="modal-actions">
        <button class="cancel-btn" @click="$emit('close')">
          {{ cancelText }}
        </button>
        <button 
          class="confirm-btn" 
          :class="type"
          @click="$emit('confirm')"
        >
          {{ confirmText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  message: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'warning',
    validator: (value) => ['warning', 'danger', 'info', 'success'].includes(value)
  },
  confirmText: {
    type: String,
    default: 'Confirm'
  },
  cancelText: {
    type: String,
    default: 'Cancel'
  }
});

const iconClass = computed(() => {
  const icons = {
    warning: 'fa-exclamation-triangle',
    danger: 'fa-exclamation-circle',
    info: 'fa-info-circle',
    success: 'fa-check-circle'
  };
  return icons[props.type];
});
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: var(--modal-background, #fff);
  border-radius: 12px;
  width: 90%;
  max-width: 400px;
  padding: 2rem;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.modal-header h2 {
  font-size: 1.5rem;
  color: var(--text-color);
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: var(--text-color);
  cursor: pointer;
  padding: 0.5rem;
}

.modal-body {
  text-align: center;
  margin-bottom: 2rem;
}

.icon-container {
  width: 64px;
  height: 64px;
  margin: 0 auto 1rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
}

.icon-container.warning {
  background: #fff3cd;
  color: #856404;
}

.icon-container.danger {
  background: #f8d7da;
  color: #721c24;
}

.icon-container.info {
  background: #cce5ff;
  color: #004085;
}

.icon-container.success {
  background: #d4edda;
  color: #155724;
}

.message {
  color: var(--text-color);
  font-size: 1.1rem;
  line-height: 1.5;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.cancel-btn, .confirm-btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
}

.cancel-btn {
  background: var(--secondary-color, #6c757d);
  color: white;
}

.confirm-btn {
  color: white;
}

.confirm-btn.warning {
  background: #ffc107;
}

.confirm-btn.danger {
  background: #dc3545;
}

.confirm-btn.info {
  background: #0d6efd;
}

.confirm-btn.success {
  background: #198754;
}

@media (max-width: 768px) {
  .modal-content {
    padding: 1.5rem;
  }

  .modal-actions {
    flex-direction: column;
  }

  .cancel-btn, .confirm-btn {
    width: 100%;
  }
}
</style> 
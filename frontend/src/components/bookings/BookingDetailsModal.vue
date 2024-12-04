<template>
  <div v-if="show" class="modal-overlay" @click="$emit('close')">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h2>Booking Details</h2>
        <div class="action-buttons">
          <button 
            v-if="canDownloadInvoice" 
            @click="downloadInvoice" 
            class="btn btn-secondary"
          >
            <i class="fas fa-file-invoice"></i>
            Download Invoice
          </button>
          <button @click="$emit('close')" class="btn btn-close">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>

      <div class="booking-info" v-if="booking">
        <!-- Event Details -->
        <div class="info-section">
          <h3>Event Information</h3>
          <div class="info-grid">
            <div class="info-item">
              <span class="label">Event Date:</span>
              <span class="value">{{ formatDate(booking.eventDate) }}</span>
            </div>
            <div class="info-item">
              <span class="label">Event Time:</span>
              <span class="value">{{ formatTime(booking.eventTime) }}</span>
            </div>
            <div class="info-item">
              <span class="label">Venue:</span>
              <span class="value">{{ booking.venueName }}</span>
            </div>
            <div class="info-item">
              <span class="label">Guest Count:</span>
              <span class="value">{{ booking.guestCount }} persons</span>
            </div>
          </div>
        </div>

        <!-- Package Details -->
        <div class="info-section">
          <h3>Package Details</h3>
          <div class="package-details">
            <img :src="booking.package?.image" :alt="booking.package?.name">
            <div class="package-info">
              <h4>{{ booking.package?.name }}</h4>
              <p class="price">₱{{ booking.package?.price.toLocaleString() }}</p>
              <p class="description">{{ booking.package?.description }}</p>
            </div>
          </div>
        </div>

        <!-- Contact Information -->
        <div class="info-section">
          <h3>Contact Information</h3>
          <div class="info-grid">
            <div class="info-item">
              <span class="label">Full Name:</span>
              <span class="value">{{ booking.fullName }}</span>
            </div>
            <div class="info-item">
              <span class="label">Email:</span>
              <span class="value">{{ booking.email }}</span>
            </div>
            <div class="info-item">
              <span class="label">Phone:</span>
              <span class="value">{{ booking.phone }}</span>
            </div>
          </div>
        </div>

        <!-- Payment Information -->
        <div class="info-section">
          <h3>Payment Information</h3>
          <div class="payment-info">
            <div class="info-item">
              <span class="label">Payment Method:</span>
              <span class="value">{{ formatPaymentMethod(booking.paymentMethod) }}</span>
            </div>
            <div class="info-item">
              <span class="label">Payment Status:</span>
              <span :class="['status', booking.paymentStatus.toLowerCase()]">
                {{ booking.paymentStatus }}
              </span>
            </div>
            <div class="info-item total">
              <span class="label">Total Amount:</span>
              <span class="value">₱{{ booking.totalAmount.toLocaleString() }}</span>
            </div>
          </div>
        </div>

        <!-- Special Requests -->
        <div class="info-section" v-if="booking.specialRequests">
          <h3>Special Requests</h3>
          <p class="special-requests">{{ booking.specialRequests }}</p>
        </div>

        <!-- Booking Status -->
        <div class="info-section">
          <h3>Booking Status</h3>
          <div class="status-timeline">
            <div 
              v-for="(status, index) in bookingStatuses" 
              :key="status"
              :class="['status-item', { active: isStatusActive(status) }]"
            >
              <div class="status-dot"></div>
              <span class="status-text">{{ status }}</span>
              <div 
                v-if="index < bookingStatuses.length - 1" 
                class="status-line"
              ></div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="modal-actions">
          <button 
            v-if="canCancel" 
            class="cancel-btn"
            @click="handleCancel"
          >
            Cancel Booking
          </button>
          <button 
            v-if="canModify" 
            class="modify-btn"
            @click="handleModify"
          >
            Modify Booking
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  show: Boolean,
  booking: Object
});

const emit = defineEmits(['close', 'cancel', 'modify']);

const bookingStatuses = [
  'Pending',
  'Confirmed',
  'In Progress',
  'Completed',
  'Cancelled'
];

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PH', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const formatTime = (time) => {
  return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-PH', {
    hour: 'numeric',
    minute: 'numeric',
    hour12: true
  });
};

const formatPaymentMethod = (method) => {
  return method.charAt(0).toUpperCase() + method.slice(1);
};

const isStatusActive = (status) => {
  const currentIndex = bookingStatuses.indexOf(props.booking?.status);
  const statusIndex = bookingStatuses.indexOf(status);
  return statusIndex <= currentIndex;
};

const canCancel = computed(() => {
  return props.booking?.status === 'Pending' || props.booking?.status === 'Confirmed';
});

const canModify = computed(() => {
  return props.booking?.status === 'Pending';
});

const handleCancel = () => {
  emit('cancel', props.booking);
};

const handleModify = () => {
  emit('modify', props.booking);
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
  padding: 2rem;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #666;
}

.info-section {
  margin-bottom: 2rem;
}

.info-section h3 {
  color: #333;
  margin-bottom: 1rem;
  font-size: 1.2rem;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.label {
  color: #666;
  font-size: 0.9rem;
}

.value {
  color: #333;
  font-weight: 500;
}

.package-details {
  display: flex;
  gap: 1.5rem;
  background: #f8f9fa;
  padding: 1rem;
  border-radius: 8px;
}

.package-details img {
  width: 150px;
  height: 100px;
  object-fit: cover;
  border-radius: 6px;
}

.package-info h4 {
  margin-bottom: 0.5rem;
  color: #333;
}

.price {
  color: #4CAF50;
  font-weight: bold;
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
}

.description {
  color: #666;
  font-size: 0.9rem;
}

.payment-info {
  background: #f8f9fa;
  padding: 1rem;
  border-radius: 8px;
}

.status {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 500;
}

.status.pending {
  background: #fff3cd;
  color: #856404;
}

.status.confirmed {
  background: #d4edda;
  color: #155724;
}

.status.cancelled {
  background: #f8d7da;
  color: #721c24;
}

.total {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #ddd;
}

.total .value {
  color: #4CAF50;
  font-size: 1.2rem;
}

.special-requests {
  background: #f8f9fa;
  padding: 1rem;
  border-radius: 8px;
  color: #666;
  font-style: italic;
}

.status-timeline {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1rem;
  padding: 0 1rem;
}

.status-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  flex: 1;
}

.status-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #ddd;
  margin-bottom: 0.5rem;
}

.status-line {
  position: absolute;
  top: 6px;
  left: 50%;
  width: 100%;
  height: 2px;
  background: #ddd;
  z-index: -1;
}

.status-text {
  font-size: 0.8rem;
  color: #666;
  text-align: center;
}

.status-item.active .status-dot {
  background: #4CAF50;
}

.status-item.active .status-line {
  background: #4CAF50;
}

.status-item.active .status-text {
  color: #4CAF50;
  font-weight: 500;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
  justify-content: flex-end;
}

.cancel-btn, .modify-btn {
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
}

.cancel-btn {
  background: #f44336;
  color: white;
  border: none;
}

.cancel-btn:hover {
  background: #d32f2f;
}

.modify-btn {
  background: #2196F3;
  color: white;
  border: none;
}

.modify-btn:hover {
  background: #1976D2;
}

@media (max-width: 768px) {
  .modal-content {
    padding: 1rem;
  }

  .package-details {
    flex-direction: column;
  }

  .package-details img {
    width: 100%;
    height: 150px;
  }

  .status-timeline {
    flex-direction: column;
    gap: 1rem;
  }

  .status-item {
    flex-direction: row;
    gap: 1rem;
  }

  .status-line {
    display: none;
  }

  .modal-actions {
    flex-direction: column;
  }

  .cancel-btn, .modify-btn {
    width: 100%;
  }
}

.action-buttons {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.btn-secondary {
  background-color: var(--color-secondary);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.25rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background-color 0.2s;
}

.btn-secondary:hover {
  background-color: var(--color-secondary-dark);
}

.btn-secondary i {
  font-size: 1rem;
}
</style> 
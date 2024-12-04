<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Edit Booking</h2>
        <button class="close-btn" @click="$emit('close')">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="edit-form">
        <div class="form-section">
          <h3>Event Details</h3>
          
          <div class="form-group">
            <div class="form-group">
              <label for="status">Status</label>
              <select id="status" v-model="formData.status" required>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="ongoing">On-Going</option>
                <option value="preparing">Preparing</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="cancel-btn" @click="$emit('close')">Cancel</button>
          <button type="submit" class="submit-btn" :disabled="isSubmitting">
            <i class="fas fa-spinner fa-spin" v-if="isSubmitting"></i>
            {{ isSubmitting ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useAuth } from '@/composables/useAuth';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
  booking: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close', 'update']);
const { token } = useAuth();

const packages = ref([]);
const isSubmitting = ref(false);

const formData = reactive({
  eventType: props.booking.eventType,
  eventDate: props.booking.eventDate,
  eventTime: props.booking.eventTime,
  venue: props.booking.venue,
  packageId: props.booking.packageId,
  clientName: props.booking.clientName,
  clientEmail: props.booking.clientEmail,
  clientPhone: props.booking.clientPhone,
  status: props.booking.status,
  notes: props.booking.notes,
  paymentStatus: props.booking.paymentStatus,
  amountPaid: props.booking.amountPaid
});

const selectedPackage = computed(() => {
  return packages.value.find(pkg => pkg.id === formData.packageId);
});

const formatNumber = (num) => {
  return num.toLocaleString();
};

const fetchPackages = async () => {
  try {
    const response = await fetch('http://localhost:3000/api/admin/packages', {
      headers: {
        Authorization: `Bearer ${token.value}`
      }
    });

    if (response.ok) {
      packages.value = await response.json();
    }
  } catch (error) {
    console.error('Error fetching packages:', error);
  }
};

const handleSubmit = async () => {
  try {
    const updateForm = {
      id: props.booking.id,
      status: formData.status 
    };

    const response = await axios.post(`http://127.0.0.1:8000/api/update-booking`, updateForm);
    
    if (response.data.status === 200) {  
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Booking updated successfully'
      });
    } else {
      throw new Error(response.data.message || 'Failed to update booking');
    }
  } catch (error) {
    console.error('Error updating booking:', error);
  } finally {
    isSubmitting.value = false;
  }
};

// const handleSubmit = async () => {
//   try {
//     const updateForm = {
//       id: props.booking.id,
//       status: formData.status
//     };
//     // isSubmitting.value = true;

//     const response = await axios.post(`http://127.0.0.1:8000/api/update-booking`, updateForm);

//     console.log(response.data);

//     // if (response.ok) {
//     //   emit('update');
//     // } else {
//     //   throw new Error('Failed to update booking');
//     // }
//   } catch (error) {
//     console.error('Error updating booking:', error);
//   } finally {
//     isSubmitting.value = false;
//   }
// };

// onMounted(fetchPackages);
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

.edit-form {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.form-section {
  background: var(--card-background);
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.form-section h3 {
  font-size: 1.2rem;
  color: var(--text-color);
  margin-bottom: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.form-group label {
  font-weight: 500;
  color: var(--text-color);
}

input, select, textarea {
  padding: 0.75rem;
  border: 1px solid var(--border-color, #ddd);
  border-radius: 6px;
  font-size: 1rem;
  background: var(--input-background, #fff);
  color: var(--text-color);
}

textarea {
  resize: vertical;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}

.cancel-btn {
  padding: 0.75rem 1.5rem;
  background: var(--secondary-color, #6c757d);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.submit-btn {
  padding: 0.75rem 1.5rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .modal-content {
    padding: 1rem;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .cancel-btn, .submit-btn {
    width: 100%;
  }
}
</style> 
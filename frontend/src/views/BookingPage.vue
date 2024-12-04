<template>
  <div class="booking-page" style="margin-top: 5rem;">
    <div class="booking-container">
      <h1 style="font-weight: bold; font-size: 2rem;">Book Your Event</h1>
      
      <!-- Package Details -->
      <div class="package-details" v-if="selectedPackage">
        <img :src="selectedPackage.image" :alt="selectedPackage.name" class="package-image">
        <div class="package-info">
          <h2>{{ selectedPackage.name }}</h2>
          <p class="price">₱{{ selectedPackage.price.toLocaleString() }}</p>
          <p class="description">{{ selectedPackage.description }}</p>
        </div>
      </div>

      <!-- Booking Form -->
      <form @submit.prevent="handleSubmit" class="booking-form">
        <!-- Event Details -->
        <div class="form-section">
          <h3>Event Details</h3>
          <div class="form-grid">
            <div class="form-group">
              <label>Event Date*</label>
              <input 
                type="date" 
                v-model="formData.eventDate"
                required
                :min="minDate"
              />
            </div>
            <div class="form-group">
              <label>Event Time*</label>
              <input 
                type="time" 
                v-model="formData.eventTime"
                required
              />
            </div>
            <div class="form-group">
              <label for="guest">Guest Limitations</label>
              <select name="guest" id="guest" v-model="formData.guest_id">
                <option v-for="guest in allGuests" :key="guest.id" :value="guest.id">
                  Guest Count: {{ guest.guest_count }} - Price: ₱{{ guest.guest_price }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label for="package">Package</label>
              <select v-model="formData.package_id" class="package-select">
                <option v-for="pkg in allPackages" 
                  :key="pkg.id"
                  :value="pkg.id">
                  Package: {{ pkg.package_name }} = ₱{{ pkg.package_price }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label>Expected Guests*</label>
              <input 
                type="number" 
                v-model="formData.guestCount"
                required
                min="1"
                :max="selectedPackage?.maxGuests"
              />
            </div>
            <div class="form-group">
              <label>Venue Name*</label>
              <input 
                type="text" 
                v-model="formData.venueName"
                required
                placeholder="Enter venue name"
              />
            </div>
          </div>
        </div>

        <!-- Contact Information -->
        <div class="form-section">
          <h3>Contact Information</h3>
          <div class="form-grid">
            <div class="form-group">
              <label>Full Name*</label>
              <input 
                type="text" 
                v-model="formData.fullName"
                required
                placeholder="Enter your full name"
              />
            </div>
            <div class="form-group">
              <label>Email*</label>
              <input 
                type="email" 
                v-model="formData.email"
                required
                placeholder="Enter your email"
              />
            </div>
            <div class="form-group">
              <label>Phone Number*</label>
              <input 
                type="tel" 
                v-model="formData.phone"
                required
                placeholder="Enter your phone number"
                pattern="[0-9]{11}"
              />
            </div>
          </div>
        </div>

        <!-- Special Requests -->
        <div class="form-section">
          <h3>Additional Information</h3>
          <div class="form-group">
            <label>Special Requests</label>
            <textarea 
              v-model="formData.specialRequests"
              placeholder="Any special requests or notes for your event?"
              rows="4"
            ></textarea>
          </div>
        </div>

        <!-- Payment Method -->
        <div class="form-section">
          <h3>Payment Method</h3>
          <div class="payment-options">
            <label class="payment-option">
              <input type="radio" v-model="formData.paymentMethod" value="cash" required />
              <span class="radio-label">
                <i class="fas fa-money-bill-wave"></i>
                Cash Payment <br>
                <span class="payment-description">50% Down Payment</span>
              </span>
            </label>
          </div>
        </div>

        <div class="form-section terms">
          <label class="checkbox-label">
            <input type="checkbox" v-model="formData.termsAccepted" required />
            <span>I agree to the <a href="#" @click.prevent="showTerms">Terms and Conditions</a></span>
          </label>
        </div>

        <!-- Submit Button -->
        <div class="form-actions">
          <button type="submit" class="submit-btn" :disabled="isSubmitting">
            <LoadingAnimation v-if="isSubmitting" />
            <span v-else>Confirm Booking</span>
          </button>
        </div>
      </form>
    </div>

    <!-- Terms Modal -->
    <ConfirmationModal
      v-if="showTermsModal"
      title="Terms and Conditions"
      :message="termsAndConditions"
      confirmText="I Agree"
      @confirm="acceptTerms"
      @cancel="closeTerms"
    />

    <!-- Success Modal -->
    <AlertModal
      v-if="showSuccessModal"
      type="success"
      title="Booking Successful!"
      message="Your booking has been confirmed. You will receive a confirmation email shortly."
      confirmText="View Booking"
      @confirm="viewBooking"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import LoadingAnimation from '@/components/ui/LoadingAnimation.vue';
import ConfirmationModal from '@/components/modals/ConfirmationModal.vue';
import AlertModal from '@/components/modals/AlertModal.vue';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const { user, token } = useAuth();

const selectedPackage = ref(null);
const allGuests = ref([]);
const allPackages = ref([]);
const isSubmitting = ref(false);
const showTermsModal = ref(false);
const showSuccessModal = ref(false);

// Initialize formData with empty values
const formData = ref({
  eventDate: '',
  eventTime: '',
  guestCount: '',
  venueName: '',
  guest_id: null,
  package_id: null,
  fullName: '',
  email: '',
  phone: '',
  specialRequests: '',
  paymentMethod: '',
  termsAccepted: false
});

// Watch for user changes and update form data
watch(() => user.value, (newUser) => {
  if (newUser) {
    formData.value.fullName = newUser.first_name || '';
    formData.value.email = newUser.email || '';
    formData.value.phone = newUser.phone_number || '';
  }
}, { immediate: true });

const minDate = computed(() => {
  const today = new Date();
  today.setDate(today.getDate() + 7); // Minimum 7 days advance booking
  return today.toISOString().split('T')[0];
});

const getAllGuessst = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/get-all-guest');
    allGuests.value = response.data;
  } catch (error) {
    console.error('Error fetching guests:', error);
  }
};

const getAllPackages = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/get-all-packages');
    allPackages.value = response.data;
  } catch (error) {
    console.error('Error fetching packages:', error);
  }
};

const termsAndConditions = `
1. Booking and Payment
- A 50% down payment is required to confirm your booking
- Full payment must be made at least 3 days before the event
- Cancellations made less than 7 days before the event are non-refundable

2. Event Details
- Final guest count must be confirmed 7 days before the event
- Changes to event details must be made at least 3 days in advance
- Additional charges may apply for extended hours

3. Venue and Services
- Client is responsible for venue booking and permissions
- Package inclusions cannot be exchanged or refunded
- Additional services may be requested subject to availability
`;

onMounted(async () => {
  await getAllPackages();
  await getAllGuessst();
  
  if (route.params.packageId) {
    const pkg = allPackages.value.find(p => p.id === parseInt(route.params.packageId));
    if (pkg) {
      selectedPackage.value = pkg;
      formData.value.package_id = pkg.id;
    }
  }
});

const showTerms = () => {
  showTermsModal.value = true;
};

const closeTerms = () => {
  showTermsModal.value = false;
};

const acceptTerms = () => {
  formData.value.termsAccepted = true;
  showTermsModal.value = false;
};

const handleSubmit = async () => {
  try {
    isSubmitting.value = true;
    const bookingData = {
      ...formData.value,
      user_id: JSON.parse(localStorage.getItem('user_info')).id
    }

    const response = await axios.post('http://127.0.0.1:8000/api/add-booking', bookingData);

    console.log(response);
    
    showSuccessModal.value = true;
  } catch (error) {
    console.error('Booking error:', error);
    alert('Failed to create booking. Please try again.');
  } finally {
    isSubmitting.value = false;
  }
};

const viewBooking = () => {
  router.push('/dashboard');
};
</script>

<style scoped>
.booking-page {
  padding: 2rem;
  background-color: var(--background-color);
  min-height: 100vh;
}

.booking-container {
  max-width: 800px;
  margin: 0 auto;
  background: var(--card-background);
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  padding: 2rem;
}

h1 {
  text-align: center;
  color: var(--text-color);
  margin-bottom: 2rem;
}

.package-details {
  display: flex;
  gap: 2rem;
  margin-bottom: 2rem;
  padding: 1rem;
  background: var(--background-color);
  border-radius: 8px;
}

.package-image {
  width: 200px;
  height: 150px;
  object-fit: cover;
  border-radius: 8px;
}

.package-info {
  flex: 1;
}

.price {
  font-size: 1.5rem;
  color: #4CAF50;
  font-weight: bold;
  margin: 0.5rem 0;
}

.form-section {
  margin-bottom: 2rem;
}

.form-section h3 {
  color: var(--text-color);
  margin-bottom: 1rem;
  font-size: 1.2rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  color: var(--text-color);
  font-weight: 500;
}

input, select, textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
  background: var(--background-color);
  color: var(--text-color);
}

.payment-options {
  display: flex;
  gap: 2rem;
  margin-top: 1rem;
}

.payment-option {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.radio-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.terms {
  margin: 2rem 0;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.submit-btn {
  width: 100%;
  padding: 1rem;
  background: #4CAF50;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

.submit-btn:hover {
  background: #45a049;
}

.submit-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .booking-container {
    padding: 1rem;
  }

  .package-details {
    flex-direction: column;
  }

  .package-image {
    width: 100%;
    height: 200px;
  }

  .payment-options {
    flex-direction: column;
    gap: 1rem;
  }
}
</style>
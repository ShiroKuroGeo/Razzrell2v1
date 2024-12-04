<template>
  <div class="contact-page">
    <div class="contact-header">
      <h1>Get in Touch</h1>
      <p>We'd love to hear from you. Let us help make your event extraordinary.</p>
    </div>

    <div class="contact-grid">
      <!-- Contact Form -->
      <div class="contact-form-container">
        <form @submit.prevent="handleSubmit" class="contact-form">
          <div class="form-row">
            <div class="form-group">
              <label for="firstName">First Names</label>
              <div class="input-group">
                <i class="fas fa-user"></i>
                <input
                  type="text"
                  id="firstName"
                  v-model="values.firstName"
                  @blur="handleBlur('firstName')"
                  :class="{ 'error': getFieldError('firstName') }"
                  placeholder="Enter first name"
                />
              </div>
              <span class="error-message" v-if="getFieldError('firstName')">
                {{ getFieldError('firstName') }}
              </span>
            </div>

            <div class="form-group">
              <label for="lastName">Last Name</label>
              <div class="input-group">
                <i class="fas fa-user"></i>
                <input
                  type="text"
                  id="lastName"
                  v-model="values.lastName"
                  @blur="handleBlur('lastName')"
                  :class="{ 'error': getFieldError('lastName') }"
                  placeholder="Enter last name"
                />
              </div>
              <span class="error-message" v-if="getFieldError('lastName')">
                {{ getFieldError('lastName') }}
              </span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="email">Email</label>
              <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input
                  type="email"
                  id="email"
                  v-model="values.email"
                  @blur="handleBlur('email')"
                  :class="{ 'error': getFieldError('email') }"
                  placeholder="Enter email"
                />
              </div>
              <span class="error-message" v-if="getFieldError('email')">
                {{ getFieldError('email') }}
              </span>
            </div>

            <div class="form-group">
              <label for="phone">Phone Number</label>
              <div class="input-group">
                <i class="fas fa-phone"></i>
                <input
                  type="tel"
                  id="phone"
                  v-model="values.phone"
                  @blur="handleBlur('phone')"
                  :class="{ 'error': getFieldError('phone') }"
                  placeholder="Enter phone number"
                />
              </div>
              <span class="error-message" v-if="getFieldError('phone')">
                {{ getFieldError('phone') }}
              </span>
            </div>
          </div>

          <div class="form-group">
            <label for="eventType">Event Type</label>
            <div class="input-group">
              <i class="fas fa-calendar-alt"></i>
              <select
                id="eventType"
                v-model="values.eventType"
                @blur="handleBlur('eventType')"
                :class="{ 'error': getFieldError('eventType') }"
              >
                <option value="">Select event type</option>
                <option value="wedding">Wedding</option>
                <option value="corporate">Corporate Event</option>
                <option value="birthday">Birthday Party</option>
                <option value="social">Social Gathering</option>
                <option value="other">Other</option>
              </select>
            </div>
            <span class="error-message" v-if="getFieldError('eventType')">
              {{ getFieldError('eventType') }}
            </span>
          </div>

          <div class="form-group">
            <label for="message">Message</label>
            <div class="input-group">
              <i class="fas fa-comment-alt"></i>
              <textarea
                id="message"
                v-model="values.message"
                @blur="handleBlur('message')"
                :class="{ 'error': getFieldError('message') }"
                placeholder="Tell us about your event"
                rows="5"
              ></textarea>
            </div>
            <span class="error-message" v-if="getFieldError('message')">
              {{ getFieldError('message') }}
            </span>
          </div>

          <button
            type="submit"
            class="submit-btn"
          >
            <span>Send Message</span>
          </button>
        </form>
      </div>

      <!-- Contact Info -->
      <div class="contact-info">
        <div class="info-card">
          <div class="info-header">
            <h2>Contact Information</h2>
            <p>Get in touch with us through any of these channels</p>
          </div>

          <div class="info-items">
            <div class="info-item">
              <div class="info-icon">
                <i class="fas fa-map-marker-alt"></i>
              </div>
              <div class="info-content">
                <h3>Location</h3>
                <p>Camolinas Poblacion Cordova Cebu</p>
              </div>
            </div>

            <div class="info-item">
              <div class="info-icon">
                <i class="fas fa-phone"></i>
              </div>
              <div class="info-content">
                <h3>Phone</h3>
                <p>+63 912 345 6789</p>
                <p>+63 998 765 4321</p>
              </div>
            </div>

            <div class="info-item">
              <div class="info-icon">
                <i class="fas fa-envelope"></i>
              </div>
              <div class="info-content">
                <h3>Email</h3>
                <p>info@razzrellevents.com</p>
                <p>support@razzrellevents.com</p>
              </div>
            </div>

            <div class="info-item">
              <div class="info-icon">
                <i class="fas fa-clock"></i>
              </div>
              <div class="info-content">
                <h3>Business Hours</h3>
                <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
                <p>Saturday: 10:00 AM - 4:00 PM</p>
              </div>
            </div>
          </div>

          <div class="social-links">
            <a href="#" class="social-link">
              <i class="fab fa-facebook"></i>
            </a>
            <a href="#" class="social-link">
              <i class="fab fa-instagram"></i>
            </a>
            <a href="#" class="social-link">
              <i class="fab fa-twitter"></i>
            </a>
            <a href="#" class="social-link">
              <i class="fab fa-linkedin"></i>
            </a>
          </div>
        </div>

        <!-- Map -->
        <div class="map-container">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3925.5676766857187!2d123.95103595!3d10.25283333!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTDCsDE1JzEwLjIiTiAxMjPCsDU3JzAzLjciRQ!5e0!3m2!1sen!2sph!4v1234567890"
            width="100%"
            height="300"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
          ></iframe>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useValidation } from '@/composables/useValidation';
import { useNotifications } from '@/composables/useNotifications';
import { useAuth } from '@/composables/useAuth';
import { useLoading } from '@/composables/useLoading';
import { useApi } from '@/composables/useApi';

const { contactUs } = useAuth();
const { success, error: showError } = useNotifications();
const { post } = useApi();

const {
  values,
  errors,
  touched,
  validate,
  validateAll,
  handleBlur,
  isValid,
  getFieldError,
} = useValidation({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  eventType: '',
  message: '',
});

const validationRules = {
  firstName: ['required', ['minLength', 2]],
  lastName: ['required', ['minLength', 2]],
  email: ['required', 'email'],
  phone: ['required', 'phone'],
  eventType: ['required'],
  message: ['required', ['minLength', 20]],
};

const handleSubmit = async () => {
  if (!validateAll(validationRules)) return;

  try {
    const success = {
      first_name: values.value.firstName,
      last_name: values.value.lastName,
      email: values.value.email,
      phone: values.value.phone,
      event_type: values.value.eventType,
      message: values.value.message,
    };
    
    const response = await contactUs(success);


    success('Thank you for your message! We will get back to you soon.');
    values.value = {
      firstName: '',
      lastName: '',
      email: '',
      phone: '',
      eventType: '',
      message: '',
    };
  } catch (err) {
    showError('Failed to send message. Please try again.');
  }
};
</script>

<style scoped>
.contact-page {
  padding: 4rem 2rem;
  background: var(--background-color);
}

.contact-header {
  text-align: center;
  margin-bottom: 3rem;
}

.contact-header h1 {
  font-size: 2.5rem;
  color: var(--text-color);
  margin-bottom: 1rem;
}

.contact-header p {
  font-size: 1.2rem;
  color: var(--text-muted);
}

.contact-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.contact-form-container {
  background: var(--card-background);
  padding: 2rem;
  border-radius: 12px;
  box-shadow: var(--shadow-md);
}

.contact-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
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
}

.form-group label {
  font-weight: 500;
  color: var(--text-color);
}

.input-group {
  position: relative;
  display: flex;
  align-items: center;
}

.input-group i {
  position: absolute;
  left: 1rem;
  color: var(--text-muted);
}

.input-group input,
.input-group select,
.input-group textarea {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  font-size: 1rem;
  background: var(--input-background);
  color: var(--text-color);
  transition: border-color 0.2s;
}

.input-group textarea {
  resize: vertical;
}

.input-group input:focus,
.input-group select:focus,
.input-group textarea:focus {
  outline: none;
  border-color: var(--primary-color);
}

.input-group input.error,
.input-group select.error,
.input-group textarea.error {
  border-color: var(--error-color);
}

.error-message {
  font-size: 0.875rem;
  color: var(--error-color);
}

.submit-btn {
  padding: 0.75rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: background-color 0.2s;
}

.submit-btn:hover:not(:disabled) {
  background: var(--primary-hover);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.contact-info {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.info-card {
  background: var(--card-background);
  padding: 2rem;
  border-radius: 12px;
  box-shadow: var(--shadow-md);
}

.info-header {
  text-align: center;
  margin-bottom: 2rem;
}

.info-header h2 {
  font-size: 1.8rem;
  color: var(--text-color);
  margin-bottom: 0.5rem;
}

.info-header p {
  color: var(--text-muted);
}

.info-items {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.info-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
}

.info-icon {
  width: 40px;
  height: 40px;
  background: var(--primary-color);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.2rem;
  flex-shrink: 0;
}

.info-content h3 {
  font-size: 1.1rem;
  color: var(--text-color);
  margin-bottom: 0.25rem;
}

.info-content p {
  color: var(--text-muted);
  font-size: 0.95rem;
}

.social-links {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid var(--border-color);
}

.social-link {
  width: 40px;
  height: 40px;
  background: var(--input-background);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-color);
  font-size: 1.2rem;
  transition: all 0.3s;
  text-decoration: none;
}

.social-link:hover {
  background: var(--primary-color);
  color: white;
  transform: translateY(-2px);
}

.map-container {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow-md);
}

@media (max-width: 768px) {
  .contact-page {
    padding: 2rem 1rem;
  }

  .contact-grid {
    grid-template-columns: 1fr;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .contact-header h1 {
    font-size: 2rem;
  }

  .info-header h2 {
    font-size: 1.5rem;
  }
}
</style> 
<template>
  <div class="auth-page">
    <div class="auth-container">
      <div class="auth-card">
        <div class="auth-header">
          <router-link to="/">
            <img src="@/assets/logo2.png" alt="Razz Rell Events" class="auth-logo" />
          </router-link>
          <h1>Reset Password</h1>
          <p>Enter your email to receive password reset instructions</p>
        </div>

        <form @submit.prevent="handleSubmit" class="auth-form">
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
                placeholder="Enter your email"
                autocomplete="email"
              />
            </div>
            <span class="error-message" v-if="getFieldError('email')">
              {{ getFieldError('email') }}
            </span>
          </div>

          <button
            type="submit"
            class="submit-btn"
            :disabled="!isValid || isLoading"
          >
            <i class="fas fa-spinner fa-spin" v-if="isLoading"></i>
            <span v-else>Send Reset Link</span>
          </button>
        </form>

        <div class="auth-footer">
          <p>Remember your password?</p>
          <router-link to="/login" class="login-link">
            Back to login
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import { useValidation } from '@/composables/useValidation';
import { useNotifications } from '@/composables/useNotifications';
import { useLoading } from '@/composables/useLoading';

const router = useRouter();
const { resetPassword } = useAuth();
const { success, error: showError } = useNotifications();
const { isLoading, withLoading } = useLoading();

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
  email: '',
});

const validationRules = {
  email: ['required', 'email'],
};

const handleSubmit = async () => {
  if (!validateAll(validationRules)) return;

  try {
    const success = await withLoading('reset-password', () => 
      resetPassword(values.value.email)
    );

    if (success) {
      success('Password reset instructions have been sent to your email.');
      router.push('/login');
    }
  } catch (err) {
    showError('Failed to send reset instructions. Please try again.');
  }
};
</script>

<style scoped>
.auth-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--background-color);
  padding: 2rem;
}

.auth-container {
  width: 100%;
  max-width: 420px;
}

.auth-card {
  background: var(--card-background);
  border-radius: 12px;
  padding: 2rem;
  box-shadow: var(--shadow-lg);
}

.auth-header {
  text-align: center;
  margin-bottom: 2rem;
}

.auth-logo {
  width: 120px;
  height: auto;
  margin-bottom: 1.5rem;
}

.auth-header h1 {
  font-size: 1.8rem;
  color: var(--text-color);
  margin-bottom: 0.5rem;
}

.auth-header p {
  color: var(--text-muted);
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
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

.input-group input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  font-size: 1rem;
  background: var(--input-background);
  color: var(--text-color);
  transition: border-color 0.2s;
}

.input-group input:focus {
  outline: none;
  border-color: var(--primary-color);
}

.input-group input.error {
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
  transition: background-color 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.submit-btn:hover:not(:disabled) {
  background: var(--primary-hover);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.auth-footer {
  margin-top: 2rem;
  text-align: center;
  color: var(--text-muted);
}

.login-link {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 500;
  margin-left: 0.5rem;
}

.login-link:hover {
  text-decoration: underline;
}

@media (max-width: 480px) {
  .auth-page {
    padding: 1rem;
  }

  .auth-card {
    padding: 1.5rem;
  }
}
</style> 
<template>
  <div class="profile">
    <div class="profile-grid">
      <!-- Profile Header -->
      <div class="profile-header">
        <div class="profile-cover">
          <img :src="userProfile.coverImage || '/images/default-cover.jpg'" alt="Cover Image">
          <button class="edit-cover" @click="triggerCoverUpload">
            <i class="fas fa-camera"></i>
            <span>Change Cover</span>
          </button>
          <input 
            type="file" 
            ref="coverInput" 
            @change="handleCoverUpload" 
            accept="image/*" 
            class="hidden"
          >
        </div>
        <div class="profile-info">
          <div class="profile-avatar">
            <img :src="userProfile.avatar || '@/assets/default-profile.png'" alt="Profile Picture">
            <button class="edit-avatar" @click="triggerAvatarUpload">
              <i class="fas fa-camera"></i>
            </button>
            <input 
              type="file" 
              ref="avatarInput" 
              @change="handleAvatarUpload" 
              accept="image/*" 
              class="hidden"
            >
          </div>
          <div class="profile-details">
            <h1>{{ userProfile.fullName }}</h1>
            <p class="email">{{ userProfile.email }}</p>
            <p class="member-since">Member since {{ formatDate(userProfile.createdAt) }}</p>
          </div>
        </div>
      </div>

      <!-- Profile Content -->
      <div class="profile-content">
        <!-- Personal Information -->
        <div class="section personal-info">
          <div class="section-header">
            <h2>Personal Information</h2>
            <button @click="editMode.personal = !editMode.personal" class="edit-btn">
              <i :class="editMode.personal ? 'fas fa-times' : 'fas fa-pen'"></i>
              {{ editMode.personal ? 'Cancel' : 'Edit' }}
            </button>
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label>Full Name</label>
              <input 
                v-if="editMode.personal"
                v-model="formData.fullName"
                type="text"
                placeholder="Enter your full name"
              >
              <p v-else>{{ userProfile.fullName }}</p>
            </div>
            <div class="form-group">
              <label>Phone Number</label>
              <input 
                v-if="editMode.personal"
                v-model="formData.phone"
                type="tel"
                placeholder="Enter your phone number"
              >
              <p v-else>{{ userProfile.phone }}</p>
            </div>
            <div class="form-group">
              <label>Email</label>
              <input 
                v-if="editMode.personal"
                v-model="formData.email"
                type="email"
                placeholder="Enter your email"
              >
              <p v-else>{{ userProfile.email }}</p>
            </div>
            <div class="form-group">
              <label>Address</label>
              <textarea 
                v-if="editMode.personal"
                v-model="formData.address"
                placeholder="Enter your address"
              ></textarea>
              <p v-else>{{ userProfile.address }}</p>
            </div>
            <div v-if="editMode.personal" class="form-actions">
              <button @click="savePersonalInfo" class="btn-save">Save Changes</button>
            </div>
          </div>
        </div>

        <!-- Password & Security -->
        <div class="section security">
          <div class="section-header">
            <h2>Password & Security</h2>
            <button @click="editMode.security = !editMode.security" class="edit-btn">
              <i :class="editMode.security ? 'fas fa-times' : 'fas fa-pen'"></i>
              {{ editMode.security ? 'Cancel' : 'Edit' }}
            </button>
          </div>
          <div v-if="editMode.security" class="form-grid">
            <div class="form-group">
              <label>Current Password</label>
              <input 
                v-model="formData.currentPassword"
                type="password"
                placeholder="Enter current password"
              >
            </div>
            <div class="form-group">
              <label>New Password</label>
              <input 
                v-model="formData.newPassword"
                type="password"
                placeholder="Enter new password"
              >
            </div>
            <div class="form-group">
              <label>Confirm New Password</label>
              <input 
                v-model="formData.confirmPassword"
                type="password"
                placeholder="Confirm new password"
              >
            </div>
            <div class="form-actions">
              <button @click="changePassword" class="btn-save">Update Password</button>
            </div>
          </div>
          <div v-else class="security-info">
            <p>Last password change: {{ formatDate(userProfile.lastPasswordChange) }}</p>
            <p>Two-factor authentication: {{ userProfile.twoFactorEnabled ? 'Enabled' : 'Disabled' }}</p>
          </div>
        </div>

        <!-- Notification Settings -->
        <div class="section notifications">
          <div class="section-header">
            <h2>Notification Settings</h2>
          </div>
          <div class="notification-settings">
            <div class="setting-item">
              <div class="setting-info">
                <h3>Email Notifications</h3>
                <p>Receive booking updates and reminders via email</p>
              </div>
              <label class="switch">
                <input 
                  type="checkbox"
                  v-model="formData.emailNotifications"
                  @change="updateNotificationSettings"
                >
                <span class="slider"></span>
              </label>
            </div>
            <div class="setting-item">
              <div class="setting-info">
                <h3>SMS Notifications</h3>
                <p>Receive booking updates and reminders via SMS</p>
              </div>
              <label class="switch">
                <input 
                  type="checkbox"
                  v-model="formData.smsNotifications"
                  @change="updateNotificationSettings"
                >
                <span class="slider"></span>
              </label>
            </div>
            <div class="setting-item">
              <div class="setting-info">
                <h3>Marketing Updates</h3>
                <p>Receive promotional offers and news</p>
              </div>
              <label class="switch">
                <input 
                  type="checkbox"
                  v-model="formData.marketingUpdates"
                  @change="updateNotificationSettings"
                >
                <span class="slider"></span>
              </label>
            </div>
          </div>
        </div>

        <!-- Danger Zone -->
        <div class="section danger-zone">
          <div class="section-header">
            <h2>Danger Zone</h2>
          </div>
          <div class="danger-actions">
            <div class="danger-item">
              <div class="danger-info">
                <h3>Delete Account</h3>
                <p>Permanently delete your account and all associated data</p>
              </div>
              <button @click="confirmDeleteAccount" class="btn-danger">
                Delete Account
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <div v-if="showConfirmModal" class="modal-overlay">
      <div class="modal">
        <h2>Delete Account</h2>
        <p>Are you sure you want to delete your account? This action cannot be undone.</p>
        <div class="modal-actions">
          <button @click="showConfirmModal = false" class="btn-cancel">Cancel</button>
          <button @click="deleteAccount" class="btn-danger">Delete Account</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

const store = useStore();
const router = useRouter();

// Refs for file inputs
const avatarInput = ref(null);
const coverInput = ref(null);

// Modal state
const showConfirmModal = ref(false);

// Edit mode states
const editMode = reactive({
  personal: false,
  security: false
});

// Mock user profile data - Replace with actual API data
const userProfile = ref({
  fullName: 'John Doe',
  email: 'john.doe@example.com',
  phone: '+63 912 345 6789',
  address: '123 Main St, Manila, Philippines',
  avatar: null,
  coverImage: null,
  createdAt: '2023-01-01',
  lastPasswordChange: '2023-06-15',
  twoFactorEnabled: false
});

// Form data
const formData = reactive({
  fullName: '',
  email: '',
  phone: '',
  address: '',
  currentPassword: '',
  newPassword: '',
  confirmPassword: '',
  emailNotifications: true,
  smsNotifications: true,
  marketingUpdates: false
});

// Initialize form data with user profile
onMounted(() => {
  Object.assign(formData, {
    fullName: userProfile.value.fullName,
    email: userProfile.value.email,
    phone: userProfile.value.phone,
    address: userProfile.value.address
  });
});

// Utility functions
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

// File upload handlers
const triggerAvatarUpload = () => {
  avatarInput.value.click();
};

const triggerCoverUpload = () => {
  coverInput.value.click();
};

const handleAvatarUpload = async (event) => {
  const file = event.target.files[0];
  if (file) {
    try {
      // Upload logic here
      const formData = new FormData();
      formData.append('avatar', file);
      
      const response = await fetch('http://localhost:3000/api/profile/avatar', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${store.state.token}`
        },
        body: formData
      });

      if (response.ok) {
        const data = await response.json();
        userProfile.value.avatar = data.avatarUrl;
      }
    } catch (error) {
      console.error('Error uploading avatar:', error);
    }
  }
};

const handleCoverUpload = async (event) => {
  const file = event.target.files[0];
  if (file) {
    try {
      // Upload logic here
      const formData = new FormData();
      formData.append('cover', file);
      
      const response = await fetch('http://localhost:3000/api/profile/cover', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${store.state.token}`
        },
        body: formData
      });

      if (response.ok) {
        const data = await response.json();
        userProfile.value.coverImage = data.coverUrl;
      }
    } catch (error) {
      console.error('Error uploading cover:', error);
    }
  }
};

// Form submission handlers
const savePersonalInfo = async () => {
  try {
    const response = await fetch('http://localhost:3000/api/profile', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${store.state.token}`
      },
      body: JSON.stringify({
        fullName: formData.fullName,
        email: formData.email,
        phone: formData.phone,
        address: formData.address
      })
    });

    if (response.ok) {
      const data = await response.json();
      Object.assign(userProfile.value, data);
      editMode.personal = false;
    }
  } catch (error) {
    console.error('Error updating profile:', error);
  }
};

const changePassword = async () => {
  if (formData.newPassword !== formData.confirmPassword) {
    alert('New passwords do not match');
    return;
  }

  try {
    const response = await fetch('http://localhost:3000/api/profile/password', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${store.state.token}`
      },
      body: JSON.stringify({
        currentPassword: formData.currentPassword,
        newPassword: formData.newPassword
      })
    });

    if (response.ok) {
      editMode.security = false;
      formData.currentPassword = '';
      formData.newPassword = '';
      formData.confirmPassword = '';
      userProfile.value.lastPasswordChange = new Date().toISOString();
    }
  } catch (error) {
    console.error('Error changing password:', error);
  }
};

const updateNotificationSettings = async () => {
  try {
    const response = await fetch('http://localhost:3000/api/profile/notifications', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${store.state.token}`
      },
      body: JSON.stringify({
        emailNotifications: formData.emailNotifications,
        smsNotifications: formData.smsNotifications,
        marketingUpdates: formData.marketingUpdates
      })
    });

    if (response.ok) {
      // Settings updated successfully
    }
  } catch (error) {
    console.error('Error updating notification settings:', error);
  }
};

const confirmDeleteAccount = () => {
  showConfirmModal.value = true;
};

const deleteAccount = async () => {
  try {
    const response = await fetch('http://localhost:3000/api/profile', {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${store.state.token}`
      }
    });

    if (response.ok) {
      store.commit('logout');
      router.push('/');
    }
  } catch (error) {
    console.error('Error deleting account:', error);
  }
};
</script>

<style lang="scss" scoped>
.profile {
  padding: 2rem;
  background: var(--background-color);

  .profile-grid {
    max-width: 1200px;
    margin: 0 auto;
  }

  .profile-header {
    background: var(--card-background);
    border-radius: 15px;
    overflow: hidden;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);

    .profile-cover {
      position: relative;
      height: 300px;

      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }

      .edit-cover {
        position: absolute;
        bottom: 1rem;
        right: 1rem;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: background 0.3s ease;

        &:hover {
          background: rgba(0, 0, 0, 0.7);
        }
      }
    }

    .profile-info {
      padding: 2rem;
      position: relative;
      display: flex;
      align-items: flex-end;
      gap: 2rem;
      margin-top: -80px;

      .profile-avatar {
        position: relative;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        border: 4px solid var(--card-background);
        overflow: hidden;

        img {
          width: 100%;
          height: 100%;
          object-fit: cover;
        }

        .edit-avatar {
          position: absolute;
          bottom: 0;
          left: 0;
          right: 0;
          background: rgba(0, 0, 0, 0.5);
          color: white;
          border: none;
          padding: 0.5rem;
          cursor: pointer;
          transition: background 0.3s ease;

          &:hover {
            background: rgba(0, 0, 0, 0.7);
          }
        }
      }

      .profile-details {
        flex: 1;

        h1 {
          font-size: 2rem;
          color: var(--text-color);
          margin-bottom: 0.5rem;
        }

        .email {
          color: var(--info-dark);
          margin-bottom: 0.5rem;
        }

        .member-since {
          font-size: 0.9rem;
          color: var(--info-dark);
        }
      }
    }
  }

  .section {
    background: var(--card-background);
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;

      h2 {
        font-size: 1.5rem;
        color: var(--text-color);
      }

      .edit-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border: 1px solid var(--primary-color);
        border-radius: 8px;
        background: transparent;
        color: var(--primary-color);
        cursor: pointer;
        transition: all 0.3s ease;

        &:hover {
          background: var(--primary-color);
          color: white;
        }
      }
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;

      .form-group {
        label {
          display: block;
          font-size: 0.9rem;
          color: var(--info-dark);
          margin-bottom: 0.5rem;
        }

        input, textarea {
          width: 100%;
          padding: 0.75rem;
          border: 1px solid #ddd;
          border-radius: 8px;
          background: var(--input-background);
          color: var(--text-color);
          transition: border-color 0.3s ease;

          &:focus {
            outline: none;
            border-color: var(--primary-color);
          }
        }

        textarea {
          height: 100px;
          resize: vertical;
        }

        p {
          padding: 0.75rem;
          color: var(--text-color);
        }
      }
    }

    .form-actions {
      grid-column: 1 / -1;
      display: flex;
      justify-content: flex-end;

      .btn-save {
        padding: 0.75rem 2rem;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.3s ease;

        &:hover {
          background: darken(#7380ec, 10%);
        }
      }
    }
  }

  .notification-settings {
    .setting-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.5rem 0;
      border-bottom: 1px solid #eee;

      &:last-child {
        border-bottom: none;
        padding-bottom: 0;
      }

      .setting-info {
        h3 {
          font-size: 1.1rem;
          color: var(--text-color);
          margin-bottom: 0.5rem;
        }

        p {
          color: var(--info-dark);
          font-size: 0.9rem;
        }
      }

      .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;

        input {
          opacity: 0;
          width: 0;
          height: 0;

          &:checked + .slider {
            background-color: var(--primary-color);
          }

          &:checked + .slider:before {
            transform: translateX(26px);
          }
        }

        .slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: #ccc;
          transition: .4s;
          border-radius: 34px;

          &:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
          }
        }
      }
    }
  }

  .danger-zone {
    .danger-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.5rem;
      background: rgba(255, 119, 130, 0.1);
      border-radius: 8px;

      .danger-info {
        h3 {
          font-size: 1.1rem;
          color: var(--danger-color);
          margin-bottom: 0.5rem;
        }

        p {
          color: var(--info-dark);
          font-size: 0.9rem;
        }
      }

      .btn-danger {
        padding: 0.75rem 2rem;
        background: var(--danger-color);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.3s ease;

        &:hover {
          background: darken(#ff7782, 10%);
        }
      }
    }
  }
}

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

  .modal {
    background: var(--card-background);
    padding: 2rem;
    border-radius: 15px;
    width: 90%;
    max-width: 500px;

    h2 {
      font-size: 1.5rem;
      color: var(--danger-color);
      margin-bottom: 1rem;
    }

    p {
      color: var(--text-color);
      margin-bottom: 2rem;
    }

    .modal-actions {
      display: flex;
      gap: 1rem;
      justify-content: flex-end;

      button {
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.3s ease;

        &.btn-cancel {
          background: #eee;
          color: var(--text-color);

          &:hover {
            background: darken(#eee, 10%);
          }
        }

        &.btn-danger {
          background: var(--danger-color);
          color: white;

          &:hover {
            background: darken(#ff7782, 10%);
          }
        }
      }
    }
  }
}

.hidden {
  display: none;
}

// Responsive Design
@media (max-width: 768px) {
  .profile {
    padding: 1rem;

    .profile-header {
      .profile-info {
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 1rem;

        .profile-avatar {
          margin-top: -80px;
        }

        .profile-details {
          h1 {
            font-size: 1.5rem;
          }
        }
      }
    }

    .section {
      padding: 1.5rem;

      .form-grid {
        grid-template-columns: 1fr;
      }
    }

    .notification-settings {
      .setting-item {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
      }
    }

    .danger-zone {
      .danger-item {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
      }
    }
  }
}
</style> 
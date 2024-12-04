<template>
  <aside class="admin-sidebar">
    <div class="sidebar-header">
      <h2>Admin Panel</h2>
    </div>
    
    <nav class="sidebar-nav">
      <router-link 
        v-for="item in navigationItems" 
        :key="item.path"
        :to="item.path"
        class="nav-item"
        :class="{ active: isActive(item.path) }"
      >
        <i :class="item.icon"></i>
        <span>{{ item.name }}</span>
        <span v-if="item.badge" class="badge">{{ item.badge }}</span>
      </router-link>
    </nav>

    <div class="sidebar-footer">
      <div class="user-info">
        <img :src="user?.avatar || '/default-avatar.png'" alt="User" />
        <div>
          <p class="user-name">{{ user?.name || 'Admin User' }}</p>
          <p class="user-role">Administrator</p>
        </div>
      </div>
      <button @click="logout" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i>
        Logout
      </button>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import axios from 'axios';

const route = useRoute()
const { logout } = useAuth();
const user = JSON.parse(localStorage.getItem('user_info'));
const token = ref(localStorage.getItem('token'));
const stats = ref({
  totalBookings: 0,
});

const navigationItems = computed(() => [
  {
    name: 'Dashboard',
    path: '/admin',
    icon: 'fas fa-home'
  },
  {
    name: 'Bookings', 
    path: '/admin/bookings',
    icon: 'fas fa-calendar-alt',
    badge: stats.value.totalBookings // Now this will update reactively
  },
  {
    name: 'Packages',
    path: '/admin/packages',
    icon: 'fas fa-box'
  },
  {
    name: 'Users',
    path: '/admin/users', 
    icon: 'fas fa-users'
  },
  {
    name: 'Reports',
    path: '/admin/reports',
    icon: 'fas fa-chart-bar'
  },
  {
    name: 'Settings',
    path: '/admin/settings',
    icon: 'fas fa-cog'
  }
]);

const isActive = (path) => {
  return route.path === path
}

const fetchBookings = async () => {
  const response = await axios.get('http://127.0.0.1:8000/api/get-all-bookings', {
    headers: {
      Authorization: `Bearer ${token.value}`
    }
  });
  stats.value.totalBookings = response.data.length;
};

onMounted(async () => {
  await fetchBookings();
});
</script>

<style scoped>
.admin-sidebar {
  width: 280px;
  background: white;
  border-right: 1px solid #E5E7EB;
  position: fixed;
  top: 64px; /* Height of navbar */
  left: 0;
  bottom: 0;
  display: flex;
  flex-direction: column;
  z-index: 50;
}

.sidebar-header {
  padding: 1.5rem;
  border-bottom: 1px solid #E5E7EB;
}

.sidebar-header h2 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
}

.sidebar-nav {
  flex: 1;
  padding: 1rem 0;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 1.5rem;
  color: #4B5563;
  text-decoration: none;
  gap: 0.75rem;
}

.nav-item:hover {
  background: #F3F4F6;
}

.nav-item.active {
  background: #EEF2FF;
  color: #4F46E5;
  border-right: 3px solid #4F46E5;
}

.badge {
  margin-left: auto;
  background: #EF4444;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 9999px;
  font-size: 0.75rem;
}

.sidebar-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid #E5E7EB;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.user-info img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.user-name {
  font-weight: 500;
  color: #111827;
}

.user-role {
  font-size: 0.875rem;
  color: #6B7280;
}

.logout-btn {
  width: 100%;
  padding: 0.75rem;
  background: #EF4444;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

@media (max-width: 768px) {
  .admin-sidebar {
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }

  .admin-sidebar.open {
    transform: translateX(0);
  }
}
</style>
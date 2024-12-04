<template>
    <div class="admin-layout">
      <nav class="admin-navbar">
        <div class="navbar-brand">
          <img src="@/assets/logo.png" alt="Razz Rell Events" />
        </div>
        <div class="navbar-end">
          <button class="settings-btn">
            <i class="fas fa-cog"></i>
          </button>
          <div class="user-menu">
            <img :src="userAvatar" alt="User" />
            <i class="fas fa-chevron-down"></i>
          </div>
        </div>
      </nav>
  
      <div class="admin-container">
        <AdminSidebar />
        <main class="admin-content">
          <router-view></router-view>
        </main>
      </div>
    </div>
  </template>
  
  <script setup>
  import AdminSidebar from '@/components/admin/AdminSidebar.vue';
  import { computed } from 'vue';
  
  const userAvatar = computed(() => {
    const user = JSON.parse(localStorage.getItem('user_info'));
    return user?.avatar || '/default-avatar.png';
  });
  </script>
  
  <style scoped>
  .admin-layout {
    min-height: 60vh;
    background: #ffffff;
  }
  
  .admin-navbar {
    height: 64px;
    background: white;
    border-bottom: 1px solid #E5E7EB;
    display: flex;
    align-items: center;
    padding: 0 2rem;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 100;
  }
  
  .navbar-brand img {
    height: 40px;
  }
  
  .navbar-menu {
    display: flex;
    gap: 2rem;
    margin-left: 4rem;
  }
  
  .navbar-menu a {
    color: #4B5563;
    text-decoration: none;
    font-weight: 500;
  }
  
  .navbar-menu a:hover {
    color: #1F2937;
  }
  
  .navbar-end {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  
  .settings-btn {
    background: none;
    border: none;
    color: #4B5563;
    cursor: pointer;
    padding: 0.5rem;
  }
  
  .user-menu {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
  }
  
  .user-menu img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
  }
  
  .admin-container {
    display: flex;
    padding-top: 64px; /* Height of navbar */
    min-height: 100vh;
  }
  
  .admin-content {
    flex: 1;
    margin-left: 280px; /* Width of the sidebar */
    padding: 2rem;
    overflow-y: auto;
  }
  
  @media (max-width: 768px) {
    .navbar-menu {
      display: none;
    }
  
    .admin-content {
      margin-left: 0;
      padding: 1rem;
    }
  }
  </style>
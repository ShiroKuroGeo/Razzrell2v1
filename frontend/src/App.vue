<template>
  <div class="app">
    <nav-bar v-if="showNav" />
    <main class="main-content">
      <router-view v-slot="{ Component }">
        <transition name="page" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import NavBar from '@/components/layout/Navbar.vue';

const route = useRoute();
const showNav = computed(() => !['login', 'register'].includes(route.name));
</script>

<style>
/* CSS Reset */
*, *::before, *::after {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* CSS Variables */
:root {
  --primary: #7380ec;
  --danger: #ff7782;
  --success: #41f1b6;
  --warning: #ffbb55;
  --white: #fff;
  --info-dark: #7d8da1;
  --info-light: #dce1eb;
  --dark: #363949;
  --light: rgba(132, 139, 200, 0.18);
  --background: #f6f6f9;
  --box-shadow: 0 2rem 3rem var(--light);
}

/* Base layout */
.app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: var(--background);
  color: var(--dark);
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
}

.main-content {
  flex: 1;
  width: 100%;
  max-width: 1920px;
  margin: 0 auto;
  padding: 1rem;
}

/* Page transitions with browser prefixes */
.page-enter-active,
.page-leave-active {
  -webkit-transition: opacity 0.3s ease, transform 0.3s ease;
  -moz-transition: opacity 0.3s ease, transform 0.3s ease;
  -o-transition: opacity 0.3s ease, transform 0.3s ease;
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.page-enter-from {
  opacity: 0;
  -webkit-transform: translateX(-20px);
  -moz-transform: translateX(-20px);
  -ms-transform: translateX(-20px);
  transform: translateX(-20px);
}

.page-leave-to {
  opacity: 0;
  -webkit-transform: translateX(20px);
  -moz-transform: translateX(20px);
  -ms-transform: translateX(20px);
  transform: translateX(20px);
}

/* Media Queries */
@media screen and (max-width: 768px) {
  .main-content {
    padding: 0.5rem;
  }
}

/* Custom Scrollbar with fallback */
* {
  scrollbar-width: thin;
  scrollbar-color: var(--primary) var(--light);
}

/* Webkit scrollbar */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: var(--light);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background-color: var(--primary);
  border-radius: 4px;
  border: 2px solid var(--light);
}

/* Selection */
::selection {
  background-color: var(--primary);
  color: var(--white);
}

::-moz-selection {
  background-color: var(--primary);
  color: var(--white);
}

/* Focus styles */
:focus-visible {
  outline: 2px solid var(--primary);
  outline-offset: 2px;
}

/* Image optimization */
img {
  max-width: 100%;
  height: auto;
  display: block;
}

/* Font rendering optimization */
html {
  -webkit-text-size-adjust: 100%;
  -webkit-tap-highlight-color: transparent;
  scroll-behavior: smooth;
}

body {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-rendering: optimizeLegibility;
  line-height: 1.5;
  font-synthesis: none;
}
</style> 
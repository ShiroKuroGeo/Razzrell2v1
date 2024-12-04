import { ref, watch } from 'vue';

const THEME_KEY = 'razz_rell_theme';
const currentTheme = ref(localStorage.getItem(THEME_KEY) || 'light');

// CSS Variables for themes
const themes = {
  light: {
    '--background-color': '#f8f9fa',
    '--card-background': '#ffffff',
    '--text-color': '#2c3e50',
    '--text-muted': '#6c757d',
    '--border-color': '#dee2e6',
    '--input-background': '#ffffff',
    '--primary-color': '#3b82f6',
    '--primary-hover': '#2563eb',
    '--secondary-color': '#64748b',
    '--success-color': '#22c55e',
    '--warning-color': '#f59e0b',
    '--error-color': '#ef4444',
    '--info-color': '#3b82f6',
    '--shadow-sm': '0 1px 2px rgba(0, 0, 0, 0.05)',
    '--shadow-md': '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
    '--shadow-lg': '0 10px 15px -3px rgba(0, 0, 0, 0.1)',
  },
  dark: {
    '--background-color': '#1a1b1e',
    '--card-background': '#25262b',
    '--text-color': '#e5e7eb',
    '--text-muted': '#9ca3af',
    '--border-color': '#374151',
    '--input-background': '#374151',
    '--primary-color': '#3b82f6',
    '--primary-hover': '#2563eb',
    '--secondary-color': '#64748b',
    '--success-color': '#22c55e',
    '--warning-color': '#f59e0b',
    '--error-color': '#ef4444',
    '--info-color': '#3b82f6',
    '--shadow-sm': '0 1px 2px rgba(0, 0, 0, 0.3)',
    '--shadow-md': '0 4px 6px -1px rgba(0, 0, 0, 0.4)',
    '--shadow-lg': '0 10px 15px -3px rgba(0, 0, 0, 0.5)',
  },
};

// Apply theme variables to :root
const applyTheme = (theme) => {
  const root = document.documentElement;
  const themeVariables = themes[theme];

  Object.entries(themeVariables).forEach(([property, value]) => {
    root.style.setProperty(property, value);
  });
};

// Watch for theme changes
watch(currentTheme, (newTheme) => {
  localStorage.setItem(THEME_KEY, newTheme);
  applyTheme(newTheme);
}, { immediate: true });

export function useTheme() {
  const toggleTheme = () => {
    currentTheme.value = currentTheme.value === 'light' ? 'dark' : 'light';
  };

  const setTheme = (theme) => {
    if (themes[theme]) {
      currentTheme.value = theme;
    }
  };

  const isDarkMode = () => currentTheme.value === 'dark';

  return {
    currentTheme,
    toggleTheme,
    setTheme,
    isDarkMode,
  };
} 
<template>
  <div class="settings-page">
    <header class="page-header">
      <div class="header-content">
        <h1>Settings</h1>
        <p>Manage system settings and configurations</p>
      </div>
      <div class="header-actions">
        <button 
          class="action-btn"
          @click="exportSettings"
          :disabled="isExporting"
        >
          <i class="fas fa-file-export"></i>
          <span>Export Settings</span>
          <i class="fas fa-spinner fa-spin" v-if="isExporting"></i>
        </button>
        <button 
          class="action-btn"
          @click="$refs.importInput.click()"
          :disabled="isImporting"
        >
          <i class="fas fa-file-import"></i>
          <span>Import Settings</span>
          <i class="fas fa-spinner fa-spin" v-if="isImporting"></i>
        </button>
        <input
          type="file"
          ref="importInput"
          accept=".json"
          style="display: none"
          @change="handleImport"
        />
      </div>
    </header>

    <div class="settings-nav">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        class="nav-btn"
        :class="{ active: activeTab === tab.id }"
        @click="activeTab = tab.id"
      >
        <i :class="tab.icon"></i>
        <span>{{ tab.label }}</span>
      </button>
    </div>

    <div class="settings-content">
      <Transition name="fade" mode="out-in">
        <GeneralSettings
          v-if="activeTab === 'general'"
          v-model="settings.general"
          @save="handleSave"
        />

        <BookingSettings
          v-else-if="activeTab === 'booking'"
          v-model="settings.booking"
          @save="handleSave"
        />

        <NotificationSettings
          v-else-if="activeTab === 'notifications'"
          v-model="settings.notifications"
          @save="handleSave"
        />

        <AppearanceSettings
          v-else-if="activeTab === 'appearance'"
          v-model="settings.appearance"
          @save="handleSave"
        />

        <SecuritySettings
          v-else-if="activeTab === 'security'"
          v-model="settings.security"
          @save="handleSave"
        />

        <IntegrationsSettings
          v-else-if="activeTab === 'integrations'"
          v-model="settings.integrations"
          @save="handleSave"
        />

        <MaintenanceSettings
          v-else-if="activeTab === 'maintenance'"
          v-model="settings.maintenance"
          @save="handleSave"
        />
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useSettings } from '@/composables/useSettings';
import { useNotifications } from '@/composables/useNotifications';
import GeneralSettings from '@/components/admin/settings/GeneralSettings.vue';
import BookingSettings from '@/components/admin/settings/BookingSettings.vue';
import NotificationSettings from '@/components/admin/settings/NotificationSettings.vue';
import AppearanceSettings from '@/components/admin/settings/AppearanceSettings.vue';
import SecuritySettings from '@/components/admin/settings/SecuritySettings.vue';
import IntegrationsSettings from '@/components/admin/settings/IntegrationsSettings.vue';
import MaintenanceSettings from '@/components/admin/settings/MaintenanceSettings.vue';

const { settings, fetchSettings, exportSettings: exportSettingsData, importSettings: importSettingsData } = useSettings();
const { showNotification } = useNotifications();

const activeTab = ref('general');
const isExporting = ref(false);
const isImporting = ref(false);

const tabs = [
  { id: 'general', label: 'General', icon: 'fas fa-cog' },
  { id: 'booking', label: 'Booking', icon: 'fas fa-calendar-alt' },
  { id: 'notifications', label: 'Notifications', icon: 'fas fa-bell' },
  { id: 'appearance', label: 'Appearance', icon: 'fas fa-paint-brush' },
  { id: 'security', label: 'Security', icon: 'fas fa-shield-alt' },
  { id: 'integrations', label: 'Integrations', icon: 'fas fa-plug' },
  { id: 'maintenance', label: 'Maintenance', icon: 'fas fa-tools' }
];

const handleSave = () => {
  showNotification({
    type: 'success',
    message: 'Settings saved successfully'
  });
};

const exportSettings = async () => {
  isExporting.value = true;
  try {
    await exportSettingsData();
  } catch (error) {
    console.error('Error exporting settings:', error);
    showNotification({
      type: 'error',
      message: 'Failed to export settings'
    });
  } finally {
    isExporting.value = false;
  }
};

const handleImport = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  isImporting.value = true;
  try {
    await importSettingsData(file);
    showNotification({
      type: 'success',
      message: 'Settings imported successfully'
    });
  } catch (error) {
    console.error('Error importing settings:', error);
    showNotification({
      type: 'error',
      message: 'Failed to import settings'
    });
  } finally {
    isImporting.value = false;
    event.target.value = ''; // Reset file input
  }
};

// Initialize settings
onMounted(async () => {
  try {
    await fetchSettings();
  } catch (error) {
    console.error('Error fetching settings:', error);
    showNotification({
      type: 'error',
      message: 'Failed to load settings'
    });
  }
});
</script>

<style scoped>
.settings-page {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.header-content h1 {
  font-size: 2rem;
  color: var(--text-color);
  margin-bottom: 0.5rem;
}

.header-content p {
  color: var(--text-muted);
}

.header-actions {
  display: flex;
  gap: 1rem;
}

.action-btn {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  background: var(--background-color);
  border: 1px solid var(--border-color);
  color: var(--text-color);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.action-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.settings-nav {
  display: flex;
  gap: 0.5rem;
  padding: 0.5rem;
  background: var(--card-background);
  border: 1px solid var(--border-color);
  border-radius: 12px;
  margin-bottom: 2rem;
  overflow-x: auto;
}

.nav-btn {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  background: none;
  border: none;
  color: var(--text-muted);
  display: flex;
  align-items: center;
  gap: 0.75rem;
  white-space: nowrap;
  transition: all 0.2s ease;
}

.nav-btn:hover {
  color: var(--text-color);
  background: var(--background-color);
}

.nav-btn.active {
  color: var(--primary-color);
  background: var(--primary-color-light);
}

.settings-content {
  min-height: 400px;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@media (max-width: 768px) {
  .settings-page {
    padding: 1rem;
  }

  .page-header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }

  .header-actions {
    width: 100%;
  }

  .header-actions button {
    flex: 1;
  }

  .settings-nav {
    padding: 0.5rem;
  }

  .nav-btn {
    padding: 0.75rem;
  }

  .nav-btn span {
    display: none;
  }
}
</style> 
<template>
  <div class="packages-page">
    <header class="packages-header">
      <h1>Booking Page</h1>
      <p>Manage your bookings</p>
    </header>

    <div class="packages-grid">
      <PackageCard 
        v-for="pkg in filteredPackages" 
        :key="pkg._id"
        :package="pkg"
      />
    </div>

    <div v-if="filteredPackages.length === 0" class="no-results">
      <i class="fas fa-search"></i>
      <p>No packages found matching your criteria</p>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import PackageCard from '@/components/packages/PackageCard.vue';
import bookingCard from '@/components/packages/bookingCard.vue';
import { useApi } from '@/composables/useApi';
import { useLoading } from '@/composables/useLoading';
import { useNotifications } from '@/composables/useNotifications';

export default {
  name: 'PackagesPage',
  components: {
    bookingCard
  },
  setup() {
    const { api } = useApi();
    const { showLoading, hideLoading } = useLoading();
    const { showNotification } = useNotifications();

    const packages = ref([]);
    const searchQuery = ref('');
    const selectedType = ref('All');
    const sortBy = ref('price-asc');

    const eventTypes = ['All', 'Wedding', 'Debut', 'Christening', 'Party'];

    

    const fetchPackages = async () => {
      try {
        showLoading();
        const response = await api.get('/packages');
        packages.value = response.data;
      } catch (error) {
        showNotification('Error loading packages', 'error');
      } finally {
        hideLoading();
      }
    };

    const filteredPackages = computed(() => {
      let filtered = [...packages.value];

      // Filter by type
      if (selectedType.value !== 'All') {
        filtered = filtered.filter(pkg => pkg.eventType === selectedType.value);
      }

      // Filter by search query
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(pkg => 
          pkg.name.toLowerCase().includes(query) ||
          pkg.description.toLowerCase().includes(query)
        );
      }

      // Sort packages
      switch (sortBy.value) {
        case 'price-asc':
          filtered.sort((a, b) => a.price - b.price);
          break;
        case 'price-desc':
          filtered.sort((a, b) => b.price - a.price);
          break;
        case 'rating':
          filtered.sort((a, b) => b.rating - a.rating);
          break;
        case 'popularity':
          filtered.sort((a, b) => b.bookingsCount - a.bookingsCount);
          break;
      }

      return filtered;
    });

    const filterByType = (type) => {
      selectedType.value = type;
    };

    const filterPackages = () => {
      // Debounce implementation could be added here
    };

    const sortPackages = () => {
      // Additional sort logic if needed
    };

    onMounted(async () => {
      await fetchBookings();
    });

    return {
      packages,
      searchQuery,
      selectedType,
      sortBy,
      eventTypes,
      filteredPackages,
      filterByType,
      filterPackages,
      sortPackages
    };
  }
};
</script>

<style scoped>
.packages-page {
  padding-top: 60px;
  padding-left: 2rem;
  padding-right: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.packages-header {
  text-align: center;
  margin-bottom: 4rem;
}

.packages-header h1 {
  font-size: 2.5rem;
  color: var(--primary-color);
  margin-bottom: 1rem;
}

.packages-header p {
  font-size: 1.1rem;
  color: var(--text-secondary);
  margin-bottom: 2rem;
}

.packages-filters {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  gap: 1rem;
  flex-wrap: wrap;
}

.search-bar input {
  padding: 0.75rem 1rem;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  width: 300px;
  font-size: 1rem;
}

.filter-buttons {
  display: flex;
  gap: 0.5rem;
}

.filter-buttons button {
  padding: 0.5rem 1rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
}

.filter-buttons button.active {
  background: var(--primary-color);
  color: white;
  border-color: var(--primary-color);
}

.sort-options select {
  padding: 0.5rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  background: white;
  font-size: 0.9rem;
}

.packages-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.no-results {
  text-align: center;
  padding: 3rem;
  color: var(--text-secondary);
}

.no-results i {
  font-size: 3rem;
  margin-bottom: 1rem;
}

@media (max-width: 768px) {
  .packages-filters {
    flex-direction: column;
    align-items: stretch;
  }

  .search-bar input {
    width: 100%;
  }

  .filter-buttons {
    justify-content: center;
  }
}
</style>
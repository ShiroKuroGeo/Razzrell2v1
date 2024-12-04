<template>
  <div class="packages-management">
    <main class="management-content">
      
      <div class="page-header">
        <h1>Packages Management</h1>
        <div class="header-actions">
          <div class="search-bar">
            <i class="fas fa-search"></i>
            <input 
              type="text" 
              v-model="searchQuery" 
              placeholder="Search packages..."
              @input="handleSearch"
            />
          </div>
          <div class="filters">
            <select v-model="package_typeFilter">
              <option value="">All Event Types</option>
              <option value="Wedding">Wedding</option>
              <option value="Debut">Debut</option>
              <option value="Christening">Christening</option>
              <option value="Party">Party</option>
            </select>
            <button class="add-package-btn" @click="openAddPackageModal">
              <i class="fas fa-plus"></i>
              Add Package
            </button>
          </div>
        </div>
      </div>

      <div class="packages-grid">
        <div v-for="packageItem in filteredPackages" :key="packageItem.id" class="package-card">
          <div class="package-image">
            <img :src="packageItem.package_image ? `http://127.0.0.1:8000/storage/${packageItem.package_image}` : '/default-package.jpg'" :alt="packageItem.name" />
            <div class="package-actions">
              <button @click="editPackage(packageItem)" class="action-btn edit">
                <i class="fas fa-edit"></i>
              </button>
              <button @click="deletePackage(packageItem)" class="action-btn delete">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
          
          <div class="package-content">
            <div class="package-header">
              <h3>{{ packageItem.package_name }}</h3>
              <span class="event-type" :class="packageItem.package_type.toLowerCase()">
                {{ packageItem.package_type }}
              </span>
            </div>
            
            <div class="package-price">
              ₱{{ formatNumber(packageItem.package_price) }} <br>
              <span class="event-type" :class="packageItem.status.toLowerCase()">
                {{ packageItem.status }}
              </span>
            </div>
            
            <p class="package-description">{{ packageItem.description }}</p>
            
            <div class="package-inclusions">
              <h4>Inclusions:</h4>
              <ul>
                <li v-for="(inclusion, index) in packageItem.showAll ? JSON.parse(packageItem.package_inclusion) : JSON.parse(packageItem.package_inclusion).slice(0, 3)" 
                    :key="index">
                  {{ inclusion }}
                </li>
              </ul>
              <button 
                v-if="JSON.parse(packageItem.package_inclusion).length > 3"
                @click="packageItem.showAll = !packageItem.showAll"
                class="show-more-btn">
                {{ packageItem.showAll ? 'Show Less' : 'Show More' }}
              </button>
            </div>

            <div class="package-stats">
              <div class="stat">
                <i class="fas fa-calendar-check"></i>
                <span>{{ packageItem.bookingsCount }} Bookings</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="filteredPackages.length === 0" class="no-results">
        <i class="fas fa-box-open"></i>
        <p>No packages founds in {{ package_typeFilter }}</p>
      </div>
    </main>

    <!-- Modals -->
    <AddPackageModal
      v-if="showAddModal"
      @close="showAddModal = false"
      @success="handlePackageAdded"
    />

    <EditPackageModal
      v-if="showEditModal"
      :package="selectedPackage"
      @close="showEditModal = false"
      @update="handlePackageUpdated"
    />

    <ConfirmationModal
      v-if="showDeleteModal"
      title="Delete Package"
      message="Are you sure you want to delete this package? This action cannot be undone."
      type="danger"
      confirmText="Delete"
      @confirm="confirmDeletePackage"
      @close="showDeleteModal = false"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useAuth } from '@/composables/useAuth';
import AdminSidebar from '@/components/admin/AdminSidebar.vue';
import AddPackageModal from '@/components/admin/AddPackageModal.vue';
import EditPackageModal from '@/components/admin/EditPackageModal.vue';
import ConfirmationModal from '@/components/ui/ConfirmationModal.vue';
import axios from 'axios';

export default {
  name: 'PackagesManagement',
  components: {
    AdminSidebar,
    AddPackageModal,
    EditPackageModal,
    ConfirmationModal
  },
  setup() {
    const { token } = useAuth();

    // State
    const packages = ref([]);
    const searchQuery = ref('');
    const package_typeFilter = ref('');
    const showAddModal = ref(false);
    const showEditModal = ref(false);
    const showDeleteModal = ref(false);
    const selectedPackage = ref(null);

    // Computed
    const filteredPackages = computed(() => {
      return packages.value.filter(packageItem => {
        const matchesSearch = !searchQuery.value || 
          packageItem.full_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          packageItem.package_type.toLowerCase().includes(searchQuery.value.toLowerCase());
        
        const matchespackage_type = !package_typeFilter.value || 
          packageItem.package_type.toLowerCase() === package_typeFilter.value.toLowerCase();
        
        return matchesSearch && matchespackage_type;
      });
    });

    

    // Methods
    const fetchPackages = async () => {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/get-all-packages');

        packages.value = response.data.map(pkg => ({
          ...pkg,
          showAll: false
        }));

        // if (response.ok) {
        //   packages.value = await response.json();
        // }


      } catch (error) {
        console.error('Error fetching packages:', error);
      }
    };

    const handleSearch = () => {
      package_typeFilter.value = '';
    };

    const formatNumber = (num) => {
      return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    };

    const openAddPackageModal = () => {
      showAddModal.value = true;
    };

    const editPackage = (pkg) => {
      selectedPackage.value = pkg.id;
      showEditModal.value = true;
    };

    const deletePackage = (pkg) => {
      selectedPackage.value = pkg;
      showDeleteModal.value = true;
    };

    const confirmDeletePackage = async (id) => {
      try {
        const response = await axios.post(`http://127.0.0.1:8000/api/delete-package/${selectedPackage.value.id}`);

        alert(response.data.status);
      } catch (error) {
        console.error('Error deleting package:', error);
      }
    };

    const handlePackageAdded = async () => {
      await fetchPackages();
      showAddModal.value = false;
    };

    const handlePackageUpdated = async () => {
      await fetchPackages();
      showEditModal.value = false;
    };

    onMounted(async () => {
      await fetchPackages();
    });

    return {
      packages,
      searchQuery,
      package_typeFilter,
      showAddModal,
      showEditModal,
      showDeleteModal,
      selectedPackage,
      filteredPackages,
      handleSearch,
      formatNumber,
      openAddPackageModal,
      editPackage,
      deletePackage,
      confirmDeletePackage,
      handlePackageAdded,
      handlePackageUpdated
    };
  }
};
</script>

<style scoped>
.packages-management {
  display: flex;
  min-height: 100vh;
  background: var(--background);
}

.management-content {
  flex: 1;
  padding: 2rem;
  margin-left: 250px;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 1.8rem;
  color: var(--dark);
  margin-bottom: 1rem;
}

.header-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
  flex-wrap: wrap;
}

.search-bar {
  position: relative;
  flex: 1;
  min-width: 200px;
}

.search-bar input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid var(--light);
  border-radius: 0.5rem;
  background: var(--white);
}

.search-bar i {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--info-dark);
}

.filters {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.filters select {
  padding: 0.75rem;
  border: 1px solid var(--light);
  border-radius: 0.5rem;
  background: var(--white);
  min-width: 150px;
}

.add-package-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: var(--primary);
  color: var(--white);
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.add-package-btn:hover {
  background: var(--primary-dark);
}

.packages-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
}

.package-card {
  background: var(--white);
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: var(--box-shadow);
  transition: transform 0.2s;
}

.package-card:hover {
  transform: translateY(-5px);
}

.package-image {
  position: relative;
  height: 200px;
}

.package-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.package-actions {
  position: absolute;
  top: 1rem;
  right: 1rem;
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 50%;
  border: none;
  background: var(--white);
  color: var(--dark);
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-btn.edit:hover {
  background: var(--primary);
  color: var(--white);
}

.action-btn.delete:hover {
  background: var(--danger);
  color: var(--white);
}

.package-content {
  padding: 1.5rem;
}

.package-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.package-header h3 {
  font-size: 1.2rem;
  color: var(--dark);
}

.event-type {
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.875rem;
  font-weight: 500;
}

.event-type.wedding {
  background: #FFE2EC;
  color: #FF4081;
}

.event-type.debut {
  background: #E3F2FD;
  color: #2196F3;
}

.event-type.christening {
  background: #E8F5E9;
  color: #4CAF50;
}

.event-type.party {
  background: #FFF3E0;
  color: #FF9800;
}

.package-price {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--primary);
  margin-bottom: 1rem;
}

.package-description {
  color: var(--info-dark);
  margin-bottom: 1rem;
  line-height: 1.5;
}

.package-inclusions {
  margin-bottom: 1rem;
}

.package-inclusions h4 {
  font-size: 1rem;
  color: var(--dark);
  margin-bottom: 0.5rem;
}

.package-inclusions ul {
  list-style: none;
  padding-left: 0;
}

.package-inclusions li {
  color: var(--info-dark);
  padding: 0.25rem 0;
  display: flex;
  align-items: center;
}

.package-inclusions li::before {
  content: "•";
  color: var(--primary);
  font-weight: bold;
  margin-right: 0.5rem;
}

.package-stats {
  display: flex;
  gap: 1rem;
  padding-top: 1rem;
  border-top: 1px solid var(--light);
}

.stat {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--info-dark);
}

.stat i {
  color: var(--primary);
}

.no-results {
  text-align: center;
  padding: 3rem;
  color: var(--info-dark);
}

.no-results i {
  font-size: 3rem;
  margin-bottom: 1rem;
}

@media (max-width: 768px) {
  .management-content {
    margin-left: 0;
    padding: 1rem;
  }

  .header-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .search-bar {
    width: 100%;
  }

  .filters {
    flex-direction: column;
    width: 100%;
  }

  .filters select {
    width: 100%;
  }

  .add-package-btn {
    width: 100%;
    justify-content: center;
  }
}
</style> 
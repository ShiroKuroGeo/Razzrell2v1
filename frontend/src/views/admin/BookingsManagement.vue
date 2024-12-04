<template>
  <div class="bookings-management">
    <main class="management-content">
      <div class="page-header">
        <h1>Bookings Management</h1>
        <div class="header-actions">
          <div class="search-bar">
            <i class="fas fa-search"></i>
            <input 
              type="text" 
              v-model="searchQuery" 
              placeholder="Search bookings..."
              @input="handleSearch"
            />
          </div>
          <div class="filters">
            <select v-model="statusFilter">
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="confirmed">Confirmed</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
            <select v-model="eventTypeFilter">
              <option value="">All Event Types</option>
              <option value="wedding">Wedding</option>
              <option value="debut">Debut</option>
              <option value="christening">Christening</option>
              <option value="kiddie">Kiddie Party</option>
            </select>
            <button class="filter-btn" @click="applyFilters">
              <i class="fas fa-filter"></i>
              Apply Filters
            </button>
          </div>
        </div>
      </div>

      <div class="bookings-table-container">
        <table class="bookings-table">
          <thead>
            <tr>
              <th>
                <div class="th-content" @click="sortBy('id')">
                  ID
                  <i class="fas" :class="getSortIcon('id')"></i>
                </div>
              </th>
              <th>
                <div class="th-content" @click="sortBy('clientName')">
                  Client
                  <i class="fas" :class="getSortIcon('clientName')"></i>
                </div>
              </th>
              <th>
                <div class="th-content" @click="sortBy('eventType')">
                  Event Type
                  <i class="fas" :class="getSortIcon('eventType')"></i>
                </div>
              </th>
              <th>
                <div class="th-content" @click="sortBy('eventDate')">
                  Event Date
                  <i class="fas" :class="getSortIcon('eventDate')"></i>
                </div>
              </th>
              <th>
                <div class="th-content" @click="sortBy('status')">
                  Status
                  <i class="fas" :class="getSortIcon('status')"></i>
                </div>
              </th>
              <th>
                <div class="th-content" @click="sortBy('amount')">
                  Amount
                  <i class="fas" :class="getSortIcon('amount')"></i>
                </div>
              </th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="booking in sortedBookings" :key="booking.id">
              <td>#{{ booking.id }}</td>
              <td>
                <div class="client-info">
                  <div>
                    <span class="client-name">{{ booking.fullName }}</span>
                    <span class="client-email">{{ booking.email }}</span>
                  </div>
                </div>
              </td>
              <td>
                <span class="event-type text-uppercase" :class="booking.package.package_type">
                  {{ booking.package.package_type }}
                </span>
              </td>
              <td>
                <div class="date-info">
                  <span class="date">{{ formatDate(booking.event_date) }}</span>
                  <span class="time">{{ formatTime(booking.event_time) }}</span>
                </div>
              </td>
              <td>
                <span class="status" :class="booking.status">
                  {{ booking.status }}
                </span>
              </td>
              <td>₱{{ formatNumber(booking.guest.guest_price) }}</td>
              <td>
                <div class="action-buttons">
                  <button @click="viewBooking(booking)" class="btn-action view">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button @click="editBooking(booking)" class="btn-action edit">
                    <i class="fas fa-edit"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="pagination">
        <button 
          :disabled="currentPage === 1" 
          @click="changePage(currentPage - 1)"
          class="page-btn"
        >
          <i class="fas fa-chevron-left"></i>
        </button>
        <span class="page-info">
          Page {{ currentPage }} of {{ totalPages }}
        </span>
        <button 
          :disabled="currentPage === totalPages" 
          @click="changePage(currentPage + 1)"
          class="page-btn"
        >
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </main>

    <!-- Modals -->
    <BookingDetailsModal
      v-if="showBookingModal"
      :booking="selectedBooking"
      @close="showBookingModal = false"
    />

    <EditBookingModal
      v-if="showEditModal"
      :booking="selectedBooking"
      @close="showEditModal = false"
      @update="handleBookingUpdate"
    />

    <ConfirmationModal
      v-if="showConfirmModal"
      :title="'Cancel Booking'"
      :message="'Are you sure you want to cancel this booking? This action cannot be undone.'"
      @confirm="confirmCancelBooking"
      @close="showConfirmModal = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuth } from '@/composables/useAuth';
// import AdminSidebar from '@/components/admin/AdminSidebar.vue';
import BookingDetailsModal from '@/components/bookings/BookingDetailsModal.vue';
import EditBookingModal from '@/components/admin/EditBookingModal.vue';
import ConfirmationModal from '@/components/ui/ConfirmationModal.vue';
import axios from 'axios';

const { token } = useAuth();

// State
const bookings = ref([]);
const searchQuery = ref('');
const statusFilter = ref('');
const eventTypeFilter = ref('');
const currentPage = ref(1);
const itemsPerPage = 10;
const sortField = ref('eventDate');
const sortDirection = ref('desc');
const showBookingModal = ref(false);
const showEditModal = ref(false);
const showConfirmModal = ref(false);
const selectedBooking = ref(null);

// Computed
const filteredBookings = computed(() => {
  return bookings.value.filter(booking => {
    const matchesSearch = searchQuery.value === '' || 
      booking.clientName.includes(searchQuery.value) ||
      booking.clientEmail.includes(searchQuery.value);
    
    const matchesStatus = statusFilter.value === '' || 
      booking.status === statusFilter.value;
    
    const matchesEventType = eventTypeFilter.value === '' || 
      booking.eventType === eventTypeFilter.value;
    
    return matchesSearch && matchesStatus && matchesEventType;
  });
});

const sortedBookings = computed(() => {
  const sorted = [...filteredBookings.value].sort((a, b) => {
    if (sortField.value === 'eventDate') {
      return new Date(a[sortField.value]) - new Date(b[sortField.value]);
    }
    if (sortField.value === 'amount') {
      return a[sortField.value] - b[sortField.value];
    }
    return a[sortField.value].localeCompare(b[sortField.value]);
  });
  
  return sortDirection.value === 'desc' ? sorted.reverse() : sorted;
});

const totalPages = computed(() => {
  return Math.ceil(filteredBookings.value.length / itemsPerPage);
});

// Methods
const fetchBookings = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/get-all-bookings');
    bookings.value = response.data;
  } catch (error) {
    console.error('Error fetching bookings:', error);
  }
};

const handleSearch = () => {
  currentPage.value = 1;
};

const applyFilters = () => {
  currentPage.value = 1;
};

const sortBy = (field) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
};

const getSortIcon = (field) => {
  if (sortField.value !== field) return 'fa-sort';
  return sortDirection.value === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
};

const changePage = (page) => {
  currentPage.value = page;
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const formatTime = (time) => {
  return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-PH', {
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatNumber = (num) => {
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

const viewBooking = (booking) => {
  selectedBooking.value = booking;
  showBookingModal.value = true;
};

const editBooking = (booking) => {
  selectedBooking.value = booking;
  showEditModal.value = true;
};

const cancelBooking = (booking) => {
  selectedBooking.value = booking;
  showConfirmModal.value = true;
};

const confirmCancelBooking = async () => {
  try {
    const response = await fetch(`http://localhost:3000/api/admin/bookings/${selectedBooking.value.id}/cancel`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token.value}`
      }
    });

    if (response.ok) {
      await fetchBookings();
      showConfirmModal.value = false;
    }
  } catch (error) {
    console.error('Error cancelling booking:', error);
  }
};

const handleBookingUpdate = async () => {
  await fetchBookings();
  showEditModal.value = false;
};

onMounted(async () => {
  await fetchBookings();
});
</script>

<style scoped>
.bookings-management {
  display: flex;
  min-height: 100vh;
  background: var(--background-color);
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
  color: var(--text-color);
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

.search-bar i {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
}

.search-bar input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  font-size: 1rem;
  background: var(--input-background);
  color: var(--text-color);
}

.filters {
  display: flex;
  gap: 1rem;
  align-items: center;
  flex-wrap: wrap;
}

.filters select {
  padding: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  background: var(--input-background);
  color: var(--text-color);
}

.filter-btn {
  padding: 0.75rem 1.5rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.bookings-table-container {
  background: var(--card-background);
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow-x: auto;
  margin-bottom: 2rem;
}

.bookings-table {
  width: 100%;
  border-collapse: collapse;
}

.th-content {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

th, td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid var(--border-color);
}

th {
  font-weight: 600;
  color: var(--text-color);
  background: var(--table-header-background, rgba(0, 0, 0, 0.02));
}

.client-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.client-info img {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
}

.client-info div {
  display: flex;
  flex-direction: column;
}

.client-email {
  font-size: 0.9rem;
  color: var(--text-muted);
}

.event-type {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 500;
}

.event-type.wedding {
  background: #e8f5e9;
  color: #2e7d32;
}

.event-type.debut {
  background: #fff3e0;
  color: #ef6c00;
}

.event-type.christening {
  background: #e3f2fd;
  color: #1565c0;
}

.event-type.kiddie {
  background: #f3e5f5;
  color: #7b1fa2;
}

.date-info {
  display: flex;
  flex-direction: column;
}

.time {
  font-size: 0.9rem;
  color: var(--text-muted);
}

.status {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 500;
}

.status.pending {
  background: #fff3cd;
  color: #856404;
}

.status.confirmed {
  background: #d4edda;
  color: #155724;
}

.status.completed {
  background: #cce5ff;
  color: #004085;
}

.status.cancelled {
  background: #f8d7da;
  color: #721c24;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.btn-action {
  padding: 0.5rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  color: white;
}

.btn-action.view {
  background: var(--primary-color);
}

.btn-action.edit {
  background: var(--warning-color, #ffc107);
}

.btn-action.cancel {
  background: var(--danger-color, #dc3545);
}

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
}

.page-btn {
  padding: 0.5rem 1rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  background: var(--card-background);
  color: var(--text-color);
  cursor: pointer;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  color: var(--text-color);
}

@media (max-width: 768px) {
  .management-content {
    margin-left: 0;
    padding: 1rem;
  }

  .header-actions {
    flex-direction: column;
  }

  .search-bar {
    width: 100%;
  }

  .filters {
    width: 100%;
  }

  .filters select {
    flex: 1;
  }

  .filter-btn {
    width: 100%;
  }
}
</style> 
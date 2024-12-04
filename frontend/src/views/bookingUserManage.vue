<template>
  <div class="booking-history" style="margin-top: 5rem;">
    <main class="history-content">
      <div class="page-header">
        <h1>My Booking History</h1>
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
              <option value="preparing">Preparing</option>
              <option value="confirmed">Confirmed</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
            <select v-model="eventTypeFilter">
              <option value="">All Event Types</option>
              <option value="wedding">Wedding</option>
              <option value="debut">Debut</option>
              <option value="christening">Christening</option>
            </select>
          </div>
        </div>
      </div>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th @click="sortBy('id')">
                Booking ID
                <i :class="['fas', getSortIcon('id')]"></i>
              </th>
              <th @click="sortBy('eventType')">
                Event Type
                <i :class="['fas', getSortIcon('eventType')]"></i>
              </th>
              <th @click="sortBy('eventDate')">
                Event Date
                <i :class="['fas', getSortIcon('eventDate')]"></i>
              </th>
              <th @click="sortBy('status')">
                Status
                <i :class="['fas', getSortIcon('status')]"></i>
              </th>
              <th @click="sortBy('amount')">
                Amount
                <i :class="['fas', getSortIcon('amount')]"></i>
              </th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="booking in paginatedBookings" :key="booking.id">
              <td>BOOKING ID: #{{ booking.id }}</td>
              <td>
                <span class="event-type" :class="booking.package.package_type.toLowerCase()">
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
                <span class="status" :class="booking.status.toLowerCase()">
                  {{ booking.status }}
                </span>
              </td>
              <td>₱{{ formatNumber(booking.package.package_price) }}</td>
              <td>
                <div class="action-buttons">
                  <button @click="viewBooking(booking)" class="btn-action view">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button 
                    v-if="booking.status === 'pending'"
                    @click="cancelBooking(booking)" 
                    class="btn-action cancel"
                  >
                    <i class="fas fa-times"></i>
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
import BookingDetailsModal from '@/components/bookings/BookingDetailsModal.vue';
import ConfirmationModal from '@/components/ui/ConfirmationModal.vue';
import axios from 'axios';

const { user, token } = useAuth();

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
const showConfirmModal = ref(false);
const selectedBooking = ref(null);

// Computed
const filteredBookings = computed(() => {
  return bookings.value.filter(booking => {
    const matchesSearch = searchQuery.value === '' || 
      booking.id.toString().includes(searchQuery.value.toLowerCase());
    
    const matchesStatus = statusFilter.value === '' || 
      booking.status.toLowerCase() === statusFilter.value.toLowerCase();
    
    const matchesEventType = eventTypeFilter.value === '' || 
      booking.eventType.toLowerCase() === eventTypeFilter.value.toLowerCase();
    
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

const paginatedBookings = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return sortedBookings.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(filteredBookings.value.length / itemsPerPage);
});

// Methods
const fetchBookings = async () => {
  try {
    const userInfo = JSON.parse(localStorage.getItem('user_info'));
    const response = await axios.get(`http://127.0.0.1:8000/api/bookings/user/${userInfo.id}`);
    bookings.value = response.data.bookings;
    console.log(bookings.value);
  } catch (error) {
    console.error('Error fetching bookings:', error);
  }
};

const handleSearch = () => {
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

const cancelBooking = (booking) => {
  selectedBooking.value = booking;
  showConfirmModal.value = true;
};

const confirmCancelBooking = async () => {
  try {
    const response = await axios.post(
      `http://127.0.0.1:8000/api/bookings/${selectedBooking.value.id}/cancel`,
      {},
      {
        headers: {
          Authorization: `Bearer ${token.value}`
        }
      }
    );

    if (response.status === 200) {
      await fetchBookings();
      showConfirmModal.value = false;
    }
  } catch (error) {
    console.error('Error cancelling booking:', error);
  }
};

onMounted(async () => {
  await fetchBookings();
});
</script>

<style scoped>
.booking-history {
  padding: 2rem;
  background: var(--background-color);
  min-height: 100vh;
}

.history-content {
  max-width: 1200px;
  margin: 0 auto;
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
}

.search-bar {
  position: relative;
  flex: 1;
}

.search-bar input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  background: var(--card-background);
  color: var(--text-color);
}

.search-bar i {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
}

.filters {
  display: flex;
  gap: 1rem;
}

.filters select {
  padding: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  background: var(--card-background);
  color: var(--text-color);
  min-width: 150px;
}

.table-container {
  background: var(--card-background);
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  overflow-x: auto;
  margin-bottom: 2rem;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  padding: 1rem;
  text-align: left;
  color: var(--text-color);
  font-weight: 600;
  border-bottom: 2px solid var(--border-color);
  cursor: pointer;
  white-space: nowrap;
}

th i {
  margin-left: 0.5rem;
}

td {
  padding: 1rem;
  border-bottom: 1px solid var(--border-color);
  color: var(--text-color);
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

.btn-action.cancel {
  background: var(--danger-color);
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
  .booking-history {
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
    flex-direction: column;
  }

  .filters select {
    width: 100%;
  }
}
</style> 
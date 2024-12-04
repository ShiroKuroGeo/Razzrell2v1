<template>
  <div class="dashboard">
    <div class="dashboard-grid">
      <!-- Overview Cards -->
      <div class="overview-cards">
        <div class="card upcoming-events">
          <div class="card-icon">
            <i class="fas fa-calendar-alt"></i>
          </div>
          <div class="card-info">
            <h3>Upcoming Events</h3>
            <p class="number">{{ upcomingEvents.length }}</p>
            <p class="description">Events in next 30 days</p>
          </div>
        </div>

        <div class="card total-bookings">
          <div class="card-icon">
            <i class="fas fa-book"></i>
          </div>
          <div class="card-info">
            <h3>Total Bookings</h3>
            <p class="number">{{ totalBookings }}</p>
            <p class="description">All time bookings</p>
          </div>
        </div>

        <div class="card pending-payments">
          <div class="card-icon">
            <i class="fas fa-money-bill-wave"></i>
          </div>
          <div class="card-info">
            <h3>Pending Payments</h3>
            <p class="number">₱{{ pendingPayments.toLocaleString() }}</p>
            <p class="description">Total unpaid amount</p>
          </div>
        </div>

        <div class="card notifications">
          <div class="card-icon">
            <i class="fas fa-bell"></i>
          </div>
          <div class="card-info">
            <h3>Notifications</h3>
            <p class="number">{{ unreadNotifications }}</p>
            <p class="description">Unread notifications</p>
          </div>
        </div>
      </div>

      <!-- Upcoming Events Section -->
      <div class="section upcoming-events-list">
        <div class="section-header">
          <h2>Upcoming Events</h2>
          <router-link to="/bookings" class="view-all">View All</router-link>
        </div>
        <div class="events-grid">
          <div v-for="event in upcomingEvents" :key="event.id" class="event-card">
            <div class="event-image">
              <img :src="event.package.image" :alt="event.package.name">
              <span :class="['status-badge', event.status]">{{ event.status }}</span>
            </div>
            <div class="event-info">
              <h3>{{ event.package.name }}</h3>
              <div class="event-details">
                <p><i class="fas fa-calendar"></i> {{ formatDate(event.eventDate) }}</p>
                <p><i class="fas fa-clock"></i> {{ formatTime(event.eventTime) }}</p>
                <p><i class="fas fa-map-marker-alt"></i> {{ event.venue }}</p>
              </div>
              <div class="event-actions">
                <button @click="viewEventDetails(event)" class="btn-details">
                  View Details
                </button>
                <button 
                  v-if="event.status === 'pending'" 
                  @click="makePayment(event)"
                  class="btn-pay"
                >
                  Pay Now
                </button>
              </div>
            </div>
          </div>
          <div v-if="upcomingEvents.length === 0" class="no-events">
            <i class="fas fa-calendar-times"></i>
            <p>No upcoming events</p>
            <router-link to="/booking" class="btn-book">Book Now</router-link>
          </div>
        </div>
      </div>

      <!-- Recent Activity & Quick Actions -->
      <div class="dashboard-sidebar">
        <!-- Quick Actions -->
        <div class="section quick-actions">
          <h2>Quick Actions</h2>
          <div class="actions-grid">
            <button @click="$router.push('/booking')" class="action-btn">
              <i class="fas fa-plus"></i>
              <span>New Booking</span>
            </button>
            <button @click="$router.push('/packages')" class="action-btn">
              <i class="fas fa-box-open"></i>
              <span>View Packages</span>
            </button>
            <button @click="$router.push('/profile')" class="action-btn">
              <i class="fas fa-user-cog"></i>
              <span>Profile Settings</span>
            </button>
            <button @click="openChat" class="action-btn">
              <i class="fas fa-comments"></i>
              <span>Chat Support</span>
            </button>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="section recent-activity">
          <h2>Recent Activity</h2>
          <div class="activity-list">
            <div v-for="activity in recentActivities" :key="activity.id" class="activity-item">
              <div class="activity-icon" :class="activity.type">
                <i :class="getActivityIcon(activity.type)"></i>
              </div>
              <div class="activity-content">
                <p class="activity-text">{{ activity.text }}</p>
                <span class="activity-time">{{ formatTimeAgo(activity.timestamp) }}</span>
              </div>
            </div>
            <div v-if="recentActivities.length === 0" class="no-activity">
              <p>No recent activity</p>
            </div>
          </div>
        </div>

        <!-- Payment Summary -->
        <div class="section payment-summary">
          <h2>Payment Summary</h2>
          <div class="payment-stats">
            <div class="stat-item">
              <span class="label">Total Spent</span>
              <span class="value">₱{{ totalSpent.toLocaleString() }}</span>
            </div>
            <div class="stat-item">
              <span class="label">Pending</span>
              <span class="value">₱{{ pendingPayments.toLocaleString() }}</span>
            </div>
          </div>
          <div class="payment-chart">
            <canvas ref="paymentChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Event Details Modal -->
    <BookingDetailsModal
      v-if="selectedEvent"
      :booking="selectedEvent"
      @close="selectedEvent = null"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';
import Chart from 'chart.js/auto';
import BookingDetailsModal from '../components/bookings/BookingDetailsModal.vue';

const router = useRouter();
const store = useStore();
const paymentChartRef = ref(null);
const selectedEvent = ref(null);

// Mock data - Replace with actual API calls
const upcomingEvents = ref([
  {
    id: 1,
    package: {
      name: 'Wedding Package',
      image: '/images/wedding.jpg'
    },
    eventDate: '2024-03-15',
    eventTime: '14:00',
    venue: 'Grand Ballroom',
    status: 'confirmed'
  },
  // Add more events...
]);

const recentActivities = ref([
  {
    id: 1,
    type: 'booking',
    text: 'New booking confirmed for Wedding Package',
    timestamp: new Date(Date.now() - 3600000)
  },
  // Add more activities...
]);

const totalBookings = ref(12);
const pendingPayments = ref(25000);
const unreadNotifications = ref(3);
const totalSpent = ref(150000);

// Utility functions
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const formatTime = (time) => {
  return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-PH', {
    hour: 'numeric',
    minute: 'numeric',
    hour12: true
  });
};

const formatTimeAgo = (timestamp) => {
  const seconds = Math.floor((new Date() - new Date(timestamp)) / 1000);
  
  let interval = seconds / 31536000;
  if (interval > 1) return Math.floor(interval) + ' years ago';
  
  interval = seconds / 2592000;
  if (interval > 1) return Math.floor(interval) + ' months ago';
  
  interval = seconds / 86400;
  if (interval > 1) return Math.floor(interval) + ' days ago';
  
  interval = seconds / 3600;
  if (interval > 1) return Math.floor(interval) + ' hours ago';
  
  interval = seconds / 60;
  if (interval > 1) return Math.floor(interval) + ' minutes ago';
  
  return Math.floor(seconds) + ' seconds ago';
};

const getActivityIcon = (type) => {
  switch (type) {
    case 'booking':
      return 'fas fa-calendar-check';
    case 'payment':
      return 'fas fa-money-bill';
    case 'update':
      return 'fas fa-sync';
    default:
      return 'fas fa-bell';
  }
};

// Action handlers
const viewEventDetails = (event) => {
  selectedEvent.value = event;
};

const makePayment = (event) => {
  router.push(`/payment/${event.id}`);
};

const openChat = () => {
  store.commit('setChatOpen', true);
};

// Initialize payment chart
onMounted(() => {
  const ctx = paymentChartRef.value.getContext('2d');
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Paid', 'Pending'],
      datasets: [{
        data: [totalSpent.value, pendingPayments.value],
        backgroundColor: ['#41f1b6', '#ff7782'],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });

  // Fetch data
  fetchDashboardData();
});

const fetchDashboardData = async () => {
  try {
    const response = await fetch('http://localhost:3000/api/dashboard', {
      headers: {
        'Authorization': `Bearer ${store.state.token}`
      }
    });

    if (response.ok) {
      const data = await response.json();
      // Update reactive refs with actual data
      upcomingEvents.value = data.upcomingEvents;
      recentActivities.value = data.recentActivities;
      totalBookings.value = data.totalBookings;
      pendingPayments.value = data.pendingPayments;
      unreadNotifications.value = data.unreadNotifications;
      totalSpent.value = data.totalSpent;
    }
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
  }
};
</script>

<style lang="scss" scoped>
.dashboard {
  padding: 2rem;
  background: var(--background-color);

  .dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 2rem;
  }

  .overview-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;

    .card {
      background: var(--card-background);
      padding: 1.5rem;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      display: flex;
      align-items: center;
      gap: 1.5rem;
      transition: transform 0.3s ease;

      &:hover {
        transform: translateY(-5px);
      }

      .card-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;

        i {
          color: white;
        }
      }

      &.upcoming-events .card-icon {
        background: var(--primary-color);
      }

      &.total-bookings .card-icon {
        background: var(--success-color);
      }

      &.pending-payments .card-icon {
        background: var(--warning-color);
      }

      &.notifications .card-icon {
        background: var(--danger-color);
      }

      .card-info {
        h3 {
          font-size: 1rem;
          color: var(--text-color);
          margin-bottom: 0.5rem;
        }

        .number {
          font-size: 1.8rem;
          font-weight: 700;
          color: var(--text-color);
          margin-bottom: 0.25rem;
        }

        .description {
          font-size: 0.9rem;
          color: var(--info-dark);
        }
      }
    }
  }

  .section {
    background: var(--card-background);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;

      h2 {
        font-size: 1.25rem;
        color: var(--text-color);
      }

      .view-all {
        color: var(--primary-color);
        font-size: 0.9rem;
        text-decoration: none;

        &:hover {
          text-decoration: underline;
        }
      }
    }
  }

  .events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;

    .event-card {
      background: var(--card-background);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;

      &:hover {
        transform: translateY(-5px);
      }

      .event-image {
        position: relative;
        height: 200px;

        img {
          width: 100%;
          height: 100%;
          object-fit: cover;
        }

        .status-badge {
          position: absolute;
          top: 1rem;
          right: 1rem;
          padding: 0.5rem 1rem;
          border-radius: 20px;
          font-size: 0.85rem;
          font-weight: 500;
          text-transform: capitalize;

          &.confirmed {
            background: var(--success-color);
            color: white;
          }

          &.pending {
            background: var(--warning-color);
            color: white;
          }

          &.cancelled {
            background: var(--danger-color);
            color: white;
          }
        }
      }

      .event-info {
        padding: 1.5rem;

        h3 {
          font-size: 1.2rem;
          color: var(--text-color);
          margin-bottom: 1rem;
        }

        .event-details {
          margin-bottom: 1.5rem;

          p {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-color);
            margin-bottom: 0.5rem;

            i {
              color: var(--primary-color);
              width: 20px;
            }
          }
        }

        .event-actions {
          display: flex;
          gap: 1rem;

          button {
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;

            &.btn-details {
              background: transparent;
              border: 1px solid var(--primary-color);
              color: var(--primary-color);

              &:hover {
                background: var(--primary-color);
                color: white;
              }
            }

            &.btn-pay {
              background: var(--primary-color);
              color: white;

              &:hover {
                background: darken(#7380ec, 10%);
              }
            }
          }
        }
      }
    }

    .no-events {
      grid-column: 1 / -1;
      text-align: center;
      padding: 3rem;
      color: var(--info-dark);

      i {
        font-size: 3rem;
        margin-bottom: 1rem;
      }

      p {
        margin-bottom: 1.5rem;
      }

      .btn-book {
        display: inline-block;
        padding: 0.75rem 2rem;
        background: var(--primary-color);
        color: white;
        border-radius: 8px;
        text-decoration: none;
        transition: background 0.3s ease;

        &:hover {
          background: darken(#7380ec, 10%);
        }
      }
    }
  }

  .dashboard-sidebar {
    .quick-actions {
      .actions-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;

        .action-btn {
          display: flex;
          flex-direction: column;
          align-items: center;
          gap: 0.5rem;
          padding: 1rem;
          background: transparent;
          border: 1px solid #ddd;
          border-radius: 8px;
          cursor: pointer;
          transition: all 0.3s ease;

          i {
            font-size: 1.5rem;
            color: var(--primary-color);
          }

          span {
            font-size: 0.9rem;
            color: var(--text-color);
          }

          &:hover {
            background: rgba(115, 128, 236, 0.05);
            border-color: var(--primary-color);
          }
        }
      }
    }

    .recent-activity {
      .activity-list {
        .activity-item {
          display: flex;
          align-items: flex-start;
          gap: 1rem;
          padding: 1rem 0;
          border-bottom: 1px solid #eee;

          &:last-child {
            border-bottom: none;
          }

          .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;

            &.booking {
              background: rgba(115, 128, 236, 0.1);
              color: var(--primary-color);
            }

            &.payment {
              background: rgba(65, 241, 182, 0.1);
              color: var(--success-color);
            }

            &.update {
              background: rgba(255, 187, 85, 0.1);
              color: var(--warning-color);
            }
          }

          .activity-content {
            flex: 1;

            .activity-text {
              color: var(--text-color);
              margin-bottom: 0.25rem;
            }

            .activity-time {
              font-size: 0.85rem;
              color: var(--info-dark);
            }
          }
        }

        .no-activity {
          text-align: center;
          padding: 2rem;
          color: var(--info-dark);
        }
      }
    }

    .payment-summary {
      .payment-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;

        .stat-item {
          text-align: center;

          .label {
            display: block;
            font-size: 0.9rem;
            color: var(--info-dark);
            margin-bottom: 0.5rem;
          }

          .value {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-color);
          }
        }
      }

      .payment-chart {
        height: 200px;
      }
    }
  }
}

// Responsive Design
@media (max-width: 1200px) {
  .dashboard {
    .dashboard-grid {
      grid-template-columns: 1fr;
    }
  }
}

@media (max-width: 768px) {
  .dashboard {
    padding: 1rem;

    .overview-cards {
      grid-template-columns: 1fr;
    }

    .events-grid {
      grid-template-columns: 1fr;
    }

    .quick-actions {
      .actions-grid {
        grid-template-columns: 1fr;
      }
    }
  }
}
</style> 
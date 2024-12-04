<template>
  <div class="reports-management">
    
    <main class="management-content">
      <div class="page-header">
        <h1>Reports Management</h1>
        <div class="header-actions">
          <button class="generate-btn" @click="generateReport">
            <i class="fas fa-file-export"></i>
            Generate Report
          </button>
        </div>
      </div>

      <div class="reports-grid">
        <!-- Revenue Reports -->
        <div class="report-card">
          <div class="report-header">
            <h3>Revenue Overview</h3>
            <div class="period-selector">
              <button 
                v-for="period in periods" 
                :key="period"
                :class="['period-btn', { active: selectedPeriod === period }]"
                @click="selectedPeriod = period"
              >
                {{ period }}
              </button>
            </div>
          </div>
          
          <div class="chart-container">
            <LineChart :data="revenueData" :options="chartOptions" />
          </div>

          <div class="stats-grid">
            <div class="stat-item">
              <span class="label">Total Revenue</span>
              <span class="value">₱{{ formatNumber(stats.totalRevenue) }}</span>
              <span class="trend" :class="{ up: stats.revenueTrend > 0 }">
                <i class="fas" :class="stats.revenueTrend > 0 ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
                {{ Math.abs(stats.revenueTrend) }}%
              </span>
            </div>
            <div class="stat-item">
              <span class="label">Average Order Value</span>
              <span class="value">₱{{ formatNumber(stats.averageOrderValue) }}</span>
              <span class="trend" :class="{ up: stats.aovTrend > 0 }">
                <i class="fas" :class="stats.aovTrend > 0 ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
                {{ Math.abs(stats.aovTrend) }}%
              </span>
            </div>
          </div>
        </div>

        <!-- Bookings Reports -->
        <div class="report-card">
          <div class="report-header">
            <h3>Bookings by Event Type</h3>
            <button class="export-btn" @click="exportBookingsReport">
              <i class="fas fa-download"></i>
              Export
            </button>
          </div>
          
          <div class="chart-container">
            <DoughnutChart :data="bookingsData" :options="chartOptions" />
          </div>

          <div class="stats-grid">
            <div class="stat-item">
              <span class="label">Total Bookings</span>
              <span class="value">{{ stats.totalBookings }}</span>
              <span class="trend" :class="{ up: stats.bookingsTrend > 0 }">
                <i class="fas" :class="stats.bookingsTrend > 0 ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
                {{ Math.abs(stats.bookingsTrend) }}%
              </span>
            </div>
            <div class="stat-item">
              <span class="label">Completion Rate</span>
              <span class="value">{{ stats.completionRate }}%</span>
              <span class="trend" :class="{ up: stats.completionTrend > 0 }">
                <i class="fas" :class="stats.completionTrend > 0 ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
                {{ Math.abs(stats.completionTrend) }}%
              </span>
            </div>
          </div>
        </div>

        <!-- Customer Reports -->
        <div class="report-card">
          <div class="report-header">
            <h3>Customer Analytics</h3>
            <button class="export-btn" @click="exportCustomerReport">
              <i class="fas fa-download"></i>
              Export
            </button>
          </div>
          
          <div class="chart-container">
            <BarChart :data="customerData" :options="chartOptions" />
          </div>

          <div class="stats-grid">
            <div class="stat-item">
              <span class="label">Active Users</span>
              <span class="value">{{ stats.activeUsers }}</span>
              <span class="trend" :class="{ up: stats.usersTrend > 0 }">
                <i class="fas" :class="stats.usersTrend > 0 ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
                {{ Math.abs(stats.usersTrend) }}%
              </span>
            </div>
            <div class="stat-item">
              <span class="label">Customer Satisfaction</span>
              <span class="value">{{ stats.satisfaction }}%</span>
              <span class="trend" :class="{ up: stats.satisfactionTrend > 0 }">
                <i class="fas" :class="stats.satisfactionTrend > 0 ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
                {{ Math.abs(stats.satisfactionTrend) }}%
              </span>
            </div>
          </div>
        </div>

        <!-- Popular Packages -->
        <div class="report-card">
          <div class="report-header">
            <h3>Popular Packages</h3>
            <button class="export-btn" @click="exportPackagesReport">
              <i class="fas fa-download"></i>
              Export
            </button>
          </div>

          <div class="packages-list">
            <div v-for="pkg in popularPackages" :key="pkg.id" class="package-item">
              <div class="package-info">
                <span class="package-name">{{ pkg.name }}</span>
                <span class="event-type" :class="pkg.eventType.toLowerCase()">
                  {{ pkg.eventType }}
                </span>
              </div>
              <div class="package-stats">
                <div class="stat">
                  <span class="label">Bookings</span>
                  <span class="value">{{ pkg.bookings }}</span>
                </div>
                <div class="stat">
                  <span class="label">Revenue</span>
                  <span class="value">₱{{ formatNumber(pkg.revenue) }}</span>
                </div>
                <div class="stat">
                  <span class="label">Rating</span>
                  <span class="value">{{ pkg.rating.toFixed(1) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Report Generator Modal -->
      <div v-if="showGenerateModal" class="modal-overlay" @click.self="showGenerateModal = false">
        <div class="modal-content">
          <div class="modal-header">
            <h2>Generate Report</h2>
            <button class="close-btn" @click="showGenerateModal = false">
              <i class="fas fa-times"></i>
            </button>
          </div>

          <form @submit.prevent="handleGenerateReport" class="generate-form">
            <div class="form-group">
              <label for="reportType">Report Type</label>
              <select id="reportType" v-model="reportForm.type" required>
                <option value="revenue">Revenue Report</option>
                <option value="bookings">Bookings Report</option>
                <option value="customers">Customer Analytics</option>
                <option value="packages">Package Performance</option>
              </select>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="startDate">Start Date</label>
                <input
                  type="date"
                  id="startDate"
                  v-model="reportForm.startDate"
                  required
                />
              </div>

              <div class="form-group">
                <label for="endDate">End Date</label>
                <input
                  type="date"
                  id="endDate"
                  v-model="reportForm.endDate"
                  required
                />
              </div>
            </div>

            <div class="form-group">
              <label for="format">Export Format</label>
              <select id="format" v-model="reportForm.format" required>
                <option value="pdf">PDF</option>
                <option value="excel">Excel</option>
                <option value="csv">CSV</option>
              </select>
            </div>

            <div class="form-actions">
              <button type="button" class="cancel-btn" @click="showGenerateModal = false">
                Cancel
              </button>
              <button type="submit" class="submit-btn" :disabled="isGenerating">
                <i class="fas fa-spinner fa-spin" v-if="isGenerating"></i>
                {{ isGenerating ? 'Generating...' : 'Generate Report' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Line as LineChart, Doughnut as DoughnutChart, Bar as BarChart } from 'vue-chartjs';
import AdminSidebar from '@/components/admin/AdminSidebar.vue';
import { useAuth } from '@/composables/useAuth';

const { token } = useAuth();

// State
const periods = ['Day', 'Week', 'Month', 'Year'];
const selectedPeriod = ref('Month');
const showGenerateModal = ref(false);
const isGenerating = ref(false);
const stats = ref({
  totalRevenue: 0,
  averageOrderValue: 0,
  totalBookings: 0,
  completionRate: 0,
  activeUsers: 0,
  satisfaction: 0,
  revenueTrend: 0,
  aovTrend: 0,
  bookingsTrend: 0,
  completionTrend: 0,
  usersTrend: 0,
  satisfactionTrend: 0
});

const reportForm = ref({
  type: 'revenue',
  startDate: '',
  endDate: '',
  format: 'pdf'
});

const popularPackages = ref([]);

// Chart Data
const revenueData = ref({
  labels: [],
  datasets: [{
    label: 'Revenue',
    data: [],
    borderColor: '#4CAF50',
    tension: 0.4
  }]
});

const bookingsData = ref({
  labels: ['Wedding', 'Debut', 'Christening', 'Kiddie Party'],
  datasets: [{
    data: [],
    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
  }]
});

const customerData = ref({
  labels: [],
  datasets: [{
    label: 'New Users',
    data: [],
    backgroundColor: '#4CAF50'
  }]
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false
};

// Methods
const formatNumber = (num) => {
  return num.toLocaleString();
};

const fetchReportData = async () => {
  try {
    const response = await fetch(`http://localhost:3000/api/admin/reports/dashboard?period=${selectedPeriod.value.toLowerCase()}`, {
      headers: {
        Authorization: `Bearer ${token.value}`
      }
    });

    if (response.ok) {
      const data = await response.json();
      stats.value = data.stats;
      revenueData.value.labels = data.revenueData.labels;
      revenueData.value.datasets[0].data = data.revenueData.values;
      bookingsData.value.datasets[0].data = data.bookingsData;
      customerData.value.labels = data.customerData.labels;
      customerData.value.datasets[0].data = data.customerData.values;
      popularPackages.value = data.popularPackages;
    }
  } catch (error) {
    console.error('Error fetching report data:', error);
  }
};

const generateReport = () => {
  showGenerateModal.value = true;
};

const handleGenerateReport = async () => {
  try {
    isGenerating.value = true;

    const response = await fetch('http://localhost:3000/api/admin/reports/generate', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token.value}`
      },
      body: JSON.stringify(reportForm.value)
    });

    if (response.ok) {
      const blob = await response.blob();
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `report-${reportForm.value.type}-${reportForm.value.startDate}.${reportForm.value.format}`;
      document.body.appendChild(a);
      a.click();
      window.URL.revokeObjectURL(url);
      showGenerateModal.value = false;
    }
  } catch (error) {
    console.error('Error generating report:', error);
  } finally {
    isGenerating.value = false;
  }
};

const exportBookingsReport = () => {
  reportForm.value = {
    type: 'bookings',
    startDate: new Date().toISOString().split('T')[0],
    endDate: new Date().toISOString().split('T')[0],
    format: 'excel'
  };
  handleGenerateReport();
};

const exportCustomerReport = () => {
  reportForm.value = {
    type: 'customers',
    startDate: new Date().toISOString().split('T')[0],
    endDate: new Date().toISOString().split('T')[0],
    format: 'excel'
  };
  handleGenerateReport();
};

const exportPackagesReport = () => {
  reportForm.value = {
    type: 'packages',
    startDate: new Date().toISOString().split('T')[0],
    endDate: new Date().toISOString().split('T')[0],
    format: 'excel'
  };
  handleGenerateReport();
};

onMounted(fetchReportData);
</script>

<style scoped>
.reports-management {
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
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 1.8rem;
  color: var(--text-color);
}

.generate-btn {
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

.reports-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.report-card {
  background: var(--card-background);
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.report-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.report-header h3 {
  font-size: 1.2rem;
  color: var(--text-color);
}

.period-selector {
  display: flex;
  gap: 0.5rem;
}

.period-btn {
  padding: 0.5rem 1rem;
  background: none;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  color: var(--text-color);
  cursor: pointer;
}

.period-btn.active {
  background: var(--primary-color);
  color: white;
  border-color: var(--primary-color);
}

.export-btn {
  padding: 0.5rem 1rem;
  background: none;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  color: var(--text-color);
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.chart-container {
  height: 300px;
  margin-bottom: 1.5rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.stat-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.stat-item .label {
  font-size: 0.9rem;
  color: var(--text-muted);
}

.stat-item .value {
  font-size: 1.5rem;
  font-weight: bold;
  color: var(--text-color);
}

.trend {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.9rem;
  color: #f44336;
}

.trend.up {
  color: #4CAF50;
}

.packages-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.package-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: var(--background-color);
  border-radius: 6px;
}

.package-info {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.package-name {
  font-weight: 500;
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

.event-type.kiddie {
  background: #f3e5f5;
  color: #7b1fa2;
}

.package-stats {
  display: flex;
  gap: 2rem;
}

.package-stats .stat {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.25rem;
}

.package-stats .label {
  font-size: 0.8rem;
  color: var(--text-muted);
}

.package-stats .value {
  font-weight: 500;
  color: var(--text-color);
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: var(--modal-background);
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  padding: 2rem;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.modal-header h2 {
  font-size: 1.5rem;
  color: var(--text-color);
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: var(--text-color);
  cursor: pointer;
}

.generate-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 500;
  color: var(--text-color);
}

.form-row {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

input, select {
  padding: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  background: var(--input-background);
  color: var(--text-color);
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}

.cancel-btn {
  padding: 0.75rem 1.5rem;
  background: var(--secondary-color, #6c757d);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.submit-btn {
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

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .management-content {
    margin-left: 0;
    padding: 1rem;
  }

  .reports-grid {
    grid-template-columns: 1fr;
  }

  .package-item {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }

  .package-stats {
    width: 100%;
    justify-content: space-around;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .cancel-btn, .submit-btn {
    width: 100%;
  }
}
</style> 
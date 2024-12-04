<template>
  <div class="home">
    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-content">
        <h1>Create Unforgettable Moments</h1>
        <p>Your premier event planning partner for weddings, debuts, christenings, and kiddie parties</p>
        <div class="hero-buttons">
          <router-link to="/booking" class="btn-primary">Book Now</router-link>
          <router-link to="/packages" class="btn-secondary">View Packages</router-link>
        </div>
      </div>
    </section>

    <!-- Statistics Section -->
    <StatisticsSection />

    <!-- Featured Packages -->
    <section class="featured-packages">
      <h2>Our Event Packages</h2>
      <div class="packages-grid">
        <div v-for="package in packages" :key="package.id" class="package-card">
          <img :src="package.image" :alt="package.name">
          <div class="package-content">
            <h3>{{ package.name }}</h3>
            <p>{{ package.description }}</p>
            <p class="price">Starting at ₱{{ package.price.toLocaleString() }}</p>
            <button @click="viewPackageDetails(package)" class="btn-view">View Details</button>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <TestimonialsSection />

    <!-- FAQ Section -->
    <FaqSection />

    <!-- Contact Section -->
    <section class="contact-section">
      <div class="contact-content">
        <h2>Ready to Start Planning?</h2>
        <p>Get in touch with us and let's make your dream event a reality</p>
        <div class="contact-buttons">
          <router-link to="/contact" class="btn-contact">Contact Us</router-link>
          <a href="tel:+1234567890" class="btn-phone">
            <i class="fas fa-phone"></i> Call Now
          </a>
        </div>
      </div>
    </section>

    <!-- Package Details Modal -->
    <PackageDetailsModal
      v-if="selectedPackage"
      :package="selectedPackage"
      @close="selectedPackage = null"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import StatisticsSection from '../components/guest/StatisticsSection.vue';
import TestimonialsSection from '../components/guest/TestimonialsSection.vue';
import FaqSection from '../components/guest/FaqSection.vue';
import PackageDetailsModal from '../components/modals/PackageDetailsModal.vue';

const selectedPackage = ref(null);
const packages = ref([
  {
    id: 1,
    name: 'Wedding Package',
    description: 'Make your special day truly magical with our comprehensive wedding package.',
    price: 150000,
    image: '/images/wedding.jpg'
  },
  {
    id: 2,
    name: 'Debut Package',
    description: 'Celebrate your coming of age with an unforgettable debut party.',
    price: 100000,
    image: '/images/debut.jpg'
  },
  {
    id: 3,
    name: 'Christening Package',
    description: 'Welcome your little one with a beautiful christening celebration.',
    price: 75000,
    image: '/images/christening.jpg'
  },
  {
    id: 4,
    name: 'Party Package',
    description: 'Create magical memories for your special day.',
    price: 50000,
    image: '/images/kiddie.jpg'
  }
]);

const viewPackageDetails = (package) => {
  selectedPackage.value = package;
};
</script>

<style lang="scss" scoped>
.home {
  .hero {
    height: 100vh;
    background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('/images/hero-bg.jpg') no-repeat center center;
    background-size: cover;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white;
    margin-top: -70px; // Compensate for navbar height

    .hero-content {
      max-width: 800px;
      padding: 2rem;

      h1 {
        font-size: 3.5rem;
        margin-bottom: 1.5rem;
        font-weight: 700;
      }

      p {
        font-size: 1.2rem;
        margin-bottom: 2rem;
      }

      .hero-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;

        a {
          padding: 1rem 2rem;
          border-radius: 50px;
          font-weight: 600;
          transition: all 0.3s ease;
        }

        .btn-primary {
          background: var(--primary-color);
          color: white;

          &:hover {
            background: darken(#7380ec, 10%);
            transform: translateY(-2px);
          }
        }

        .btn-secondary {
          background: transparent;
          border: 2px solid white;
          color: white;

          &:hover {
            background: white;
            color: var(--primary-color);
            transform: translateY(-2px);
          }
        }
      }
    }
  }

  .featured-packages {
    padding: 5rem 2rem;
    background: var(--background-color);

    h2 {
      text-align: center;
      margin-bottom: 3rem;
      font-size: 2.5rem;
      color: var(--text-color);
    }

    .packages-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }

    .package-card {
      background: var(--card-background);
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;

      &:hover {
        transform: translateY(-5px);
      }

      img {
        height: 200px;
        object-fit: cover;
      }

      .package-content {
        padding: 1.5rem;

        h3 {
          font-size: 1.5rem;
          margin-bottom: 0.5rem;
          color: var(--text-color);
        }

        p {
          color: var(--text-color);
          margin-bottom: 1rem;
        }

        .price {
          font-size: 1.25rem;
          font-weight: 600;
          color: var(--primary-color);
          margin-bottom: 1.5rem;
        }

        .btn-view {
          width: 100%;
          padding: 0.75rem;
          background: var(--primary-color);
          color: white;
          border: none;
          border-radius: 8px;
          cursor: pointer;
          transition: background 0.3s ease;

          &:hover {
            background: darken(#7380ec, 10%);
          }
        }
      }
    }
  }

  .contact-section {
    padding: 5rem 2rem;
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                url('/images/contact-bg.jpg') no-repeat center center;
    background-size: cover;
    color: white;
    text-align: center;

    .contact-content {
      max-width: 600px;
      margin: 0 auto;

      h2 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
      }

      p {
        margin-bottom: 2rem;
        color: white;
      }

      .contact-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;

        a {
          padding: 1rem 2rem;
          border-radius: 50px;
          font-weight: 600;
          transition: all 0.3s ease;
        }

        .btn-contact {
          background: var(--primary-color);
          color: white;

          &:hover {
            background: darken(#7380ec, 10%);
            transform: translateY(-2px);
          }
        }

        .btn-phone {
          background: white;
          color: var(--primary-color);

          &:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
          }

          i {
            margin-right: 0.5rem;
          }
        }
      }
    }
  }
}

// Responsive Design
@media (max-width: 768px) {
  .home {
    .hero {
      .hero-content {
        h1 {
          font-size: 2.5rem;
        }

        p {
          font-size: 1rem;
        }

        .hero-buttons {
          flex-direction: column;
          
          a {
            width: 100%;
          }
        }
      }
    }

    .featured-packages {
      .packages-grid {
        grid-template-columns: 1fr;
      }
    }

    .contact-section {
      .contact-buttons {
        flex-direction: column;

        a {
          width: 100%;
        }
      }
    }
  }
}
</style> 
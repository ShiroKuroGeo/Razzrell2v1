<template>
  <div class="landing-page">
    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-content">
        <h1 class="hero-title">
          Create Unforgettables
          <span class="highlight">Events</span>
        </h1>
        <p class="hero-subtitle">
          Transform your special moments into extraordinary memories with our professional event planning services
        </p>
        <div class="hero-actions">
          <router-link to="/book" class="cta-button">
            Book Now
            <i class="fas fa-arrow-right"></i>
          </router-link>
          <button class="video-button" @click="showVideo = true">
            <i class="fas fa-play"></i>
            Watch Video
          </button>
        </div>
      </div>
      <div class="hero-image">
        <img src="@/assets/herobck.png" alt="Event Planning" />
      </div>
    </section>

    <!-- Services Section -->
    <section class="services">
      <h2 class="section-title">Our Services</h2>
      <div class="services-grid">
        <div
          v-for="service in services"
          :key="service.id"
          class="service-card"
          @mouseenter="activeService = service.id"
          @mouseleave="activeService = null"
          :class="{ active: activeService === service.id }"
        >
          <div class="service-icon">
            <i :class="service.icon"></i>
          </div>
          <h3>{{ service.title }}</h3>
          <p>{{ service.description }}</p>
          <router-link :to="service.link" class="learn-more">
            Learn More
            <i class="fas fa-chevron-right"></i>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features">
      <div class="features-content">
        <h2 class="section-title">Why Choose Us</h2>
        <div class="features-grid">
          <div
            v-for="feature in features"
            :key="feature.id"
            class="feature-item"
          >
            <div class="feature-icon">
              <i :class="feature.icon"></i>
            </div>
            <h3>{{ feature.title }}</h3>
            <p>{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery">
      <h2 class="section-title">Our Portfolio</h2>
      <div class="gallery-grid">
        <div
          v-for="image in galleryImages"
          :key="image.id"
          class="gallery-item"
          @click="openGallery(image.id)"
        >
          <img :src="image.thumbnail" :alt="image.title" />
          <div class="gallery-overlay">
            <h4>{{ image.title }}</h4>
            <p>{{ image.category }}</p>
          </div>
        </div>
      </div>
      <button class="view-all-btn" @click="$router.push('/gallery')">
        View All Projects
        <i class="fas fa-arrow-right"></i>
      </button>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
      <h2 class="section-title">What Our Clients Say</h2>
      <div class="testimonials-slider" ref="testimonialsSlider">
        <div
          v-for="testimonial in testimonials"
          :key="testimonial.id"
          class="testimonial-card"
          :class="{ active: currentTestimonial === testimonial.id }"
        >
          <div class="testimonial-content">
            <div class="quote-icon">
              <i class="fas fa-quote-right"></i>
            </div>
            <p class="testimonial-text">{{ testimonial.text }}</p>
            <div class="testimonial-author">
              <img :src="testimonial.avatar" :alt="testimonial.name" />
              <div class="author-info">
                <h4>{{ testimonial.name }}</h4>
                <p>{{ testimonial.role }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="testimonial-controls">
        <button
          class="control-btn prev"
          @click="prevTestimonial"
          :disabled="currentTestimonial === 0"
        >
          <i class="fas fa-chevron-left"></i>
        </button>
        <div class="testimonial-dots">
          <button
            v-for="(_, index) in testimonials"
            :key="index"
            class="dot"
            :class="{ active: currentTestimonial === index }"
            @click="setTestimonial(index)"
          ></button>
        </div>
        <button
          class="control-btn next"
          @click="nextTestimonial"
          :disabled="currentTestimonial === testimonials.length - 1"
        >
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
      <div class="stats-grid">
        <div
          v-for="stat in stats"
          :key="stat.id"
          class="stat-item"
        >
          <div class="stat-value">
            <span class="counter">{{ stat.value }}</span>
            <span class="suffix">{{ stat.suffix }}</span>
          </div>
          <p class="stat-label">{{ stat.label }}</p>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
      <div class="cta-content">
        <h2>Ready to Create Your Dream Event?</h2>
        <p>Let's work together to make your vision a reality</p>
        <div class="cta-actions">
          <router-link to="/contact" class="contact-button">
            Contact Us
            <i class="fas fa-envelope"></i>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Video Modal -->
    <div v-if="showVideo" class="video-modal" @click="showVideo = false">
      <div class="video-container" @click.stop>
        <button class="close-video" @click="showVideo = false">
          <i class="fas fa-times"></i>
        </button>
        <iframe
          src="https://www.youtube.com/embed/VIDEO_ID"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen
        ></iframe>
      </div>
    </div>

    <!-- Gallery Modal -->
    <div v-if="selectedImage" class="gallery-modal" @click="closeGallery">
      <div class="gallery-modal-content" @click.stop>
        <button class="close-gallery" @click="closeGallery">
          <i class="fas fa-times"></i>
        </button>
        <img :src="selectedImage.full" :alt="selectedImage.title" />
        <div class="gallery-modal-info">
          <h3>{{ selectedImage.title }}</h3>
          <p>{{ selectedImage.description }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useAuth } from '@/composables/useAuth';

const showVideo = ref(false);
const activeService = ref(null);
const currentTestimonial = ref(0);
const selectedImage = ref(null);  
let testimonialInterval;

const { isAdmin } = useAuth();

const services = [
  {
    id: 1,
    title: 'Weddings',
    description: 'Create your perfect wedding day with our comprehensive planning services.',
    icon: 'fas fa-rings',
    link: '/services/weddings',
  },
  {
    id: 2,
    title: 'Corporate Events',
    description: 'Professional event planning for conferences, meetings, and team building.',
    icon: 'fas fa-briefcase',
    link: '/services/corporate',
  },
  {
    id: 3,
    title: 'Social Gatherings',
    description: 'From birthdays to anniversaries, we make every celebration special.',
    icon: 'fas fa-glass-cheers',
    link: '/services/social',
  },
  {
    id: 4,
    title: 'Themed Parties',
    description: 'Unique and creative themed events that leave lasting impressions.',
    icon: 'fas fa-hat-wizard',
    link: '/services/themed',
  },
];

const features = [
  {
    id: 1,
    title: 'Expert Planning',
    description: 'Our experienced team ensures every detail is perfect.',
    icon: 'fas fa-clipboard-check',
  },
  {
    id: 2,
    title: 'Custom Packages',
    description: 'Tailored solutions to match your vision and budget.',
    icon: 'fas fa-box-open',
  },
  {
    id: 3,
    title: '24/7 Support',
    description: 'Always available to assist you throughout the process.',
    icon: 'fas fa-headset',
  },
  {
    id: 4,
    title: 'Quality Service',
    description: 'Premium vendors and top-notch execution.',
    icon: 'fas fa-award',
  },
];

const testimonials = [
  {
    id: 1,
    text: 'Working with Razz Rell Events was the best decision we made for our wedding. Their attention to detail and professionalism made our day perfect!',
    name: 'Sarah Johnson',
    role: 'Bride',
    avatar: '@/assets/testimonials/sarah.jpg',
  },
  {
    id: 2,
    text: 'The team went above and beyond for our corporate event. The planning was seamless, and our clients were thoroughly impressed.',
    name: 'Michael Chen',
    role: 'Marketing Director',
    avatar: '@/assets/testimonials/michael.jpg',
  },
  {
    id: 3,
    text: 'From concept to execution, they nailed every aspect of our themed party. Highly recommended for any special occasion!',
    name: 'Emily Rodriguez',
    role: 'Client',
    avatar: '@/assets/testimonials/emily.jpg',
  },
];

const stats = [
  {
    id: 1,
    value: '500',
    suffix: '+',
    label: 'Events Completed',
  },
  {
    id: 2,
    value: '98',
    suffix: '%',
    label: 'Client Satisfaction',
  },
  {
    id: 3,
    value: '15',
    suffix: '+',
    label: 'Years Experience',
  },
  {
    id: 4,
    value: '50',
    suffix: '+',
    label: 'Team Members',
  },
];

const galleryImages = [
  {
    id: 1,
    title: 'Elegant Wedding',
    category: 'Wedding',
    thumbnail: '@/assets/gallery/wedding-thumb.jpg',
    full: '@/assets/gallery/wedding-full.jpg',
    description: 'A beautiful beachside wedding ceremony.',
  },
  // Add more gallery images...
];

const nextTestimonial = () => {
  if (currentTestimonial.value < testimonials.length - 1) {
    currentTestimonial.value++;
  }
};

const prevTestimonial = () => {
  if (currentTestimonial.value > 0) {
    currentTestimonial.value--;
  }
};

const setTestimonial = (index) => {
  currentTestimonial.value = index;
};

const openGallery = (imageId) => {
  selectedImage.value = galleryImages.find(img => img.id === imageId);
};

const closeGallery = () => {
  selectedImage.value = null;
};

onMounted(() => {

  // Auto-rotate testimonials
  testimonialInterval = setInterval(() => {
    if (currentTestimonial.value < testimonials.length - 1) {
      currentTestimonial.value++;
    } else {
      currentTestimonial.value = 0;
    }
  }, 5000);
});

onBeforeUnmount(() => {
  clearInterval(testimonialInterval);
});
</script>

<style scoped>
.landing-page {
  overflow-x: hidden;
}

/* Hero Section */
.hero {
  min-height: 100vh;
  display: flex;
  align-items: center;
  padding: 2rem;
  background: linear-gradient(
    135deg,
    var(--background-color) 0%,
    var(--card-background) 100%
  );
}

.hero-content {
  flex: 1;
  max-width: 600px;
  padding: 2rem;
}

.hero-title {
  font-size: 4rem;
  line-height: 1.2;
  margin-bottom: 1.5rem;
  color: var(--text-color);
}

.highlight {
  color: var(--primary-color);
}

.hero-subtitle {
  font-size: 1.25rem;
  color: var(--text-muted);
  margin-bottom: 2rem;
}

.hero-actions {
  display: flex;
  gap: 1rem;
}

.cta-button {
  padding: 1rem 2rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.3s;
  text-decoration: none;
}

.cta-button:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
}

.video-button {
  padding: 1rem 2rem;
  background: transparent;
  color: var(--text-color);
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.3s;
}

.video-button:hover {
  background: var(--card-background);
  transform: translateY(-2px);
}

.hero-image {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
}

.hero-image img {
  max-width: 100%;
  height: auto;
  border-radius: 12px;
  box-shadow: var(--shadow-lg);
}

/* Services Section */
.services {
  padding: 6rem 2rem;
  background: var(--card-background);
}

.section-title {
  text-align: center;
  font-size: 2.5rem;
  color: var(--text-color);
  margin-bottom: 3rem;
}

.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.service-card {
  padding: 2rem;
  background: var(--background-color);
  border-radius: 12px;
  text-align: center;
  transition: all 0.3s;
}

.service-card:hover {
  transform: translateY(-10px);
  box-shadow: var(--shadow-lg);
}

.service-icon {
  width: 64px;
  height: 64px;
  margin: 0 auto 1.5rem;
  background: var(--primary-color);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
}

.service-card h3 {
  font-size: 1.5rem;
  color: var(--text-color);
  margin-bottom: 1rem;
}

.service-card p {
  color: var(--text-muted);
  margin-bottom: 1.5rem;
}

.learn-more {
  color: var(--primary-color);
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  font-weight: 500;
}

/* Features Section */
.features {
  padding: 6rem 2rem;
  background: var(--background-color);
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.feature-item {
  text-align: center;
  padding: 2rem;
}

.feature-icon {
  width: 56px;
  height: 56px;
  margin: 0 auto 1rem;
  background: var(--primary-color);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
}

.feature-item h3 {
  font-size: 1.25rem;
  color: var(--text-color);
  margin-bottom: 0.5rem;
}

.feature-item p {
  color: var(--text-muted);
}

/* Gallery Section */
.gallery {
  padding: 6rem 2rem;
  background: var(--card-background);
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  max-width: 1200px;
  margin: 0 auto 2rem;
}

.gallery-item {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
}

.gallery-item img {
  width: 100%;
  height: 300px;
  object-fit: cover;
  transition: transform 0.3s;
}

.gallery-item:hover img {
  transform: scale(1.1);
}

.gallery-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  opacity: 0;
  transition: opacity 0.3s;
}

.gallery-item:hover .gallery-overlay {
  opacity: 1;
}

.gallery-overlay h4 {
  color: white;
  font-size: 1.25rem;
  margin-bottom: 0.5rem;
}

.gallery-overlay p {
  color: rgba(255, 255, 255, 0.8);
}

.view-all-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0 auto;
  padding: 1rem 2rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: all 0.3s;
}

.view-all-btn:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
}

/* Testimonials Section */
.testimonials {
  padding: 6rem 2rem;
  background: var(--background-color);
}

.testimonials-slider {
  position: relative;
  max-width: 800px;
  margin: 0 auto;
  overflow: hidden;
}

.testimonial-card {
  position: absolute;
  width: 100%;
  opacity: 0;
  transform: translateX(100%);
  transition: all 0.5s;
}

.testimonial-card.active {
  opacity: 1;
  transform: translateX(0);
}

.testimonial-content {
  background: var(--card-background);
  padding: 2rem;
  border-radius: 12px;
  box-shadow: var(--shadow-md);
}

.quote-icon {
  color: var(--primary-color);
  font-size: 2rem;
  margin-bottom: 1rem;
}

.testimonial-text {
  font-size: 1.1rem;
  color: var(--text-color);
  margin-bottom: 1.5rem;
  line-height: 1.6;
}

.testimonial-author {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.testimonial-author img {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
}

.author-info h4 {
  color: var(--text-color);
  margin-bottom: 0.25rem;
}

.author-info p {
  color: var(--text-muted);
  font-size: 0.875rem;
}

.testimonial-controls {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-top: 2rem;
}

.control-btn {
  width: 40px;
  height: 40px;
  border: none;
  border-radius: 50%;
  background: var(--card-background);
  color: var(--text-color);
  cursor: pointer;
  transition: all 0.3s;
}

.control-btn:hover:not(:disabled) {
  background: var(--primary-color);
  color: white;
}

.control-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.testimonial-dots {
  display: flex;
  gap: 0.5rem;
}

.dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--border-color);
  border: none;
  cursor: pointer;
  transition: all 0.3s;
}

.dot.active {
  background: var(--primary-color);
  transform: scale(1.5);
}

/* Stats Section */
.stats {
  padding: 4rem 2rem;
  background: var(--primary-color);
  color: white;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  text-align: center;
}

.stat-value {
  font-size: 3rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.25rem;
}

.stat-label {
  font-size: 1.1rem;
  opacity: 0.9;
}

/* CTA Section */
.cta {
  padding: 6rem 2rem;
  background: var(--card-background);
  text-align: center;
}

.cta-content {
  max-width: 800px;
  margin: 0 auto;
}

.cta-content h2 {
  font-size: 2.5rem;
  color: var(--text-color);
  margin-bottom: 1rem;
}

.cta-content p {
  font-size: 1.25rem;
  color: var(--text-muted);
  margin-bottom: 2rem;
}

.cta-actions {
  display: flex;
  justify-content: center;
  gap: 1rem;
}

.contact-button {
  padding: 1rem 2rem;
  background: transparent;
  color: var(--text-color);
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.3s;
  text-decoration: none;
}

.contact-button:hover {
  background: var(--card-background);
  transform: translateY(-2px);
}

/* Video Modal */
.video-modal {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.video-container {
  position: relative;
  width: 90%;
  max-width: 1000px;
  aspect-ratio: 16/9;
}

.close-video {
  position: absolute;
  top: -40px;
  right: 0;
  background: none;
  border: none;
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
}

.video-container iframe {
  width: 100%;
  height: 100%;
}

/* Gallery Modal */
.gallery-modal {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.gallery-modal-content {
  position: relative;
  max-width: 90%;
  max-height: 90vh;
}

.close-gallery {
  position: absolute;
  top: -40px;
  right: 0;
  background: none;
  border: none;
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
}

.gallery-modal-content img {
  max-width: 100%;
  max-height: 80vh;
  object-fit: contain;
}

.gallery-modal-info {
  background: var(--card-background);
  padding: 1rem;
  border-radius: 0 0 12px 12px;
}

.gallery-modal-info h3 {
  color: var(--text-color);
  margin-bottom: 0.5rem;
}

.gallery-modal-info p {
  color: var(--text-muted);
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero {
    flex-direction: column;
    text-align: center;
    padding: 4rem 1rem;
  }

  .hero-content {
    padding: 0;
    margin-bottom: 2rem;
  }

  .hero-title {
    font-size: 3rem;
  }

  .hero-actions {
    justify-content: center;
  }

  .services,
  .features,
  .gallery,
  .testimonials,
  .stats,
  .cta {
    padding: 4rem 1rem;
  }

  .section-title {
    font-size: 2rem;
  }

  .cta-actions {
    flex-direction: column;
  }

  .cta-button,
  .contact-button {
    width: 100%;
    justify-content: center;
  }
}
</style> 
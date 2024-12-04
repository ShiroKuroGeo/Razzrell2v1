<template>
  <div>
    <!-- Hero Section -->
    <section class="about-hero">
      <div class="about-hero-pattern"></div>
      <div class="about-hero-content">
        <h1 class="text-4xl md:text-6xl font-bold mb-6">Creating Unforgettable Moments</h1>
        <p class="text-xl md:text-2xl opacity-90">Your premier partner in event planning and management</p>
      </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
      <div class="container mx-auto">
        <div class="about-grid">
          <div class="about-image">
            <img src="@/assets/images/about-events.jpg" alt="Event Planning" class="w-full h-full object-cover">
          </div>
          <div class="about-content">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Our Story</h2>
            <p class="text-gray-600 dark:text-gray-300 mb-4">
              Founded with a passion for creating extraordinary experiences, Razzrel Events has been transforming dreams into reality since 2020. We believe that every event tells a unique story, and we're here to help you tell yours.
            </p>
            <p class="text-gray-600 dark:text-gray-300">
              Our team of dedicated professionals brings creativity, attention to detail, and years of expertise to every project, ensuring your event is nothing short of spectacular.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Values Section -->
    <section class="bg-gray-50 dark:bg-gray-900 py-24">
      <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-16">Our Core Values</h2>
        <div class="values-grid">
          <div class="value-card" v-for="(value, index) in values" :key="index">
            <div class="value-icon">
              <i :class="value.icon"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ value.title }}</h3>
            <p class="text-gray-600 dark:text-gray-300">{{ value.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Team Section -->
    <section class="py-24">
      <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-16">Meet Our Team</h2>
        <div class="team-grid">
          <div class="team-card" v-for="(member, index) in team" :key="index">
            <div class="team-image">
              <img :src="member.image" :alt="member.name" class="w-full h-full object-cover">
              <div class="team-info">
                <h3 class="text-xl font-semibold mb-1">{{ member.name }}</h3>
                <p class="text-sm opacity-90">{{ member.position }}</p>
              </div>
              <div class="team-social">
                <a v-for="social in member.socials" 
                   :key="social.platform" 
                   :href="social.link" 
                   class="team-social-icon">
                  <i :class="social.icon"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-container">
      <div class="container mx-auto px-6">
        <div class="stats-grid">
          <div class="stat-item" v-for="(stat, index) in stats" :key="index">
            <div class="stat-number">{{ stat.value }}+</div>
            <p class="text-lg mt-2 opacity-90">{{ stat.label }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Mission Section -->
    <section class="mission-container">
      <div class="mission-bg"></div>
      <div class="mission-content">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Our Mission</h2>
        <p class="mission-quote">
          "To create extraordinary experiences that exceed expectations and leave lasting impressions, one event at a time."
        </p>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'AboutUs',
  data() {
    return {
      values: [
        {
          icon: 'fas fa-star',
          title: 'Excellence',
          description: 'We strive for perfection in every detail, ensuring each event exceeds expectations.'
        },
        {
          icon: 'fas fa-heart',
          title: 'Passion',
          description: 'Our love for what we do drives us to create unique and memorable experiences.'
        },
        {
          icon: 'fas fa-handshake',
          title: 'Integrity',
          description: 'We build trust through transparency, honesty, and reliability in all our dealings.'
        }
      ],
      team: [
        {
          name: 'Sarah Johnson',
          position: 'Creative Director',
          image: '@/assets/images/team/sarah.jpg',
          socials: [
            { platform: 'linkedin', icon: 'fab fa-linkedin', link: '#' },
            { platform: 'twitter', icon: 'fab fa-twitter', link: '#' }
          ]
        },
        {
          name: 'Michael Chen',
          position: 'Event Manager',
          image: '@/assets/images/team/michael.jpg',
          socials: [
            { platform: 'linkedin', icon: 'fab fa-linkedin', link: '#' },
            { platform: 'instagram', icon: 'fab fa-instagram', link: '#' }
          ]
        },
        {
          name: 'Emma Davis',
          position: 'Design Specialist',
          image: '@/assets/images/team/emma.jpg',
          socials: [
            { platform: 'linkedin', icon: 'fab fa-linkedin', link: '#' },
            { platform: 'behance', icon: 'fab fa-behance', link: '#' }
          ]
        },
        {
          name: 'David Wilson',
          position: 'Technical Director',
          image: '@/assets/images/team/david.jpg',
          socials: [
            { platform: 'linkedin', icon: 'fab fa-linkedin', link: '#' },
            { platform: 'github', icon: 'fab fa-github', link: '#' }
          ]
        }
      ],
      stats: [
        { value: '500', label: 'Events Completed' },
        { value: '50', label: 'Team Members' },
        { value: '100', label: 'Partner Venues' },
        { value: '1000', label: 'Happy Clients' }
      ]
    }
  }
}
</script>

<style scoped>
/* Any additional component-specific styles can go here */
.container {
  max-width: 1280px;
}

/* Add Font Awesome CDN in your index.html */
/* <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> */
</style> 
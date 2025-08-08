<template>
  <div class="page-body">
    <!-- Reusable Header -->
    <Header 
      :is-logged-in="isLoggedIn" 
      :user="user" 
      @request-login="$emit('request-login')" 
      @logout="$emit('logout')" 
    />

    <!-- Main Content Area -->
    <main class="main-content">
      <div v-if="user" class="profile-container">
        <div class="profile-header">
          <div class="profile-avatar">
            {{ userInitial }}
          </div>
          <h1 class="username">{{ user.name }}</h1>
          <span class="user-role">{{ user.role }}</span>
        </div>
        <div class="profile-details">
          <h2 class="details-title">Informasi Akun</h2>
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">Username</span>
              <span class="info-value">{{ user.username }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Email</span>
              <span class="info-value">{{ user.email || 'Not Provided' }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Role</span>
              <span class="info-value">{{ user.role }}</span>
            </div>
            <!-- <div class="info-item">
              <span class="info-label">Member Since</span>
              <span class="info-value">{{ formattedJoinDate }}</span>
            </div> -->
          </div>
        </div>
      </div>
      <div v-else class="loading-container">
        <p>Loading user profile...</p>
      </div>
    </main>
  </div>
</template>

<script>
import Header from './Header.vue';

export default {
  components: {
    Header
  },
  props: {
    isLoggedIn: Boolean,
    user: Object
  },
  computed: {
    userInitial() {
      return this.user && this.user.username ? this.user.username.charAt(0).toUpperCase() : '?';
    },
    formattedJoinDate() {
      if (!this.user || !this.user.created_at) {
        return 'N/A';
      }
      // Format the date to be more readable, e.g., "July 21, 2025"
      const date = new Date(this.user.created_at);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    }
  }
}
</script>

<style scoped>
.page-body {
  background-color: #1F3D7B;
  font-family: 'Figtree', sans-serif;
  min-height: 100vh;
}
.main-content {
  padding: 3rem 1rem;
}
.profile-container {
  max-width: 700px;
  margin: 0 auto;
  background-color: #ffffff;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
  overflow: hidden;
}
.profile-header {
  background-color: #f8f9fa;
  padding: 2rem;
  text-align: center;
  border-bottom: 1px solid #dee2e6;
}
.profile-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background-color: #1F3D7B;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  font-weight: 600;
  margin: 0 auto 1rem auto;
  border: 4px solid white;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
.username {
  margin: 0;
  font-size: 1.75rem;
  color: #333;
}
.user-role {
  display: inline-block;
  margin-top: 0.5rem;
  background-color: #e9ecef;
  color: #495057;
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.9rem;
  font-weight: 500;
  text-transform: capitalize;
}
.profile-details {
  padding: 2rem;
}
.details-title {
  margin: 0 0 1.5rem 0;
  font-size: 1.25rem;
  border-bottom: 1px solid #eee;
  padding-bottom: 0.75rem;
}
.info-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}
.info-item {
  display: flex;
  flex-direction: column;
}
.info-label {
  font-size: 0.875rem;
  color: #6c757d;
  margin-bottom: 0.25rem;
}
.info-value {
  font-size: 1rem;
  color: #212529;
  font-weight: 500;
}
.loading-container {
  text-align: center;
  color: white;
  padding: 3rem;
}

@media (max-width: 768px) {
  .details-title{
    margin-bottom: 0rem;
  }
  .profile-container {
    padding: 1rem;
    border-radius: 0px;
    box-shadow: none;
  }
  .profile-header{
    padding-top: 1rem;
    padding-bottom: 1rem;
  }
  .profile-details {
    padding-top: 1rem;
    padding-bottom: 3rem;
  }
  .profile-avatar {
    width: 80px;
    height: 80px;
    font-size: 2rem;
  }
  .username {
    font-size: 1.3rem;
  }
  .main-content {
    padding:0rem;
  }
  .page-body {
    background-color:#ffffff;
  }
  .info-grid{
    gap: 0.5rem
  }
}
</style>
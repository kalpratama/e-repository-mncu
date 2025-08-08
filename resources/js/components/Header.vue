<template>
  <!-- Top Header -->
  <header class="top-header">
      <div class="header-content">
        <router-link to="/" class="logo-container">
          <img :src="logo" alt="MNCU Logo" class="company-logo">
        </router-link>
        <div class="communities-link">
          <span>Communities in MNCU-IR</span>
        </div>
        <div>
          <!-- Conditionally show Login Button or Profile Bubble -->
          <button v-if="!isLoggedIn" @click="$emit('request-login')" class="login-button">Login</button>
          <ProfileBubble v-else :user="user" @logout="$emit('logout')" />
        </div>
      </div>
    </header>
</template>

<script>
import logo from '../assets/mncu_logo_wide.png';
import ProfileCircle from './ProfileCircle.vue';

export default {
  name: 'Header',
  components: {
    ProfileBubble: ProfileCircle
  },
  props: {
    isLoggedIn: {
      type: Boolean,
      default: false
    },
    user: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      logo: logo
    }
  }
}
</script>

<style scoped>
/* --- Top Header --- */
.top-header {
  background-color: #ffffff;
  padding-top: .5rem;
  padding-bottom: .5rem;
  padding-left: 5rem;
  padding-right: 5rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  border-bottom: 1px solid #e0e0e0;
  position: sticky;
  top: 0;
  z-index: 1000;
}

.header-content {
  display: flex;
  justify-content: space-between;
  gap: 2rem;
  align-items: center;
  max-width: 1400px;
  /* margin-left: 3rem;
  margin-right: 3rem; */
}

.logo-container {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 600;
  font-size: 1.1rem;
  /* margin-left: 3rem; */
}

.company-logo {
  max-height: 57px;
  width: auto;
}

.communities-link {
  font-weight: 900;
  font-size: 1.5rem;
}

button.login-button {
  width: 100%;
  padding: 0.9rem;
  background-color: #1F3D7B;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
  transition: background-color 0.2s;
}

button.login-button:hover {
  background-color: #0056b3;
}

@media (max-width: 768px) {
  .top-header {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  .header-content {
    flex-direction: row;
    gap: 1rem;
  }
  .communities-link {
    display: none;
  }
  .company-logo {
    max-width: 80%;
    height: auto;
  }
}
</style>

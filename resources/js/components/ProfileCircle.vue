<template>
  <div class="dropdown">
    <button class="profile-bubble">
      {{ userInitial }}
    </button>
    <div class="dropdown-menu">
      <div class="dropdown-header">
        Halo,<br><strong>{{ user.name }}!</strong>
      </div>
       
      <router-link to="/profile" class="dropdown-item">Profil</router-link>
      <router-link v-if="user && user.role === 'admin'" to="/admin/articles/create" class="admin-button">
        Unggah Baru
      </router-link>
      <router-link v-if="user && user.role === 'admin'" to="/admin/users/manage" class="admin-button">
        Kelola Pengguna
      </router-link>
      <div class="dropdown-divider"></div>
      <button v-if="user && user.role === 'admin'" class="admin-button" @click="toggleDebug">Debug
      </button>
      <a href="#" @click.prevent="$emit('logout')" class="dropdown-item logout">Logout</a>
      
    </div>
  </div>
</template>

<script>
export default {
  props: {
    user: {
      type: Object,
      required: true
    }
  },
  computed: {
    // Get the first letter of the user's name for the bubble
    userInitial() {
      return this.user.username ? this.user.username.charAt(0).toUpperCase() : '?';
    },
    userName() {
      return this.user.username || 'User';
    },
    realName() {
      return this.user.name || 'User';
    }
  },
  methods:{
    toggleDebug() {
      if (this.user && this.user.role === 'admin') {
        this.debugVisible = !this.debugVisible;
      } else {
        alert("You are not authorized to access debug tools.");
      }
    }
  }
}
</script>

<style scoped>
.dropdown {
  position: relative;
  display: inline-block;
}
.profile-bubble {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background-color: #bd0000;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  font-weight: 600;
  border: 2px solid white;
  cursor: pointer;
}
.dropdown-menu {
  visibility: hidden;
  opacity: 0;
  position: absolute;
  right: 0;
  top: 120%;
  background-color: white;
  min-width: 180px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
  border-radius: 6px;
  padding: 0.5rem 0;
  transition: visibility 0s linear 0.2s, opacity 0.2s linear;
}
.dropdown:hover .dropdown-menu {
  display: block;
  visibility: visible;
  opacity: 1;
  transition-delay: 0s;
}
.dropdown-header {
  padding: 0.75rem 1rem;
  color: #6c757d;
  font-size: 0.9rem;
}
.dropdown-item {
  color: black;
  padding: 0.75rem 1rem;
  text-decoration: none;
  display: block;
  font-size: 1rem;
}
.logout{
  color: red;
}
.dropdown-item:hover {
  background-color: #f1f1f1;
}
.dropdown-divider {
  height: 1px;
  margin: 0.5rem 0;
  overflow: hidden;
  background-color: #e9ecef;
}

.admin-button {
  color: black;
  padding: 0.75rem 1rem;
  text-decoration: none;
  display: block;
  font-size: 1rem;
}
.admin-button:hover {
  background-color: #f1f1f1;
}
</style>
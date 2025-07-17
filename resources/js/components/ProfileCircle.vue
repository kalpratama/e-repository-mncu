<template>
  <div class="dropdown">
    <button class="profile-bubble">
      {{ userInitial }}
    </button>
    <div class="dropdown-menu">
      <div class="dropdown-header">
        Signed in as<br><strong>{{ userName }}</strong>
      </div>
      <a href="#" class="dropdown-item">Profile</a>
      <div class="dropdown-divider"></div>
      <a href="#" @click.prevent="$emit('logout')" class="dropdown-item">Logout</a>
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
  background-color: #007bff;
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
  display: none;
  position: absolute;
  right: 0;
  top: 120%;
  background-color: white;
  min-width: 180px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
  border-radius: 6px;
  padding: 0.5rem 0;
}
.dropdown:hover .dropdown-menu {
  display: block;
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
.dropdown-item:hover {
  background-color: #f1f1f1;
}
.dropdown-divider {
  height: 1px;
  margin: 0.5rem 0;
  overflow: hidden;
  background-color: #e9ecef;
}
</style>
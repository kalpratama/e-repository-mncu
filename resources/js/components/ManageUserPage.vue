<template>
  <div class="page-body">
    <!-- Header -->
    <Header 
      :is-logged-in="isLoggedIn" 
      :user="user" 
      @request-login="$emit('request-login')" 
      @logout="$emit('logout')" 
    />
    <div v-if ="isLoading" class="loading-container">
      <p>Memuat...</p>
    </div>

    <main v-else-if="user && user.role === 'admin'" class="main-content">
      <h1 class="page-title">Kelola Pengguna</h1>

      <!-- User List -->
      <div class="content-box">
        <form @submit.prevent="submitSearch" class="search-bar-container">
          <input 
            type="text" 
            v-model="internalQuery" 
            placeholder="X Belum bisa search pengguna. X" 
            class="search-input"
          >
          <!-- USER SEARCH BELUM DIIMPLEMENTASIKAN -->
          <button type="submit" class="search-button" aria-label="Search">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          </button>
        </form>
        <!-- <SearchBar :initial-query="searchQuery" @perform-search="navigateToSearch" /> -->
        <div class="user-list">
          <p v-if="users.length === 0">Tidak ada pengguna yang ditemukan.</p>
          <div v-for="userItem in users" :key="userItem.id" class="user-card">
            <router-link :to="'/admin/users/edit/' + userItem.id">
              <div class="text-container header">
                <h3 class="text-name">{{ userItem.name }}</h3>
                <p class="text-username">{{ userItem.username }}</p>
              </div>
              <div class="info-container">
                <div class="text-container email">
                  <p class="text">{{ userItem.email }}</p>
                </div>
                <div class="text-container info">
                  <p class="text">{{ userItem.prodi || '-' }}</p>
                  <p class="text">{{ userItem.role }}</p>
                </div>
              </div>
            </router-link>
          </div>
        </div>
      </div>
    </main>
    <main v-else class="main-content">
      <h1 class="page-title">Anda tidak memiliki akses!</h1>
    </main>
  </div>
</template>

<script>
import Header from './Header.vue';
import SearchBar from './SearchBar.vue';
import axios from 'axios';

export default {
  components: { 
    Header,
  SearchBar
 },
  props: {
    isLoggedIn: Boolean,
    user: Object
  },
  data() {
    return {
      users: [],
      errorMessage: '',
      isLoading: true,
    };
  },
  created() {
    this.fetchUsers();
  },
  methods: {
    async fetchUsers() {
      try {
        const response = await axios.get(`/api/users`);
        console.log("Fetched Users: ", response.data);
        this.users = response.data.data || response.data;
      } catch (error) {
        console.error('Fetch users failed:', error);
        this.errorMessage = 'Gagal memuat data pengguna.';
      } finally {
        this.isLoading = false;
      }
    },
    editUser(userId) {
      this.$router.push(`/users/edit/${userId}`);
    }
  }
};
</script>

<style scoped>
.page-body {
  background-color: #1F3D7B;
  font-family: 'Figtree', sans-serif;
  min-height: 100vh;
}
.main-content { 
  padding-left: 5rem;
  padding-right: 5rem;
  padding-bottom: 0rem;
  padding-top: .5rem;
}
.page-title {
  color: #ffffff;
  text-align: center;
  font-size: 1.5rem;
  font-weight: 600;
  margin-top: 0;
  margin-bottom: .5rem;
}
.user-card {
  display: block;
  text-decoration: none;
  background-color: white;
  color: inherit;
  padding: 0.5rem 2rem;
  margin-bottom: .5rem;
  border-radius: 19px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.user-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.18);
}
.text-container{
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  /* width: 90%; */
  gap: 3rem;
}
.text-container.header{
  justify-content: left;
  gap: 1rem;
}
.text-container.email{
  padding-bottom: 0.5rem;
  /* padding-left: 20%; */
}
.text-container.info{
  justify-content: end;
  padding-bottom: 0.5rem;
  /* padding-left: 20%; */
}
.info-container{
  display: flex;
  flex-direction: row;
  justify-content: space-between;
}
.text-name{
  font-size: large;
  font-weight: bold;
  padding-top:0.7rem;
  padding-bottom: 0.5rem;
  margin-left: 0.2rem;
}
.text-username{
  display: flex;
  width: 120px;
  align-items: center;
  padding-top: 0.7rem;
  padding-bottom: 0.5rem;
}
.text{
  display: flex;
  max-width: 300px;
  min-width: 100px;
  height: auto;
  justify-content: center;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  padding-left: 1rem;
  padding-right: 1rem;
  border-radius: 15px;
  box-shadow: 0 4px 5px rgba(0,0,0,0.15);
}
.content-box {
  background-color: #ffffff;
  border-radius: 15px;
  padding: 1.5rem 2rem; 
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  max-height: 650px;
  overflow-y: auto;
}
.loading-container {
  text-align: center;
  color: white;
  padding: 5rem;
  font-size: 1.2rem;
}
.search-bar-container{
  margin-bottom: 1rem;
}

/* Search Bar */
.search-bar-container {
  display: flex;
  gap: 0.5rem;
  width: 100%;
}
.search-input {
  flex-grow: 1;
  border: 1px solid #ccc;
  padding: 0.75rem;
  border-radius: 15px;
  font-size: 1rem;
}
.search-button {
  width: auto;
  padding: 0.75rem 1.25rem;
  background-color: #1F3D7B;
  color: white;
  border: none;
  border-radius: 15px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s;
}
.search-button:hover {
  background-color: #0056b3;
}

@media (max-width: 768px){
  .main-content {
    padding: 0rem;
  }
  .content-box{
    border-radius: 0px;
  }
  .text-container{
    flex-direction: column;
    gap: 1rem;
    width: 100%;
  }
  .text-container.header{
    flex-direction: row;
    justify-content: space-between;
  }
  p.text{
    width: 100%;
  }
  p.text-username{
    justify-content: end;
  }
  .user-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  }
  .user-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.18);
  }
}
</style>

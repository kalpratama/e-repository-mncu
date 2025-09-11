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
        <div class="search-bar-container">
          <input
            v-model="searchQuery"
            @input="filterUsers"
            type="text"
            placeholder="Cari pengguna berdasarkan nama, username, email, atau prodi..."
            class="search-input"
          />
          <button type="submit" class="search-button" aria-label="Search">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          </button>
        </div>

        <!-- <SearchBar @perform-search="navigateToSearch" /> -->

        <div class="user-list">
          <!-- If there are no users -->
          <p v-if="users.length === 0" class="no-users">
            Tidak ada pengguna yang ditemukan.
          </p>

          <!-- If there are users, display them in a table -->
          <div v-else class="table-container">
            <table class="user-table">
            <thead>
              <tr>
                <th>No.</th>
                <th>ID</th>
                <th>Username</th>
                <th>Nama</th>
                <th>Email</th>
                <th>No. Induk</th>
                <th>Program Studi</th>
                <th>Role</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(userItem, index) in filteredUsers" :key="userItem.id">
                <td>{{ index + 1 }}.</td>
                <td>{{ userItem?.id }}</td>
                <td class="username-text">@{{ userItem?.username }}</td>
                <td>{{ userItem?.name }}</td>
                <td>{{ userItem?.email }}</td>
                <td>{{ userItem?.id_number }}</td>
                <td>{{ userItem?.prodi || '-' }}</td>
                <td>{{ userItem?.role }}</td>
                <td class="action-buttons">
                <!-- Edit User Button -->
                <router-link
                  :to="'/admin/users/edit/' + userItem.id"
                  class="icon-button edit-button"
                  title="Edit Pengguna"
                >
                  <!-- Pencil Icon -->
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 4h2m-6.586 9.586a2 2 0 010-2.828l7-7a2 2 0 012.828 0l2.172 2.172a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0L7.414 15.414z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 5l3 3" />
                  </svg>
                </router-link>

                <!-- Delete User Button -->
                <button
                  @click="deleteUser(userItem.id)"
                  class="icon-button delete-button"
                  title="Hapus Pengguna"
                >
                  <!-- Trash Icon -->
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-4 0a1 1 0 00-1 1v1h6V4a1 1 0 00-1-1m-4 0h4" />
                  </svg>
                </button>
              </td>

              </tr>
            </tbody>
          </table>
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
      filteredUsers: [],
      searchQuery: '',
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
        const response = await axios.get("/api/users");

        // Ensure the response is an array
        if (Array.isArray(response.data)) {
          this.users = response.data;
        } else if (response.data && Array.isArray(response.data.data)) {
          // If API returns { data: [...] }
          this.users = response.data.data;
        } else {
          this.users = [];
        }

        this.filteredUsers = [...this.users];
        this.isLoading = false;
      } catch (error) {
        this.errorMessage = "Gagal memuat data pengguna.";
        console.error(error);
        this.isLoading = false;
      }
    },
    filterUsers() {
      const query = this.searchQuery.trim().toLowerCase();

      if (!query) {
        // If search is empty, show all users again
        this.filteredUsers = this.users;
        return;
      }

      // Filter users based on name, username, email, prodi, or role
      this.filteredUsers = this.users.filter((user) =>
        (user.name && user.name.toLowerCase().includes(query)) ||
        (user.username && user.username.toLowerCase().includes(query)) ||
        (user.email && user.email.toLowerCase().includes(query)) ||
        (user.prodi && user.prodi.toLowerCase().includes(query)) ||
        (user.role && user.role.toLowerCase().includes(query))
      );
    },
    editUser(id) {
      this.$router.push(`/admin/users/edit/${id}`);
    },
    async deleteUser(id) {
      if (!confirm("Apakah Anda yakin ingin menghapus pengguna ini?")) return;

      try {
        await axios.delete(`/api/users/${id}`);
        this.users = this.users.filter((user) => user.id !== id);
        this.filteredUsers = this.filteredUsers.filter((user) => user.id !== id);
      } catch (error) {
        console.error(error);
        alert("Gagal menghapus pengguna.");
      }
    },
  },
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

/* Tables */
.table-container {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}
.user-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 600px;
  margin-top: 15px;
  font-size: 14px;
  background-color: #fff;
  box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  overflow: hidden;
}
.user-table thead {
  background-color: #f4f4f4;
}
.user-table th,
.user-table td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}
.user-table th {
  font-weight: 600;
  color: #333;
}
.user-table tr:hover {
  background-color: #f9f9f9;
}
.username-text {
  font-weight: 600;
  color: #1F3D7B;
}
.action-buttons {
  display: flex;
  gap: 6px;
  justify-content: center;
  align-items: center;
}

.icon-button {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.2s ease;
}

.edit-button {
  background-color: #1d4ed8; /* Blue */
  color: white;
}

.delete-button {
  background-color: #ef4444; /* Red */
  color: white;
}

.icon-button:hover {
  filter: brightness(1.15);
}

.icon-button svg {
  pointer-events: none;
}

/* .action-buttons {
  display: flex;
  flex-direction: row;
  align-items: center;
} */

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
  .user-table th:nth-child(2),
  .user-table td:nth-child(2) {
    display: none; /* Example: hides "Role" column */
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

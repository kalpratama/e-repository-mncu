<template>
  <div class="page-body">
    <!-- Header -->
    <Header 
      :is-logged-in="isLoggedIn" 
      :user="user" 
      @request-login="$emit('request-login')" 
      @logout="$emit('logout')" 
    />

    <main class="main-content">
      <h1 class="page-title">Kelola Pengguna</h1>

      <!-- Error Message -->
      <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>

      <!-- User List -->
      <div v-if="users.length > 0" class="user-list">
        <div v-for="userItem in users" :key="userItem.id" class="user-card">
          <div class="user-info">
            <p><strong>Nama:</strong> {{ userItem.name }}</p>
            <p><strong>Username:</strong> {{ userItem.username }}</p>
            <p><strong>Email:</strong> {{ userItem.email }}</p>
            <p><strong>Role:</strong> {{ userItem.role }}</p>
            <p><strong>Prodi:</strong> {{ userItem.prodi }}</p>
          </div>
          <button @click="editUser(userItem.id)">Edit</button>
        </div>
      </div>

      <!-- Empty State -->
      <p v-else class="no-users">Belum ada pengguna terdaftar.</p>
    </main>
  </div>
</template>

<script>
import Header from './Header.vue';
import axios from 'axios';

export default {
  components: { Header },
  props: {
    isLoggedIn: Boolean,
    user: Object
  },
  data() {
    return {
      users: [],
      errorMessage: ''
    };
  },
  created() {
    this.checkAdminAccess();
    this.fetchUsers();
  },
  methods: {
    checkAdminAccess() {
      if (!this.user || this.user.role !== 'admin') {
        this.$router.push('/'); // Non-admin users redirected to homepage
      }
    },
    async fetchUsers() {
      try {
        const response = await axios.get('/api/users');
        this.users = response.data;
      } catch (error) {
        this.errorMessage = 'Gagal memuat data pengguna.';
        console.error(error);
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
  padding: 3rem 1rem;
}
.form-container {
  max-width: 1250px;
  margin: 0 auto;
  background-color: #ffffff;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.form-title {
  font-size: 2rem;
  font-weight: 600;
  color: #1F3D7B;
  margin: 0 0 0.2rem 0;
}
.form-subtitle {
  font-size: 1rem;
  color: #666;
  margin-bottom: .3rem;
  border-bottom: 1px solid #eee;
  padding-bottom: 1rem;
}
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}
.form-section {
  margin-top: 1.5rem;
}

/* **** NEW: Two-column layout **** */
.form-columns {
  display: flex;
  flex-direction: row;
  gap: 2.5rem;
}
.form-column {
  flex: 1;
  display: flex;
  flex-direction: column;
}
.form-group {
  display: flex;
  flex-direction: column;
}
.form-group.full-width {
  grid-column: 1 / -1; /* Span across both columns */
}
label {
  margin-top: .75rem;
  margin-bottom: 0rem;
  font-weight: 500;
  color: #333;
}
input, select, textarea {
  border: 1px solid #ccc;
  padding: 0.3rem;
  border-radius: 12px;
  font-size: 1rem;
  font-family: inherit;
  width: 100%;
  box-sizing: border-box;
}
input:focus, select:focus, textarea:focus {
  outline: none;
  border-color: #1F3D7B;
  box-shadow: 0 0 0 2px rgba(31, 61, 123, 0.2);
}
textarea {
  resize: vertical;
}
.form-actions {
  margin-top: 2rem;
  text-align: right;
}
button {
  padding: 0.3rem 1rem;
  background-color: #1F3D7B;
  color: white;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
  transition: background-color 0.2s;
}
button:hover:not(:disabled) {
  background-color: #0056b3;
}
button:disabled {
  background-color: #a0aec0;
  cursor: not-allowed;
}
.author-group {
  display: flex;
  gap: 1rem;
  align-items: center;
  margin-bottom: 1rem;
}
.message {
  padding: 1rem;
  border-radius: 5px;
  margin-bottom: 1.5rem;
  font-weight: 500;
}
.success-message {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}
.error-message {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}
@media (max-width: 1024px) { 
  .form-columns {
    flex-direction: column; /* Stack columns on tablet and smaller screens */
  }
}
@media (max-width: 768px) {
  .main-content {
    padding: 0rem;
  }
  .form-grid {
    grid-template-columns: 1fr;
  }
  .form-title {
    font-size: 1.5rem;
    margin-top: 0.5rem;
  }
  .form-subtitle {
    font-size: 0.8rem;
    margin-bottom: 0.5rem;
  }
  .form-columns{
    gap: 0.5rem;
  }
  .form-container{
    border-radius: 0px;
    padding: 0.75rem;
    padding-bottom: 8rem;
  }
  .documenttype-dropdown{
    max-width: 100%;
  }
  legend.document-type{
    font-size: 1.1rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
  }
  label{
    font-size: 0.8rem;
    margin-top: 0.25rem;
  }
  .author-group {
    flex-direction: column; /* Stack author fields on smaller screens */
    gap: 0.5rem;
    padding: 0.5rem;
    align-items: flex-start;
  }
  .author-groutp input{
    width: 100%;
  }
  input{
    width: 100%;
  }
}
</style>

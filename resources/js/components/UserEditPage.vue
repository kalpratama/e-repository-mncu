<template>
  <div class="page-body">
    <!-- Header -->
    <Header 
      :is-logged-in="isLoggedIn" 
      :user="user" 
      @request-login="$emit('request-login')" 
      @logout="$emit('logout')" 
    />
    <div v-if="isLoading" class="loading-container">
      <p>Memuat user untuk diedit...</p>
    </div>

    <main v-else-if="user && user.role === 'admin'" class="main-content">
      <div class="form-container">
        <h1 class="form-title">Edit Informasi Pengguna</h1>
        <p class="form-subtitle">Perbarui detail pengguna</p>
        <form @submit.prevent="updateUser">
          <!-- Success Message -->
          <div v-if="successMessage" class="message success-message">
            {{ successMessage }}
          </div>
          <!-- Error Message -->
          <div v-if="errorMessage" class="message error-message">
            {{ errorMessage }}
            
          </div>
          <div class="form-group">
            <label for="name">Nama</label>
            <input type="text" id="name" v-model="form.name" required />
          </div>

          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" v-model="form.username" required />
          </div>
          <!-- <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" v-model="form.email" required />
          </div> -->
          <div class="form-group">
            <label for="prodi">Program Studi</label>
            <select id="prodi" v-model="form.prodi" required>
              <option value="Manajemen">Manajemen</option>
              <option value="Akuntansi">Akuntansi</option>
              <option value="Pendidikan Matematika">Pendidikan Matematika</option>
              <option value="Pendidikan Bahasa Inggris">Pendidikan Bahasa Inggris</option>
              <option value="Sains Komunikasi">Sains Komunikasi</option>
              <option value="Desain Komunikasi Visual">Desain Komunikasi Visual</option>
              <option value="Sistem Informasi">Sistem Informasi</option>
              <option value="Ilmu Komputer">Ilmu Komputer</option>
            </select>
          </div>
          <div class="form-group">
            <label for="id_number">Nomor Induk</label>
            <input type="text" id="id_number" v-model="form.id_number" required />
          </div>
          <div class="form-group">
            <label>Role</label>
            <select v-model="form.role" required>
              <option value="dosen">Dosen</option>
              <option value="mahasiswa">Mahasiswa</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div class="form-actions">
            <!-- <button type="button" @click="deleteUser">Hapus</button> -->
            <button type="button" @click="$router.push('/admin/users/manage')">Batal</button>
            <button type="submit" :disabled="isSubmitting">
                {{ isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </div>
        </form>
      </div>
    </main>
    <main v-else class="main-content">
      <h1 class="page-title">Anda tidak memiliki akses!</h1>
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
      form: {
        name: '',
        username: '',
        email: '',
        prodi: '',
        id_number: '',
        role: ''
      },
      isSubmitting: false,
      successMessage: '',
      errorMessage: '',
      isLoading: true
    };
  },
  created() {
    this.loadInitialData();
    this.fetchUser();
  },
  methods: {
    async loadInitialData(){
      this.isLoading = true;
      const userId = this.$route.params.id;

      const getUserData = axios.get(`/api/users/${userId}`);

      try {
        const [userData] = await Promise.all([getUserData]);
        const users = userData.data;
        this.form = { ...users };
      } catch (error) {
        this.errorMessage = 'Gagal memuat data pengguna.';
      } finally {
        this.isLoading = false;
      }
    },

    async fetchUser() {
      try {
        const { id } = this.$route.params;
        const response = await axios.get(`/api/users/${id}`);
        this.form = { ...response.data };
      } catch (error) {
        this.errorMessage = 'Gagal memuat data pengguna.';
      } finally {
        this.isLoading = false;
      }
    },
    hasOverlongWord(input){
      return /\S{30,}/.test(input);
    },
    async updateUser() {
      this.isSubmitting = true;
      this.successMessage = '';
      this.errorMessage = '';
      
      const formData = new FormData();
      const fieldsToCheck = ['title', 'abstract', 'publisher', 'description', 'conference_name',  'location', 'achievement_type', 'championship', 'champ_ranking'];
      const overlongInput = fieldsToCheck.find(field => this.hasOverlongWord(this.form[field]));
      if (overlongInput) {
        this.isSubmitting = false;
        this.errorMessage = `"${overlongInput}" tidak boleh memiliki kata lebih dari 30 karakter tanpa spasi.`;
        return;
      }
      if (this.form.id_number !== null && this.form.id_number !== undefined) {
        this.form.id_number = String(this.form.id_number).trim();
      }

      Object.keys(this.form).forEach(key => {
        // if (typeof this.form[key] === 'string') {
        //   this.form[key] = this.form[key].trim();
        // }
        if (this.form[key] !== null) {
          formData.append(key, this.form[key]);
        }
      });
      this.form.role = this.form.role.toLowerCase();

      try {
        const { id } = this.$route.params;
        await axios.put(`/api/users/${id}`, this.form);
        // await axios.put(`/api/users/${id}`, this.form, {
        //   headers: {
        //     'Content-Type': 'application/json'
        //   }
        // });

        this.successMessage = 'Pengguna berhasil diperbarui!';
        setTimeout(() => {
          this.$router.push('/admin/users/manage');
        }, 1500);
      } catch (error) {
        this.errorMessage = 'Gagal memperbarui pengguna.';
        console.error(error);
      } finally {
        this.isSubmitting = false;
        this.isLoading = false;
      }
    }
  }
};
</script>

<style scoped>
.main-content {
  padding-left: 5rem;
  padding-right: 5rem;
  padding-bottom: 2rem;
  padding-top: 3rem;
}
.page-body {
  background-color: #1F3D7B;
  font-family: 'Figtree', sans-serif;
  min-height: 100vh;
}
.page-title {
  color: #ffffff;
  text-align: center;
  font-size: 1.5rem;
  font-weight: 600;
  margin-top: 0;
  margin-bottom: .5rem;
}
.loading-container {
  text-align: center;
  color: white;
  padding: 5rem;
  font-size: 1.2rem;
}
.form-container {
  max-width: 550px;
  margin: 0 auto;
  background-color: #ffffff;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.form-group {
  display: flex;
  flex-direction: column;
}
.form-group.full-width {
  grid-column: 1 / -1; /* Span across both columns */
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
  margin: 0 0 1rem 0;
  border-bottom: 1px solid #eee;
  padding-bottom: 1rem;
}
input, select{
  border: 1px solid #ccc;
  padding: 0.3rem 0.7rem;
  border-radius: 12px;
  font-size: 1rem;
  font-family: inherit;
  width: 100%;
  box-sizing: border-box;
}
input:focus, select:focus{
  outline: none;
  border-color: #1F3D7B;
  box-shadow: 0 0 0 2px rgba(31, 61, 123, 0.2);
}
.form-actions {
  margin-top: 2rem;
  text-align: right;
}
label {
  margin-top: .75rem;
  margin-bottom: 0rem;
  font-weight: 500;
  color: #333;
}
button {
  padding: 0.3rem 1rem;
  margin-left: 1rem;
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
.message {
  padding: 1rem;
  border-radius: 12px;
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

@media (max-width: 768px){
  .main-content {
    padding: 0rem;
   }
   .form-container{
    border-radius: 0px;;
   }
}
</style>
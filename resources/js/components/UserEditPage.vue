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
      <h1>Edit Pengguna</h1>

      <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
      <p v-if="successMessage" class="success-message">{{ successMessage }}</p>

      <form @submit.prevent="updateUser">
        <div>
          <label>Nama:</label>
          <input type="text" v-model="form.name" required />
        </div>

        <div>
          <label>Username:</label>
          <input type="text" v-model="form.username" required />
        </div>

        <div>
          <label>Email:</label>
          <input type="email" v-model="form.email" required />
        </div>

        <div>
          <label>Prodi:</label>
          <input type="text" v-model="form.prodi" required />
        </div>

        <div>
          <label>Role:</label>
          <select v-model="form.role" required>
            <option value="mahasiswa">Mahasiswa</option>
            <option value="dosen">Dosen</option>
            <option value="admin">Admin</option>
          </select>
        </div>

        <button type="submit" :disabled="isSubmitting">Simpan Perubahan</button>
        <button type="button" @click="$router.push('/manage-users')">Batal</button>
      </form>
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
        role: ''
      },
      isSubmitting: false,
      successMessage: '',
      errorMessage: ''
    };
  },
  created() {
    this.fetchUser();
  },
  methods: {
    async fetchUser() {
      try {
        const { id } = this.$route.params;
        const response = await axios.get(`/api/users/${id}`);
        this.form = { ...response.data };
      } catch (error) {
        this.errorMessage = 'Gagal memuat data pengguna.';
      }
    },
    async updateUser() {
      this.isSubmitting = true;
      this.successMessage = '';
      this.errorMessage = '';

      try {
        const { id } = this.$route.params;
        await axios.put(`/api/users/${id}`, this.form);
        this.successMessage = 'Pengguna berhasil diperbarui!';
        setTimeout(() => {
          this.$router.push('/manage-users');
        }, 1500);
      } catch (error) {
        this.errorMessage = 'Gagal memperbarui pengguna.';
        console.error(error);
      } finally {
        this.isSubmitting = false;
      }
    }
  }
};
</script>

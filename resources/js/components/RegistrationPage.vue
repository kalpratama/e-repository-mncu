<template>
  <div class="page-container">
    <div class="content-wrapper">

      <div class="logo-area">
        <img :src="logo" alt="MNCU Logo" class="logo-image">
      </div>

      <div class="login-box">
        <h1 class="app-name">E-Repository</h1>
        <p class="company-name">Universitas Media Nusantara Citra</p>
        <form @submit.prevent="handleRegister">
          <div class="input-group">
            <label for="username">Username</label>
            <input type="text" id="username" v-model="username" required autocomplete="username" placeholder="Masukkan username">
          </div>
          <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" v-model="password" required autocomplete="current-password" placeholder="Masukkan password">
          </div>
          <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" v-model="email" required autocomplete="email" placeholder="Masukkan email">
          </div>
          <div class="input-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" id="name" v-model="name" required autocomplete="name" placeholder="Masukkan nama lengkap">
          </div>
          <div class="input-group">
            <label for="id_number">NIM</label>
            <input type="text" id="id_number" v-model="id_number" required autocomplete="id_number" placeholder="Masukkan NIM">
          </div>
          <div class="input-group">
            <label for="prodi">Program Studi</label>
            <select id="prodi" v-model="prodi" required>
              <option value="Manajemen">Manajemen</option>
                <option value="Akuntansi">Akuntansi</option>
                <option value="Pendidikan Matematika">Pendidikan Matematika</option>
                <option value="Pendidikan Bahasa Inggris">Pendidikan Bahasa Inggris</option>
                <option value="Sains Komunikasi">Sains Komunikasi</option>
                <option value="DKV">DKV</option>
                <option value="Sistem Informasi">Sistem Informasi</option>
                <option value="Ilmu Komputer">Ilmu Komputer</option>
            </select>
          </div>

          <div class="register">
            <router-link to="/login">Sudah punya akun? Masuk</router-link>
          </div>
          <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
          <p v-if="successMessage" class="success-message">{{ successMessage }}</p>
          <button type="submit" :disabled="isLoading">
            <span v-if="isLoading">Mendaftar...</span>
            <span v-else>Daftar</span>
          </button>
        </form>
      </div>

    </div>
  </div>
</template>

<script>
import axios from 'axios';
import logo from '../assets/mncu_logo.png';

export default {
  data() {
    return {
      logo: logo,
      username: '',
      password: '',
      email: '',
      name: '',
      id_number:'',
      prodi: '',
      errorMessage: '',
      successMessage:'',
      isLoading: false,
    };
  },
  methods: {
    async handleRegister() {
      this.errorMessage = '';
      this.successMessage = '';

      if (!this.email.endsWith("@mncu.ac.id")) {
        this.errorMessage = "Hanya email @mncu.ac.id yang diperbolehkan.";
        this.success = false;
        return;
      }

      if (/[^0-9]/.test(this.id_number)) {
        this.errorMessage = "NIM hanya boleh terdiri dari angka.";
        this.success = false;
        return;
      }

      this.isLoading = true; // start loading
      this.message = "";
      
      try {
        const response = await axios.post('/api/register', {
          username: this.username,
          name: this.name,
          email: this.email,
          password: this.password,
          id_number: this.id_number,
          prodi: this.prodi
        });

        // Save email for verification
        const registeredEmail = response.data.email;
        localStorage.setItem("registeredEmail", registeredEmail);

        alert("Registrasi berhasil. Silakan verifikasi email Anda.");
        this.successMessage = "Registrasi berhasil. Silakan verifikasi email Anda.";
        this.$router.push('/verify-otp');
      } catch (error) {
        if (error.response && error.response.data.errors) {
          this.errorMessage = Object.values(error.response.data.errors).flat().join(' ');
        } else {
          this.errorMessage = 'Terjadi kesalahan saat registrasi.';
        }
        console.error('Register error:', error);
      }
      this.isLoading = false; // stop loading
    }
  }
};
</script>

<style scoped>
.page-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f0f2f5;
  font-family: 'Figtree', sans-serif;
  padding: 1rem;
  box-sizing: border-box;
}
.content-wrapper {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  width: 80%;
  max-width: 1000px;
}
.logo-area {
  flex-basis: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  margin-top: 2rem;
}
.logo-image {
  width: 50%;
  height: 50%;
  object-fit: contain;
}
.app-name {
  text-align: center;
  font-size: 1.5rem;
  font-weight: bold;
  color: #1c1c1c;
  margin: 0;
}
.company-name {
  text-align: center;
  font-size: 1rem;
  color: #666;
  margin-top: 0.5rem;
  margin-bottom: 2rem;
}
.login-box {
  flex-basis: 90%;
  padding: 2rem;
  width: 90%;
  max-width: 400px;
}
.login-box h2 {
  text-align: center;
  margin-top: 0;
  margin-bottom: 2rem;
  color: #333;
  font-weight: bold;
}
.input-group {
  margin-bottom: 0.75rem;
}
.input-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #555;
  font-weight: 500;
}
.input-group input, select {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 12px;
  box-sizing: border-box;
  transition: border-color 0.2s;
}
.input-group input:focus {
  outline: none;
  border-color: #1F3D7B;
}
button {
  width: 100%;
  padding: 0.9rem;
  background-color: #1F3D7B;
  color: white;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
  transition: background-color 0.2s;
}
button:hover {
  background-color: #0056b3;
}
.register {
  text-align: center;
  color: #1F3D7B;
  margin-bottom: 1rem;
  font-size: 0.9rem;
}
.register:hover {
  text-decoration: underline;
}
.error-message {
  color: #d93025;
  text-align: center;
  margin-bottom: 1rem;
  font-size: 0.9rem;
}
.success-message {
  color: #0f5200;
  text-align: center;
  margin-bottom: 1rem;
  font-size: 0.9rem;
}

@media (max-width: 768px) {
  .page-container {
    padding: 0rem;
    background-color: #ffffff;
  }
  .content-wrapper {
    flex-direction: column;
    gap: 0rem;
    padding: 1rem;
    width: 100%;
    box-shadow: none;
    border-radius: 0px;
  }
  .logo-area, .login-box {
    flex-basis: 100%;
    max-width: 420px;
    padding-top: 0rem;
  }
  .logo-area {
    margin-top: 0rem;
    padding-bottom: 2rem;
  }
}
</style>

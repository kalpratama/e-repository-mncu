<template>
  <div class="page-container">
    <div class="content-wrapper">

      <div class="logo-area">
        <img :src="logo" alt="MNCU Logo" class="logo-image">
      </div>

      <div class="login-box">
        <h1 class="app-name">E-Repository</h1>
        <p class="company-name">Universitas Media Nusantara Citra</p>
        <form @submit.prevent="submitOtp">
          <div class="input-group">
              <label for="otp-code">Kode OTP</label>
              <input type="text" id="otp-code" v-model="otp" required placeholder="Masukkan 6 digit kode OTP"/>
          </div>
          <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
          <p v-if="successMessage" class="success-message">{{ successMessage }}</p>
          <button type="submit" :disabled="isLoading">
            <span v-if="isLoading">Memverifikasi...</span>
            <span v-else>Verifikasi</span>
          </button>
          <div class="register">
            <router-link to="/login">Sudah diverifikasi admin? Masuk</router-link>
          </div>
          <div class="register">
            <router-link to="/">Tidak menerima email? Hubungi admin</router-link>
          </div>
        </form>
      </div>

    </div>
  </div>
</template>

<script>
import axios from "axios";
import logo from '../assets/mncu_logo.png';

export default {
  name: "VerifyOtp",
  data() {
    return {
      logo: logo,
      email: localStorage.getItem("registeredEmail"),
      otp: "",
      message: "",
      success: false,
      isLoading: false,
    };
  },
  methods: {
    async submitOtp() {
      this.isLoading = true; // start loading
      this.message = "";

      if (!this.email){
        this.errorMessage = "Tidak ada email yang didaftarkan";
        this.isLoading = false;
        return;
      }

      try {
        const response = await axios.post('/api/verify-otp', {
          email: this.email,
          otp: this.otp,
        });

        this.message = response.data.message;
        this.success = true;

        localStorage.removeItem("registeredEmail");

        // Redirect after success
        setTimeout(() => {
          this.$router.push("/login");
        }, 1000);
      } catch (error) {
        this.success = false;
        this.errorMessage =
          error.response?.data?.message || "Verifikasi gagal, coba lagi.";
      }
      this.isLoading = false;
    },
  },
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
  width: 60%;
  max-width: 1000px;
}
.logo-area {
  flex-basis: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}
.logo-image {
  width: 50%;
  height: 50%;
  object-fit: contain;
  margin: 2rem;
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
.register {
  text-align: center;
  color: #1F3D7B;
  margin-top: 1rem;;
  margin-bottom: 1rem;
  font-size: 0.9rem;
}
.register:hover {
  text-decoration: underline;
}
button:hover {
  background-color: #0056b3;
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
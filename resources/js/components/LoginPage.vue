<template>
  <div class="page-container">
    <div class="content-wrapper">

      <!-- Left Side: Logo and Company Name -->
      <div class="logo-area">
        <!-- Placeholder SVG Logo -->
        <img :src="logo" alt="MNCU Logo" class="logo-image">
      </div>

      <!-- Right Side: Login Form -->
      <div class="login-box">
        <h1 class="app-name">E-Repository</h1>
        <p class="company-name">Universitas Media Nusantara Citra</p>
        <form @submit.prevent="handleLogin">
          <div class="input-group">
            <label for="email">email</label>
            <input type="text" id="email" v-model="email" required autocomplete="email">
          </div>
          <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" v-model="password" required autocomplete="current-password">
          </div>
          <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
          <button type="submit">Log In</button>
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
      email: '',
      password: '',
      errorMessage: ''
    };
  },
  methods: {
    async handleLogin() {
      this.errorMessage = '';
      try {
        await axios.post('/login', {
          email: this.email,
          password: this.password
        });
        this.$emit('login-success');
      } catch (error) {
        this.errorMessage = 'email atau password tidak sesuai.';
        console.error('Login failed:', error);
      }
    }
  }
};
</script>

<style scoped>
/* Main container for the whole page, centers content */
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
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  gap: 4rem;
  width: 100%;
  max-width: 1000px;
}

/* --- Logo Area --- */
.logo-area {
  flex-basis: 60%;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 2rem;
}

.logo-image {
  width: 50%;
  height: 50%;
  object-fit: contain;
  margin-bottom: 0.5rem;
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

/* --- Login Box --- */
.login-box {
  flex-basis: 80%;
  padding: 2.5rem;
  width: 100%;
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
  margin-bottom: 1.5rem;
}

.input-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #555;
  font-weight: 500;
}

.input-group input {
  width: 100%;
  padding: 0.8rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
  transition: border-color 0.2s;
}

.input-group input:focus {
  outline: none;
  border-color: #072546;
}

button {
  width: 100%;
  padding: 0.9rem;
  background-color: #072546;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
  transition: background-color 0.2s;
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

/* --- Responsive Design --- */
@media (max-width: 800px) {
  .content-wrapper {
    flex-direction: column;
    gap: 2rem;
    padding: 1rem;
  }

  .logo-area, .login-box {
    flex-basis: 100%;
    max-width: 420px;
  }

  .logo-area {
    padding-bottom: 0;
  }
}
</style>

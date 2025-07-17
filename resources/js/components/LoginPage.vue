<template>
  <div class="page-container">
    <div class="content-wrapper">

      <div class="logo-area">
        <img :src="logo" alt="MNCU Logo" class="logo-image">
      </div>

      <div class="login-box">
        <h1 class="app-name">E-Repository</h1>
        <p class="company-name">Universitas Media Nusantara Citra</p>
        <form @submit.prevent="handleLogin">
          <div class="input-group">
            <label for="username">Username</label>
            <input type="text" id="username" v-model="username" required autocomplete="username">
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

/* -- API connections -- */
<script>
import axios from 'axios';
import logo from '../assets/mncu_logo.png';

export default {
  data() {
    return {
      logo: logo,
      username: '',
      password: '',
      errorMessage: ''
    };
  },
  methods: {
    async handleLogin() {
      this.errorMessage = '';
      try {
        // Send login request to the server
        await axios.post('/login', {
          username: this.username,
          password: this.password
        });
<<<<<<< Updated upstream
<<<<<<< Updated upstream
        this.$emit('login-success');
=======
        this.$emit('login-success', response.data);
>>>>>>> Stashed changes
=======
        this.$emit('login-success', response.data);
>>>>>>> Stashed changes

      } catch (error) {
        this.errorMessage = 'Username atau password tidak sesuai!';
        console.error('Login failed:', error);
      }
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
  border-radius: 8px;
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
  border-radius: 8px;
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

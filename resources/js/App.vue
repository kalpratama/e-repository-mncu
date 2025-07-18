<template>
  <div>
    <!-- ======================= DEBUG BLOCK ======================= -->
    <div class="debug-info">
      <strong>-- DEBUG INFO --</strong><br>
      <p>Current Page: <strong>{{ currentPage }}</strong></p>
      <p>Is Logged In: <strong>{{ isLoggedIn }}</strong></p>
      <p>User Data: <strong>{{ user || 'null' }}</strong></p>
      <p>Token: <strong>{{ token ? 'Available' : 'Missing' }}</strong></p>
      <p>LocalStorage Token: <strong>{{ $data.debugToken || 'Missing' }}</strong></p>
      <button @click="debugStorage" style="margin-top: 10px;">Debug Storage</button>
      <button @click="checkAuthStatus" style="margin-top: 10px; margin-left: 10px;">Re-check Auth</button>
    
    </div>
    <!-- =========================================================== -->

    <router-view 
      :is-logged-in="isLoggedIn"
      :user="user"
      @request-login="goToLogin"
      @login-success="handleLoginSuccess"
      @logout="handleLogout"
    />

    <!-- Show the Dashboard page, passing login state and user data down as props -->
    <!-- <DashboardPage 
      v-if="currentPage === 'dashboard'" 
      :is-logged-in="isLoggedIn"
      :user="user"
      @request-login="showLoginPage"
      @logout="handleLogout"
    />

    Show Login page when login button is clicked
    <LoginPage 
      v-else-if="currentPage === 'login'" 
      @login-success="handleLoginSuccess" 
    /> -->
  </div>
</template>

<script>
import axios from 'axios';
import LoginPage from './components/LoginPage.vue';
import DashboardPage from './components/LibDashboardPage.vue';
import ProfileCircle from './components/ProfileCircle.vue';

export default {
  components: {
    LoginPage,
    DashboardPage,
    ProfileCircle
  },
  data() {
    return {
      currentPage: 'dashboard',
      isLoggedIn: false,
      user: null,
      token: null,
      debugToken: null
    };
  },
  methods: {
    // showLoginPage(){
    //   this.currentPage = 'login';
    // },

    // showDashboardPage(){
    //   this.currentPage = 'dashboard';
    // },

    goToLogin(){
      this.$router.push('/login');
    },

    handleLoginSuccess(data) {
      localStorage.setItem('access_token', data.access_token);
      axios.defaults.headers.common['Authorization'] = `Bearer ${data.access_token}`;
      this.isLoggedIn = true;
      this.user = data.user;
      // this.currentPage = 'dashboard';
      this.$router.push('/');
    },

    async handleLogout() {
      try{
        await axios.post('/api/logout');
      } catch (error) {
        console.error('Logout API call failed:', error);
      } finally{
        localStorage.removeItem('access_token');
        delete axios.defaults.headers.common['Authorization'];
        this.isLoggedIn = false;
        this.user = null;
        this.$router.push('/');
      }
    },

    async checkAuthStatus() {
      const token = localStorage.getItem('access_token');
      if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        this.token = token;
        this.debugToken = token; // For debugging purposes
        try {
          const response = await axios.get('/api/user');
          if (response.data) {
            this.isLoggedIn = true;
            this.user = response.data;
          } 
        } catch (error) {
          localStorage.removeItem('access_token');
          delete axios.defaults.headers.common['Authorization'];
          this.isLoggedIn = false;
          this.user = null;
          this.token = null;
          console.log('User not authenticated:', error);
        }
      }
    },

    setupAxios() {
      // Set base URL if your API is on a different domain/port
      axios.defaults.baseURL = 'http://127.0.0.1:8000';
      
      // Set default headers
      axios.defaults.headers.common['Content-Type'] = 'application/json';
      axios.defaults.headers.common['Accept'] = 'application/json';
      
      // Add request interceptor for debugging
      axios.interceptors.request.use(
        (config) => {
          console.log('Making request:', config.method.toUpperCase(), config.url);
          return config;
        },
        (error) => {
          console.error('Request error:', error);
          return Promise.reject(error);
        }
      );
      
      // Add response interceptor for debugging
      axios.interceptors.response.use(
        (response) => {
          console.log('Response received:', response.status, response.data);
          return response;
        },
        (error) => {
          console.error('Response error:', error.response?.status, error.response?.data);
          
          // Handle 401 errors (unauthorized)
          if (error.response?.status === 401) {
            this.handleLogout();
          }
          
          return Promise.reject(error);
        }
      );
    }
  },

  created(){
    this.setupAxios();
    this.checkAuthStatus();

  }
};
</script>

<style>
.debug-info {
  background-color: #e7e7e7;
  padding: 10px;
  margin: 10px;
  border: 1px solid #ccc;
  border-radius: 20px;
  font-family: monospace;
}

button {
  background-color: white;
  border: 1px solid #ccc;
  border-radius: 15px;
  padding: 5px 10px;
}

button:hover {
  background-color: #e0e0e0;
} 
</style>
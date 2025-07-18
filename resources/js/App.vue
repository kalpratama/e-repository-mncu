<template>
  <div>
    <!-- ======================= DEBUG BLOCK ======================= -->
    <!-- This box will always show the current state of your app. -->
    <!-- <div class="debug-info">
      <strong>-- DEBUG INFO --</strong><br>
      <p>Current Page: <strong>{{ currentPage }}</strong></p>
      <p>Is Logged In: <strong>{{ isLoggedIn }}</strong></p>
      <p>User Data:</p>
      <pre>{{ user || 'null' }}</pre>
    </div> -->
    <!-- =========================================================== -->


    <!-- Show the Dashboard page, passing login state and user data down as props -->
    <DashboardPage 
      v-if="currentPage === 'dashboard'" 
      :is-logged-in="isLoggedIn"
      :user="user"
      @request-login="showLoginPage"
      @logout="handleLogout"
    />

    <!-- Show the Login page when needed -->
    <LoginPage 
      v-else-if="currentPage === 'login'" 
      @login-success="handleLoginSuccess" 
    />
    <!-- <DashboardPage
      v-if="currentPage === 'dashboard'"
      @request-login="showLoginPage"
    />

    <LoginPage
      v-if="currentPage === 'login'"
      @login-success="showDashboardPage"
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
      user: null
    };
  },
  methods: {
    showLoginPage(){
      this.currentPage = 'login';
    },

    showDashboardPage(){
      this.currentPage = 'dashboard';
    },

    handleLoginSuccess(data) {
      localStorage.setItem('access_token', data.access_token);
      axios.defaults.headers.common['Authorization'] = `Bearer ${data.access_token}`;
      this.isLoggedIn = true;
      this.user = user;
      this.currentPage = 'dashboard';
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
      }
    },

    async checkAuthStatus() {
      const token = localStorage.getItem('access_token');
      if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        try {
          const response = await axios.get('/api/user');
          if (response.data) {
            this.isLoggedIn = true;
            this.user = response.data;
          } 
        } catch (error) {
          localStorage.removeItem('access_token');
          console.log('User not authenticated:', error);
        }
      }

    //   try {
    //     const response = await axios.get('/api/user');
    //     if (response.data) {
    //       this.isLoggedIn = true;
    //       this.user = response.data;
    //     } 
    //   } catch (error) {
    //     this.isLoggedIn = false;
    //     this.user = null;
    //     console.log('User not authenticated:', error);
    //   }
    },

    
  },

  created(){
      this.checkAuthStatus();
    }
};
</script>
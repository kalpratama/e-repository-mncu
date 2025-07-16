<template>
  <div>
    <!--LoginPage v-if="!isLoggedIn" @login-success="onLoginSuccess" /-->
    <DashboardPage
      v-if="currentPage === 'dashboard'"
      @request-login="showLoginPage"
    />

    <LoginPage
      v-if="currentPage === 'login'"
      @login-success="showDashboardPage"
    />
  </div>
</template>

<script>
import axios from 'axios';
import LoginPage from './components/LoginPage.vue';
import DashboardPage from './components/LibDashboardPage.vue';

export default {
  components: {
    LoginPage,
    DashboardPage
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

    handleLoginSuccess(user) {
      this.isLoggedIn = true;
      this.user = user;
      this.currentPage = 'dashboard';
    },

    async handleLogout() {
      try{
        await axios.post('/api/logout');
      } catch (error) {
        console.error('Logout failed:', error);
      } finally{
        this.isLoggedIn = false;
        this.user = null;
      }
    },

    async checkAuthStatus() {
      try {
        const response = await axios.get('/api/user');
        if (response.data) {
          this.isLoggedIn = true;
          this.user = response.data;
        } 
      } catch (error) {
        this.isLoggedIn = false;
        this.user = null;
        console.log('User not authenticated:', error);
      }
    },

    created(){
      this.checkAuthStatus();
    }
  }
};
</script>
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
import LoginPage from './components/LoginPage.vue';
import DashboardPage from './components/LibDashboardPage.vue';

export default {
  components: {
    LoginPage,
    DashboardPage
  },
  data() {
    return {
      currentPage: 'dashboard', };
  },
  methods: {
    showLoginPage(){
        this.currentPage = 'login';
      },
      showDashboardPage(){
        this.currentPage = 'dashboard';
      }
  }
};
</script>
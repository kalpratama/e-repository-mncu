<template>
  <div>
    <!-- If user is not logged in, show the login page -->
    <LoginPage v-if="!isLoggedIn" @login-success="onLoginSuccess" />

    <!-- If user is logged in, show the dashboard -->
    <DashboardPage v-else />
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
      isLoggedIn: false
    };
  },
  methods: {
    onLoginSuccess() {
      this.isLoggedIn = true;
    }
  }
};
</script>
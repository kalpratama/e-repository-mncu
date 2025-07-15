<template>
  <div class="dashboard-container">
    <nav class="navbar">
      <h1>Dashboard</h1>
      <div class="dropdown">
        <button class="dropdown-toggle">Menu</button>
        <div class="dropdown-menu">
          <a href="#">Profile</a>
          <a href="#">Settings</a>
          <a href="#">Logout</a>
        </div>
      </div>
    </nav>
    <main>
      <h2>Welcome, {{ username }}</h2>
      <p>You have successfully logged in.</p>
    </main>
  </div>
</template>

<style scoped>
.dashboard-container {
  font-family: sans-serif;
}
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #333;
  color: white;
  padding: 1rem 2rem;
}
.dropdown {
  position: relative;
  display: inline-block;
}
.dropdown-toggle {
  background-color: #007bff;
  color: white;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
.dropdown-menu {
  display: none;
  position: absolute;
  right: 0;
  background-color: white;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
  border-radius: 4px;
}
.dropdown-menu a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}
.dropdown-menu a:hover {
  background-color: #f1f1f1;
}
.dropdown:hover .dropdown-menu {
  display: block;
}
main {
  padding: 2rem;
}
</style>
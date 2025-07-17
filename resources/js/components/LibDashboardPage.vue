<template>
  <div class="dashboard-body">
    <!-- Top Header -->
    <header class="top-header">
      <div class="header-content">
        <div class="logo-container">
          <img :src="logo" alt="MNCU Logo" class="company-logo">
        </div>
        <div class="communities-link">
          <span>Communities in MNCU-IR</span>
        </div>
        <div>
          <button @click="$emit('request-login')" class="login-button">Login</button>
        </div>
      </div>
    </header>

    <!-- Main Content Area -->
    <main class="main-content">
      <h1 class="repository-title">Repositori Institusional MNC University</h1>
      <div class="content-boxes">
        <!-- Left Box: Publication Types -->
        <div class="content-box">
          <h2 class="box-title">Terbitan Pustaka</h2>
          <ol class="publication-list">
            <!-- The v-for now iterates through objects -->
            <li v-for="item in publicationTypes" :key="item.name">
              <a href="#">
                <!-- ** FIXED: Displaying item.name instead of the whole item object ** -->
                {{ item.name }}
                <!-- Add an indicator if there's a dropdown -->
                <span v-if="item.children" class="dropdown-arrow">&#9656;</span>
              </a>
              <!-- First-level dropdown -->
              <ul v-if="item.children" class="dropdown-menu">
                <li v-for="child in item.children" :key="child.name">
                  <a href="#">
                    {{ child.name }}
                    <span v-if="child.children" class="dropdown-arrow">&#9656;</span>
                  </a>
                  <!-- Second-level dropdown -->
                  <ul v-if="child.children" class="dropdown-menu">
                    <li v-for="grandchild in child.children" :key="grandchild.name">
                      <a href="#">{{ grandchild.name }}</a>
                    </li>
                  </ul>
                </li>
              </ul>
            </li>
          </ol>
        </div>

        <!-- Right Box: Recently Published -->
        <div class="content-box">
          <h2 class="box-title">Baru Diterbitkan</h2>
          <div class="recent-publications">
            <a href="#" class="publication-item" v-for="(item, index) in recentPublications" :key="index">
              <h3>{{ item.title }}</h3>
              <p class="meta">{{ item.meta }}</p>
              <p class="description">{{ item.description }}</p>
            </a>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import logo from '../assets/mncu_logo_wide.png';

export default {
  data() {
    return {
      logo: logo,
      publicationTypes: [
        "01. Artikel Jurnal",
        "02. Artikel Jurnal Tidak Terbit",
        "03. Artikel",
        "04. Buku",
        "05. Bab Buku",
        "06. Skripsi",
        "07. Tugas Akhir",
        "08. Makalah Konferensi",
        "09. Modul Pembelajaran",
        "10. Laporan Penelitian",
        "11. Laporan Magang Mahasiswa",
        "12. Poster Ilmiah",
        "13. Dokumentasi Prestasi Mahasiswa",
      ],
      recentPublications: [
        {
          title: "Judul Terbitan 1 (Lorem ipsum dolor sit amet)",
          meta: "Lastname, Firstname (Prodi, Universitas, Tahun)",
          description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."
        },
        {
          title: "Judul Terbitan 2 (Lorem ipsum dolor sit amet)",
          meta: "Lastname, Firstname (Prodi, Universitas, Tahun)",
          description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."
        }
      ]
    };
  }
}
</script>

<style scoped>
.dashboard-body {
  background-color: #f4f7f6;
  font-family: 'Figtree', sans-serif;
  min-height: 100vh;
}

/* --- Top Header --- */
.top-header {
  background-color: #ffffff;
  padding: 1rem 2rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  border-bottom: 1px solid #e0e0e0;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1400px;
  margin: 0 auto;
}

.logo-container {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 600;
  font-size: 1.1rem;
  margin-left: 3rem;
}

.company-logo {
  height: 40px;
  width: auto;
}

.communities-link {
  font-weight: 900;
  font-size: 1.5rem;
}

/* --- Main Content --- */
.main-content {
  background-color: #1F3D7B;
  padding-left: 5rem;
  padding-right: 5rem;
  padding-bottom: 8rem;
  padding-top: 1rem;
}

.repository-title {
  color: #ffffff;
  text-align: center;
  font-size: 1.75rem;
  font-weight: 600;
  margin-top: 0;
  margin-bottom: 1rem;
}

.content-boxes {
  display: flex;
  flex-direction: row;
  gap: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.content-box {
  background-color: #ffffff;
  border-radius: 8px;
  padding: 1.5rem 2rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.content-boxes > .content-box:first-child {
  flex: 1;
}

.content-boxes > .content-box:last-child {
  flex: 2;
}

.box-title {
  margin-top: 0;
  margin-bottom: 1.5rem;
  font-size: 1.25rem;
  font-weight: 600;
  align-content: center;
  border-bottom: 1px solid #eee;
  padding-bottom: 0.75rem;
}

/* --- Left Box List with Dropdowns --- */
.publication-list {
  list-style: none;
  padding-left: 0;
  margin: 0;
}

.publication-list li {
  position: relative;
  margin-bottom: 0.25rem;
}

.publication-list a {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.25rem 1rem;
  text-decoration: none;
  color: inherit;
  box-shadow: 0 4px 5px rgba(0,0,0,0.15);
  border: 1px solid #eee;
  border-radius: 5px;
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.publication-list li:hover > a {
  background-color: #e9ecef;
}

<<<<<<< Updated upstream
<<<<<<< Updated upstream
=======
.dropdown-arrow {
  font-size: 0.8em;
  color: #888;
=======
.dropdown-arrow {
  font-size: 0.8em;
  color: #888;
}

/* --- Dropdown Menu Styling --- */
.dropdown-menu {
  opacity: 0;
  visibility: hidden;
  position: absolute;
  left: 100%; /* Position to the right of the parent */
  top: -1px; /* Align with the top of the parent */
  list-style: none;
  padding: 0;
  margin: 0 0 0 5px; /* Small gap from the parent */
  min-width: 200px;
  background-color: white;
  border: 1px solid #ddd;
  border-radius: 5px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  z-index: 10;
  transition: visibility 0s linear 0.2s, opacity 0.2s linear;
}

/* Show dropdown on parent hover */
.publication-list li:hover > .dropdown-menu {
  display: block;
  visibility: visible;
  opacity: 1;
  /* On hover, remove the delay so it appears quickly */
  transition-delay: 0s;
>>>>>>> Stashed changes
}

/* --- Dropdown Menu Styling --- */
.dropdown-menu {
  opacity: 0;
  visibility: hidden;
  position: absolute;
  left: 100%; /* Position to the right of the parent */
  top: -1px; /* Align with the top of the parent */
  list-style: none;
  padding: 0;
  margin: 0 0 0 5px; /* Small gap from the parent */
  min-width: 200px;
  background-color: white;
  border: 1px solid #ddd;
  border-radius: 5px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  z-index: 10;
  transition: visibility 0s linear 0.2s, opacity 0.2s linear;
}

/* Show dropdown on parent hover */
.publication-list li:hover > .dropdown-menu {
  display: block;
  visibility: visible;
  opacity: 1;
  /* On hover, remove the delay so it appears quickly */
  transition-delay: 0s;
}

>>>>>>> Stashed changes
/* --- Right Box List --- */
.recent-publications {
  max-height: 500px;
  max-width: 100%;
  overflow-y: auto;
  padding-right: 1rem;
}

a.publication-item {
  display: block;
  text-decoration: none;
  color: inherit;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  border-radius: 6px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

a.publication-item:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.18);
}

a.publication-item:last-child {
  margin-bottom: 0;
}

.publication-item h3 {
  margin: 0 0 0.25rem 0;
  font-size: 1.1rem;
  color: #1F3D7B;
  font-weight: 600;
}

a.publication-item:hover h3 {
  text-decoration: underline;
}

.publication-item .meta {
  font-size: 0.85rem;
  color: #666;
  margin: 0 0 0.5rem 0;
}

.publication-item .description {
  font-size: 0.95rem;
  color: #444;
  line-height: 1.6;
  margin: 0;
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

/* --- Responsive Design --- */
@media (max-width: 992px) {
  .content-boxes {
    flex-direction: column;
  }
  
  .dropdown-menu {
    left: 0;
    top: 100%;
    margin: 5px 0 0 0;
    width: 100%;
  }

  /* Adjust nested dropdowns as well */
  .dropdown-menu .dropdown-menu {
    left: 0;
    top: 100%;
    margin: 5px 0 0 0;
    width: 100%;
  }
}

@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    gap: 1rem;
  }
  .main-content {
    padding: 1rem;
  }
  .repository-title {
    font-size: 1.5rem;
  }

}
</style>

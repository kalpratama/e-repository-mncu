<template>
  <div class="dashboard-body">
    <!-- Top Header -->
    <header class="top-header">
      <div class="header-content">
        <div class="logo-container">
          <img :src="logo" alt="Company Logo" class="company-logo">
        </div>
        <div class="communities-link">
          <span>Communities in MNCU-IR</span>
        </div>
        <div>
          <!-- Conditionally show Login Button or Profile Bubble -->
          <button v-if="!isLoggedIn" @click="$emit('request-login')" class="login-button">Login</button>
          <ProfileBubble v-else :user="user" @logout="$emit('logout')" />
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
            <li v-for="item in publicationTypes" :key="item">
              <a href="#">{{ item }}</a>
            </li>
          </ol>
        </div>

        <!-- Right Box: Recently Published -->
        <div class="content-box">
          <h2 class="box-title">Baru Diterbitkan</h2>
          <div class="recent-publications">
            <div class="publication-item" v-for="(item, index) in recentPublications" :key="index">
              <h3>
                <a href="#">{{ item.title }}</a>
              </h3>
              <p class="meta">{{ item.meta }}</p>
              <p class="description">{{ item.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import logo from '../assets/mncu_logo_wide.png';
import ProfileCircle from './ProfileCircle.vue';

export default {
  components: {
    ProfileBubble: ProfileCircle
  },
  props: {
    isLoggedIn: {
      type: Boolean,
      default: false
    },
    user: {
      type: Object,
      default: () => ({})
    }
  },

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
  padding-top: 2rem;
}

.repository-title {
  color: #ffffff;
  text-align: center;
  font-size: 1.75rem;
  font-weight: 600;
  margin-top: 0;
  margin-bottom: 2rem;
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

/* --- Left Box List --- */
.publication-list {
  list-style-position: inside;
  padding-left: 0;
  margin: 0;
}

.publication-list li {
  margin-bottom: 0.5rem;
}

.publication-list a {
  text-decoration: none;
  color: #333;
  transition: color 0.2s;
}

.publication-list a:hover {
  color: #007bff;
}

/* --- Right Box List --- */
.recent-publications .publication-item {
  margin-bottom: 2rem;
}

.recent-publications .publication-item:last-child {
  margin-bottom: 0;
}

.publication-item h3 {
  margin: 0 0 0.25rem 0;
  font-size: 1.1rem;
}

.publication-item h3 a {
  text-decoration: none;
  color: #001F3D7B;
  font-weight: 600;
}

.publication-item h3 a:hover {
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

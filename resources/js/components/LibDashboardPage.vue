<template>
  <div class="page-body">
    <!-- Top Header -->
    <Header 
      :is-logged-in="isLoggedIn" 
      :user="user" 
      @request-login="$emit('request-login')" 
      @logout="$emit('logout')"
    />

    <!-- Main Content Area -->
    <main class="main-content">
      <h1 class="repository-title">Repositori Institusional MNC University</h1>
      <div v-if ="isLoading" class="loading-container">
        <p>Memuat...</p>
      </div>

      <div class="content-boxes">
        <!-- Left Column -->
        <div class="content-box left-column">
          <h2 class="box-title">Terbitan Pustaka</h2>
          <ol class="publication-list">
            <menu-item v-for="item in publicationTypes" :key="item.id" :item="item" />
          </ol>
        </div>

        <!-- Right Column -->
        <div class="right-column">
          <div class="content-box search-box">
            <SearchBar @perform-search="navigateToSearch" />
          </div>

          <!-- Recently Published Box below -->
          <div class="content-box recent-publication">
            <h2 class="box-title">Baru Diterbitkan</h2>
            <div class="recent-publications">
              <p v-if="recentPublications.length === 0">Belum ada publikasi terbaru.</p>
              <router-link :to="'/article/' + item.id" class="publication-item" v-for="item in recentPublications" :key="item.id">
                <p class="meta kategori">Kategori: {{ formatCategory(item) }}</p>
                <h3>{{ item.title }}</h3>
                <!-- The author names are now formatted by a helper method -->
                <p class="meta">{{ formatMeta(item) }}</p>
                <p class="description">{{ truncateAbstract(item.abstract) }}</p>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import Header from './Header.vue';
import MenuItem from './MenuItem.vue';
import SearchBar from './SearchBar.vue';
import axios from 'axios';

export default {
  components: {
    Header,
    MenuItem,
    SearchBar
  },
  props: {
    isLoggedIn: {
      type: Boolean,
      default: false
    },
    user: {
      type: Object,
      // default: () => ({})
    }
  },
  data() {
    return {
      publicationTypes: [],
      recentPublications: [],
      isLoading: true,
      searchQuery: ''
    };
  },
  created(){
    this.fetchDashboardData();
  },
  methods:{
    async fetchDashboardData() {
      try {
        const response = await axios.get('/api/dashboard-data');
        this.publicationTypes = response.data.publicationTypes;
        this.recentPublications = response.data.recentPublications;
      } catch (error) {
        console.error('Error fetching dashboard data:', error);
      } finally {
        this.isLoading = false;
      }
    },
    formatCategory(item) {
      // Check if the item and its nested document_type object exist
      if (item && item.document_type && item.document_type.name) {
        return item.document_type.name;
      }
      return 'Tidak Ditemukan';
    },

    formatMeta(item) {
      if (!item) return '';
      
      const authors = item.authors && item.authors.length > 0
        ? item.authors.map(author => {
            const parts = author.name.trim().split(' ');
            const firstName = parts.slice(0, -1).join(' ');
            const lastName = parts.slice(-1)[0];
            return `${lastName}, ${firstName}`;
          }).join('; ')
        : 'Unknown Author';

      const programs = item.authors
        .map(author => author.program_studi)
        .filter((prog, index, arr) => prog && arr.indexOf(prog) === index);

      let detailsInParens = [];
      if (programs.length > 0) detailsInParens.push(programs.join(', '));
      if (item.year) detailsInParens.push(item.year);

      return detailsInParens.length > 0
        ? `${authors} (${detailsInParens.join(', ')})`
        : authors;
    },
    truncateAbstract(text, wordLimit = 30) {
      if (!text) return '';
      const words = text.split(' ');
      if (words.length <= wordLimit) {
        return text;
      }
      return words.slice(0, wordLimit).join(' ') + '...';
    },
    formatAuthors(authors) {
      if (!authors || authors.length === 0) {
        return 'Unknown Author';
      }
      return authors.map(author => author.name).join(', ');
    },
    performSearch() {
      if (this.searchQuery.trim()) {
        this.$router.push({ name: 'Search', query: { q: this.searchQuery } });
      }
    },
    navigateToSearch(query) {
      this.$router.push({ name: 'Search', query: { q: query } });
    }
  }
}
</script>

<style scoped>
.page-body {
  background-color: #1F3D7B;
  font-family: 'Figtree', sans-serif;
  min-height: 100vh;
  /* overflow-x:hidden;
  overflow-y: auto; */
}
.loading-container {
  text-align: center;
  color: white;
  padding: 1rem;
  font-size: 1.2rem;
}
/* --- Main Content --- */
.main-content {
  padding-left: 5rem;
  padding-right: 5rem;
  padding-bottom: 3rem;
  padding-top: .5rem;
}
.repository-title {
  color: #ffffff;
  text-align: center;
  font-size: 1.5rem;
  font-weight: 600;
  margin-top: 0;
  margin-bottom: .5rem;
}
.content-boxes {
  display: flex;
  flex-direction: row;
  gap: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}
.content-boxes > .content-box:first-child {
  flex: 1;
}
.content-boxes > .content-box:last-child {
  flex: 2;
}
.content-box {
  background-color: #ffffff;
  border-radius: 8px;
  padding: 1.5rem 2rem; 
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.recent-publication{
  max-height: 500rem;
  max-width: 100%;
  padding-right: 1rem;
}
.left-column {
  flex: 1;
}
.right-column {
  flex: 2;
  display: flex;
  flex-direction: column;
  gap: 2rem;
  /* justify-content: space-between; */
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

/* --- Search Bar --- */
.search-bar-container {
  display: flex;
  gap: 0.5rem;
}
.search-input {
  flex-grow: 1;
  border: 1px solid #ccc;
  padding: 0.75rem;
  border-radius: 5px;
  font-size: 1rem;
}
.search-button {
  width: auto;
  padding: 0.75rem 1.5rem;
  background-color: #1F3D7B;
}
.search-button:hover {
  background-color: #0056b3;
}

/* --- Left Box List with Dropdowns --- */
.publication-list {
  list-style: none;
  padding-left: 0;
  margin: 0;
  /* max-height: 520px;
  overflow-y: auto;
  overflow-x: hidden;
  padding-right: 1rem; */
}
.publication-list li {
  position: relative;
  margin-bottom: 0.25rem;
}
.publication-list a {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: .3rem, 1rem;
  text-decoration: none;
  border-radius: 5px;
  box-shadow: 0 4px 5px rgba(0,0,0,0.15);
  transition: background-color 0.2s;
  cursor: pointer;
}
.publication-list li:hover > a {
  background-color: #e9ecef;
}
.dropdown-arrow {
  font-size: 0.8em;
  color: #888;
}

/* --- Dropdown Menu Styling --- */
.dropdown-menu {
  opacity: 0;
  visibility: hidden;
  position: absolute;
  left: 100%;
  top: -1px;
  list-style: none;
  padding: 0;
  margin: 0 0 0 5px;
  min-width: 200px;
  background-color: white;
  border: 1px solid #ddd;
  border-radius: 5px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  z-index: 10;
  transition: visibility 0s linear 0.2s, opacity 0.2s linear;
}
.publication-list li:hover > .dropdown-menu {
  visibility: visible;
  opacity: 1;
  transition-delay: 0s;
}
.recent-publications {
  max-height: 400px; /* Limit the height of this container */
  overflow-y: auto; /* Add vertical scrollbar only when needed */
  padding-right: 1rem; /* Add space so text doesn't touch the scrollbar */
}
/* --- Right Box List --- */
.recent-publications {
  max-height: 350px;
  max-width: 100%;
  overflow-y: auto;
  padding-right: 1rem;
}
a.publication-item {
  display: block;
  text-decoration: none;
  color: inherit;
  padding: 1.5rem;
  margin-bottom: .5rem;
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
  margin: 0 0 0.2rem 0;
}
.publication-item .description {
  font-size: 0.95rem;
  color: #444;
  line-height: 1.6;
  margin: 0;
}
button.login-button {
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
button.login-button:hover {
  background-color: #0056b3;
}
button.search-button{
  padding: 0.75rem 1.5rem;
  background-color: #1F3D7B;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.2s;
}
button.search-button:hover {
  background-color: #0056b3;
}

/* --- Responsive Design --- */
@media (max-width: 882px) {
  .content-boxes {
    flex-direction: column;
  }
  /* **** NEW: Reorder boxes on mobile **** */
  .right-column {
    display: contents; /* This makes children of .right-column direct flex items */
  }
  .search-box {
    order: -2; /* This moves the search box to the top */
  }
  .left-column {
      order: -1; /* This moves the publication list to the middle */
  }
  .recent-box {
    order: 0; /* This keeps the recent box at the bottom */
  }
  .dropdown-menu {
    left: 0;
    top: 100%;
    margin: 5px 0 0 0;
    width: 70%;
  }
  .dropdown-menu .dropdown-menu {
    left: 6rem;
    top: 100%;
    margin: 5px 0 0 0;
    width: 70%;
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

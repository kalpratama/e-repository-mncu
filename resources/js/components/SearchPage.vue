<template>
  <div class="page-body">
    <!-- Header -->
    <Header 
      :is-logged-in="isLoggedIn" 
      :user="user" 
      @request-login="$emit('request-login')" 
      @logout="$emit('logout')" 
    />

    <!-- Main Content -->
    <main class="main-content">
        <h1 class="search-title">Hasil Pencarian untuk: "{{ searchQuery }}"</h1>
      <div v-if="isLoading" class="loading-container">
        <p>Mencari...</p>
      </div>
      
      <div v-else class="content-boxes">
        
        <!-- Left Column -->
        <div class="content-box document-list">
          <SearchBar :initial-query="searchQuery" @perform-search="navigateToSearch" />
          <div v-if="results.length > 0">
            <router-link :to="'/article/' + item.id" class="publication-item" v-for="item in results" :key="item.id">
                <p class="meta">Kategori: {{ formatCategory(item) }}</p>
              <h3 class="document-title">{{ item.title }}</h3>
              <p class="meta">{{ formatMeta(item) }}</p>
              <p class="description">{{ truncateAbstract(item.abstract) }}</p>
            </router-link>
          </div>
          <p v-else class="no-documents-message">Tidak ada dokumen yang ditemukan.</p>
        </div>

        <!-- Right Column -->
        <!-- <div class="right-column">
          <div class="content-box search-box">
          </div>
        </div> -->
      </div>
    </main>
  </div>
</template>

<script>
import Header from './Header.vue';
import SearchBar from './SearchBar.vue';
import axios from 'axios';

export default {
  components: { 
    Header, 
    SearchBar 
  },
  props: { 
    isLoggedIn: Boolean, 
    user: Object 
  },
  data() {
    return {
      searchQuery: '',
      results: [],
      isLoading: true,
    };
  },
  watch: {
    // Watch for changes in the URL query string (e.g., a new search)
    '$route.query.q': {
      handler: 'performSearch',
      immediate: true
    }
  },
  methods: {
    async performSearch() {
      this.isLoading = true;
      this.searchQuery = this.$route.query.q || '';
      if (!this.searchQuery) {
        this.results = [];
        this.isLoading = false;
        return;
      }
      
      try {
        const response = await axios.get(`/api/search?q=${this.searchQuery}`);
        this.results = response.data;
        document.title = `Search: ${this.searchQuery}`;
      } catch (error) {
        console.error("Failed to perform search:", error);
        this.results = [];
      } finally {``
        this.isLoading = false;
      }
    },
    formatCategory(item) {
      // Check if the item and its nested document_type object exist
      if (item && item.document_type && item.document_type.name) {
        return item.document_type.name;
      }
      return 'Tidak Ditemukan'; // Fallback text
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

      // Get unique program_studi values from all authors
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
    truncateAbstract(text, wordLimit = 45) {
      if (!text) return '';
      const words = text.split(' ');
      if (words.length <= wordLimit) {
        return text;
      }
      return words.slice(0, wordLimit).join(' ') + '...';
    },
    navigateToSearch(query) {
      this.$router.push({ name: 'Search', query: { q: query } });
    },
    // performSearch() {
    //   if (this.searchQuery.trim()) {
    //     this.$router.push({ name: 'Search', query: { q: this.searchQuery } });
    //   }
    // }
  }
}
</script>

<style scoped>
.page-body {
  background-color: #1F3D7B;
  font-family: 'Figtree', sans-serif;
  min-height: 100vh;
}
.loading-container { 
  text-align: center; 
  color: white; 
  padding: 4rem; 
  font-size: 1.2rem; 
}
.main-content {
  padding-left: 5rem;
  padding-right: 5rem;
  padding-bottom: 9rem;
  padding-top: .5rem;
}
.search-title { 
  color: #ffffff;
  text-align: center;
  font-size: 1.5rem;
  font-weight: 600;
  margin-top: 0;
  margin-bottom: .5rem;
}
/* .content-boxes { 
  display: flex;
  flex-direction: row;
  gap: 2rem;
  max-width: 1400px;
  margin: 0 auto;
} */
.content-box.document-list { 
  background-color: #ffffff;
  border-radius: 8px;
  padding: 1.5rem 2rem; 
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  width:100%;
}
.content-box {
  background-color: #ffffff;
  border-radius: 8px;
  padding: 1.5rem 2rem; 
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.left-column {
  flex: 1;
}
.right-column {
  flex: 2;
  display: flex;
  flex-direction: column;
  gap: 2rem; /* Space between the two boxes on the right */
}

/* --- Search Bar --- */
.search-bar-container {
  display: flex;
  gap: 0.5rem;
  padding-bottom: 1rem;
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
.no-documents-message { text-align: center; font-size: 1.1rem; color: #666; padding: 2rem; }
a.publication-item { display: block; text-decoration: none; color: inherit; padding: 1.5rem; margin-bottom: 1rem; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; }
a.publication-item:hover { transform: translateY(-3px); box-shadow: 0 6px 15px rgba(0,0,0,0.15); }
a.publication-item:last-child { margin-bottom: 0; }
.publication-item h3 { margin: 0 0 0.25rem 0; font-size: 1.1rem; color: #1F3D7B; font-weight: 600; }
a.publication-item:hover h3 { text-decoration: underline; }
.publication-item .meta { font-size: 0.85rem; color: #666; margin: 0 0 0.5rem 0; }
.publication-item .description { font-size: 0.95rem; color: #444; line-height: 1.6; margin: 0; }
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
}

@media (max-width: 768px) {
  .main-content {
    padding: 0rem;
    padding-bottom: 8rem;
  }
  .search-title {
    font-size: 1rem;
    margin-top: 0.5rem;
  }
  .content-box.document-list{
    border-radius: 0px;
    padding: 1rem
  }
  h3.document-title{
    font-size: medium;
  }
  a.publication-item{
    padding: 0.65rem;
  }
  p.meta {
    font-size: small;
  }
  p.description {
    font-size: small;
  }
}
</style>
<template>
  <div class="page-body">
    <Header 
      :is-logged-in="isLoggedIn" 
      :user="user" 
      @request-login="$emit('request-login')" 
      @logout="$emit('logout')" 
    />

    <!-- Main Content -->
    <main class="main-content">
      <h1 class="category-title">Kategori: {{ category.name }}</h1>
      <div v-if="isLoading" class="loading-container">
        <p>Memuat Dokumen...</p>
      </div>
      
      <div v-else class="category-container">
        <!-- Left Column -->
        <aside class="filters-column">
            <div class="content-box">
              <h3 class="filter-title">Filter Dengan Tahun</h3>
              <div v-if="years.length > 0" class="filter-group">
                <div v-for="year in years" :key="year" class="checkbox-item">
                  <input type="checkbox" :id="'year-' + year" :value="year" v-model="selectedYears" @change="fetchCategoryData">
                  <label :for="'year-' + year">{{ year }}</label>
                </div>
              </div>
              <p v-else>Tidak ada tahun publikasi yang tersedia.</p>
            </div>
            <div class="content-box">
              <h3 class="filter-title">Filter Program Studi</h3>
              <div v-if="programStudi.length > 0" class="filter-group">
                <div v-for="prodi in programStudi" :key="prodi" class="radio-item">
                   <input type="checkbox" :id="'prodi-' + prodi" :value="prodi" v-model="selectedProdi" @change="fetchCategoryData">
                   <label :for="'prodi-' + prodi">{{ prodi }}</label>
                </div>
              </div>
              <p v-else>Tidak ada program studi yang tersedia.</p>
            </div>
          </aside>

        <!-- Right Column -->
        <!-- Main Contents -->
        <div class="content-box right-column">
        <SearchBar :initial-query="searchQuery" @perform-search="navigateToSearch" />

          <div v-if="documents.length > 0" class="document-list">
            <router-link :to="'/article/' + item.id" class="publication-item" v-for="item in documents" :key="item.id">
              <h3>{{ item.title }}</h3>
              <p class="meta">{{ formatMeta(item) }}</p>
              <p class="description">{{ truncateAbstract(item.abstract) }}</p>
            </router-link>
          </div>
          <p v-else class="no-documents-message">Belum ada dokumen pada kategori ini.</p>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import Header from './Header.vue';
import SearchBar from './SearchBar.vue';
import axios from 'axios';

export default {
  components: { Header, SearchBar },
  props: { isLoggedIn: Boolean, user: Object },
  data() {
    return {
      searchQuery: '',
      category: {},
      documents: [],
      years: [],
      programStudi: [],
      isLoading: true,
      selectedYears: [],
      selectedProdi: [],
    };
  },
  watch: {
    '$route.params.slug': {
      handler(newSlug, oldSlug) {
        if (newSlug !== oldSlug) {
          // When navigating to a new category, clear filters
          this.selectedYears = [];
          this.selectedProdi = [];
        }
        this.fetchCategoryData();
      },
      immediate: true
    }
  },
  methods: {
    async fetchCategoryData() {
      this.isLoading = true;
      const slug = this.$route.params.slug;

      const params = new URLSearchParams();
      
      // Add multiple year filters
      if (Array.isArray(this.selectedYears) && this.selectedYears.length > 0) {
        this.selectedYears.forEach(year => {
          params.append('years[]', year);
        });
      }
      
      // Add multiple program studi filters
      if (Array.isArray(this.selectedProdi) && this.selectedProdi.length > 0) {
        this.selectedProdi.forEach(prodi => {
          params.append('prodi[]', prodi);
        });
      }

      const queryString = params.toString();
      const url = queryString ? `/api/category/${slug}?${queryString}` : `/api/category/${slug}`;

      try {
        const response = await axios.get(url);
        this.category = response.data.category;
        this.documents = response.data.documents.data; // We get paginated data
        
        // Always show all available filters
        this.years = response.data.years;
        this.programStudi = response.data.program_studi;
        
        document.title = `Kategori: ${this.category.name}`;
      } catch (error) {
        console.error("Gagal fetching data kategori:", error);
        this.category = { name: 'Tidak Ditemukan' };
        this.documents = [];
      } finally {
        this.isLoading = false;
      }
    },

    // async fetchCategoryData() {
    //   this.isLoading = true;
    //   const slug = this.$route.params.slug;

    //   const params = new URLSearchParams();
    //   if (this.selectedYear) {
    //     params.append('year', this.selectedYear);
    //   }
    //   if (this.selectedProdi) {
    //     params.append('prodi', this.selectedProdi);
    //   }
    //   const queryString = params.toString();

    //   try {
    //     const response = await axios.get(`/api/category/${slug}?${queryString}`);
    //     this.category = response.data.category;
    //     this.documents = response.data.documents.data; // We get paginated data
    //     if (!this.selectedYear && !this.selectedProdi) {
    //         this.years = response.data.years;
    //         this.programStudi = response.data.program_studi;
    //     }
    //     document.title = `Category: ${this.category.name}`;
    //   } catch (error) {
    //     console.error("Failed to fetch category data:", error);
    //     this.category = { name: 'Not Found' };
    //     this.documents = [];
    //   } finally {
    //     this.isLoading = false;
    //   }
    // },
    formatMeta(item) {
      if (!item) return '';
      const authors = item.authors && item.authors.length > 0 
        ? item.authors.map(author => author.name).join(', ') 
        : 'Unknown Author';
      let detailsInParens = [];
      if (item.authors[0].program_studi) detailsInParens.push(item.authors[0].program_studi);
      if (item.year) detailsInParens.push(item.year);
      if (detailsInParens.length > 0) {
        return `${authors} (${detailsInParens.join(', ')})`;
      }
      return authors;
    },
    truncateAbstract(text, wordLimit = 20) {
      if (!text) return '';
      const words = text.split(' ');
      if (words.length <= wordLimit) {
        return text;
      }
      return words.slice(0, wordLimit).join(' ') + '...';
    },
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
    filterByYear(year) {
      // If clicking the same year again, clear the filter
      this.selectedYear = this.selectedYear === year ? null : year;
      this.fetchCategoryData();
    },
    filterByProdi(prodi) {
      this.selectedProdi = this.selectedProdi === prodi ? null : prodi;
      this.fetchCategoryData();
    },
    clearFilters() {
      this.selectedYear = null;
      this.selectedProdi = null;
      this.fetchCategoryData();
    },
  }
}
</script>

<style scoped>
.page-body { 
    background-color: #1F3D7B;
    min-height: 100vh; 
    font-family: 'Figtree', sans-serif;
}
.main-content { 
  padding-left: 5rem;
  padding-right: 5rem;
  padding-bottom: 3rem;
  padding-top: .5rem;
}
.loading-container {   
    text-align: center; 
    color: white; 
    padding: 4rem; 
    font-size: 1.2rem; 
}
.category-container {
  display: flex;
  flex-direction: row;
  gap: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}
.category-title { 
    color: #ffffff;
  text-align: center;
  font-size: 1.5rem;
  font-weight: 600;
  margin-top: 0;
  margin-bottom: .5rem; 
}
.content-box { 
    background-color: #ffffff; 
    border-radius: 8px; 
    padding: 1.5rem 2rem; 
    box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
}
.content-box {
  background-color: #ffffff;
  border-radius: 8px;
  padding: 1.5rem 2rem; 
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.right-column {
  flex: 2;
  display: flex;
  flex-direction: column;
  gap: 2rem; /* Space between the two boxes on the right */
}
.document-list {
  max-height: 500px; /* Limit the height of this container */
  overflow-y: auto; /* Add vertical scrollbar only when needed */
  padding-right: 1rem; /* Add space so text doesn't touch the scrollbar */

}
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
    padding: 0.75rem 1.25rem; 
    background-color: #1F3D7B; 
    color: white; 
    border: none; 
    border-radius: 8px; 
    cursor: pointer; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    transition: background-color 0.2s; 
}
.search-button:hover { 
    background-color: #0056b3; 
}
.document-list { 
    padding-top: 1rem; 
}
.no-documents-message { 
    text-align: center; 
    font-size: 1.1rem; 
    color: #666; 
    padding: 2rem; 
}
a.publication-item { 
    display: block; 
    text-decoration: none; 
    color: inherit; 
    padding: 1.5rem; 
    margin-bottom: 1rem; 
    border-radius: 6px; 
    box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; 
}
a.publication-item:hover { 
    transform: translateY(-3px); 
    box-shadow: 0 6px 15px rgba(0,0,0,0.15); 
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

/* years box */
/* **** NEW STYLES for the two-column layout **** */
.category-grid {
  display: grid;
  grid-template-columns: 1fr 3fr; /* 1 part for filters, 3 parts for documents */
  gap: 2rem;
}
.filters-column {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}
.filter-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 0.5rem;
  margin-bottom: -1rem;
}
.clear-button {
  background: none;
  border: none;
  color: #0056b3;
  cursor: pointer;
  font-size: 0.8rem;
  font-weight: 600;
}
.filter-title {
  margin-top: 0;
  margin-bottom: 1rem;
  font-size: 1.1rem;
  font-weight: 600;
  border-bottom: 1px solid #eee;
  padding-bottom: 0.75rem;
}
.filter-list {
  list-style: none;
  padding: 0;
  margin: 0;
}
.filter-list li a {
  display: block;
  padding: 0.5rem 0.25rem;
  text-decoration: none;
  color: #333;
  border-radius: 4px;
  transition: color 0.2s, background-color 0.2s;
}
.filter-list li a:hover {
  color: #0056b3;
}
.filter-list li a.active {
  background-color: #1F3D7B;
  color: white;
  font-weight: 600;
  padding-left: 0.5rem;
}
.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.radio-item {
  display: flex;
  align-items: center;
}
.radio-item input[type="radio"] {
  margin-right: 0.5rem;
  accent-color: #1F3D7B; /* Modern way to color radio buttons */
}
.radio-item label {
  cursor: pointer;
}
a {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.35rem 1rem;
  text-decoration: none;
  border-radius: 5px;
  box-shadow: 0 4px 5px rgba(0,0,0,0.15);
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  cursor: pointer;
}
li:hover  > a{
  /* background-color: #e9ecef; */
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.18);
}

@media (max-width: 882px) {
  .category-container {
    flex-direction: column;
  }
  .content-boxes {
    flex-direction: column;
  }
  .search-box {
    order: -2; 
  }
  .right-column {
      order: -1;
    }
  .recent-box {
    order: 0; 
  }
  .filters-column {
    flex-direction: row;
    justify-content: space-between;
  }
}

@media (max-width: 768px) {
  .main-content {
    padding: 1rem;
  }
}
</style>
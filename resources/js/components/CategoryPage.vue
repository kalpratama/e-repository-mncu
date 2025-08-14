<template>
  <div class="page-body">
    <!-- Top Header -->
    <Header 
      :is-logged-in="isLoggedIn" 
      :user="user" 
      @request-login="$emit('request-login')" 
      @logout="$emit('logout')" 
    />

    <!-- Main Content -->
    <main class="main-content">
      <h1 class="category-title">{{ category.name }}</h1>
      <div v-if="isLoading" class="loading-container">
        <p>Memuat Dokumen...</p>
      </div>
      
      <div v-else class="content-boxes">
        <!-- Left Column -->
        <div class="content-box left-filter" :class="{ 'open': isFilterOpen }">
            <div v-if="roles.length > 1" class="sub-filter">
              <h3 class="filter-title">Filter Role Penulis</h3>
              <div class="filter-group">
                <div v-for="role in roles" :key="role" class="checkbox-item">
                  <input type="checkbox" :id="'role-' + role" :value="role" :checked="selectedRoles.includes(role)" @change="filterByRole(role)">
                  <label :for="'role-' + role">{{ role }}</label>
                </div>
              </div>
            </div>
            <div class="sub-filter">
              <h3 class="filter-title">Filter Program Studi</h3>
              <div v-if="programStudi.length > 0" class="filter-group">
                <div v-for="prodi in programStudi" :key="prodi" class="checkbox-item">
                   <input type="checkbox" :id="'prodi-' + prodi" :value="prodi" v-model="selectedProdi" @change="fetchCategoryData">
                   <label :for="'prodi-' + prodi">{{ prodi }}</label>
                </div>
              </div>
              <p v-else>-</p>
            </div>
            <div class="sub-filter">
              <h3 class="filter-title">Filter Tahun</h3>
              <div v-if="years.length > 0" class="filter-group">
                <div v-for="year in years" :key="year" class="checkbox-item">
                  <input type="checkbox" :id="'year-' + year" :value="year" v-model="selectedYears" @change="fetchCategoryData">
                  <label :for="'year-' + year">{{ year }}</label>
                </div>
              </div>
              <p v-else>-</p>
            </div>
        </div>

        <!-- Right Column -->
         <div class="right-column">
          <div class="content-box search-container">
            <div class="content-box filter-burger"> 
              <button class="filter-toggle" @click="isFilterOpen = !isFilterOpen">
                ☰
              </button>
            </div>
            <div class="content-box search-box">
              <SearchBar @perform-search="navigateToSearch" />
            </div>
          </div>
          
            <div class="content-box document-box">
          <div v-if="paginatedDocuments.length > 0" class="document-list">
            <router-link :to="'/article/' + item.id" class="publication-item" v-for="item in paginatedDocuments" :key="item.id">
              <p v-if="isAllCategory" class="meta kategori">Kategori: {{ formatCategory(item) }}</p>
              <h3 class="document-title">{{ item.title }}</h3>
              <p class="meta">{{ formatMeta(item) }}</p>
              <p class="description">{{ truncateAbstract(item.abstract) }}</p>
            </router-link>
          </div>
          <p v-else class="no-documents-message">Belum ada dokumen pada kategori ini.</p>
          
          <!-- CLIENT-SIDE PAGINATION COMPONENT - NEW ADDITION -->
          <div v-if="totalPages > 1" class="pagination-container">
            <div class="pagination-controls">
              <button 
                class="pagination-btn prev-btn" 
                :disabled="currentPage === 1"
                @click="goToPage(currentPage - 1)"
              >←</button>
              
              <div class="pagination-numbers">
                <button 
                  v-for="page in visiblePages" 
                  :key="page"
                  class="pagination-btn page-btn"
                  :class="{ active: page === currentPage }"
                  @click="goToPage(page)"
                >
                  {{ page }}
                </button>
              </div>
              
              <button 
                class="pagination-btn next-btn"
                :disabled="currentPage === totalPages"
                @click="goToPage(currentPage + 1)"
              >→</button>
            </div>
            <div class="pagination-info">
              <p>Menampilkan {{ startIndex + 1 }}-{{ Math.min(endIndex, allDocuments.length) }} dari {{ allDocuments.length }} dokumen</p>
            </div>
          </div>
        </div>

        </div>
        <!-- Main Contents -->
        
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
      allDocuments: [],
      years: [],
      selectedYears: [],
      programStudi: [],
      selectedProdi: [],
      roles: [],
      selectedRoles: [],
      isLoading: true,
      currentPage: 1,
      itemsPerPage: 3,

      isFilterOpen: false,
    };
  },
  computed: {
    isAllCategory() {
      return this.$route.params.slug === 'all';
    },
    // CLIENT-SIDE PAGINATION COMPUTED PROPERTIES - NEW ADDITION
    totalPages() {
      return Math.ceil(this.allDocuments.length / this.itemsPerPage);
    },
    startIndex() {
      return (this.currentPage - 1) * this.itemsPerPage;
    },
    endIndex() {
      return this.startIndex + this.itemsPerPage;
    },
    paginatedDocuments() {
      return this.allDocuments.slice(this.startIndex, this.endIndex);
    },
    visiblePages() {
      const current = this.currentPage;
      const total = this.totalPages;
      const pages = [];
      
      // Show up to 5 page numbers
      let start = Math.max(1, current - 2);
      let end = Math.min(total, current + 2);
      
      // Adjust if we're near the beginning or end
      if (current <= 3) {
        end = Math.min(total, 5);
      }
      if (current > total - 2) {
        start = Math.max(1, total - 4);
      }
      
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      
      return pages;
    },
    // BACKWARD COMPATIBILITY - Keep documents computed for template
    documents() {
      return this.allDocuments;
    }
  },

  watch: {
    '$route.params.slug': {
      handler(newSlug, oldSlug) {
        if (newSlug !== oldSlug) {
          // When navigating to a new category, clear filters and reset pagination
          this.selectedYears = [];
          this.selectedProdi = [];
          this.selectedRoles = [];
          this.currentPage = 1; // RESET PAGINATION - NEW ADDITION
        }
        this.fetchCategoryData();
      },
      immediate: true
    }
  },
  methods: {
    async fetchCategoryData() {
      this.isLoading = true;
      const slug = this.$route.params.slug ?? 'all';

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

      if (Array.isArray(this.selectedRoles) && this.selectedRoles.length > 0) {
        this.selectedRoles.forEach(role => {
          params.append('roles[]', role);
        });
      }

      const queryString = params.toString();
      const url = queryString ? `/api/category/${slug}?${queryString}` : `/api/category/${slug}`;

      try {
        const response = await axios.get(url);
        this.category = response.data.category;
        
        // STORE ALL DOCUMENTS - MODIFIED
        this.allDocuments = Array.isArray(response.data.documents) 
          ? response.data.documents 
          : (response.data.documents.data || []); // Handle both array and paginated response
        
        // Always show all available filters
        this.years = response.data.years;
        this.programStudi = response.data.program_studi;
        this.roles = response.data.roles || []; // Ensure roles is always an array
        
        document.title = `${this.category.name}`;
      } catch (error) {
        console.error("Gagal fetching data kategori:", error);
        this.category = { name: 'Tidak Ditemukan' };
        this.allDocuments = []; // RESET ALL DOCUMENTS - MODIFIED
      } finally {
        this.isLoading = false;
      }
    },
    // CLIENT-SIDE PAGINATION METHOD - NEW ADDITION
    goToPage(page) {
      if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
        this.currentPage = page;
        // Scroll to top of document list
        const documentBox = document.querySelector('.document-box');
        if (documentBox) {
          documentBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }
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
    formatCategory(item) {
      if (item && item.document_type && item.document_type.name) {
        return item.document_type.name;
      }
      return 'Tidak Ditemukan';
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
      } finally {
        this.isLoading = false;
      }
    },
    navigateToSearch(query) {
      this.$router.push({ name: 'Search', query: { q: query } });
    },
    filterByYear(year) {
      // If clicking the same year again, clear the filter
      this.selectedYear = this.selectedYear === year ? null : year;
      this.currentPage = 1; // RESET PAGINATION ON FILTER CHANGE - NEW ADDITION
      this.fetchCategoryData();
    },
    filterByProdi(prodi) {
      this.selectedProdi = this.selectedProdi === prodi ? null : prodi;
      this.currentPage = 1; // RESET PAGINATION ON FILTER CHANGE - NEW ADDITION
      this.fetchCategoryData();
    },
    filterByRole(role) {
      const index = this.selectedRoles.indexOf(role);
      if (index === -1) {
        this.selectedRoles.push(role);
      } else {
        this.selectedRoles.splice(index, 1);
      }
      this.currentPage = 1; // RESET PAGINATION ON FILTER CHANGE - NEW ADDITION
      this.fetchCategoryData();
    },
    clearFilters() {
      this.selectedYear = null;
      this.selectedProdi = null;
      this.selectedRoles = [];
      this.currentPage = 1; // RESET PAGINATION ON CLEAR FILTERS - NEW ADDITION
      this.fetchCategoryData();
    },
  }
}
</script>

<style scoped>
/* CLIENT-SIDE PAGINATION STYLES - NEW ADDITION */
.pagination-container {
  margin-top: 0.5rem;
  padding-top: 0.5rem;
  border-top: 1px solid #e5e7eb;
}

.pagination-info {
  text-align: center;
  margin-bottom: 1rem;
}

.pagination-info p {
  color: #6b7280;
  font-size: 0.875rem;
  margin: 0;
}

.pagination-controls {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.4rem;
  flex-wrap: wrap;
  margin-bottom: 0.5rem;
}

.pagination-numbers {
  display: flex;
  gap: 0.25rem;
}

.pagination-btn {
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  background-color: white;
  color: #374151;
  border-radius: 0.375rem;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s ease;
  min-width: 2.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.pagination-btn:hover:not(:disabled) {
  background-color: #f3f4f6;
  border-color: #9ca3af;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  background-color: #f9fafb;
  color: #9ca3af;
}

.pagination-btn.active {
  background-color: #3b82f6;
  border-color: #3b82f6;
  color: white;
}

.pagination-btn.active:hover {
  background-color: #2563eb;
}

.prev-btn, .next-btn {
  padding: 0.5rem 0.75rem;
}

.page-body { 
    background-color: #1F3D7B;
    min-height: 100vh; 
    font-family: 'Figtree', sans-serif;
}
.main-content { 
  padding-left: 5rem;
  padding-right: 5rem;
  padding-bottom: 0rem;
  padding-top: .5rem;
}
.loading-container {   
    text-align: center; 
    color: white; 
    padding: 4rem; 
    font-size: 1.2rem; 
}
.content-boxes {
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
    border-radius: 12px; 
    padding: 1rem 2rem; 
    /* box-shadow: 0 4px 12px rgba(0,0,0,0.15);  */
}
.right-column {
  background-color: #ffffff;
  border-radius: 12px;
  flex: 2;
  display: flex;
  flex-direction: column;
  gap: 0rem;
}
.document-list {
  height: 450px;
  overflow-y: auto;
  padding-right: 1rem;
  padding-left: 0.2rem;
  padding-bottom: 0.5rem;

}
.search-bar-container { 
    display: flex; 
    gap: 0.5rem;
}
.document-list { 
    padding-top: 0.5rem; 
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
    border-radius: 15px; 
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
  background-color: #ffffff;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  /* gap: 1.5rem; */
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
  font-size: 1.1rem;
  font-weight: 600;
  padding-top: 0.75rem;
  padding-bottom: 0.25rem;
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
  gap: 0rem;
}
.checkbox-item{
  /* display: flex;
  gap: 0.5rem; */
  display: flex;
  /* justify-content: space-between; */
  padding-left: 0.5rem;
  align-items: center;
  text-decoration: none;
  /* color: #1F3D7B; */
  border-radius: 5px;
  box-shadow: 0 4px 5px rgba(0,0,0,0.15);
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  margin-bottom: 0.1rem;
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
}
label{
  padding-left: 1rem;
  width: 100%;
  cursor: pointer;
  padding: 0.2rem 1rem;
}
.checkbox-item:hover{
  /* background-color: #e9ecef; */
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.18);
}
.left-filter {
  width: 250px;
  transition: transform 0.3s ease-in-out;
}
.filter-burger{
  padding:0rem;
}
.search-box {
  padding: 0rem;
}

@media (max-width: 882px) {
  .content-boxes {
    flex-direction: column;
  }
  .right-column {
    display: contents; /* This makes children of .right-column direct flex items */
  }
  .search-box {
    order: -2; 
  }
  .left-filter {
      order: -1;
  }
  .document-box {
    order: 0; 
  }
  .filters-column {
    flex-direction: row;
    justify-content: space-between;
  }
}

@media (max-width: 768px) {
  .main-content {
    padding: 0rem;
  }
  .content-box{
    border-radius: 0px;
  }
  .search-container{
    display: inline-block;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    gap: 0rem;
    padding: 0.3rem;
  }
  .content-box .filter-burger {
    padding: 0.5rem;
    align-items: center;
  }
  .content-box .search-box {
    padding: 0.2rem;
    padding-top: .75rem;
    border-radius: 0px;
    order: 1;
    width: 100%; 
  }
  .right-column {
    /* padding: 0.5rem; */
    gap: 0rem
  }
  .content-boxes {
    display: flex;
    flex-direction: column;
    padding: 0rem;
    gap: 0rem;
  }
  .category-title {
    font-size: 1rem;
    margin-top: 0.5rem;
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
  .content-box{
    padding: 1.5rem;
  }
  .document-list{
    padding-right: 0rem;
    overflow-y: auto;
    max-height: 10000px;
  }
  h3.document-title{
    font-size: medium;
  }
  .filter-title {
    font-size: 1rem;
  }
  label{
    font-size: small;
  }
  .left-filter{
    padding-left: 2rem;
    padding-right: 2rem;
    padding-top: 0rem;
    padding-bottom: 0rem;
  }
  .filters-column {
    justify-content: center;
    border-radius: 0px;
  }
  .left-filter {
    position: fixed;
    top: 0;
    left: 0;
    max-height: 60%;
    background: #fff;
    box-shadow: 2px 0 8px rgba(0,0,0,0.2);
    transform: translateY(1440px);
    z-index: 1000;
    overflow-y: auto;
    border-radius: 15px;
    padding: 1rem;
    box-shadow: 0 9px 15px #1F3D7B;
  }

  .left-filter.open {
    transform: translateX(0);
    transform: translateY(200px);
    max-height: 60%;
    overflow-y: auto;
    border-radius: 15px;
    padding: 1rem;
    box-shadow: 0 9px 15px #1F3D7B;
  }

  .filter-toggle {
    display: inline-block;
    margin: 5px;
    /* padding: 0.75rem 1.25rem; */
    /* background-color: #1F3D7B; */
    color: black;
    font-size: larger;
    border: none;
    border-radius: 15px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s;
  }
}
@media (min-width: 769px) {
  .filter-toggle {
    display: none; /* hide button on wide screens */
  }
}
</style>
<template>
  <div class="page-body">
    <Header :is-logged-in="isLoggedIn" :user="user" @request-login="$emit('request-login')" @logout="$emit('logout')" />
    <main class="main-content">
        <h1 class="category-title">Kategori: {{ category.name }} ({{ year }})</h1>
      <div v-if="isLoading" class="loading-container"><p>Loading documents...</p></div>
      <div v-else class="category-container">
        <div class="content-box document-list">
          <div v-if="documents.length > 0">
            <router-link :to="'/article/' + item.id" class="publication-item" v-for="item in documents" :key="item.id">
              <h3>{{ item.title }}</h3>
              <p class="meta">{{ formatMeta(item) }}</p>
              <p class="description">{{ truncateAbstract(item.abstract) }}</p>
            </router-link>
          </div>
          <p v-else class="no-documents-message">No documents found for this year in this category.</p>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import Header from './Header.vue';
import axios from 'axios';
export default {
  components: { Header },
  props: { isLoggedIn: Boolean, user: Object },
  data() {
    return {
      category: {},
      documents: [],
      year: this.$route.params.year,
      isLoading: true,
    };
  },
  watch: {
    '$route.params.year': {
      handler: 'fetchYearData',
      immediate: true
    }
  },
  methods: {
    async fetchYearData() {
      this.isLoading = true;
      const slug = this.$route.params.slug;
      const year = this.$route.params.year;
      this.year = year;
      try {
        // We use the same category endpoint but add a query parameter for the year
        const response = await axios.get(`/api/category/${slug}?year=${year}`);
        this.category = response.data.category;
        this.documents = response.data.documents.data;
        document.title = `Category: ${this.category.name} (${year})`;
      } catch (error) {
        console.error("Failed to fetch year data:", error);
      } finally {
        this.isLoading = false;
      }
    },
    // ... (copy formatMeta and truncateAbstract methods here)
    formatMeta(item) {
      if (!item) return '';
      const authors = item.authors && item.authors.length > 0 
        ? item.authors.map(author => author.name).join(', ') 
        : 'Unknown Author';
      let detailsInParens = [];
      if (item.program_studi) detailsInParens.push(item.program_studi);
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
  }
}
</script>
<style scoped>
/* You can reuse many styles from CategoryPage.vue */
.page-body { 
    background-color: #1F3D7B; 
    min-height: 100vh; 
    font-family: 'Figtree', sans-serif;
}
.main-content { 
  padding-left: 5rem;
  padding-right: 5rem;
  padding-bottom: 9rem;
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
  max-width: 1400px;
  align-content: center;
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
.left-column {
  flex: 1;
}
.right-column {
  flex: 2;
  display: flex;
  flex-direction: column;
  gap: 2rem; /* Space between the two boxes on the right */
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
  /* This column will hold the year filter box */
}
.documents-column {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}
.filter-title {
  margin-top: 0;
  margin-bottom: 1.5rem;
  font-size: 1.25rem;
  font-weight: 600;
  align-content: center;
  border-bottom: 1px solid #eee;
  padding-bottom: 0.75rem;
}
.year-list {
  list-style: none;
  padding: 0;
  margin: 0;
}
.year-list li a {
  display: block;
  padding: 0.5rem 0;
  text-decoration: none;
  color: #333;
  transition: color 0.2s;
}
.year-list li a:hover {
  color: #0056b3;
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
li:hover > a {
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
  .right-column {
    display: contents;
  }
  .search-box {
    order: -2; 
  }
  .left-column {
      order: -1;
    }
  .recent-box {
    order: 0; 
  }
}

@media (max-width: 768px) {
  .main-content {
    padding: 1rem;
  }
}
</style>
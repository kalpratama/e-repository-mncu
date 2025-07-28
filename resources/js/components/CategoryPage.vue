<template>
  <div class="page-body">
    <Header 
      :is-logged-in="isLoggedIn" 
      :user="user" 
      @request-login="$emit('request-login')" 
      @logout="$emit('logout')" 
    />

    <main class="main-content">
      <div v-if="isLoading" class="loading-container">
        <p>Loading documents...</p>
      </div>
      
      <div v-else class="category-container">
        <h1 class="category-title">Category: {{ category.name }}</h1>
        
        <div class="content-box search-box">
          <!-- Reusing the search bar component -->
          <div class="search-bar-container">
            <input type="text" placeholder="Search within this category..." class="search-input">
            <button class="search-button" aria-label="Search">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
          </div>
        </div>

        <div class="content-box document-list">
          <div v-if="documents.length > 0">
            <router-link :to="'/article/' + item.id" class="publication-item" v-for="item in documents" :key="item.id">
              <h3>{{ item.title }}</h3>
              <p class="meta">{{ formatMeta(item) }}</p>
              <p class="description">{{ truncateAbstract(item.abstract) }}</p>
            </router-link>
          </div>
          <p v-else class="no-documents-message">No documents are available for this category yet.</p>
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
      isLoading: true,
    };
  },
  watch: {
    // Watch for changes in the route (e.g., navigating from one category to another)
    '$route.params.slug': {
      handler: 'fetchCategoryData',
      immediate: true // Run the handler immediately when the component is created
    }
  },
  methods: {
    async fetchCategoryData() {
      this.isLoading = true;
      const slug = this.$route.params.slug;
      try {
        const response = await axios.get(`/api/category/${slug}`);
        this.category = response.data.category;
        this.documents = response.data.documents.data; // We get paginated data
        document.title = `Category: ${this.category.name}`;
      } catch (error) {
        console.error("Failed to fetch category data:", error);
        this.category = { name: 'Not Found' };
        this.documents = [];
      } finally {
        this.isLoading = false;
      }
    },
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
    }
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
    max-width: 900px; 
    margin: 0 auto; 
    display: flex; 
    flex-direction: column; 
    gap: 2rem; 
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
</style>
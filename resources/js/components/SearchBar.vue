<template>
  <form @submit.prevent="submitSearch" class="search-bar-container">
    <input 
      type="text" 
      v-model="internalQuery" 
      :placeholder="placeholder" 
      class="search-input"
    >
    <button type="submit" class="search-button" aria-label="Search">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
    </button>
  </form>
</template>

<script>
export default {
  name: 'SearchBar',
  props: {
    // Allows the parent page to set the initial search query
    initialQuery: {
      type: String,
      default: ''
    },
    placeholder: {
      type: String,
      default: 'Cari di MNCU-IR'
    }
  },
  data() {
    return {
      // We use an internal data property to avoid directly modifying the prop
      internalQuery: this.initialQuery
    };
  },
  methods: {
    submitSearch() {
      if (this.internalQuery.trim()) {
        // When submitted, it emits an event to the parent page with the query
        this.$emit('perform-search', this.internalQuery);
      }
    }
  },
  watch: {
    // If the parent page's query changes (e.g., browser back button), update the input
    initialQuery(newQuery) {
      this.internalQuery = newQuery;
    }
  }
}
</script>

<style scoped>
/* Styles for the search bar itself */
.search-bar-container {
  display: flex;
  gap: 0.5rem;
  width: 100%;
}
.search-input {
  flex-grow: 1;
  border: 1px solid #ccc;
  padding: 0.75rem;
  border-radius: 15px;
  font-size: 1rem;
}
.search-button {
  width: auto;
  padding: 0.75rem 1.25rem;
  background-color: #1F3D7B;
  color: white;
  border: none;
  border-radius: 15px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s;
}
.search-button:hover {
  background-color: #0056b3;
}
/* .search-input{
    text-align: center;
} */
@media (max-width: 600px) {
  .search-input {
    width: 100%;
  }
  .search-button {
    width: 20%;
    text-align: center;
    padding: 0rem;
  }
  .search-input{
    padding: 0.5rem;
    font-size: 0.9rem;
  }
}
</style>
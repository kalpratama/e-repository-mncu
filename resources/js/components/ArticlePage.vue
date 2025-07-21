<template>
  <div class="page-body">
    <!-- Reusable Header -->
    <Header 
      :is-logged-in="isLoggedIn" 
      :user="user" 
      @request-login="$emit('request-login')" 
      @logout="$emit('logout')" 
    />

    <!-- Main Content Area -->
    <main class="main-content">
      <h1 class="repository-title">{{ article.title }}</h1>
      <div class="content-boxes">
        <!-- Left Column: Article Details -->
        <div v-if="article" class="content-box article-details">
          <div class="article-header">
            <div class="title-section">
              <h1>{{ article.title }}</h1>
              <p class="meta">{{ article.meta }}</p>
            </div>
            <div class="thumbnail-section">
              <img :src="article.thumbnail || 'https://placehold.co/100x120/e2e8f0/718096?text=No%0AImage'" alt="Article Thumbnail" class="thumbnail">
            </div>
          </div>
          <div class="abstract-section">
            <h2 class="box-title"> Abstrak</h2>
            <p>{{ article.description }}</p>
          </div>
        </div>
        <!-- Fallback if article not found -->
        <div v-else class="content-box article-details">
          <h1>Article Not Found</h1>
          <p>The article you are looking for does not exist.</p>
        </div>

        <!-- Right Column: Search and other info -->
        <div class="right-column">
          <div class="content-box search-box">
            <div class="search-bar-container">
              <input type="text" placeholder="Search the repository..." class="search-input">
              <button class="search-button" aria-label="Search">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
              </button>
            </div>
          </div>
          <div class="content-box">
            <p>Additional information or related articles can be displayed here.</p>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import Header from './Header.vue';
import dummyData from '../data/dummyData.json';

export default {
  components: {
    Header
  },
  props: {
    isLoggedIn: Boolean,
    user: Object
  },
  data() {
    return {
      article: null
    }
  },
  
  created() {
    const articleId = parseInt(this.$route.params.id);
    if (articleId > 0 && articleId <= dummyData.recentPublications.length) {
      this.article = dummyData.recentPublications[articleId - 1];

      if (this.article){
        document.title = this.article.title + ' | MNCU-IR';
      }
    }
  }
}
</script>

<style scoped>
.page-body {
  background-color: #1F3D7B;
  font-family: 'Figtree', sans-serif;
  min-height: 100vh;
}

.main-content {
  padding-left: 5rem;
  padding-right: 5rem;
  padding-bottom: 9rem;
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

.article-details {
  flex: 2; /* Takes up more space */
}

.right-column {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.box-title {
  margin-top: 0;
  margin-bottom: 1.5rem;
  font-size: 1.25rem;
  font-weight: 600;
  align-content: center;
  padding-bottom: 0.1rem;
}

.article-header {
  display: flex;
  justify-content: space-between;
  gap: 2rem;
  border-bottom: 1px solid #eee;
  padding-bottom: 1.5rem;
  margin-bottom: 1.5rem;
}

.title-section h1 {
  margin: 0 0 0.5rem 0;
  font-size: 2rem;
  line-height: 1.2;
  color: #1F3D7B;
}

.meta {
  font-size: 1rem;
  color: #666;
  margin: 0;
}

.thumbnail {
  display: block;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.abstract-section h2 {
  margin: 0 0 1rem 0;
  font-size: 1.25rem;
}

.abstract-section p {
  line-height: 1.7;
  text-align: justify;
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

@media (max-width: 882px) {
  .content-boxes {
    flex-direction: column;
  }

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

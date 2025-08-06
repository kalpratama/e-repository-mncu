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
      <div v-if="isLoading" class="loading-container">
        <p>Memuat artikel untuk diedit...</p>
      </div>
      <div v-else class="form-container">
        <h1 class="form-title">Edit Dokumen</h1>
        <p class="form-subtitle">Perbaharui detail dokumen </p>

        <form @submit.prevent="submitUpdate" enctype="multipart/form-data">
          <!-- Success/Error Messages -->
          <div v-if="successMessage" class="message success-message">
            {{ successMessage }}
          </div>
          <div v-if="errorMessage" class="message error-message">
            <p>{{ errorMessage }}</p>
            <ul v-if="validationErrors"><li v-for="(error, key) in validationErrors" :key="key">{{ error[0] }}</li></ul>
          </div>

          <div class="form-columns">
            <!-- Left Column -->
            <div class="form-column">
              <fieldset class="form-section">
                <legend>Detail Dokumen</legend>
                <div class="form-group">
                  <label for="title">Judul</label>
                  <input type="text" id="title" v-model="form.title" required>
                </div>
                <div class="form-group">
                  <label>Kategori Tingkat 1</label>
                  <select v-model="selectedLevel1" required>
                    <option disabled value="">Pilih Tipe</option>
                    <option v-for="type in documentTypes" :key="type.id" :value="type">{{ type.name }}</option>
                  </select>
                </div>
                <div v-if="level2Options.length > 0" class="form-group">
                  <label>Kategori Tingkat 2</label>
                  <select v-model="selectedLevel2" required>
                    <option disabled value="">Pilih Sub-Tipe</option>
                    <option v-for="type in level2Options" :key="type.id" :value="type">{{ type.name }}</option>
                  </select>
                </div>
                <div v-if="level3Options.length > 0" class="form-group">
                  <label>Kategori Tingkat 3</label>
                  <select v-model="selectedLevel3" required>
                    <option disabled value="">Pilih Sub-Tipe</option>
                    <option v-for="type in level3Options" :key="type.id" :value="type">{{ type.name }}</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="year">Tahun</label>
                  <input type="number" id="year" v-model.number="form.year" placeholder="e.g., 2024">
                </div>
                <div class="form-group">
                  <label for="abstract">Abstrak / Deskripsi</label>
                  <textarea id="abstract" v-model="form.abstract" rows="8"></textarea>
                </div>
              </fieldset>
            </div>

            <!-- Right Column -->
            <div class="form-column">
              <fieldset class="form-section">
                <legend>Informasi Publikasi</legend>
                <div class="form-group"><label for="publisher">Penerbit</label><input type="text" id="publisher" v-model="form.publisher"></div>
                <div class="form-group"><label for="issn">ISSN</label><input type="text" id="issn" v-model="form.issn"></div>
                <!-- <div class="form-group"><label for="conference_name">Nama Konferensi</label><input type="text" id="conference_name" v-model="form.conference_name"></div> -->
                <div class="form-group"><label for="publication_link">Tautan Publikasi</label><input type="url" id="publication_link" v-model="form.publication_link" placeholder="https://example.com"></div>
              </fieldset>

              <fieldset class="form-section">
                <!-- <legend>Penulis</legend> -->
                <div v-for="(author, index) in form.authors" :key="index" class="author-group">
                  <div class="form-group"><label :for="'author_name_' + index">Nama Penulis</label><input :id="'author_name_' + index" type="text" v-model="author.name" required></div>
                  <div class="form-group"><label :for="'author_id_' + index">NIM/NIK</label><input :id="'author_id_' + index" type="text" v-model="author.identifier"></div>
                  
                  <div class="form-group">
                    <label :for="'program_studi_' + index">Program Studi</label>
                    <input type="text" :id="'program_studi_' + index" v-model="author.program_studi">
                  </div>
                  <button type="button" @click="removeAuthor(index)" class="remove-btn">&times;</button>
                </div>
                <button type="button" @click="addAuthor" class="add-btn">Tambah Penulis</button>
              </fieldset>

              <fieldset class="form-section">
                <div class="form-group">
                  <label for="document_file">Unggah Dokumen (PDF, maks 10MB)</label>
                  <input type="file" id="document_file" @change="handleFileUpload" accept=".pdf">
                </div>
              </fieldset>
            </div>
          </div>
          
          <div class="form-actions">
            <button type="submit" :disabled="isSubmitting">
                {{ isSubmitting ? 'memberpaharui...' : 'Perbaharui Dokumen' }}
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script>
import Header from './Header.vue';
import axios from 'axios';

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
      form: {
        title: '',
        document_type_id: '',
        abstract: '',
        year: null,
        publisher: '',
        issn: '',
        conference_name: '',
        publication_link: '',
        authors: [{ name: '', identifier: '', program_studi: '', role: '' }],
        document_file: null,
        fileError: ''
      },
      documentTypes: [],
      selectedLevel1: '', 
      selectedLevel2: '', 
      selectedLevel3: '',
      isLoading: true,
      isSubmitting: false,
      successMessage: '',
      errorMessage: '',
      validationErrors: null,
    };
  },
  computed: {
    level2Options() {
      return this.selectedLevel1?.children || [];
    },
    level3Options() {
      return this.selectedLevel2?.children || [];
    }
  },
  watch: {
    selectedLevel1(v) { this.selectedLevel2 = ''; this.selectedLevel3 = ''; this.form.document_type_id = (v && !v.children?.length) ? v.id : null; },
    selectedLevel2(v) { this.selectedLevel3 = ''; this.form.document_type_id = (v && !v.children?.length) ? v.id : null; },
    selectedLevel3(v) { if(v) this.form.document_type_id = v.id; }
  },
  methods: {
    async loadInitialData() {
      this.isLoading = true;
      const articleId = this.$route.params.id;
      
      // Create promises for both API calls
      const getArticleData = axios.get(`/api/articles/${articleId}`);
      const getDocumentTypes = axios.get('/api/document-types');

      try {
        // Use Promise.all to wait for both requests to complete
        const [articleResponse, typesResponse] = await Promise.all([getArticleData, getDocumentTypes]);
        
        // Populate document types first
        this.documentTypes = typesResponse.data;

        // Then populate the form with the article data
        const article = articleResponse.data;
        this.form.title = article.title;
        this.form.document_type_id = article.document_type_id;
        this.form.abstract = article.abstract;
        this.form.year = article.year;
        this.form.publisher = article.publisher;
        this.form.issn = article.issn;
        this.form.conference_name = article.conference_name;
        this.form.publication_link = article.publication_link;
        this.form.authors = article.authors.map(a => ({ name: a.name, identifier: a.identifier, program_studi: a.program_studi || '', role: a.role || '' }));

      } catch (error) {
        console.error("Failed to load initial data for editing:", error);
        this.errorMessage = "Could not load article data.";
      } finally {
        // This will now only run after both promises are settled
        this.isLoading = false;
      }
    },
    handleFileUpload(event) {
      const file = event.target.files[0];
      this.fileError = '';

      if (file) {
        if (file.type !== 'application/pdf') {
          this.fileError = 'Hanya file PDF yang diperbolehkan';
          event.target.value = '';
          this.form.document_file = null;
          return;
        }

        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
          this.fileError = 'Ukuran file maksimal 10MB';
          event.target.value = '';
          this.form.document_file = null;
          return;
        }

        this.form.document_file = file;
        console.log('File selected:', file.name, 'Size:', (file.size / 1024 / 1024).toFixed(2) + 'MB');
      }
    },

    
    addAuthor() {
      this.form.authors.push({ name: '', identifier: '', program_studi: '' });
    },
    removeAuthor(index) {
      if (this.form.authors.length > 1) {
        this.form.authors.splice(index, 1);
      }
    },
    async submitUpdate() {
      this.isSubmitting = true;
      this.successMessage = '';
      this.errorMessage = '';
      this.validationErrors = null;
      const articleId = this.$route.params.id;
      try {
        const response = await axios.put(`/api/articles/${articleId}`, this.form);
        this.successMessage = `Document "${response.data.title}" updated successfully!`;
        // Redirect back to the article page after a short delay
        setTimeout(() => this.$router.push(`/article/${articleId}`), 2000);
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errorMessage = 'Please fix the errors below.';
          this.validationErrors = error.response.data.errors;
        } else {
          this.errorMessage = 'An error occurred during the update.';
        }
      } finally {
        this.isSubmitting = false;
      }
    },
  },
  created() {
    this.loadInitialData();
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
  padding: 3rem 1rem;
}
.form-container {
  max-width: 1250px;
  margin: 0 auto;
  background-color: #ffffff;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.form-title {
  font-size: 2rem;
  font-weight: 600;
  color: #1F3D7B;
  margin: 0 0 0.2rem 0;
}
.form-subtitle {
  font-size: 1rem;
  color: #666;
  margin: 0 0 1rem 0;
  border-bottom: 1px solid #eee;
  padding-bottom: 1rem;
}
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

/* **** NEW: Two-column layout **** */
.form-columns {
  display: flex;
  flex-direction: row;
  gap: 2.5rem;
}
.form-column {
  flex: 1;
  display: flex;
  flex-direction: column;
}
.form-group {
  display: flex;
  flex-direction: column;
}
.form-group.full-width {
  grid-column: 1 / -1; /* Span across both columns */
}
label {
  margin-top: 1rem;
  margin-bottom: .3rem;
  font-weight: 500;
  color: #333;
}
input, select, textarea {
  border: 1px solid #ccc;
  padding: 0.75rem;
  border-radius: 5px;
  font-size: 1rem;
  font-family: inherit;
  width: 100%;
  box-sizing: border-box;
}
input:focus, select:focus, textarea:focus {
  outline: none;
  border-color: #1F3D7B;
  box-shadow: 0 0 0 2px rgba(31, 61, 123, 0.2);
}
textarea {
  resize: vertical;
}
.form-actions {
  margin-top: 2rem;
  text-align: right;
}
button {
  padding: 0.3rem 1rem;
  background-color: #1F3D7B;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
  transition: background-color 0.2s;
}
button:hover:not(:disabled) {
  background-color: #0056b3;
}
button:disabled {
  background-color: #a0aec0;
  cursor: not-allowed;
}
.author-group {
  display: flex;
  gap: 1rem;
  align-items: center;
  margin-bottom: 1rem;
}
.message {
  padding: 1rem;
  border-radius: 5px;
  margin-bottom: 1.5rem;
  font-weight: 500;
}
.success-message {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}
.error-message {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}
@media (max-width: 1024px) { 
  .form-columns {
    flex-direction: column; /* Stack columns on tablet and smaller screens */
  }
}
@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
  .main-content {
    padding: 1rem;
  }
}
</style>
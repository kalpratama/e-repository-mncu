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
      <div class="form-container">
        <h1 class="form-title">Unggah Artikel Baru</h1>
        <p class="form-subtitle">Isi detail untuk item repositori baru.</p>

        <form @submit.prevent="submitArticle">
          <!-- Success Message -->
          <div v-if="successMessage" class="message success-message">
            {{ successMessage }}
          </div>
          <!-- Error Message -->
          <div v-if="errorMessage" class="message error-message">
            {{ errorMessage }}
          </div>

          <!-- **** NEW: Two-column layout for the form **** -->
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
                  <label for="document_type">Jenis Dokumen</label>
                  <select id="document_type" v-model="form.document_type_id" required>
                    <option disabled value="">Pilih salah satu</option>
                    <option v-for="type in documentTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="tahun">Tahun</label>
                  <input type="number" id="tahun" v-model.number="form.tahun" placeholder="e.g., 2025">
                </div>
                <div class="form-group">
                  <label for="program_studi">Program Studi</label>
                  <input type="text" id="program_studi" v-model="form.program_studi">
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
                <!-- <div class="form-group"><label for="conference_name">Conference Name</label><input type="text" id="conference_name" v-model="form.conference_name"></div> -->
                <div class="form-group"><label for="publication_link">Tautan Publikasi</label><input type="url" id="publication_link" v-model="form.publication_link" placeholder="https://example.com"></div>
              </fieldset>

              <fieldset class="form-section">
                <!-- <legend>Penulis</legend> -->
                <div v-for="(author, index) in form.authors" :key="index" class="author-group">
                  <div class="form-group"><label :for="'author_name_' + index">Nama Penulis</label><input :id="'author_name_' + index" type="text" v-model="author.name" required></div>
                  <div class="form-group"><label :for="'author_id_' + index">Nomor Induk (NIM/NIK)</label><input :id="'author_id_' + index" type="text" v-model="author.identifier"></div>
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
              {{ isSubmitting ? 'Mengunggah...' : 'Unggah' }}
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
      // Form data is stored in this object
      form: {
        title: '',
        document_type_id: '',
        abstract: '',
        year: null,
        program_studi: '',
        publisher: '',
        issn: '',
        //conference_name: '',
        publication_link: '',
        authors: [{ name: '', identifier: '' }],
        document_file: null,
      },
      // For now, we hardcode the document types. Later, we can fetch this via API.
      documentTypes: [
        { id: 1, name: "Artikel Jurnal" },
        { id: 2, name: "Artikel Jurnal Tidak Terbit" },
        { id: 3, name: "Artikel" },
        { id: 4, name: "Buku" },
        { id: 5, name: "Bab Buku" },
        { id: 6, name: "Skripsi" },
        { id: 7, name: "Tugas Akhir (Project Improvement)" },
        { id: 8, name: "Makalah Konferensi" },
        { id: 9, name: "Modul Pembelajaran" },
        { id: 10, name: "Laporan Penelitian" },
        { id: 11, name: "Laporan Magang Mahasiswa" },
        { id: 12, name: "Poster Ilmiah" },
        { id: 13, name: "Dokumentasi Prestasi Mahasiswa" },
      ],
      isSubmitting: false,
      successMessage: '',
      errorMessage: '',
      validationErrors: null,
    };
  },
  methods: {
    handleFileUpload(event){
      this.form.document_file = event.target.files[0];
    },

    AddAuthor(){
      this.form.authors.push({ name: '', identifier: '' });
    },

    removeAuthor(index) {
      if (this.form.authors.length > 1) {
        this.form.authors.splice(index, 1);
      } else {
        alert('At least one author is required.');
      }
    },

    resetForm(){
      this.form = {
        title: '', document_type_id: '', abstract: '', year: null, program_studi: '',
            publisher: '', issn: '', conference_name: '', publication_link: '',
            authors: [{ name: '', identifier: '' }],
            document_file: null,
        };
        document.getElementById('document_file').value = '';
    },
  
    async submitArticle() {
      this.isSubmitting = true;
      this.successMessage = '';
      this.errorMessage = '';
      this.validationErrors = null;
      const formData = new FormData();
      Object.keys(this.form).forEach(key => {
        if (key === 'authors') {
          this.form.authors.forEach((author, index) => {
            formData.append(`authors[${index}][name]`, author.name);
            formData.append(`authors[${index}][identifier]`, author.identifier);
          });
        } else if (this.form[key] !== null) {
          formData.append(key, this.form[key]);
        }
      });

      try {
        const response = await axios.post('/api/articles', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        this.successMessage = `Document "${response.data.title}" created successfully!`;
        this.resetForm();
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errorMessage = 'Please fix the errors below.';
          this.validationErrors = error.response.data.errors;
        } else if (error.response && error.response.data.message) {
          this.errorMessage = `Error: ${error.response.data.message}`;
        } else {
          this.errorMessage = 'An unexpected error occurred. Please try again.';
        }
      } finally {
        this.isSubmitting = false;
      }

      // try {
      //   const response = await axios.post('/api/articles', this.form);
      //   this.successMessage = `Article "${response.data.title}" created successfully!`;
      //   // Optionally, reset the form
      //   this.form = {
      //     title: '',
      //     document_type_id: '',
      //     abstract: '',
      //     year: null,
      //     program_studi: '',
      //   };
      // } catch (error) {
      //   if (error.response && error.response.data.message) {
      //     this.errorMessage = `Error: ${error.response.data.message}`;
      //   } else {
      //     this.errorMessage = 'An unexpected error occurred. Please try again.';
      //   }
      //   console.error("Article submission failed:", error);
      // } finally {
      //   this.isSubmitting = false;
      // }
    },
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
  padding: 0.75rem 2rem;
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

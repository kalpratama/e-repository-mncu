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
      <div class="form-container">
        <h1 class="form-title">Buat Unggahan Baru</h1>
        <p class="form-subtitle">Isi detail untuk item repositori baru.</p>

        <!-- **** POINT OF CHANGE: Step 1 - Category Selection **** -->
        <div class="form-group">
          <label for="document_type">Kategori Dokumen</label>
          <select id="document_type" v-model="form.document_type_id" @change="onTypeChange" required>
            <option disabled value="">Pilih Kategori</option>
            <option v-for="type in flatDocumentTypes" :key="type.id" :value="type.id">
              {{ type.name }}
            </option>
          </select>
        </div>

        <!-- **** POINT OF CHANGE: Step 2 - The rest of the form, shown conditionally **** -->
        <form v-if="form.document_type_id" @submit.prevent="submitArticle" enctype="multipart/form-data">
          <!-- Success Message -->
          <div v-if="successMessage" class="message success-message">
            {{ successMessage }}
          </div>
          <!-- Error Message -->
          <div v-if="errorMessage" class="message error-message">
            {{ errorMessage }}
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
                
                <!-- Conditionally show fields based on the selected category -->
                <div v-if="shouldShow('year')" class="form-group">
                  <label for="year">Tahun</label>
                  <input type="number" id="year" v-model.number="form.year" placeholder="e.g., 2025">
                </div>
                <div v-if="shouldShow('abstract')" class="form-group">
                  <label for="abstract">Abstrak / Deskripsi</label>
                  <textarea id="abstract" v-model="form.abstract" rows="8"></textarea>
                </div>
              </fieldset>
            </div>

            <!-- Right Column -->
            <div class="form-column">
              <fieldset v-if="shouldShow('publisher') || shouldShow('issn') || shouldShow('conference_name') || shouldShow('publication_link')" class="form-section">
                <legend>Informasi Publikasi</legend>
                <div v-if="shouldShow('publisher')" class="form-group"><label for="publisher">Penerbit</label><input type="text" id="publisher" v-model="form.publisher"></div>
                
                <!-- ISSN --> 
                <div v-if="shouldShow('issn')" class="form-group"><label for="issn">ISSN</label><input type="text" id="issn" v-model="form.issn"></div>
                <div v-if="shouldShow('isbn')" class="form-group"><label for="issn">ISBN</label><input type="text" id="issn" v-model="form.issn"></div>
                
                <div v-if="shouldShow('conference_name')" class="form-group"><label for="conference_name">Nama Konferensi</label><input type="text" id="conference_name" v-model="form.conference_name"></div>
                <div v-if="shouldShow('publication_link')" class="form-group"><label for="publication_link">Tautan Publikasi</label><input type="url" id="publication_link" v-model="form.publication_link" placeholder="https://example.com"></div>
              </fieldset>

              <fieldset class="form-section">
                <legend>Penulis</legend>
                <div v-for="(author, index) in form.authors" :key="index" class="author-group">
                  <div class="form-group"><label>Nama Penulis</label><input type="text" v-model="author.name" required></div>
                  <div v-if="shouldShow('identifier')" class="form-group"><label>Nomor Induk</label><input type="text" v-model="author.identifier"></div>
                  <div v-if="shouldShow('program_studi')" class="form-group">
                    <label>Program Studi</label>
                    <select v-model="author.program_studi">
                      <option value="Manajemen">Manajemen</option>
                      <option value="Akuntansi">Akuntansi</option>
                      <option value="Pendidikan Matematika">Pendidikan Matematika</option>
                      <option value="Pendidikan Bahasa Inggris">Pendidikan Bahasa Inggris</option>
                      <option value="Sains Komunikasi">Sains Komunikasi</option>
                      <option value="DKV">DKV</option>
                      <option value="Sistem Informasi">Sistem Informasi</option>
                      <option value="Ilmu Komputer">Ilmu Komputer</option>
                    </select>
                  </div>
                  <div v-if="shouldShow('role')" class="form-group">
                    <label>Role</label>
                    <select v-model="author.role">
                      <option value="Dosen">Dosen</option>
                      <option value="Mahasiswa">Mahasiswa</option>
                    </select>
                  </div>
                  <button type="button" @click="removeAuthor(index)" class="remove-btn">&times;</button>
                </div>
                <button type="button" @click="addAuthor" class="add-btn">Tambah Penulis</button>
              </fieldset>

              <fieldset v-if="shouldShow('file_path')" class="form-section">
                <legend>Unggah Dokumen</legend>
                <div class="form-group">
                  <label for="document_file">(PDF, max 10MB)</label>
                  <input type="file" id="document_file" @change="handleFileUpload" accept=".pdf">
                </div>
              </fieldset>
            </div>
          </div>
          
          <div class="form-actions">
            <button type="submit" :disabled="isSubmitting">{{ isSubmitting ? 'Mengunggah...' : 'Unggah' }}</button>
          </div>
        </form>

      </div>
    </main>
  </div>
</template>

<script>
import Header from './Header.vue';
import axios from 'axios';

const fieldConfig = {
  1: ['title', 'year', 'issn', 'publisher', 'abstract', 'publication_link', 'authors', 'identifier', 'program_studi', 'role', 'file_path'], // Artikel Jurnal
  2: ['title', 'year', 'issn', 'publisher', 'abstract', 'publication_link', 'authors', 'identifier', 'program_studi', 'role', 'file_path'], // Artikel JTT
  3: ['title', 'year', 'issn', 'publisher', 'abstract', 'publication_link', 'authors', 'identifier', 'program_studi', 'role', 'file_path'], // Artikel
  4: ['title', 'year', 'isbn', 'publisher', 'abstract', 'authors', 'program_studi', 'file_path'], // Buku (kurang tempat terbit)
  5: ['title', 'year', 'isbn', 'publisher', 'abstract', 'authors', 'program_studi', 'file_path'], // Bab buku (kurang tempat terbit)
  6: ['title', 'year', 'abstract', 'authors', 'program_studi', 'role', 'file_path'], // Skripsi
  7: ['title', 'year', 'abstract', 'authors', 'program_studi', 'role', 'file_path'], // TA
  8: ['title', 'year', 'issn', 'publisher', 'abstract', 'publication_link', 'authors', 'program_studi', 'role', 'file_path'], // Makalah Konferensi
  9: ['title', 'year', 'isbn', 'publisher', 'abstract', 'publication_link', 'authors', 'program_studi', 'file_path'], // Modul Pembelajaran (kurang tempat terbit)
  10: ['title', 'year', 'abstract', 'authors', 'identifier', 'program_studi', 'role', 'file_path'],// Laporan Penelitian
  11: ['title', 'year', 'abstract', 'authors', 'identifier', 'program_studi', 'role', 'file_path'],// Laporan Magang Mahasiswa
  12: ['title', 'year', 'publication_link', 'authors', 'program_studi', 'role', 'file_path'],// Poster Ilmiah
  13: ['title', 'year', 'authors', 'program_studi', 'file_path'],// Dokumentasi Prestasi mhs

  14: ['title', 'year', 'issn', 'publisher', 'abstract', 'publication_link', 'authors', 'identifier', 'program_studi', 'role', 'file_path'],// Jurnal Nasional
  15: ['title', 'year', 'issn', 'publisher', 'abstract', 'publication_link', 'authors', 'identifier', 'program_studi', 'role', 'file_path'],// Jurnal Internasional
  16: ['title', 'year', 'issn', 'publisher', 'abstract', 'publication_link', 'authors', 'identifier', 'program_studi', 'role', 'file_path'],// Jurnal Internal
  17: ['title', 'year', 'issn', 'publisher', 'abstract', 'publication_link', 'authors', 'program_studi', 'role', 'file_path'],// Majalah
  18: ['title', 'year', 'issn', 'publisher', 'abstract', 'publication_link', 'authors', 'program_studi', 'role', 'file_path'],// Koran
};

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
        authors: [{ name: '', identifier: '', program_studi: '', role: '' }], // Default role set to 'Dosen'
        document_file: null,
        fileError: '' // For file upload error messages
      },
      documentTypes: [],
      selectedLevel1: '', 
      selectedLevel2: '', 
      selectedLevel3: '',
      isSubmitting: false,
      successMessage: '',
      errorMessage: '',
      validationErrors: null,
    };
  },

  computed: {
    flatDocumentTypes() {
      const flatten = (types) => {
        let list = [];
        for (const type of types) {
          list.push({ id: type.id, name: type.name });
          if (type.children) {
            list = list.concat(flatten(type.children));
          }
        }
        return list;
      };
      return flatten(this.documentTypes);
    }
  },

  methods: {
    shouldShow(fieldName) {
      if (!this.form.document_type_id) return false;
      const fields = fieldConfig[this.form.document_type_id];
      return fields ? fields.includes(fieldName) : false;
    },
    onTypeChange() {
      this.resetForm(this.form.document_type_id);
    },
    async fetchDocumentTypes() {
      try {
        const response = await axios.get('/api/document-types');
        this.documentTypes = response.data;
      } catch (error) {
        console.error("Failed to fetch document types:", error);
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
    addAuthor(){
      this.form.authors.push({ name: '', identifier: '', program_studi: '', role: '' });
    },
    removeAuthor(index) {
      if (this.form.authors.length > 1) {
        this.form.authors.splice(index, 1);
      } else {
        alert('Harus ada minimal satu penulis.');
      }
    },
    resetForm(selectedTypeId = '') {
      this.form = {
        title: '',
        document_type_id: selectedTypeId,
        abstract: '',
        year: null,
        publisher: '',
        issn: '',
        conference_name: '',
        publication_link: '',
        authors: [{ name: '', identifier: '', program_studi: '', role: '' }],
        document_file: null,
      };
    },
    hasOverlongWord(input){
      return /\S{30,}/.test(input);
    },
    async submitArticle() {
      this.isSubmitting = true;
      this.successMessage = '';
      this.errorMessage = '';
      this.validationErrors = null;
      const formData = new FormData();
      const fieldsToCheck = ['title', 'abstract', 'publisher', 'conference_name'];
      const overlongInput = fieldsToCheck.find(field => this.hasOverlongWord(this.form[field]));
      if (overlongInput) {
        this.isSubmitting = false;
        this.errorMessage = `"${overlongInput}" tidak boleh memiliki kata lebih dari 30 karakter tanpa spasi.`;
        return;
      }
      Object.keys(this.form).forEach(key => {
        if (key === 'authors') {
          this.form.authors.forEach((author, index) => {
            formData.append(`authors[${index}][name]`, author.name);
            formData.append(`authors[${index}][identifier]`, author.identifier);
            formData.append(`authors[${index}][program_studi]`, author.program_studi);
            formData.append(`authors[${index}][role]`, author.role);
          });
        } else if (this.form[key] !== null) {
          formData.append(key, this.form[key]);
        }
      });
        if (this.form.document_file) {
          formData.append('document_file', this.form.document_file);
        }

      try {
        const response = await axios.post('/api/articles', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        this.successMessage = `Document "${response.data.title}" created successfully!`;
        this.resetForm();
        setTimeout(() => this.$router.push(`/article/${response.data.id}`), 1000);
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errorMessage = 'Perbaiki kesalahan sebelum mengunggah.';
          this.validationErrors = error.response.data.errors;
        } else if (error.response && error.response.data.message) {
          this.errorMessage = `Error: ${error.response.data.message}`;
        } else {
          this.errorMessage = 'An unexpected error occurred. Please try again.';
        }
      } finally {
        this.isSubmitting = false;
      }
    },
  },
  created() { 
    this.fetchDocumentTypes(); 
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
  margin-bottom: .3rem;
  border-bottom: 1px solid #eee;
  padding-bottom: 1rem;
}
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}
.form-section {
  margin-top: 1.5rem;
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
  margin-top: .75rem;
  margin-bottom: 0rem;
  font-weight: 500;
  color: #333;
}
input, select, textarea {
  border: 1px solid #ccc;
  padding: 0.3rem;
  border-radius: 12px;
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
  border-radius: 12px;
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
  .main-content {
    padding: 0rem;
  }
  .form-grid {
    grid-template-columns: 1fr;
  }
  .form-title {
    font-size: 1.5rem;
    margin-top: 0.5rem;
  }
  .form-subtitle {
    font-size: 0.8rem;
    margin-bottom: 0.5rem;
  }
  .form-columns{
    gap: 0.5rem;
  }
  .form-container{
    border-radius: 0px;
    padding: 0.75rem;
    padding-bottom: 8rem;
  }
  legend{
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
  }
  label{
    font-size: 0.8rem;
    margin-top: 0.25rem;
  }
  .author-group {
    flex-direction: column; /* Stack author fields on smaller screens */
    gap: 0.5rem;
    padding: 0.5rem;
    align-items: flex-start;
  }
  .author-groutp input{
    width: 100%;
  }
  input{
    width: 100%;
  }
}
</style>

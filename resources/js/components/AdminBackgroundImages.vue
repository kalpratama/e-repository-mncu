<!-- AdminBackgroundImages.vue - Complete Component -->
<template>
  <div class="admin-background-images">
    <!-- Page Header -->
    <div class="admin-header">
      <div class="header-content">
        <h2>Kelola Background Images</h2>
        <p class="header-description">Upload dan kelola gambar background yang akan ditampilkan di halaman website</p>
      </div>
      <button @click="showUploadModal = true" class="btn-primary">
        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah Gambar
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
      <div class="stat-card">
        <div class="stat-icon active">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
        </div>
        <div class="stat-content">
          <h3>{{ activeImagesCount }}</h3>
          <p>Gambar Aktif</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon total">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
          </svg>
        </div>
        <div class="stat-content">
          <h3>{{ backgroundImages.length }}</h3>
          <p>Total Gambar</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon size">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m0 0V1a1 1 0 011-1h2a1 1 0 011 1v18a1 1 0 01-1 1H4a1 1 0 01-1-1V4a1 1 0 011-1h2a1 1 0 011 1v3"></path>
          </svg>
        </div>
        <div class="stat-content">
          <h3>{{ formatFileSize(totalSize) }}</h3>
          <p>Total Ukuran</p>
        </div>
      </div>
    </div>

    <!-- Images Grid -->
    <div class="images-section">
      <div class="section-header">
        <h3>Daftar Background Images</h3>
        <div class="view-toggle">
          <button 
            @click="viewMode = 'grid'" 
            :class="['toggle-btn', { active: viewMode === 'grid' }]"
          >
            Grid
          </button>
          <button 
            @click="viewMode = 'list'" 
            :class="['toggle-btn', { active: viewMode === 'list' }]"
          >
            List
          </button>
        </div>
      </div>

      <div v-if="loading" class="loading">
        <div class="loading-spinner"></div>
        <p>Memuat data...</p>
      </div>

      <div v-else-if="backgroundImages.length === 0" class="empty-state">
        <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <h4>Belum ada background images</h4>
        <p>Upload gambar pertama Anda untuk memulai</p>
        <button @click="showUploadModal = true" class="btn-primary">
          Upload Gambar
        </button>
      </div>

      <div v-else :class="['images-container', viewMode]">
        <div v-for="image in backgroundImages" :key="image.id" class="image-card">
          <div class="image-preview">
            <img :src="image.url" :alt="image.name" @error="handleImageError" />
            <div class="image-overlay">
              <div class="overlay-actions">
                <button 
                  @click="toggleStatus(image)" 
                  :class="['action-btn', 'status-btn', image.is_active ? 'active' : 'inactive']"
                  :title="image.is_active ? 'Nonaktifkan' : 'Aktifkan'"
                >
                  <svg v-if="image.is_active" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  <svg v-else fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L12 12"></path>
                  </svg>
                </button>
                
                <button 
                  @click="editImage(image)" 
                  class="action-btn edit-btn"
                  title="Edit"
                >
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                
                <button 
                  @click="deleteImage(image)" 
                  class="action-btn delete-btn"
                  title="Hapus"
                >
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
            <div class="status-badge" :class="{ active: image.is_active }">
              {{ image.is_active ? 'Aktif' : 'Nonaktif' }}
            </div>
          </div>
          
          <div class="image-info">
            <h4>{{ image.name }}</h4>
            <div class="image-meta">
              <span class="meta-item">
                <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2"></path>
                </svg>
                {{ formatFileSize(image.file_size) }}
              </span>
              <span class="meta-item">
                <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                </svg>
                {{ formatDate(image.created_at) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Upload Modal -->
    <div v-if="showUploadModal" class="modal-backdrop" @click="closeModal">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h3>{{ editingImage ? 'Edit' : 'Upload' }} Background Image</h3>
          <button @click="closeModal" class="close-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div class="modal-body">
          <form @submit.prevent="editingImage ? updateImage() : uploadImage()">
            <div class="form-group">
              <label for="imageName">Nama Gambar</label>
              <input 
                type="text" 
                id="imageName" 
                v-model="uploadForm.name" 
                required 
                placeholder="Masukkan nama gambar"
                class="form-input"
              >
            </div>
            
            <div v-if="!editingImage" class="form-group">
              <label for="imageFile">File Gambar</label>
              <div class="file-upload-area" :class="{ 'drag-over': dragOver }" 
                   @drop="handleDrop" 
                   @dragover.prevent="dragOver = true" 
                   @dragleave="dragOver = false">
                <input 
                  type="file" 
                  id="imageFile" 
                  @change="handleFileSelect" 
                  accept="image/*" 
                  required
                  class="file-input"
                >
                <div class="file-upload-content">
                  <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                  </svg>
                  <p><strong>Drop files here</strong> atau klik untuk browse</p>
                  <small>Format yang didukung: JPG, PNG, WebP. Maksimal 5MB.</small>
                </div>
              </div>
            </div>

            <!-- Image Preview -->
            <div v-if="previewUrl" class="preview-container">
              <img :src="previewUrl" alt="Preview" class="preview-image">
            </div>

            <div class="form-group">
              <label class="checkbox-label">
                <input 
                  type="checkbox" 
                  v-model="uploadForm.is_active"
                  class="checkbox-input"
                >
                <span class="checkbox-custom"></span>
                Aktifkan langsung setelah {{ editingImage ? 'update' : 'upload' }}
              </label>
            </div>

            <div class="form-actions">
              <button type="button" @click="closeModal" class="btn-secondary">
                Batal
              </button>
              <button type="submit" :disabled="uploading" class="btn-primary">
                <span v-if="uploading">
                  <div class="loading-spinner small"></div>
                  {{ editingImage ? 'Mengupdate...' : 'Mengupload...' }}
                </span>
                <span v-else>
                  {{ editingImage ? 'Update' : 'Upload' }}
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Toast Notifications -->
    <div class="toast-container">
      <div v-for="toast in toasts" :key="toast.id" 
           :class="['toast', toast.type]" 
           @click="removeToast(toast.id)">
        <svg v-if="toast.type === 'success'" class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <svg v-else-if="toast.type === 'error'" class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        <span>{{ toast.message }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminBackgroundImages',
  data() {
    return {
      backgroundImages: [],
      loading: false,
      uploading: false,
      showUploadModal: false,
      editingImage: null,
      viewMode: 'grid',
      dragOver: false,
      uploadForm: {
        name: '',
        file: null,
        is_active: true
      },
      previewUrl: null,
      toasts: []
    };
  },

  computed: {
    activeImagesCount() {
      return this.backgroundImages.filter(img => img.is_active).length;
    },

    totalSize() {
      return this.backgroundImages.reduce((total, img) => total + (img.file_size || 0), 0);
    }
  },

  mounted() {
    this.fetchBackgroundImages();
  },

  beforeDestroy() {
    // Clean up preview URLs
    if (this.previewUrl) {
      URL.revokeObjectURL(this.previewUrl);
    }
  },

  methods: {
    async fetchBackgroundImages() {
      this.loading = true;
      try {
        const response = await axios.get('/api/admin/background-images', {
          headers: {
            'Authorization': `Bearer ${this.$store.state.auth.token}` // Adjust based on your auth system
          }
        });
        this.backgroundImages = response.data;
      } catch (error) {
        console.error('Failed to fetch background images:', error);
        this.showToast('Gagal memuat data gambar', 'error');
      } finally {
        this.loading = false;
      }
    },

    handleFileSelect(event) {
      const file = event.target.files[0];
      this.processFile(file);
    },

    handleDrop(event) {
      event.preventDefault();
      this.dragOver = false;
      const file = event.dataTransfer.files[0];
      this.processFile(file);
    },

    processFile(file) {
      if (file && file.type.startsWith('image/')) {
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
          this.showToast('Ukuran file maksimal 5MB', 'error');
          return;
        }

        this.uploadForm.file = file;
        
        // Create preview URL
        if (this.previewUrl) {
          URL.revokeObjectURL(this.previewUrl);
        }
        this.previewUrl = URL.createObjectURL(file);

        // Auto-generate name if empty
        if (!this.uploadForm.name) {
          this.uploadForm.name = file.name.replace(/\.[^/.]+$/, "");
        }
      } else {
        this.showToast('File harus berupa gambar', 'error');
      }
    },

    async uploadImage() {
      if (!this.uploadForm.file) return;

      this.uploading = true;
      const formData = new FormData();
      formData.append('name', this.uploadForm.name);
      formData.append('image', this.uploadForm.file);
      formData.append('is_active', this.uploadForm.is_active);

      try {
        await axios.post('/api/admin/background-images', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'Authorization': `Bearer ${this.$store.state.auth.token}` // Adjust based on your auth system
          }
        });
        
        this.showToast('Gambar berhasil diupload', 'success');
        this.closeModal();
        this.fetchBackgroundImages();
      } catch (error) {
        console.error('Failed to upload image:', error);
        this.showToast('Gagal mengupload gambar', 'error');
      } finally {
        this.uploading = false;
      }
    },

    editImage(image) {
      this.editingImage = image;
      this.uploadForm = {
        name: image.name,
        file: null,
        is_active: image.is_active
      };
      this.previewUrl = image.url;
      this.showUploadModal = true;
    },

    async updateImage() {
      this.uploading = true;
      try {
        await axios.patch(`/api/admin/background-images/${this.editingImage.id}`, {
          name: this.uploadForm.name,
          is_active: this.uploadForm.is_active
        }, {
          headers: {
            'Authorization': `Bearer ${this.$store.state.auth.token}` // Adjust based on your auth system
          }
        });
        
        this.showToast('Gambar berhasil diupdate', 'success');
        this.closeModal();
        this.fetchBackgroundImages();
      } catch (error) {
        console.error('Failed to update image:', error);
        this.showToast('Gagal mengupdate gambar', 'error');
      } finally {
        this.uploading = false;
      }
    },

    async toggleStatus(image) {
      try {
        await axios.patch(`/api/admin/background-images/${image.id}`, {
          is_active: !image.is_active
        }, {
          headers: {
            'Authorization': `Bearer ${this.$store.state.auth.token}` // Adjust based on your auth system
          }
        });
        
        image.is_active = !image.is_active;
        this.showToast(`Gambar ${image.is_active ? 'diaktifkan' : 'dinonaktifkan'}`, 'success');
      } catch (error) {
        console.error('Failed to toggle image status:', error);
        this.showToast('Gagal mengubah status gambar', 'error');
      }
    },

    async deleteImage(image) {
      if (!confirm(`Yakin ingin menghapus gambar "${image.name}"?`)) return;

      try {
        await axios.delete(`/api/admin/background-images/${image.id}`, {
          headers: {
            'Authorization': `Bearer ${this.$store.state.auth.token}` // Adjust based on your auth system
          }
        });
        this.backgroundImages = this.backgroundImages.filter(img => img.id !== image.id);
        this.showToast('Gambar berhasil dihapus', 'success');
      } catch (error) {
        console.error('Failed to delete image:', error);
        this.showToast('Gagal menghapus gambar', 'error');
      }
    },

    closeModal() {
      this.showUploadModal = false;
      this.editingImage = null;
      this.uploadForm = {
        name: '',
        file: null,
        is_active: true
      };
      if (this.previewUrl && !this.previewUrl.startsWith('http')) {
        URL.revokeObjectURL(this.previewUrl);
      }
      this.previewUrl = null;
    },

    handleImageError(event) {
      event.target.src = '/placeholder-image.png'; // Add a placeholder image
    },

    formatFileSize(bytes) {
      if (!bytes) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },

    formatDate(dateString) {
      if (!dateString) return '-';
      return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    },

    showToast(message, type = 'success') {
      const toast = {
        id: Date.now(),
        message,
        type
      };
      this.toasts.push(toast);
      
      setTimeout(() => {
        this.removeToast(toast.id);
      }, 4000);
    },

    removeToast(id) {
      this.toasts = this.toasts.filter(toast => toast.id !== id);
    }
  }
}
</script>

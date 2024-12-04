<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Edit Package</h2>
        <button class="close-btn" @click="$emit('close')">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="edit-form">
        <div class="form-section">
          <h3>Package Details</h3>
          
          <div class="form-group">
            <label for="packageName">Package Name</label>
            <input
              type="text"
              id="packageName"
              v-model="formData.name"
              required
              placeholder="Enter package name"
            />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="eventType">Event Type</label>
              <select id="eventType" v-model="formData.eventType" required>
                <option value="wedding">Wedding</option>
                <option value="debut">Debut</option>
                <option value="christening">Christening</option>
                <option value="kiddie">Kiddie Party</option>
              </select>
            </div>

            <div class="form-group">
              <label for="price">Price (₱)</label>
              <input
                type="number"
                id="price"
                v-model="formData.price"
                required
                min="0"
                step="0.01"
                placeholder="Enter price"
              />
            </div>
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <textarea
              id="description"
              v-model="formData.description"
              required
              rows="4"
              placeholder="Enter package description"
            ></textarea>
          </div>
        </div>

        <div class="form-section">
          <h3>Package Image</h3>
          
          <div class="image-preview">
            <img 
              v-if="imagePreview || formData.image" 
              :src="imagePreview || formData.image" 
              alt="Package Preview"
            />
            <div v-else class="upload-placeholder">
              <i class="fas fa-image"></i>
              <span>No image uploaded</span>
            </div>
          </div>

          <div class="image-upload">
            <input
              type="file"
              id="image"
              @change="handleImageUpload"
              accept="image/*"
              class="file-input"
            />
            <button type="button" class="upload-btn">
              <i class="fas fa-upload"></i>
              Change Image
            </button>
          </div>
        </div>

        <div class="form-section">
          <div class="section-header">
            <h3>Inclusions</h3>
            <button type="button" @click="addInclusion" class="add-btn">
              <i class="fas fa-plus"></i>
              Add Inclusion
            </button>
          </div>
          
          <div class="inclusions-list">
            <div v-for="(inclusion, index) in formData.inclusions" :key="index" class="inclusion-item">
              <input
                type="text"
                v-model="formData.inclusions[index]"
                placeholder="Enter inclusion"
                required
              />
              <button type="button" @click="removeInclusion(index)" class="remove-btn">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="form-section">
          <h3>Package Status</h3>
          
          <div class="form-row">
            <div class="form-group">
              <label for="status">Status</label>
              <select id="status" v-model="formData.status" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="draft">Draft</option>
              </select>
            </div>

            <div class="form-group">
              <label for="featured">Featured Package</label>
              <div class="toggle-switch">
                <input
                  type="checkbox"
                  id="featured"
                  v-model="formData.featured"
                />
                <label for="featured"></label>
              </div>
            </div>
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="cancel-btn" @click="$emit('close')">Cancel</button>
          <button type="submit" class="submit-btn" :disabled="isSubmitting">
            <i class="fas fa-spinner fa-spin" v-if="isSubmitting"></i>
            {{ isSubmitting ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useAuth } from '@/composables/useAuth';

const props = defineProps({
  package: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close', 'update']);
const { token } = useAuth();

const isSubmitting = ref(false);
const imagePreview = ref(null);

const formData = reactive({
  name: props.package.name,
  eventType: props.package.eventType,
  price: props.package.price,
  description: props.package.description,
  inclusions: [...props.package.inclusions],
  image: props.package.image,
  status: props.package.status || 'active',
  featured: props.package.featured || false
});

const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    formData.image = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const addInclusion = () => {
  formData.inclusions.push('');
};

const removeInclusion = (index) => {
  formData.inclusions = formData.inclusions.filter((_, i) => i !== index);
};

const handleSubmit = async () => {
  try {
    isSubmitting.value = true;

    const formDataToSend = new FormData();
    Object.keys(formData).forEach(key => {
      if (key === 'inclusions') {
        formDataToSend.append(key, JSON.stringify(formData[key].filter(i => i.trim())));
      } else if (key === 'image' && formData[key] instanceof File) {
        formDataToSend.append(key, formData[key]);
      } else {
        formDataToSend.append(key, formData[key]);
      }
    });

    const response = await fetch(`http://localhost:3000/api/admin/packages/${props.package.id}`, {
      method: 'PUT',
      headers: {
        Authorization: `Bearer ${token.value}`
      },
      body: formDataToSend
    });

    if (response.ok) {
      emit('update');
    } else {
      throw new Error('Failed to update package');
    }
  } catch (error) {
    console.error('Error updating package:', error);
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: var(--modal-background, #fff);
  border-radius: 12px;
  width: 90%;
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
  padding: 2rem;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.modal-header h2 {
  font-size: 1.5rem;
  color: var(--text-color);
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: var(--text-color);
  cursor: pointer;
  padding: 0.5rem;
}

.edit-form {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.form-section {
  background: var(--card-background);
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.form-section h3 {
  font-size: 1.2rem;
  color: var(--text-color);
  margin-bottom: 1.5rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.form-group label {
  font-weight: 500;
  color: var(--text-color);
}

input, select, textarea {
  padding: 0.75rem;
  border: 1px solid var(--border-color, #ddd);
  border-radius: 6px;
  font-size: 1rem;
  background: var(--input-background, #fff);
  color: var(--text-color);
}

textarea {
  resize: vertical;
}

.image-preview {
  width: 100%;
  height: 200px;
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 1rem;
}

.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.upload-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  background: var(--input-background);
  color: var(--text-muted);
}

.upload-placeholder i {
  font-size: 2rem;
}

.image-upload {
  position: relative;
}

.file-input {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
}

.upload-btn {
  width: 100%;
  padding: 0.75rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.inclusions-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.inclusion-item {
  display: flex;
  gap: 0.5rem;
}

.remove-btn {
  padding: 0.5rem;
  background: var(--danger-color, #dc3545);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.add-btn {
  padding: 0.5rem 1rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.toggle-switch {
  position: relative;
  width: 60px;
  height: 34px;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-switch label {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 34px;
}

.toggle-switch label:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

.toggle-switch input:checked + label {
  background-color: var(--primary-color);
}

.toggle-switch input:checked + label:before {
  transform: translateX(26px);
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}

.cancel-btn {
  padding: 0.75rem 1.5rem;
  background: var(--secondary-color, #6c757d);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.submit-btn {
  padding: 0.75rem 1.5rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .modal-content {
    padding: 1rem;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .cancel-btn, .submit-btn {
    width: 100%;
  }
}
</style> 
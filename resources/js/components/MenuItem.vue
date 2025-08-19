<template>
  <li>
    <div v-if="hasChildren" @click="toggleDropdown" class="category-box dropdown-trigger">
      {{ item.name }}
      <span class="dropdown-arrow" :class="{ 'rotated': isDropdownOpen }">&#9656;</span>
    </div>
    <router-link v-else :to="'/category/' + item.slug" class="category-box">
      {{ item.name }}
    </router-link>
    <ul v-if="hasChildren && isDropdownOpen" class="dropdown-menu">
      <li>
        <router-link :to="'/category/' + item.slug" class="dropdown-item">
          Semua {{ item.name }}
        </router-link>
      </li>
      <menu-item v-for="child in item.children" :key="child.id" :item="child" />
    </ul>
  </li>
</template>

<script>
export default {
  name: 'MenuItem',
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      isDropdownOpen: false
    }
  },
  computed: {
    hasChildren() {
      return this.item.children && this.item.children.length > 0
    }
  },
  methods: {
    toggleDropdown() {
      this.isDropdownOpen = !this.isDropdownOpen
    }
  }
}
</script>


<style scoped>
li {
  position: relative;
  list-style: none;
}
a, .dropdown-trigger {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.2rem 1rem;
  text-decoration: none;
  color: #1F3D7B;
  border-radius: 5px;
  box-shadow: 0 4px 5px rgba(0,0,0,0.15);
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  cursor: pointer;
}
li:hover > a, .dropdown-trigger:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.18);
}
.dropdown-arrow {
  font-size: 0.8em;
  color: #888;
}
.dropdown-arrow {
  transition: transform 0.3s ease;
}
.dropdown-arrow.rotated {
  transform: rotate(90deg);
}
.dropdown-item {
  font-weight: bold;
}
.dropdown-menu {
  visibility: hidden;
  opacity: 0;
  position: absolute;
  left: 100%;
  top: -1px;
  list-style: none;
  padding: 0;
  margin: 0 0 0 5px;
  min-width: 200px;
  background-color: white;
  border: 1px solid #ddd;
  border-radius: 5px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  z-index: 10;
  transition: visibility 0s linear 0.2s, opacity 0.2s linear;
}
li:hover > .dropdown-menu {
  display: block;
  visibility: visible;
  opacity: 1;
  transition-delay: 0s;
}
@media (max-width: 882px) {
  .dropdown-menu {
    left: 3rem;
    top: 100%;
    margin: 5px 0 0 0;
    width: 30%;
  }
  .dropdown-menu .dropdown-menu {
    left: 3rem;
    top: -1px;
    margin: 0 0 0 5px;
  }
}
@media (max-width: 768px){
  .dropdown-menu {
    left: 3rem;
    top: 100%;
    margin: 5px 0 0 0;
    width: 30%;
  }
  .dropdown-menu .dropdown-menu {
    left: 3rem;
    top: -1px;
    margin: 0 0 0 5px;
  }
}

</style>
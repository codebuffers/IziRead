<template>
  <form @submit.prevent="updateProfile">
    <!-- Name Field -->
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" v-model="user.name" required />

    <!-- Email Field -->
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" v-model="user.email" required />

    <!-- Password Field -->
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" v-model="user.password" />

    <button type="submit">Update Profile</button>
  </form>
</template>

<script setup>
import { ref } from 'vue';
import { useStore } from 'vuex';

const user = ref({
  name: '',
  email: '',
  password: '',
});

const store = useStore();

function updateProfile() {
  store.dispatch('updateUser', user.value);
}
</script>

<style scoped>
/* ...existing styles... */
</style>

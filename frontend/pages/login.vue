<template>
  <div class="login-container">
    <h1>Connexion</h1>
    <form @submit.prevent="login">
      <div>
        <label>Email</label>
        <input v-model="email" type="email" required />
      </div>
      <div>
        <label>Mot de passe</label>
        <input v-model="password" type="password" required />
      </div>
      <button type="submit" :disabled="loading">Se connecter</button>
      <div v-if="error" class="error">{{ error }}</div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const { $axios } = useNuxtApp()
const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)
const router = useRouter()

const login = async () => {
  error.value = ''
  loading.value = true
  try {
    const response = await $axios.post('/login', {
      email: email.value,
      password: password.value
    })
    if (response.data.token) {
      localStorage.setItem('token', response.data.token)
      router.push('/dashboard')
    } else {
      error.value = response.data.error || 'Identifiants invalides'
    }
  } catch (e) {
    error.value = e.response?.data?.error || 'Erreur de connexion au serveur'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-container {
  max-width: 400px;
  margin: 60px auto;
  padding: 2rem;
  border-radius: 8px;
  background: #f9f9f9;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
label {
  display: block;
  margin-bottom: 0.5rem;
}
input {
  width: 100%;
  margin-bottom: 1rem;
  padding: 0.5rem;
}
button {
  width: 100%;
  padding: 0.7rem;
  background: #007bff;
  color: #fff;
  border: none;
  border-radius: 4px;
}
.error {
  color: #d00;
  margin-top: 1rem;
  text-align: center;
}
</style>

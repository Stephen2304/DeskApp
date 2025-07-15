<template>
  <div class="dashboard">
    <h1>Mes réservations</h1>
    <div v-if="loading">Chargement...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <table v-else class="reservations-table">
      <thead>
        <tr>
          <th>Bureau</th>
          <th>Date début</th>
          <th>Date fin</th>
          <th>Statut</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="reservation in reservations" :key="reservation.id">
          <td>{{ reservation.bureau?.nom }}</td>
          <td>{{ formatDate(reservation.date_debut) }}</td>
          <td>{{ formatDate(reservation.date_fin) }}</td>
          <td>{{ reservation.statut }}</td>
        </tr>
      </tbody>
    </table>
    <button @click="logout">Déconnexion</button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const { $axios } = useNuxtApp()
const reservations = ref([])
const loading = ref(true)
const error = ref('')
const router = useRouter()

const fetchReservations = async () => {
  loading.value = true
  error.value = ''
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      router.push('/login')
      return
    }
    const response = await $axios.get('/desks/me/reservations', {
      headers: { 'Authorization': 'Bearer ' + token }
    })
    reservations.value = response.data
  } catch (e) {
    if (e.response && e.response.status === 401) {
      localStorage.removeItem('token')
      router.push('/login')
    } else {
      error.value = 'Erreur lors de la récupération des réservations'
    }
  } finally {
    loading.value = false
  }
}

const logout = () => {
  localStorage.removeItem('token')
  router.push('/login')
}

// Formatage des dates pour affichage lisible
const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleString('fr-FR')
}

onMounted(fetchReservations)
</script>

<style scoped>
.dashboard {
  max-width: 800px;
  margin: 40px auto;
  padding: 2rem;
  background: #f9f9f9;
  border-radius: 8px;
}
.reservations-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 2rem;
}
.reservations-table th, .reservations-table td {
  border: 1px solid #ddd;
  padding: 0.7rem;
  text-align: left;
}
.reservations-table th {
  background: #007bff;
  color: #fff;
}
.error {
  color: #d00;
  margin-top: 1rem;
  text-align: center;
}
button {
  margin-top: 2rem;
  padding: 0.7rem 1.5rem;
  background: #007bff;
  color: #fff;
  border: none;
  border-radius: 4px;
}
</style>

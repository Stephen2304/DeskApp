<script setup>
import { useRouter } from 'vue-router'
const router = useRouter()
if (process.client) {
  const token = localStorage.getItem('token')
  router.replace(token ? '/dashboard' : '/login')
}
</script>

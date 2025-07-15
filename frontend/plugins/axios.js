import axios from 'axios'

export default defineNuxtPlugin((nuxtApp) => {
    let token = null
    if (process.client) {
        token = localStorage.getItem('token')
    }
    const instance = axios.create({
        baseURL: 'http://localhost:8000/api'
    })
    if (token) {
        instance.defaults.headers.common['Authorization'] = `Bearer ${token}`
    }
    return {
        provide: {
            axios: instance
        }
    }
})

import axios from 'axios'

const api = axios.create({
    baseURL: '/api',
    withCredentials: true,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
})

api.interceptors.request.use(async (config) => {
    const needsCsrf = [
        'post',
        'put',
        'patch',
        'delete'
    ].includes(config.method?.toLowerCase())

    if (needsCsrf) {
        const csrfToken = document.cookie
        .split('; ')
        .find(row => row.startsWith('XSRF-TOKEN='))
        ?.split('=')[1]
        if (!csrfToken) {
            await axios.get('/sanctum/csrf-cookie', {
                withCredentials: true 
            })
        }
    }
    return config
})

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            const publicPaths = ['/login', '/register']
            if (!publicPaths.includes(window.location.pathname)) {
                window.location.href = '/login'
            }
        }
        return Promise.reject(error)
    }
)

export default api
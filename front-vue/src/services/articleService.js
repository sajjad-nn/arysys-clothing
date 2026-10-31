import api from './api'

const getArticles = async () => {
    const response = await api.get('/api/articles')
    return response.data
}

const getArticle = async (slug) => {
    const response = await api.get(`/api/articles/${slug}`)
    return response.data
}

export default {
    getArticles,
    getArticle,
}
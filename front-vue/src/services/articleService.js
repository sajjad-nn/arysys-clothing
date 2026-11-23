
import api from './api'

const getArticles = async () => {
    const response = await api.get('/api/articles')
    return response.data
}

const getArticle = async (slug) => {
    const response = await api.get(`/api/articles/${slug}`)
    return response.data
}

// دریافت نظرات مقاله
const getComments = async (articleId, page = 1) => {
    const response = await api.get(`/api/articles/${articleId}/comments`, {
        params: { page },
    })

    return response.data
}

// ثبت نظر جدید
const addComment = async (articleId, body) => {
    const response = await api.post(`/api/articles/${articleId}/comments`, {
        body,
    })

    return response.data
}

export default {
    getArticles,
    getArticle,
    getComments,
    addComment,
}

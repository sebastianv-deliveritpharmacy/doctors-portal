import axios from './axios'

export const getCurrentUser = () => axios.get('/user');
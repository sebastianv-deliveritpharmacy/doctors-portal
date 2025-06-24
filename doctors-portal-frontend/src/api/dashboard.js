import axios from './axios'

export const fetchDashboardStats = () => {
  return axios.get('/dashboard/stats')
}

import axios from './axios'

export const fetchShipments = () => axios.get('/shipment-updates');

export const fetchShipmentsByDoctor = (doctorId) => {
  return axios.get(`/shipment-updates/doctor/${doctorId}`)
}

export const updateShipment = (id, data) => {
  return axios.put(`/shipment-updates/${id}`, data)
}

export const createShipment = (data) => {
  return axios.post('/shipment-updates', data)
}
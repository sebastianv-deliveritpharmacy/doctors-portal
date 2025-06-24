import axios from './axios'

export const fetchShipments = (page = 1, perPage = 20, search = '') => {
  return axios.get('/shipment-updates', {
    params: {
      page,
      per_page: perPage,
      search
    }
  })
}

export const fetchShipmentsByDoctor = (doctorId, page = 1, perPage = 20, search = '') => {
  return axios.get(`/shipment-updates/doctor/${doctorId}`, {
    params: {
      page,
      per_page: perPage,
      search
    }
  })
}


export const updateShipment = (id, data) => {
  return axios.put(`/shipment-updates/${id}`, data)
}

export const createShipment = (data) => {
  return axios.post('/shipment-updates', data)
}
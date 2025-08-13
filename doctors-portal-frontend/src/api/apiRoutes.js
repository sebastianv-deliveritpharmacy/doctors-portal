// src/api/apiRoutes.js

const BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

export const apiRoutes = {
  register: `${BASE_URL}/register`,
  signUp: `${BASE_URL}/sign-up`,
  login: `${BASE_URL}/login`,
  logout: `${BASE_URL}/logout`,
  forgotPassword: `${BASE_URL}/forgot-password`,


  users: {
    list: `${BASE_URL}/users`,
    create: `${BASE_URL}/users`,
    detail: (id) => `${BASE_URL}/users/${id}`,
    update: (id) => `${BASE_URL}/users/${id}`,
    delete: (id) => `${BASE_URL}/users/${id}`,
  },
};

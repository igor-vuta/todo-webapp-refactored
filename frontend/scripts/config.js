// API URL - automatically uses production URL when deployed
export const API_URL = window.location.hostname === 'localhost' 
  ? 'http://localhost:8000' 
  : `${window.location.protocol}//${window.location.hostname}/api`;

export function authHeaders() {
  const t = localStorage.getItem('token');
  return t ? { Authorization: `Bearer ${t}` } : {};
}

export const API_URL = 'http://localhost:8000'; // if not already

export function authHeaders() {
  const t = localStorage.getItem('token');
  return t ? { Authorization: `Bearer ${t}` } : {};
}

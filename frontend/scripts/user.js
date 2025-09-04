// frontend/scripts/user.js
import { API_URL } from './config.js';

/* ====================== Auth helpers (exports) ====================== */
export function authHeaders() {
  const token = localStorage.getItem('token');
  return token ? { Authorization: `Bearer ${token}` } : {};
}

export async function authFetch(url, options = {}) {
  const headers = {
    'Content-Type': 'application/json',
    ...(options.headers || {}),
    ...authHeaders(),
  };
  return fetch(url, { ...options, headers });
}
/* =================================================================== */

/* -------- Users lookups (use authFetch) -------- */
export async function getUsersByGroupIds(groupIds) {
  if (!groupIds || groupIds.length === 0) return {};
  const res = await authFetch(`${API_URL}/groups/members.php?ids=${groupIds.join(',')}`, { method: 'GET' });
  if (!res.ok) return {};
  const data = await res.json();
  return data.groups || {};
}

export async function getUsersByIds(ids) {
  if (!ids || ids.length === 0) return {};
  const res = await authFetch(`${API_URL}/users/read.php?ids=${ids.join(',')}`, { method: 'GET' });
  if (!res.ok) {
    console.warn('Failed to fetch users');
    return {};
  }
  const data = await res.json();
  const userMap = {};
  if (data.success && Array.isArray(data.users)) {
    data.users.forEach(u => { userMap[u.id] = u.username; });
  }
  return userMap;
}

/* ------------------- UI helpers ------------------- */
function showNotification(message, type = 'error', duration = 3000) {
  let notification = document.getElementById('notification');
  if (!notification) {
    notification = document.createElement('div');
    notification.id = 'notification';
    document.body.appendChild(notification);
  }
  notification.textContent = message;
  notification.className = `notification show ${type}`;
  setTimeout(() => { notification.className = 'notification hidden'; }, duration);
}

function showLogin() {
  const l = document.getElementById('login-form');
  const r = document.getElementById('register-form');
  if (l) l.style.display = 'block';
  if (r) r.style.display = 'none';
}

function showRegister() {
  const l = document.getElementById('login-form');
  const r = document.getElementById('register-form');
  if (l) l.style.display = 'none';
  if (r) r.style.display = 'block';
}

/* ------------------- Auth flows ------------------- */
async function login() {
  const email = document.getElementById('login-email')?.value.trim();
  const password = document.getElementById('login-password')?.value.trim();
  if (!email || !password) {
    showNotification('Please enter email and password');
    return;
  }
  try {
    const res = await fetch(`${API_URL}/auth/login.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password })
    });
    if (!res.ok) {
      let msg = 'Server error';
      try { msg = (await res.json()).error || msg; } catch {}
      showNotification(msg, 'error');
      return;
    }
    const result = await res.json();
    if (result.token) {
      localStorage.setItem('token', result.token);
      showNotification('Login successful!', 'success');
      setTimeout(() => { window.location.href = 'index.html'; }, 1000);
    } else {
      showNotification(result.message || 'Login error', 'error');
    }
  } catch (error) {
    console.error('Login error:', error);
    showNotification(error.message || 'Login error', 'error');
  }
}

async function register() {
  const username = document.getElementById('register-name')?.value.trim();
  const email = document.getElementById('register-email')?.value.trim();
  const password = document.getElementById('register-password')?.value.trim();
  if (!username || !email || !password) {
    showNotification('Please fill in all fields');
    return;
  }
  try {
    const res = await fetch(`${API_URL}/auth/register.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, email, password })
    });
    if (!res.ok) {
      let msg = 'Registration error';
      try { msg = (await res.json()).error || msg; } catch {}
      showNotification(msg, 'error');
      return;
    }
    const result = await res.json();
    if (result.token) {
      localStorage.setItem('token', result.token);
      showNotification('Registration successful!', 'success');
      setTimeout(() => { window.location.href = 'index.html'; }, 1000);
    } else {
      showNotification(result.message || 'Registration error', 'error');
    }
  } catch (error) {
    console.error('Registration error:', error);
    showNotification(error.message || 'Registration error', 'error');
  }
}

export async function checkAuth() {
  const token = localStorage.getItem('token');
  if (!token || isTokenExpired(token)) {
    logout();
    return null;
  }
  const res = await authFetch(`${API_URL}/auth/profile.php`, { method: 'GET' });
  const text = await res.text();
  if (!res.ok) { logout(); return null; }
  try {
    const user = JSON.parse(text);
    return { token, ...user };
  } catch (e) {
    console.error('JSON.parse error in checkAuth:', e);
    logout();
    return null;
  }
}

function isTokenExpired(token) {
  try {
    const payload = JSON.parse(atob(token.split('.')[1]));
    const now = Math.floor(Date.now() / 1000);
    return payload.exp < now;
  } catch {
    return true;
  }
}

export function logout() {
  localStorage.removeItem('token');
  window.location.href = 'start.html';
}

/* -------------- Wire buttons on start.html -------------- */
document.addEventListener('DOMContentLoaded', () => {
  const loginBtn = document.getElementById('login-button');
  const registerBtn = document.getElementById('register-button');
  const toggleToRegister = document.getElementById('toggle-to-register');
  const toggleToLogin = document.getElementById('toggle-to-login');

  if (loginBtn) loginBtn.addEventListener('click', login);
  if (registerBtn) registerBtn.addEventListener('click', register);
  if (toggleToRegister) toggleToRegister.addEventListener('click', showRegister);
  if (toggleToLogin) toggleToLogin.addEventListener('click', showLogin);
});
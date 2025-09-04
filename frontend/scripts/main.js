import { loadGroups } from './groups.js'
import { loadLists } from './lists.js'
import { loadTasks } from './tasks.js'
import { initUI } from './ui.js'
import { checkAuth, logout } from './user.js'

document.addEventListener('DOMContentLoaded', async () => {
  try {
    // ✅ 1. Check authorization
    const user = await checkAuth();

    if (!user) {
      logout();
      return;
    }

    // ✅ 2. Show username
    renderUsername(user);

    // ✅ 3. Load data
    await Promise.all([
      loadGroups(),
      loadLists(),
      loadTasks() 
    ]);

    // ✅ 4. Initialize UI
    initUI();


    setupLogoutButton();

  } catch (error) {
    console.error('Ошибка инициализации приложения:', error);
    logout(); 
  }
});

function renderUsername(user) {
  const usernameEl = document.querySelector('.username');
  if (usernameEl) {
    usernameEl.textContent = user.username || 'User';
  }
}

function setupLogoutButton() {
  const logoutBtn = document.querySelector('.logout-btn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', logout);
  }
}

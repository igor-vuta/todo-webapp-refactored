// frontend/scripts/lists.js
import { API_URL } from './config.js';
import { setCurrentGroupId, setCurrentListId } from './state.js';
import { loadTasks } from './tasks.js';
import { authFetch } from './user.js';

export async function loadLists() {
  try {
    const res = await authFetch(`${API_URL}/lists/read.php`, { method: 'GET' });
    const body = await res.text(); // read once
    if (!res.ok) {
      throw new Error(`lists/read ${res.status}: ${body.slice(0, 300)}`);
    }

    let data;
    try {
      data = JSON.parse(body);
    } catch (e) {
      console.error('Non-JSON from lists/read.php:', body);
      throw new Error('lists/read returned non-JSON');
    }

    const container = document.querySelector('.lists-container');
    container.innerHTML = '';

    const totalTasks = data.total_tasks || 0;
    const allLi = document.createElement('li');
    allLi.classList.add('list-item');
    allLi.innerHTML = `<span>📋</span><span>Home</span><span>${totalTasks}</span>`;
    allLi.addEventListener('click', () => {
      setCurrentListId(null);
      loadTasks();
      document.querySelectorAll('.list-item').forEach(i => i.classList.remove('active'));
      allLi.classList.add('active');
    });
    container.appendChild(allLi);

    const lists = Array.isArray(data.lists) ? data.lists : [];
    lists.sort((a, b) => (b.task_count || 0) - (a.task_count || 0));

    const maxVisible = 6;
    const showAll = lists.length > maxVisible;
    (showAll ? lists.slice(0, maxVisible) : lists).forEach(list => {
      container.appendChild(createListItem(list));
    });
    if (showAll) {
      const btn = document.createElement('button');
      btn.className = 'show-more-lists';
      btn.textContent = 'More...';
      btn.addEventListener('click', () => {
        container.innerHTML = '';
        loadAllLists(lists, totalTasks);
      });
      container.appendChild(btn);
    }
  } catch (err) {
    console.error('Error loading lists', err);
  }
}

function loadAllLists(allLists, totalTasks) {
  const container = document.querySelector('.lists-container');
  container.innerHTML = '';

  const allLi = document.createElement('li');
  allLi.classList.add('list-item');
  allLi.innerHTML = `
    <span>📋</span>
    <span>Home</span>
    <span>${totalTasks}</span>
  `;
  allLi.addEventListener('click', () => {
    setCurrentListId(null);
    loadTasks();
    document.querySelectorAll('.list-item').forEach(item => item.classList.remove('active'));
    allLi.classList.add('active');
  });
  container.appendChild(allLi);

  allLists.forEach(list => {
    const li = createListItem(list);
    container.appendChild(li);
  });
}

function createListItem(list) {
  const li = document.createElement('li');
  li.classList.add('list-item');
  li.innerHTML = `
    <span>${list.icon || '📁'}</span>
    <span>${list.name}</span>
    <span>${list.task_count ?? 0}</span>
  `;
  li.addEventListener('click', () => {
    setCurrentListId(list.id);
    setCurrentGroupId(null);
    document.querySelectorAll('.list-item').forEach(item => item.classList.remove('active'));
    document.querySelectorAll('.group-card').forEach(card => card.classList.remove('active'));
    li.classList.add('active');
    loadTasks(list.id);
  });
  return li;
}

export async function getLists() {
  const res = await authFetch(`${API_URL}/lists/read.php`, { method: 'GET' });
  if (!res.ok) return [];
  const data = await res.json();
  return data.lists || [];
}

export async function createList(listData) {
  try {
    const res = await authFetch(`${API_URL}/lists/create.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(listData),
    });
    const result = res.ok ? await res.json() : { success: false, message: await res.text() };
    if (result.success) {
      loadLists();
    } else {
      alert(result.message || 'Failed to create list');
    }
  } catch (err) {
    console.error('Error creating list', err);
  }
}

export async function editList(listId, newName) {
  try {
    const res = await authFetch(`${API_URL}/lists/update.php`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: listId, name: newName }),
    });
    const result = res.ok ? await res.json() : { success: false, message: await res.text() };
    if (result.success) {
      loadLists();
    } else {
      alert(result.message || 'Failed to update list');
    }
  } catch (err) {
    console.error('Error updating list', err);
  }
}

export async function deleteList(listId) {
  try {
    const res = await authFetch(`${API_URL}/lists/delete.php`, {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: listId }),
    });
    const result = res.ok ? await res.json() : { success: false, message: await res.text() };
    if (result.success) {
      loadLists();
    } else {
      alert(result.message || 'Failed to delete list');
    }
  } catch (err) {
    console.error('Error deleting list', err);
  }
}
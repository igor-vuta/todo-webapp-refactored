import { API_URL, authHeaders } from './config.js'
import { createList, getLists, loadLists } from './lists.js'
import { currentGroupId, currentListId } from './state.js'
import { createTask, loadTasks } from './tasks.js'

export function initUI() {

  const createTaskBtn = document.querySelector('.create-task-button');
  const taskModal = document.querySelector('.modal-create-task');

  createTaskBtn.addEventListener('click', async () => {
    taskModal.classList.remove('hidden');
  
    const listSelect = taskModal.querySelector('.task-list-select');
    const contextEl = taskModal.querySelector('.task-context');
    listSelect.innerHTML = '<option value="">No List</option>';
  
    const lists = await getLists();
    lists.forEach(list => {
      const option = document.createElement('option');
      option.value = list.id;
      option.textContent = list.name;
      listSelect.appendChild(option);
    });
  
    if (currentGroupId) {
      listSelect.disabled = true;
      listSelect.value = '';
      contextEl.textContent = ' Task will be created in selected group.';
    } else {
      listSelect.disabled = false;
      listSelect.value = currentListId || '';
  
      if (currentListId) {
        const selected = lists.find(l => l.id == currentListId);
        contextEl.textContent = ` Task will be created in list: ${selected?.name || 'Unknown'}`;
      } else {
        contextEl.textContent = ' No list or group selected';
      }
    }
  });

  const taskNameInput = taskModal.querySelector('input[type="text"]');

  taskNameInput.addEventListener('input', (e) => {
    const input = e.target;
    const value = input.value;
    if (value.length > 0) {
      input.value = value.charAt(0).toUpperCase() + value.slice(1);
    }
  });

  const saveTaskBtn = taskModal.querySelector('.save-task');
  saveTaskBtn.addEventListener('click', () => {
    const taskNameInput = taskModal.querySelector('input[type="text"]');
    const taskListSelect = taskModal.querySelector('.task-list-select');
    const taskDateInput = taskModal.querySelector('input[type="date"]');
    const taskStartInput = taskModal.querySelector('.task-start-time');
    const taskEndInput = taskModal.querySelector('.task-end-time');
  
    const taskName = taskNameInput.value.trim();
    const taskList = taskListSelect.value;
    const dueDate = taskDateInput.value;
    const startTime = taskStartInput.value;
    const endTime = taskEndInput.value;
  
    [taskNameInput, taskDateInput, taskStartInput, taskEndInput].forEach(el => el.classList.remove('error'));
  
    let isValid = true;
  
    if (taskName.length < 3 || taskName.length > 100) {
      alert('Task name must be between 3 and 100 characters.');
      taskNameInput.classList.add('error');
      isValid = false;
    }
  
    const now = new Date();
    const due = new Date(dueDate);
    now.setHours(0,0,0,0);
  
    if (!dueDate || due < now) {
      alert('Due date must be today or in the future.');
      taskDateInput.classList.add('error');
      isValid = false;
    }
  
    if (startTime && endTime && startTime > endTime) {
      alert('Start time must be earlier than end time.');
      taskStartInput.classList.add('error');
      taskEndInput.classList.add('error');
      isValid = false;
    }
  
    if (!isValid) return;
  
    const taskData = {
      title: taskName,
      list_id: currentGroupId ? null : (taskList || null),
      group_id: currentGroupId || null,
      due_date: dueDate,
      start_time: startTime || null,
      end_time: endTime || null,
    };
  
    createTask(taskData);
    taskModal.classList.add('hidden');
  });

  const createListBtn = document.querySelector('.create-list');
  const listModal = document.querySelector('.modal-create-list');
  const saveListBtn = listModal.querySelector('.save-list');
  const closeListModalBtn = listModal.querySelector('.close-list-modal');

  createListBtn.addEventListener('click', () => {
    listModal.classList.remove('hidden');
  });

  document.getElementById('new-list-name').addEventListener('input', (e) => {
    const input = e.target;
    const value = input.value;
    if (value.length > 0) {
      input.value = value.charAt(0).toUpperCase() + value.slice(1);
    }
  });

  saveListBtn.addEventListener('click', async () => {
    const nameInput = document.getElementById('new-list-name');
    const listName = nameInput.value.trim();
  
    nameInput.classList.remove('error');
  
    if (!listName || listName.length > 50) {
      alert('Please enter a valid list name (max 50 characters).');
      nameInput.classList.add('error');
      return;
    }
  
    const lists = await getLists();
    const nameExists = lists.some(list => list.name.trim().toLowerCase() === listName.toLowerCase());
  
    if (nameExists) {
      const confirmCreate = confirm('A list with this name already exists. Create anyway?');
      if (!confirmCreate) return;
    }
  
    await createList({ name: listName });
  
    listModal.classList.add('hidden');
    nameInput.value = '';
    loadLists();
  });

  closeListModalBtn.addEventListener('click', () => {
    listModal.classList.add('hidden');
  });

  const toggleBtn = document.querySelector('.toggle-sidebar');
  const sidebar = document.querySelector('.sidebar');
  const mainSection = document.querySelector('.main-section');

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('hidden');
    mainSection.classList.toggle('expanded');
  });

  // Делегирование: любое нажатие внутри .sidebar
  sidebar.addEventListener('click', (event) => {
    const target = event.target;

    // Исключения: если нажали на одну из "непрячущих" кнопок — ничего не делаем
    if (
      target.closest('.create-list') ||
      target.closest('.create-group') ||
      target.closest('.show-more-groups')
    ) {
      return;
    }

    // Скрываем sidebar на мобильных
    if (window.innerWidth <= 768) {
      sidebar.classList.add('hidden');
      mainSection.classList.add('expanded');
    }
  });

  document.getElementById('task-filter').addEventListener('change', (e) => {
    const choice = e.target.value;
    const today = new Date();
    let dateFrom = null;
    let dateTo = null;
  
    if (choice === 'today') {
      const iso = today.toISOString().split('T')[0];
      loadTasks(currentListId, { filterDate: iso });
  
    } else if (choice === 'week') {
      dateFrom = today;
      dateTo = new Date(today);
      dateTo.setDate(today.getDate() + 7);
      loadTasks(currentListId, {
        dateFrom: dateFrom.toISOString().split('T')[0],
        dateTo: dateTo.toISOString().split('T')[0],
      });
  
    } else if (choice === 'month') {
      dateFrom = today;
      dateTo = new Date(today);
      dateTo.setDate(today.getDate() + 30);
      loadTasks(currentListId, {
        dateFrom: dateFrom.toISOString().split('T')[0],
        dateTo: dateTo.toISOString().split('T')[0],
      });
    }
  });
}

export function notify(message, type = 'success') {
  const container = document.getElementById('notifications');
  const note = document.createElement('div');
  note.className = `notification ${type}`;
  note.textContent = message;
  container.appendChild(note);

  setTimeout(() => {
    note.remove();
  }, 4000);
}

document.addEventListener('DOMContentLoaded', () => {
  const currentDateEl = document.querySelector('.current-date');
  if (currentDateEl) {
    const now = new Date();
    const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
    currentDateEl.textContent = now.toLocaleDateString("en-US", options);
  }
});

document.getElementById('update-task-btn').addEventListener('click', async () => {
  const taskId = document.getElementById('edit-task-modal').dataset.taskId;
  const listId = document.getElementById('edit-task-list').value;
  const groupInfoEl = document.getElementById('edit-task-group-info');
  const isGroupTask = !!groupInfoEl.dataset.groupId;

  const updatedData = {
    id: taskId,
    title: document.getElementById('edit-task-title').value,
    due_date: document.getElementById('edit-task-date').value,
    start_time: document.getElementById('edit-task-start').value,
    end_time: document.getElementById('edit-task-end').value,
    status: document.getElementById('edit-task-status').value,
    list_id: isGroupTask ? null : listId || null,
    group_id: isGroupTask ? Number(groupInfoEl.dataset.groupId) : null
  };

  const res = await fetch(`${API_URL}/tasks/update.php`, {
    method: 'PUT',
    headers: {
      ...authHeaders(),
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(updatedData),
  });

  const result = await res.json();
  if (result.success) {
    loadTasks();
    document.getElementById('edit-task-modal').classList.add('hidden');
  } else {
    alert(result.message || 'Failed to update task');
  }
});

document.getElementById('delete-task-btn').addEventListener('click', async () => {
  const taskId = document.getElementById('edit-task-modal').dataset.taskId;
  if (!confirm('Are you sure you want to delete this task?')) return;

  const res = await fetch(`${API_URL}/tasks/delete.php`, {
    method: 'DELETE',
    headers: {
      ...authHeaders(),
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ id: taskId }),
  });

  const result = await res.json();
  if (result.success) {
    loadTasks();
    document.getElementById('edit-task-modal').classList.add('hidden');
  } else {
    alert(result.message || 'Failed to delete task');
  }
});

document.getElementById('close-edit-modal').addEventListener('click', () => {
  document.getElementById('edit-task-modal').classList.add('hidden');
});

document.querySelectorAll('.modal-create-task, .modal-create-list, .modal-create-group, .modal-edit-task')
  .forEach(modal => {
    modal.addEventListener('click', function (e) {
      if (e.target === modal) {
        modal.classList.add('hidden');
      }
    });
  });
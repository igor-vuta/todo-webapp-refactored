import { API_URL, authHeaders } from './config.js'
import { getLists } from './lists.js'
import { getUsersByGroupIds } from './user.js'

export async function deleteTask(taskId) {
	try {
		const res = await fetch(`${API_URL}/tasks/delete.php`, {
			method: 'DELETE',
			headers: {
				...authHeaders(),
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({ id: taskId }),
		})

		const result = await res.json()
		if (result.success) {
			loadTasks()
		} else {
			alert(result.message || 'Task deletion failed')
		}
	} catch (err) {
		console.error('Task deletion error:', err)
	}
}

export async function toggleTaskComplete(taskId, completed) {
	try {
		const res = await fetch(`${API_URL}/tasks/update.php`, {
			method: 'PUT',
			headers: {
				...authHeaders(),
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				id: taskId,
				status: completed ? 'completed' : 'pending',
			}),
		})

		const result = await res.json()
		if (result.success) {
			loadTasks()
		} else {
			alert(result.message || 'Failed to update task status')
		}
	} catch (err) {
		console.error('Error updating task status:', err)
	}
}

export async function createTask(taskData) {
	try {
		const res = await fetch(`${API_URL}/tasks/create.php`, {
			method: 'POST',
			headers: {
				...authHeaders(),
				'Content-Type': 'application/json',
			},
			body: JSON.stringify(taskData),
		})

		const result = await res.json()

		if (result.success) {
			alert('Task created successfully!')
			loadTasks(taskData.list_id)
		} else {
			alert(result.message || 'Error creating task')
		}
	} catch (err) {
		console.error('Error creating task:', err)
	}
}

export async function editTask(taskData) {
	try {
		const res = await fetch(`${API_URL}/tasks/update.php`, {
			method: 'PUT',
			headers: {
				...authHeaders(),
				'Content-Type': 'application/json',
			},
			body: JSON.stringify(taskData),
		})

		const result = await res.json()
		if (result.success) {
			alert('Task updated')
			loadTasks(taskData.list_id)
		} else {
			alert(result.message || 'Error updating task')
		}
	} catch (err) {
		console.error('Error updating task:', err)
	}
}

export async function loadTasks(listId = null, options = {}) {
	try {
		const url = new URL(`${API_URL}/tasks/read.php`)

		if (options.groupId) url.searchParams.append('group_id', options.groupId)
		if (listId) url.searchParams.append('list_id', listId)
		if (options.filterDate)
			url.searchParams.append('due_date', options.filterDate)
		if (options.dateFrom) url.searchParams.append('date_from', options.dateFrom)
		if (options.dateTo) url.searchParams.append('date_to', options.dateTo)

		const res = await fetch(url.toString(), {
			headers: authHeaders(),
		})
		const data = await res.json()

		const container = document.querySelector('.todo-list')
		container.innerHTML = ''

		const groupIds = [
			...new Set(data.tasks.map(t => t.group_id).filter(Boolean)),
		]
		const groupMap = await getUsersByGroupIds(groupIds)

		const formatTime = t => t?.slice(0, 5).replace(':', '.') || ''

		data.tasks.forEach(task => {
			const today = new Date().toISOString().split('T')[0]
			const isOutdated =
				task.due_date &&
				task.due_date < today &&
				task.status !== 'completed' &&
				task.is_deleted !== '1'

			if (isOutdated) {
				fetch(`${API_URL}/tasks/update.php`, {
					method: 'PUT',
					headers: {
						...authHeaders(),
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						id: task.id,
						is_deleted: 1,
					}),
				})
				return
			}

			const taskEl = document.createElement('div')
			taskEl.classList.add('task-item')
			taskEl.innerHTML = `
        <input type="checkbox" ${
					task.status === 'completed' ? 'checked' : ''
				} />
        <div class="task-content">
          <span class="task-title">${task.title}</span>
          <div class="task-tags">
            ${task.project ? `<span>#${task.project}</span>` : ''}
          </div>
        </div>
        <div class="task-avatars"></div>
        <div class="task-time"><img class="img-responsive" src="/images/icons_svg/time-svgrepo-com.svg" alt="time icon" />${formatTime(
					task.start_time
				)} - ${formatTime(task.end_time)}</div>
        <button class="task-menu">
          <img class='img-responsive' src="/images/icons_svg/delete-1487-svgrepo-com.svg" alt="menu" />
        </button>
      `

			const avatars = taskEl.querySelector('.task-avatars')

			if (task.group_id && groupMap[task.group_id]) {
				const members = groupMap[task.group_id].slice(0, 5)
				members.forEach(member => {
					const avatar = document.createElement('div')
					avatar.className = 'user-avatar1'
					avatar.textContent = member.username.charAt(0).toUpperCase()
					avatar.style.backgroundColor = stringToColor(member.username)
					avatars.appendChild(avatar)
				})
			}

			taskEl.addEventListener('click', () => {
				openEditTaskModal(task)
			})

			taskEl
				.querySelector('input[type="checkbox"]')
				.addEventListener('click', e => {
					e.stopPropagation()
					toggleTaskComplete(task.id, e.target.checked)
				})

			taskEl.querySelector('.task-menu').addEventListener('click', e => {
				e.stopPropagation()
				if (confirm('Delete this task?')) {
					deleteTask(task.id)
				}
			})

			container.appendChild(taskEl)
		})
	} catch (err) {
		console.error('Error loading tasks:', err)
	}
}

async function openEditTaskModal(task) {
	const modal = document.getElementById('edit-task-modal')
	modal.classList.remove('hidden')

	const titleInput = document.getElementById('edit-task-title')
	titleInput.value = task.title || ''

	titleInput.addEventListener('input', () => {
		const value = titleInput.value
		if (value.length > 0) {
			titleInput.value = value.charAt(0).toUpperCase() + value.slice(1)
		}
	})
	document.getElementById('edit-task-date').value = task.due_date || ''
	document.getElementById('edit-task-start').value = task.start_time || ''
	document.getElementById('edit-task-end').value = task.end_time || ''
	document.getElementById('edit-task-status').value = task.status || 'pending'

	modal.dataset.taskId = task.id

	const listSelect = document.getElementById('edit-task-list')
	const groupInfo = document.getElementById('edit-task-group-info')
	listSelect.innerHTML = '<option value="">No List</option>'

	const lists = await getLists()

	lists.forEach(list => {
		const option = document.createElement('option')
		option.value = list.id
		option.textContent = list.name
		if (list.id == task.list_id) option.selected = true
		listSelect.appendChild(option)
	})

	if (task.group_id) {
		groupInfo.textContent = `👥 Task is assigned to group #${task.group_id}`
		groupInfo.dataset.groupId = task.group_id
		listSelect.disabled = true
	} else {
		groupInfo.textContent = ''
		groupInfo.dataset.groupId = ''
		listSelect.disabled = false
	}
}

export function stringToColor(str) {
	let hash = 0
	for (let i = 0; i < str.length; i++) {
		hash = str.charCodeAt(i) + ((hash << 5) - hash)
	}
	let color = '#'
	for (let i = 0; i < 3; i++) {
		const value = (hash >> (i * 8)) & 0xff
		color += ('00' + value.toString(16)).slice(-2)
	}
	return color
}

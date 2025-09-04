export async function handleResponse(res) {
  const data = await res.json();

  if (!res.ok) {
    let message = data.message || data.error || 'Unknown error';

    switch (res.status) {
      case 400: message = message || 'Invalid request'; break;
      case 401: message = message || 'Unauthorized'; break;
      case 403: message = message || 'Access denied'; break;
      case 409: message = message || 'Conflict'; break;
      case 500: message = message || 'Server error'; break;
    }

    notify(message, 'error');
    throw new Error(message);
  }

  return data;
}

export function notify(message, type = 'error', duration = 3000) {
  let notification = document.getElementById('notification');

  if (!notification) {
    notification = document.createElement('div');
    notification.id = 'notification';
    document.body.appendChild(notification);
  }

  notification.textContent = message;
  notification.className = `notification show ${type}`;

  setTimeout(() => {
    notification.className = 'notification hidden';
  }, duration);
}

export async function handleInviteResponse(res) {
  const data = await res.json();

  if (!res.ok) {
    let message = data.message || data.error || 'Unknown error';

    if (res.status === 409 && message.includes('already')) {
      notify(message, 'warning'); // только уведомление
      return data;
    }

    notify(message, 'error');
    throw new Error(message);
  }

  return data;
}
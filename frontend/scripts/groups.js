import { API_URL, authHeaders } from './config.js'
import { setCurrentGroupId, setCurrentListId } from './state.js'
import { loadTasks, stringToColor } from './tasks.js'
import { getUsersByGroupIds } from './user.js'
import { handleInviteResponse, handleResponse, notify } from './utils.js'

document.querySelector('.create-group').addEventListener('click', () => {
  document.getElementById('group-modal').classList.remove('hidden');
});

document.querySelector('.close-modal').addEventListener('click', () => {
  document.getElementById('group-modal').classList.add('hidden');
});

async function getUserIdByUsername(username) {
  const res = await fetch(`${API_URL}/users/get_id.php?username=${encodeURIComponent(username)}`, {
    headers: authHeaders()
  });

  const data = await res.json();
  if (data.success) {
    return data.user_id;
  } else {
    notify(`User not found: ${username}`, 'warning');
    return null;
  }
}

document.getElementById('group-name').addEventListener('input', (e) => {
  const input = e.target;
  const value = input.value;
  if (value.length > 0) {
    input.value = value.charAt(0).toUpperCase() + value.slice(1);
  }
});

document.getElementById('create-group-btn').addEventListener('click', async () => {
  const nameInput = document.getElementById('group-name');
  const invitesInput = document.getElementById('group-invites');

  const name = nameInput.value.trim();
  const invites = invitesInput.value.trim();

  nameInput.classList.remove('error');
  invitesInput.classList.remove('error');

  if (!name || name.length > 50) {
    notify('Group name must be filled and max 50 characters.', 'warning');
    nameInput.classList.add('error');
    return;
  }

  try {
    const res = await fetch(`${API_URL}/groups/create.php`, {
      method: 'POST',
      headers: authHeaders(),
      body: JSON.stringify({ name })
    });

    const data = await handleResponse(res); 

    const groupId = data.id;

    if (invites) {
      const usernames = invites
        .split(',')
        .map(u => u.trim())
        .filter(u => u.length > 0);

      for (const username of usernames) {
        const userId = await getUserIdByUsername(username);
        if (userId) {
          const inviteRes = await fetch(`${API_URL}/groups/invite.php`, {
            method: 'POST',
            headers: authHeaders(),
            body: JSON.stringify({ group_id: groupId, user_id: userId })
          });

          await handleInviteResponse(inviteRes); 
        } else {
          notify(`User not found: ${username}`, 'warning');
        }
      }
    }

    nameInput.value = '';
    invitesInput.value = '';
    document.getElementById('group-modal').classList.add('hidden');

    loadGroups();
    setCurrentGroupId(groupId);
    setCurrentListId(null);
    loadTasks(null, { groupId });

  } catch (err) {
  }
});

export async function loadGroups() {
  try {
    const res = await fetch(`${API_URL}/groups/read.php`, {
      headers: authHeaders()
    });

    const data = await handleResponse(res); 

    const container = document.querySelector('.groups-container');
    container.innerHTML = '';

    const maxVisible = 2;
    const showAll = data.groups.length > maxVisible;
    const visibleGroups = showAll ? data.groups.slice(0, maxVisible) : data.groups;

    const groupIds = data.groups.map(g => g.id);
    const groupMembersMap = await getUsersByGroupIds(groupIds);

    visibleGroups.forEach(group => {
      const groupCard = createGroupCard(group, groupMembersMap);
      container.appendChild(groupCard);
    });

    if (showAll) {
      const btn = document.createElement('button');
      btn.className = 'show-more-groups';
      btn.textContent = 'More...';
      btn.addEventListener('click', () => {
        container.innerHTML = '';
        data.groups.forEach(group => {
          container.appendChild(createGroupCard(group, groupMembersMap));
        });
      });
      container.appendChild(btn);
    }

  } catch (err) {
    console.error('Groups loading failed:', err.message);
  }
}

function createGroupCard(group, groupMembersMap) {
  const groupCard = document.createElement('div');
  groupCard.classList.add('group-card');

  const avatarBox = document.createElement('div');
  avatarBox.classList.add('group-avatar-box');

  const avatars = document.createElement('div');
  avatars.classList.add('group-avatars');

  const members = (groupMembersMap[group.id] || []).slice(0, 5);
  avatars.classList.add(`layout-${members.length}`);

  members.forEach(member => {
    const avatar = document.createElement('div');
    avatar.className = 'user-avatar';
    avatar.textContent = member.username.charAt(0).toUpperCase();
    avatar.style.backgroundColor = stringToColor(member.username);
    avatars.appendChild(avatar);
  });

  avatarBox.appendChild(avatars);

  const info = document.createElement('div');
  info.classList.add('group-info');
  info.innerHTML = `
    <h3 class="group-title">${group.name}</h3>
    <p class="group-count">${group.members_count} People</p>
  `;

  groupCard.appendChild(avatarBox);
  groupCard.appendChild(info);

  groupCard.addEventListener('click', () => {
    setCurrentGroupId(group.id);

    document.querySelectorAll('.list-item').forEach(item => item.classList.remove('active'));
    document.querySelectorAll('.group-card').forEach(card => card.classList.remove('active'));

    groupCard.classList.add('active');
    loadTasks(null, { groupId: group.id });
  });

  return groupCard;
}
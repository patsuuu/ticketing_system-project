<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Dashboard - Ticket System</title>
    <style>
:root {
        --bg: #ffffff;
        --panel: #f8fafc;
        --panel-2: #eef2f7;
        --text: #0f172a;
        --muted: #64748b;
        --accent: #2563eb;
        --accent-2: #16a34a;
        --border: rgba(15,23,42,0.12);
      }
      * { box-sizing: border-box; }

      body {

        margin: 0;
        min-height: 100vh;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        background: var(--bg);
        color: var(--text);
      }
      .app { min-height: 100vh; }
      .main { padding: 24px; }
      .topbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 20px; }

      .topbar h1 { margin: 0; font-size: 1.5rem; }
      .topbar p { margin: 4px 0 0; color: var(--muted); }
      .btn { border: none; border-radius: 10px; padding: 10px 14px; font-weight: 600; cursor: pointer; color: #0f172a; background: var(--accent); }
      .btn.primary { color: #ffffff; }
.btn.secondary { background: rgba(229, 231, 235, 0.12); color: #0f172a; border: 1px solid rgba(148, 163, 184, 0.18); }
      .profile-dropdown { position: relative; }
      .profile-trigger { display: inline-flex; align-items: center; gap: 8px; background: #eef2f7; color: #0f172a; border-radius: 999px; padding: 10px 14px; border: 1px solid var(--border); }
      .profile-icon { width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(15,23,42,0.06); }
      .profile-menu { position: absolute; right: 0; top: calc(100% + 10px); background: #ffffff; border: 1px solid var(--border); border-radius: 14px; box-shadow: 0 18px 40px rgba(2,6,23,0.12); padding: 10px 0; min-width: 170px; z-index: 10; }

      .profile-menu.hidden { display: none; }
      .profile-menu-item { width: 100%; background: transparent; color: #0f172a; border: none; text-align: left; padding: 10px 16px; cursor: pointer; font-size: 0.95rem; }
      .profile-menu-item:hover { background: rgba(15,23,42,0.06); }
      .content-grid { display: grid; grid-template-columns: 1.3fr 0.7fr; gap: 18px; }
.card, .panel {
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 10px 30px rgba(2,6,23,0.06);
      }

      /* User main panels */
      .content-grid .panel {
        background: linear-gradient(135deg, rgba(255,255,255,1) 0%, rgba(249, 231, 255, 0.75) 50%, rgba(219, 234, 254, 0.6) 100%);
        border: 1px solid rgba(168, 85, 247, 0.18);
      }



      .content-grid .panel .panel-header {
        margin-bottom: 12px;
      }

      .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
      }

      .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }


      .panel-header h3 { margin: 0; }
      .form-grid { display: grid; gap: 10px; }
      .form-group { display: grid; gap: 6px; }
      .filters input, .filters select, .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid var(--border); background: var(--panel-2); color: var(--text); }
      table { width: 100%; border-collapse: collapse; }
      th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid rgba(15,23,42,0.08); font-size: 0.95rem; }

      .badge { display: inline-block; padding: 5px 8px; border-radius: 999px; font-size: 0.8rem; font-weight: 600; }
      .badge.new { background: rgba(79, 124, 255, 0.2); color: #8fb0ff; }
      .badge.progress { background: rgba(245, 158, 11, 0.2); color: #f8c66d; }
      .badge.done { background: rgba(34, 197, 94, 0.2); color: #7ddc9b; }
      .muted { color: var(--muted); }
      .hidden { display: none; }
      .modal-overlay { position: fixed; inset: 0; background: rgba(2,6,23,0.45); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 16px; }
      .modal-overlay.hidden { display: none; }
      .modal-box { width: min(560px, 100%); background: rgba(255,255,255,0.98); border: 1px solid var(--border); border-radius: 16px; padding: 24px; box-shadow: 0 16px 40px rgba(2,6,23,0.18); }

      .modal-box .panel-header { margin-bottom: 18px; }
      .modal-box textarea { width: 100%; }
      #userMessageThread, #adminMessageThread {
        scrollbar-width: none;

        -ms-overflow-style: none;
      }
      #userMessageThread::-webkit-scrollbar, #adminMessageThread::-webkit-scrollbar {
        display: none;
      }
      .chat-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        margin-left: 8px;
        padding: 0 6px;
        border-radius: 999px;
        background: #ef4444;
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
      }
      @media (max-width: 980px) { .content-grid { grid-template-columns: 1fr; } .topbar { flex-direction: column; align-items: flex-start; } }
    </style>
  </head>
  <body>
    <div class="app">
      <div class="main">
        <div class="topbar">
          <div>
            <p class="eyebrow">User Dashboard</p>
            <h1>My Ticket Requests</h1>
            <p>Create and track your own support requests.</p>
          </div>
          <div class="profile-dropdown">
            <button class="profile-trigger" id="profileBtnUser" type="button">
              <span class="profile-icon">U</span>
              <span class="profile-label">User</span>
            </button>
            <div class="profile-menu hidden" id="profileMenuUser">
              <button class="profile-menu-item" id="logoutBtnUser" type="button">Logout</button>
            </div>
          </div>
        </div>

        <section class="content-grid">
          <div class="panel">
            <div class="panel-header"><h3>Submit Request</h3></div>
            <form id="userTicketForm" class="form-grid">
              <div class="form-group"><label>Title</label><input type="text" id="userTitle" required /></div>
              <div class="form-group"><label>Priority</label><select id="userPriority"><option>Low</option><option selected>Medium</option><option>High</option></select></div>
              <div class="form-group"><label>Description</label><textarea id="userDescription" rows="4" required></textarea></div>
              <button class="btn primary" type="submit">Send Request</button>
              <div id="submitStatus" class="muted" style="margin-top: 12px;"></div>
            </form>
          </div>

          <div class="panel">
            <div class="panel-header"><h3>My Requests</h3></div>
            <table>
              <thead><tr><th>ID</th><th>Title</th><th>Status</th><th>Priority</th><th>Feedback</th><th>Messages</th></tr></thead>
              <tbody id="userTicketBody"></tbody>
            </table>
          </div>
        </section>

        <div class="modal-overlay hidden" id="userMessageModal">
          <div class="modal-box">
            <div class="panel-header"><h3>Communication</h3><span class="muted" id="userMessageTicketLabel"></span></div>
<div id="userMessageThread" style="max-height: 300px; overflow-y: auto; margin-bottom: 14px; border: 1px solid rgba(168, 85, 247, 0.18); border-radius: 12px; padding: 12px; background: linear-gradient(135deg, rgba(168, 85, 247, 0.08), rgba(59, 130, 246, 0.06));"></div>
            <div class="form-group"><textarea id="userMessageInput" rows="4" placeholder="Type your message to IT admin..." style="resize: vertical;"></textarea></div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 12px;">
              <button class="btn secondary" id="userCloseMessageBtn" type="button">Close</button>
              <button class="btn" id="userSendMessageBtn" type="button">Send Message</button>
            </div>
            <div id="userMessageStatus" class="muted" style="margin-top: 12px;"></div>
          </div>
        </div>
      </div>
    </div>

    <script>
      const userTicketBody = document.getElementById('userTicketBody');
      const userTicketForm = document.getElementById('userTicketForm');
      const logoutBtnUser = document.getElementById('logoutBtnUser');
      const profileBtnUser = document.getElementById('profileBtnUser');
      const profileMenuUser = document.getElementById('profileMenuUser');
      const profileIconUser = document.querySelector('#profileBtnUser .profile-icon');
      const profileLabelUser = document.querySelector('#profileBtnUser .profile-label');

      function apiCall(action, body = null, role = null) {
        const query = new URLSearchParams({ action });
        if (role) query.set('role', role);
        const options = { method: body ? 'POST' : 'GET', headers: {} };
        if (body) { options.headers['Content-Type'] = 'application/json'; options.body = JSON.stringify(body); }
        return fetch(`api.php?${query.toString()}`, options).then(res => res.json());
      }

      function getStatusClass(status) {
        if (status === 'Resolved') return 'done';
        if (status === 'In Progress') return 'progress';
        return 'new';
      }

      let selectedUserTicketId = null;
      let userMessagePoll = null;
      let userLastMessageCount = 0;

      function renderUserDashboard(tickets) {
        userTicketBody.innerHTML = tickets.map(ticket => {
          const unreadCount = Number(ticket.unread_count) || 0;
          const messageButton = ticket.status === 'In Progress'
            ? `<button class="btn secondary message-btn" data-id="${ticket.id}" data-title="${ticket.title}" type="button">Chat${unreadCount ? ` <span class="chat-badge">${unreadCount}</span>` : ''}</button>`
            : '<span class="muted">-</span>';
          return `
            <tr>
              <td>#${ticket.id}</td>
              <td>${ticket.title}</td>
              <td><span class="badge ${getStatusClass(ticket.status)}">${ticket.status}</span></td>
              <td>${ticket.priority}</td>
              <td>${ticket.status === 'Resolved' ? (ticket.feedback ? ticket.feedback : `<button class="btn secondary feedback-btn" data-id="${ticket.id}" type="button">Give Feedback</button>`) : '<span class="muted">Pending</span>'}</td>
              <td>${messageButton}</td>
            </tr>
          `;
        }).join('');

        document.querySelectorAll('.feedback-btn').forEach(button => {
          button.addEventListener('click', () => {
            const ticketId = button.dataset.id;
            const feedback = prompt('Please enter your feedback for this resolved ticket:');
            if (!feedback || !feedback.trim()) return;
            apiCall('addFeedback', { ticket_id: ticketId, feedback: feedback.trim() }, 'user').then(response => {
              if (response.success) {
                const submitStatus = document.getElementById('submitStatus');
                submitStatus.textContent = 'Feedback submitted successfully.';
                clearTimeout(submitStatusTimeout);
                submitStatusTimeout = setTimeout(() => { submitStatus.textContent = ''; }, 5000);
                loadUserTickets();
              } else {
                alert(response.error || 'Unable to submit feedback.');
              }
            });
          });
        });

        document.querySelectorAll('.message-btn').forEach(button => {
          button.addEventListener('click', () => {
            selectedUserTicketId = button.dataset.id;
            document.getElementById('userMessageTicketLabel').textContent = `Ticket #${button.dataset.id}: ${button.dataset.title}`;
            document.getElementById('userMessageModal').classList.remove('hidden');
            userLastMessageCount = 0;
            loadUserMessages(selectedUserTicketId);
            startUserMessagePolling(selectedUserTicketId);
          });
        });
      }

      function startUserMessagePolling(ticketId) {
        stopUserMessagePolling();
        if (!ticketId) return;
        userMessagePoll = setInterval(() => {
          if (selectedUserTicketId) {
            loadUserMessages(selectedUserTicketId, true);
          }
        }, 5000);
      }

      function stopUserMessagePolling() {
        if (userMessagePoll) {
          clearInterval(userMessagePoll);
          userMessagePoll = null;
        }
      }

      function loadUserMessages(ticketId, silent = false) {
        apiCall('getMessages', { ticket_id: ticketId }, 'user').then(response => {
          if (response.success) {
            renderUserMessages(response.messages);
            const messageCount = response.messages.length;
            if (silent && userLastMessageCount && messageCount > userLastMessageCount) {
              const status = document.getElementById('userMessageStatus');
              status.textContent = 'New message received.';
              setTimeout(() => { status.textContent = ''; }, 3000);
            }
            userLastMessageCount = messageCount;
          }
        });
      }

      function renderUserMessages(messages) {
        const thread = document.getElementById('userMessageThread');
        if (!messages.length) {
          thread.innerHTML = '<div class="muted">No messages yet. Start the conversation with IT admin.</div>';
          return;
        }
        thread.innerHTML = messages.map(message => `
          <div style="margin-bottom: 12px;">
            <div style="font-size: 0.85rem; color: #8fa3bc;">${message.sender_name} <span style="margin-left: 8px;">${new Date(message.created_at).toLocaleString()}</span></div>
<div style="margin-top: 6px; padding: 12px; border-radius: 12px; background: rgba(255,255,255,0.12); border: 1px solid rgba(2,6,23,0.06);">${message.message}</div>
          </div>
        `).join('');
      }

      function sendUserMessage() {
        const messageInput = document.getElementById('userMessageInput');
        const text = messageInput.value.trim();
        if (!selectedUserTicketId || !text) return;
        apiCall('postMessage', { ticket_id: selectedUserTicketId, message: text }, 'user').then(response => {
          if (response.success) {
            messageInput.value = '';
            loadUserMessages(selectedUserTicketId);
          } else {
            alert(response.error || 'Unable to send message.');
          }
        });
      }

      function loadUserTickets() {
        apiCall('getTickets', null, 'user').then(response => {
          if (response.success) renderUserDashboard(response.tickets);
        });
      }

      function logout() {
        apiCall('logout', { role: 'user' }).then(() => { window.location.href = 'index.php'; });
      }

      let submitStatusTimeout;

      userTicketForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const title = document.getElementById('userTitle').value.trim();
        const description = document.getElementById('userDescription').value.trim();
        const priority = document.getElementById('userPriority').value;
        if (!title || !description) return;
        apiCall('createTicket', { title, description, priority }, 'user').then(response => {
          if (response.success) {
            userTicketForm.reset();
            const ticketId = response.ticket_id ? `#${response.ticket_id}` : 'unknown';
            const submitStatus = document.getElementById('submitStatus');
            submitStatus.textContent = `Request submitted successfully. Ticket ID: ${ticketId}`;
            clearTimeout(submitStatusTimeout);
            submitStatusTimeout = setTimeout(() => {
              submitStatus.textContent = '';
            }, 5000);
            loadUserTickets();
          }
        });
      });

      const hideUserProfileMenu = () => profileMenuUser.classList.add('hidden');
      const toggleUserProfileMenu = () => profileMenuUser.classList.toggle('hidden');

      profileBtnUser.addEventListener('click', (event) => {
        event.stopPropagation();
        toggleUserProfileMenu();
      });

      document.addEventListener('click', (event) => {
        if (!profileMenuUser.contains(event.target) && event.target !== profileBtnUser) {
          hideUserProfileMenu();
        }
      });

      logoutBtnUser.addEventListener('click', () => {
        hideUserProfileMenu();
        logout();
      });

      const userMessageModal = document.getElementById('userMessageModal');
      const userSendMessageBtn = document.getElementById('userSendMessageBtn');
      const userCloseMessageBtn = document.getElementById('userCloseMessageBtn');
      const userMessageBox = document.querySelector('#userMessageModal .modal-box');

      function closeUserModal() {
        if (userMessageModal) {
          userMessageModal.classList.add('hidden');
        }
        selectedUserTicketId = null;
        stopUserMessagePolling();
      }

      if (userSendMessageBtn) {
        userSendMessageBtn.addEventListener('click', sendUserMessage);
      }
      if (userCloseMessageBtn) {
        userCloseMessageBtn.addEventListener('click', closeUserModal);
      }
      if (userMessageModal) {
        userMessageModal.addEventListener('click', (event) => {
          if (event.target === userMessageModal) {
            closeUserModal();
          }
        });
      }
      if (userMessageBox) {
        userMessageBox.addEventListener('click', (event) => {
          event.stopPropagation();
        });
      }

      apiCall('profile', null, 'user').then(response => {
        if (!response.success) { window.location.href = 'index.php'; return; }
        const user = response.user;
        const displayName = user.full_name || user.username || 'User';
        profileLabelUser.textContent = displayName;
        profileIconUser.textContent = displayName.charAt(0).toUpperCase();
        loadUserTickets();
      }).catch(() => { window.location.href = 'index.php'; });
    </script>
  </body>
</html>

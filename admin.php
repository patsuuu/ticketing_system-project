<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Ticket System</title>
    <style>
:root {
        --bg: #ffffff;
        --panel: #f8fafc;
        --panel-2: #eef2f7;
        --text: #0f172a;
        --muted: #64748b;
        --accent: #2563eb;
        --accent-2: #16a34a;
        --warning: #f59e0b;
        --danger: #ef4444;
        --border: rgba(15, 23, 42, 0.12);
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
      .btn { border: none; border-radius: 10px; padding: 10px 14px; font-weight: 600; cursor: pointer; color: white; background: var(--accent); }
.btn.secondary { background: #e5e7eb; color: #0f172a; }
      .profile-dropdown { position: relative; }
      .profile-trigger { display: inline-flex; align-items: center; gap: 8px; background: #eef2f7; color: #0f172a; border-radius: 999px; padding: 10px 14px; border: 1px solid var(--border); }
      .profile-icon { width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(15,23,42,0.06); }
      .profile-menu { position: absolute; right: 0; top: calc(100% + 10px); background: #ffffff; border: 1px solid var(--border); border-radius: 14px; box-shadow: 0 18px 40px rgba(2,6,23,0.12); padding: 10px 0; min-width: 170px; z-index: 10; }
      .profile-menu.hidden { display: none; }
      .profile-menu-item { width: 100%; background: transparent; color: #0f172a; border: none; text-align: left; padding: 10px 16px; cursor: pointer; font-size: 0.95rem; }
      .profile-menu-item:hover { background: rgba(15,23,42,0.06); }
      .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
.card, .panel {
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 10px 30px rgba(2,6,23,0.06);
      }

      /* Stats cards */
      .stats-grid .card {
        background: linear-gradient(135deg, rgba(255,255,255,1) 0%, rgba(234, 231, 255, 0.75) 45%, rgba(227, 246, 255, 0.75) 100%);
        border: 1px solid rgba(124, 58, 237, 0.18);
      }

      /* Main panels */
      .content-grid .panel {
        background: linear-gradient(135deg, rgba(255,255,255,1) 0%, rgba(220, 252, 231, 0.75) 50%, rgba(254, 243, 199, 0.55) 100%);
        border: 1px solid rgba(16, 185, 129, 0.18);
      }

      /* Resolved archive panel */
      .panel[style*="margin-top"] {
        background: linear-gradient(135deg, rgba(255,255,255,1) 0%, rgba(249,231,255,0.75) 50%, rgba(219,234,254,0.65) 100%);
        border: 1px solid rgba(59, 130, 246, 0.18);
        color: var(--text);
      }



      .card .label { color: var(--muted); font-size: 0.9rem; margin-bottom: 8px; }
      .card .value { font-size: 1.35rem; font-weight: 700; }
      .content-grid { display: grid; grid-template-columns: 1.3fr 0.7fr; gap: 18px; }
      .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
      .panel-header h3 { margin: 0; }
      .filters { display: flex; gap: 10px; margin-bottom: 12px; }
      .filters input, .filters select, .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid var(--border); background: var(--panel-2); color: var(--text); }
      table { width: 100%; border-collapse: collapse; }
      th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid rgba(15,23,42,0.08); font-size: 0.95rem; }
      .badge { display: inline-block; padding: 5px 8px; border-radius: 999px; font-size: 0.8rem; font-weight: 600; }
      .badge.new { background: rgba(79, 124, 255, 0.2); color: #8fb0ff; }
      .badge.progress { background: rgba(245, 158, 11, 0.2); color: #f8c66d; }
      .badge.done { background: rgba(34, 197, 94, 0.2); color: #7ddc9b; }
      .form-grid { display: grid; gap: 10px; }
      .form-group { display: grid; gap: 6px; }
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
      .action-btn { padding: 6px 8px; border: none; border-radius: 8px; background: #e5e7eb; color: #0f172a; cursor: pointer; margin-right: 6px; }
      @media (max-width: 980px) { .stats-grid, .content-grid { grid-template-columns: 1fr; } .topbar { flex-direction: column; align-items: flex-start; } }
    </style>
  </head>
  <body>
    <div class="app">
      <div class="main">
        <div class="topbar">
          <div>
            <p class="eyebrow">IT Admin Dashboard</p>
            <h1>Ticket Monitoring</h1>
            <p>Manage requests and monitor support activity.</p>
          </div>
          <div class="profile-dropdown">
            <button class="profile-trigger" id="profileBtnAdmin" type="button">
              <span class="profile-icon">A</span>
              <span class="profile-label">Admin</span>
            </button>
            <div class="profile-menu hidden" id="profileMenuAdmin">
              <button class="profile-menu-item" id="logoutBtnAdmin" type="button">Logout</button>
            </div>
          </div>
        </div>

        <section class="stats-grid">
          <div class="card"><div class="label">Total Tickets</div><div class="value" id="adminTotal">0</div></div>
          <div class="card"><div class="label">Pending</div><div class="value" id="adminPending">0</div></div>
          <div class="card"><div class="label">In Progress</div><div class="value" id="adminProgress">0</div></div>
          <div class="card"><div class="label">Resolved</div><div class="value" id="adminResolved">0</div></div>
          <div class="card"><div class="label">Avg Progress</div><div class="value" id="adminAvgProgress">N/A</div></div>
        </section>

        <section class="content-grid">
          <div class="panel">
            <div class="panel-header"><h3>All Ticket Requests</h3><span class="muted">Live queue</span></div>
            <table>
              <thead><tr><th>ID</th><th>Title</th><th>Requester</th><th>Status</th><th>Progress Time</th><th>Action</th><th>Chat</th></tr></thead>
              <tbody id="adminTicketBody"></tbody>
            </table>
          </div>

          <div class="panel">
            <div class="panel-header"><h3>Admin Notes</h3></div>
            <p class="muted">Review priority tickets, assign follow-up actions, and update resolution status.</p>
            <ul>
              <li>High priority tickets should be handled first.</li>
              <li>Escalate repeated issues to the service team.</li>
              <li>Keep requesters informed after each update.</li>
            </ul>
          </div>
        </section>

        <div class="modal-overlay hidden" id="adminMessageModal">
          <div class="modal-box">
            <div class="panel-header"><h3>Communication</h3><span class="muted" id="adminMessageTicketLabel"></span></div>
<div id="adminMessageThread" style="max-height: 300px; overflow-y: auto; margin-bottom: 14px; border: 1px solid rgba(99, 102, 241, 0.18); border-radius: 12px; padding: 12px; background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(16, 185, 129, 0.06));"></div>
            <div class="form-group"><textarea id="adminMessageInput" rows="4" placeholder="Type your message to the user..." style="resize: vertical;"></textarea></div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 12px;">
              <button class="btn secondary" id="adminCloseMessageBtn" type="button">Close</button>
              <button class="btn" id="adminSendMessageBtn" type="button">Send Message</button>
            </div>
            <div id="adminMessageStatus" class="muted" style="margin-top: 12px;"></div>
          </div>
        </div>

        <section class="panel" style="margin-top: 20px;">
          <div class="panel-header"><h3>Resolved Tickets</h3><span class="muted">Completed requests archive</span></div>
          <table>
            <thead><tr><th>ID</th><th>Title</th><th>Requester</th><th>Status</th><th>Progress Time</th><th>Feedback</th></tr></thead>
            <tbody id="resolvedTicketBody"></tbody>
          </table>
        </section>
      </div>
    </div>

    <script>
      const adminTicketBody = document.getElementById('adminTicketBody');
      const logoutBtnAdmin = document.getElementById('logoutBtnAdmin');
      const profileBtnAdmin = document.getElementById('profileBtnAdmin');
      const profileMenuAdmin = document.getElementById('profileMenuAdmin');
      const profileIconAdmin = document.querySelector('#profileBtnAdmin .profile-icon');
      const profileLabelAdmin = document.querySelector('#profileBtnAdmin .profile-label');

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

      function formatElapsedTime(seconds) {
        if (seconds < 60) return `${Math.round(seconds)}s`;
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return `${minutes}m`;
        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;
        return mins === 0 ? `${hours}h` : `${hours}h ${mins}m`;
      }

      function showAdminUserMessage(message, isError = false) {
        adminUserMessage.textContent = message;
        adminUserMessage.style.color = isError ? '#ef4444' : '#7ddc9b';
      }

      function logout() {
        apiCall('logout', { role: 'admin' }).then(() => { window.location.href = 'index.php'; });
      }

      function renderAdminDashboard(tickets) {
        const activeTickets = tickets.filter(ticket => ticket.status !== 'Resolved');
        document.getElementById('adminTotal').textContent = activeTickets.length;
        document.getElementById('adminPending').textContent = activeTickets.filter(t => t.status === 'New').length;
        document.getElementById('adminProgress').textContent = activeTickets.filter(t => t.status === 'In Progress').length;
        document.getElementById('adminResolved').textContent = tickets.filter(t => t.status === 'Resolved').length;
        adminTicketBody.innerHTML = activeTickets.map(ticket => {
          const actionButtons = [];
          if (ticket.status === 'New') {
            actionButtons.push(`<button class="action-btn" data-action="In Progress" data-id="${ticket.id}">Progress</button>`);
          } else if (ticket.status === 'In Progress') {
            actionButtons.push(`<button class="action-btn" data-action="Resolved" data-id="${ticket.id}">Resolve</button>`);
          }
          const progressText = ticket.in_progress_at ? formatElapsedTime(Math.max(0, (new Date() - new Date(ticket.in_progress_at)) / 1000)) : '<span class="muted">-</span>';
          const unreadCount = Number(ticket.unread_count) || 0;
          const chatColumn = ticket.status === 'In Progress'
            ? `<button class="btn secondary admin-message-btn" data-id="${ticket.id}" data-title="${ticket.title}" type="button">Chat${unreadCount ? ` <span class="chat-badge">${unreadCount}</span>` : ''}</button>`
            : '<span class="muted">-</span>';
          return `
          <tr>
            <td>#${ticket.id}</td>
            <td><strong>${ticket.title}</strong><br><span class="muted">${ticket.description}</span></td>
            <td>${ticket.requester}</td>
            <td><span class="badge ${getStatusClass(ticket.status)}">${ticket.status}</span></td>
            <td>${progressText}</td>
            <td>${actionButtons.join('')}</td>
            <td>${chatColumn}</td>
          </tr>
        `;
        }).join('');

        const resolvedTickets = tickets.filter(ticket => ticket.status === 'Resolved');
        document.getElementById('resolvedTicketBody').innerHTML = resolvedTickets.map(ticket => {
          const progressText = ticket.in_progress_at && ticket.resolved_at ? formatElapsedTime(Math.max(0, (new Date(ticket.resolved_at) - new Date(ticket.in_progress_at)) / 1000)) : '<span class="muted">-</span>';
          return `
          <tr>
            <td>#${ticket.id}</td>
            <td><strong>${ticket.title}</strong><br><span class="muted">${ticket.description}</span></td>
            <td>${ticket.requester}</td>
            <td><span class="badge ${getStatusClass(ticket.status)}">${ticket.status}</span></td>
            <td>${progressText}</td>
            <td>${ticket.feedback ? ticket.feedback : '<span class="muted">No feedback yet</span>'}</td>
          </tr>
        `;
        }).join('');

        const averageProgress = (() => {
          const progressed = resolvedTickets.filter(ticket => ticket.in_progress_at && ticket.resolved_at);
          if (!progressed.length) return 'N/A';
          const seconds = progressed.map(ticket => Math.max(0, (new Date(ticket.resolved_at) - new Date(ticket.in_progress_at)) / 1000));
          const avgSeconds = seconds.reduce((sum, value) => sum + value, 0) / seconds.length;
          return formatElapsedTime(avgSeconds);
        })();
        document.getElementById('adminAvgProgress').textContent = averageProgress;

        adminTicketBody.querySelectorAll('.action-btn').forEach(button => {
          button.addEventListener('click', () => updateTicketStatus(button.dataset.id, button.dataset.action));
        });
        adminTicketBody.querySelectorAll('.admin-message-btn').forEach(button => {
          button.addEventListener('click', () => {
            selectedAdminTicketId = button.dataset.id;
            document.getElementById('adminMessageTicketLabel').textContent = `Ticket #${button.dataset.id}: ${button.dataset.title}`;
            document.getElementById('adminMessageModal').classList.remove('hidden');
            adminLastMessageCount = 0;
            loadAdminMessages(selectedAdminTicketId);
            startAdminMessagePolling(selectedAdminTicketId);
          });
        });
      }

      function startAdminMessagePolling(ticketId) {
        stopAdminMessagePolling();
        if (!ticketId) return;
        adminMessagePoll = setInterval(() => {
          if (selectedAdminTicketId) {
            loadAdminMessages(selectedAdminTicketId, true);
          }
        }, 5000);
      }

      function stopAdminMessagePolling() {
        if (adminMessagePoll) {
          clearInterval(adminMessagePoll);
          adminMessagePoll = null;
        }
      }

      function loadAdminMessages(ticketId, silent = false) {
        apiCall('getMessages', { ticket_id: ticketId }, 'admin').then(response => {
          if (response.success) {
            renderAdminMessages(response.messages);
            if (!silent) {
              loadAdminTickets();
            }
            const messageCount = response.messages.length;
            if (silent && adminLastMessageCount && messageCount > adminLastMessageCount) {
              const status = document.getElementById('adminMessageStatus');
              status.textContent = 'New reply received.';
              setTimeout(() => { status.textContent = ''; }, 3000);
            }
            adminLastMessageCount = messageCount;
          }
        });
      }

      function renderAdminMessages(messages) {
        const thread = document.getElementById('adminMessageThread');
        if (!messages.length) {
          thread.innerHTML = '<div class="muted">No messages yet. Start the conversation with the requester.</div>';
          return;
        }
        thread.innerHTML = messages.map(message => `
          <div style="margin-bottom: 12px;">
            <div style="font-size: 0.85rem; color: #8fa3bc;">${message.sender_name} <span style="margin-left: 8px;">${new Date(message.created_at).toLocaleString()}</span></div>
<div style="margin-top: 6px; padding: 12px; border-radius: 12px; background: rgba(255,255,255,0.12); border: 1px solid rgba(2,6,23,0.06);">${message.message}</div>
          </div>
        `).join('');
      }

      function sendAdminMessage() {
        const messageInput = document.getElementById('adminMessageInput');
        const text = messageInput.value.trim();
        if (!selectedAdminTicketId || !text) return;
        apiCall('postMessage', { ticket_id: selectedAdminTicketId, message: text }, 'admin').then(response => {
          if (response.success) {
            messageInput.value = '';
            loadAdminMessages(selectedAdminTicketId);
          } else {
            alert(response.error || 'Unable to send message.');
          }
        });
      }

      function loadAdminTickets() {
        apiCall('getTickets', null, 'admin').then(response => {
          if (response.success) renderAdminDashboard(response.tickets);
        });
      }

      function updateTicketStatus(ticketId, status) {
        apiCall('updateStatus', { ticket_id: ticketId, status }, 'admin').then(response => {
          if (response.success) loadAdminTickets();
        });
      }

      let selectedAdminTicketId = null;
      let adminMessagePoll = null;
      let adminLastMessageCount = 0;

      function init() {
        apiCall('profile', null, 'admin').then(response => {
          if (!response.success) { window.location.href = 'index.php'; return; }
          const user = response.user;
          const displayName = user.full_name || user.username || 'Admin';
          profileLabelAdmin.textContent = displayName;
          profileIconAdmin.textContent = displayName.charAt(0).toUpperCase();
          loadAdminTickets();
        }).catch(() => { window.location.href = 'index.php'; });
      }

      const hideAdminProfileMenu = () => profileMenuAdmin.classList.add('hidden');
      const toggleAdminProfileMenu = () => profileMenuAdmin.classList.toggle('hidden');

      profileBtnAdmin.addEventListener('click', (event) => {
        event.stopPropagation();
        toggleAdminProfileMenu();
      });

      const adminMessageModal = document.getElementById('adminMessageModal');
      document.getElementById('adminSendMessageBtn').addEventListener('click', sendAdminMessage);
      document.getElementById('adminCloseMessageBtn').addEventListener('click', () => {
        adminMessageModal.classList.add('hidden');
        selectedAdminTicketId = null;
        stopAdminMessagePolling();
      });
      adminMessageModal.addEventListener('click', (event) => {
        if (event.target === adminMessageModal) {
          adminMessageModal.classList.add('hidden');
          selectedAdminTicketId = null;
          stopAdminMessagePolling();
        }
      });

      document.addEventListener('click', (event) => {
        if (!profileMenuAdmin.contains(event.target) && event.target !== profileBtnAdmin) {
          hideAdminProfileMenu();
        }
      });

      logoutBtnAdmin.addEventListener('click', () => {
        hideAdminProfileMenu();
        logout();
      });
      init();
    </script>
  </body>
</html>

<?php
session_start();
$googleClientId = '573671083425-un6070u0nf8a27g3e5jki08ou4rmph80.apps.googleusercontent.com';
// Registered Google OAuth client ID for this app.
// Make sure http://localhost is added as an Authorized JavaScript origin in Google Cloud.
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ticket System Login</title>
    <style>
:root {
        --bg: #ffffff;
        --panel: #f8fafc;
        --panel-2: #eef2f7;
        --text: #0f172a;
        --muted: #64748b;
        --accent: #2563eb;
        --border: rgba(15,23,42,0.12);
      }
      * { box-sizing: border-box; }
      body {
        margin: 0;
        min-height: 100vh;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        background: #ffffff;
        color: var(--text);
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .login-box {
        width: min(420px, calc(100% - 32px));
        background: rgba(248,250,252,0.95);
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 28px;
        box-shadow: 0 18px 45px rgba(2,6,23,0.12);
      }
      .login-box h2 { margin: 0 0 14px; }
      .login-box p { margin: 0 0 18px; color: var(--muted); }
.login-box input, .login-box select { width: 100%; padding: 12px 14px; margin-bottom: 14px; border-radius: 12px; border: 1px solid var(--border); background: var(--panel); color: var(--text); }
      .btn { width: 100%; padding: 12px 14px; border: none; border-radius: 12px; background: var(--accent); color: white; font-weight: 700; cursor: pointer; }
      #googleSignInButton { width: 100%; display: flex; justify-content: center; }
      #googleSignInButton *,
      #googleSignInButton > div,
      #googleSignInButton iframe,
      #googleSignInButton div[role="button"] {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 100% !important;
        box-sizing: border-box !important;
      }
      .separator { text-align: center; color: var(--muted); margin: 14px 0; font-size: 0.95rem; }
      .help-text { color: var(--muted); margin: 8px 0 0; font-size: 0.9rem; text-align: center; }
      .error { color: #ef4444; margin-top: 10px; }
    </style>
  </head>
  <body>
    <div class="login-box">
      <h2>Ticket System Login</h2>
      <p>Admin login uses username and password. Users should sign in with Google.</p>
      <input id="usernameInput" type="text" placeholder="Admin username" autocomplete="username" />
      <input id="passwordInput" type="password" placeholder="Admin password" autocomplete="current-password" />
      <button class="btn" id="loginBtn" type="button">Admin Login</button>
      <div class="separator">or</div>
      <div id="googleSignInButton" style="margin-top:14px;"></div>
      <p class="help-text">Normal users should use Google sign-in only.</p>
      <p id="oauthWarning" class="error" style="display:none;"></p>
      <p id="loginMessage" class="error"></p>
    </div>

    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
      const usernameInput = document.getElementById('usernameInput');
      const passwordInput = document.getElementById('passwordInput');
      const loginBtn = document.getElementById('loginBtn');
      const loginMessage = document.getElementById('loginMessage');
      const googleClientId = '<?php echo addslashes($googleClientId); ?>';

      function apiCall(action, body = null) {
        const query = new URLSearchParams({ action });
        const options = { method: body ? 'POST' : 'GET', headers: {} };
        if (body) { options.headers['Content-Type'] = 'application/json'; options.body = JSON.stringify(body); }
        return fetch(`api.php?${query.toString()}`, options).then(res => res.json());
      }

      loginBtn.addEventListener('click', () => {
        const username = usernameInput.value.trim();
        const password = passwordInput.value;
        loginMessage.textContent = '';
        if (!username || !password) {
          loginMessage.textContent = 'Username and password are required.';
          return;
        }
        apiCall('login', { username, password }).then(response => {
          if (response.success) {
            window.location.href = 'admin.php';
          } else {
            loginMessage.textContent = response.error || 'Login failed.';
          }
        }).catch(() => {
          loginMessage.textContent = 'Unable to connect to server.';
        });
      });

      function handleGoogleCredentialResponse(response) {
        loginMessage.textContent = '';
        if (!response?.credential) {
          loginMessage.textContent = 'Google sign-in failed. Please try again.';
          return;
        }

        apiCall('googleLogin', { id_token: response.credential }).then(response => {
          if (response.success) {
            window.location.href = response.user?.role === 'admin' ? 'admin.php' : 'user.php';
          } else {
            loginMessage.textContent = response.error || 'Google login failed.';
          }
        }).catch(() => {
          loginMessage.textContent = 'Unable to connect to server.';
        });
      }

      function initGoogleSignIn() {
        const oauthWarning = document.getElementById('oauthWarning');
        if (!googleClientId || googleClientId.includes('YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com')) {
          oauthWarning.style.display = 'block';
          oauthWarning.textContent = 'Google OAuth is not configured. Set your Google client ID in index.php and add http://localhost as an authorized origin.';
          return;
        }

        google.accounts.id.initialize({
          client_id: googleClientId,
          callback: handleGoogleCredentialResponse,
          ux_mode: 'popup'
        });
        const googleButtonWrapper = document.getElementById('googleSignInButton');
        const buttonWidth = Math.max(googleButtonWrapper.clientWidth, 220);
        google.accounts.id.renderButton(
          googleButtonWrapper,
          { theme: 'outline', size: 'large', width: buttonWidth }
        );
      }

      window.onload = initGoogleSignIn;

      passwordInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
          loginBtn.click();
        }
      });
    </script>
  </body>
</html>

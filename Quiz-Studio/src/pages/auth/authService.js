export function setAuth(token, admin) {
  const timestamp = Date.now();
  localStorage.setItem('token', token);
  localStorage.setItem('admin', JSON.stringify(admin));
  localStorage.setItem('lastActivity', timestamp);
}

export function getAuth() {
  const token = localStorage.getItem('token');
  const admin = JSON.parse(localStorage.getItem('admin'));
  const lastActivity = parseInt(localStorage.getItem('lastActivity'));

  if (lastActivity) {
    const currentTime = Date.now();
    const twentyFourHours = 24 * 60 * 60 * 1000;

    if (currentTime - lastActivity > twentyFourHours) {
      logout();
      return {};
    }

    localStorage.setItem('lastActivity', currentTime);
  }

  return { token, admin };
}

export function isLoggedIn() {
  const { token } = getAuth();
  return !!token;
}

export function logout() {
  localStorage.removeItem('token');
  localStorage.removeItem('admin');
  localStorage.removeItem('lastActivity');
  window.location.href = '/auth/login';
}
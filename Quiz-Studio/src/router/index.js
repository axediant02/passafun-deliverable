import { createRouter, createWebHistory } from 'vue-router/auto'
import { routes } from 'vue-router/auto-routes'
import { getAuth } from '@/pages/auth/authService'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

router.beforeEach((to, from, next) => {
  const { admin } = getAuth();
  const isAuthenticated = !!admin;
  const publicPaths = ['/auth/login', '/auth/reset-password'];

  if (to.path === '/admins' && admin && admin.role_id === 2) {
    next({ path: '/dashboard' });
  } else if (!publicPaths.includes(to.path) && !isAuthenticated) {
    next('/auth/login');
  } else if (to.path === '/auth/login' && isAuthenticated) {
    next('/dashboard');
  } else {
    next();
  }
});

router.onError((err, to) => {
  if (err?.message?.includes?.('Failed to fetch dynamically imported module')) {
    if (!localStorage.getItem('vuetify:dynamic-reload')) {
      console.log('Reloading page to fix dynamic import error')
      localStorage.setItem('vuetify:dynamic-reload', 'true')
      location.assign(to.fullPath)
    } else {
      console.error('Dynamic import error, reloading page did not fix it', err)
    }
  } else {
    console.error(err)
  }
})

router.isReady().then(() => {
  localStorage.removeItem('vuetify:dynamic-reload')
})

export default router

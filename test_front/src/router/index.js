import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LeaveBalanceView from '../views/LeaveBalanceView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/address-book',
    },
    {
      path: '/address-book',
      name: 'address-book',
      component: HomeView,
    },
    {
      path: '/leave-balance',
      name: 'leave-balance',
      component: LeaveBalanceView,
    },
  ],
})

export default router

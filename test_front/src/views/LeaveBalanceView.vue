<script setup>
import { reactive, ref } from 'vue'
import { leaveApi } from '@/services/leaveApi'

const form = reactive({
  employee_name: '',
  years_of_service: 1,
  months_worked: 12,
  previous_year_unused_days: 0,
  taken_leave_days: 0,
})

const errorMessage = ref('')
const result = ref(null)

const calculate = async () => {
  errorMessage.value = ''
  result.value = null

  try {
    const response = await leaveApi.calculateLeaveBalance({
      years_of_service: Number(form.years_of_service),
      months_worked: Number(form.months_worked),
      previous_year_unused_days: Number(form.previous_year_unused_days),
      taken_leave_days: Number(form.taken_leave_days),
    })
    result.value = response.result
  } catch (error) {
    errorMessage.value = error.message
  }
}
</script>

<template>
  <main class="min-h-screen bg-amber-50 px-4 py-8">
    <div class="mx-auto w-full max-w-4xl">
      <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-3xl font-bold text-slate-900">HR Leave Balance Calculator</h1>
        <RouterLink to="/address-book" class="rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-900">
          Address Book
        </RouterLink>
      </div>

      <section class="rounded-xl border border-amber-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">Ethiopian Civil Service Rules (Simplified)</h2>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-slate-700">
          <li>20 days annual leave for first year.</li>
          <li>+1 day for each extra service year, max 30 days.</li>
          <li>Leave earned proportionally per month.</li>
          <li>Carry forward capped at 5 unused days.</li>
          <li>Taken leave reduces final balance.</li>
        </ul>

        <form class="mt-5 grid gap-3 sm:grid-cols-2" @submit.prevent="calculate">
          <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">Employee Name</label>
            <input
              v-model.trim="form.employee_name"
              type="text"
              placeholder="Example: Abebe Kebede (optional)"
              class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 outline-none ring-amber-500 focus:ring-2"
            >
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Years of Service</label>
            <input
              v-model.number="form.years_of_service"
              type="number"
              min="1"
              placeholder="Example: 3"
              required
              class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 outline-none ring-amber-500 focus:ring-2"
            >
            <p class="mt-1 text-xs text-slate-500">Starts from 1 year minimum.</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Months Worked This Year</label>
            <input
              v-model.number="form.months_worked"
              type="number"
              min="0"
              max="12"
              placeholder="0 to 12"
              required
              class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 outline-none ring-amber-500 focus:ring-2"
            >
            <p class="mt-1 text-xs text-slate-500">Leave is earned proportionally by month.</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Previous Year Unused Days</label>
            <input
              v-model.number="form.previous_year_unused_days"
              type="number"
              min="0"
              step="0.01"
              placeholder="Example: 4"
              class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 outline-none ring-amber-500 focus:ring-2"
            >
            <p class="mt-1 text-xs text-slate-500">Only up to 5 days can be carried forward.</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Taken Leave Days</label>
            <input
              v-model.number="form.taken_leave_days"
              type="number"
              min="0"
              step="0.01"
              placeholder="Example: 2"
              class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 outline-none ring-amber-500 focus:ring-2"
            >
            <p class="mt-1 text-xs text-slate-500">This value is subtracted from available leave.</p>
          </div>

          <button type="submit" class="sm:col-span-2 rounded-md bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700">
            Calculate Balance
          </button>
        </form>
      </section>

      <p v-if="errorMessage" class="mt-4 rounded-md bg-red-100 px-4 py-3 text-sm text-red-800">{{ errorMessage }}</p>

      <section v-if="result" class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">
          Result{{ form.employee_name ? ` - ${form.employee_name}` : '' }}
        </h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-2">
          <div class="rounded-md bg-slate-100 p-3 text-sm"><strong>Annual entitlement:</strong> {{ result.annual_entitlement_days }} days</div>
          <div class="rounded-md bg-slate-100 p-3 text-sm"><strong>Earned this year:</strong> {{ result.earned_this_year_days }} days</div>
          <div class="rounded-md bg-slate-100 p-3 text-sm"><strong>Carried forward:</strong> {{ result.carried_forward_days }} days</div>
          <div class="rounded-md bg-slate-100 p-3 text-sm"><strong>Total available:</strong> {{ result.total_available_days }} days</div>
        </div>
        <p class="mt-4 rounded-md px-4 py-3 text-sm font-semibold" :class="result.current_balance_days < 0 ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800'">
          Current balance: {{ result.current_balance_days }} days
        </p>
      </section>
    </div>
  </main>
</template>

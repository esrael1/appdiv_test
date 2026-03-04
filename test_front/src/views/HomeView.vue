<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { contactApi } from '@/services/contactApi'

const contacts = ref([])
const selectedContact = ref(null)
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const editingId = ref(null)

const form = reactive({
  first_name: '',
  last_name: '',
  phone: '',
  email: '',
  address: '',
})

const isEditing = computed(() => editingId.value !== null)

const clearMessages = () => {
  errorMessage.value = ''
  successMessage.value = ''
}

const resetForm = () => {
  form.first_name = ''
  form.last_name = ''
  form.phone = ''
  form.email = ''
  form.address = ''
  editingId.value = null
}

const loadContacts = async () => {
  loading.value = true
  clearMessages()

  try {
    contacts.value = await contactApi.getContacts()
  } catch (error) {
    errorMessage.value = error.message
  } finally {
    loading.value = false
  }
}

const handleSubmit = async () => {
  clearMessages()
  const payload = {
    first_name: form.first_name,
    last_name: form.last_name,
    phone: form.phone,
    email: form.email || null,
    address: form.address || null,
  }

  try {
    if (isEditing.value) {
      await contactApi.updateContact(editingId.value, payload)
      successMessage.value = 'Contact updated successfully.'
    } else {
      await contactApi.createContact(payload)
      successMessage.value = 'Contact added successfully.'
    }

    resetForm()
    await loadContacts()
  } catch (error) {
    errorMessage.value = error.message
  }
}

const startEdit = (contact) => {
  clearMessages()
  editingId.value = contact.id
  form.first_name = contact.first_name
  form.last_name = contact.last_name
  form.phone = contact.phone
  form.email = contact.email || ''
  form.address = contact.address || ''
}

const deleteContact = async (contact) => {
  clearMessages()

  if (!window.confirm(`Delete ${contact.first_name} ${contact.last_name}?`)) {
    return
  }

  try {
    await contactApi.deleteContact(contact.id)
    successMessage.value = 'Contact deleted successfully.'
    if (selectedContact.value?.id === contact.id) {
      selectedContact.value = null
    }
    if (editingId.value === contact.id) {
      resetForm()
    }
    await loadContacts()
  } catch (error) {
    errorMessage.value = error.message
  }
}

const viewDetails = async (id) => {
  clearMessages()
  try {
    selectedContact.value = await contactApi.getContact(id)
  } catch (error) {
    errorMessage.value = error.message
  }
}

onMounted(loadContacts)
</script>

<template>
  <main class="min-h-screen bg-slate-100 px-4 py-8">
    <div class="mx-auto w-full max-w-5xl">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-3xl font-bold text-slate-900">Address Book</h1>
        <RouterLink to="/leave-balance" class="rounded-md bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700">
          Leave Balance
        </RouterLink>
      </div>

      <p v-if="errorMessage" class="mt-4 rounded-md bg-red-100 px-4 py-3 text-sm text-red-800">{{ errorMessage }}</p>
      <p v-if="successMessage" class="mt-4 rounded-md bg-emerald-100 px-4 py-3 text-sm text-emerald-800">{{ successMessage }}</p>

      <section class="mt-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">{{ isEditing ? 'Edit Contact' : 'Add Contact' }}</h2>
        <form class="mt-4 grid gap-3" @submit.prevent="handleSubmit">
          <input v-model.trim="form.first_name" type="text" placeholder="First name" required class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 outline-none ring-blue-500 focus:ring-2">
          <input v-model.trim="form.last_name" type="text" placeholder="Last name" required class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 outline-none ring-blue-500 focus:ring-2">
          <input v-model.trim="form.phone" type="text" placeholder="Phone" required class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 outline-none ring-blue-500 focus:ring-2">
          <input v-model.trim="form.email" type="email" placeholder="Email" class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 outline-none ring-blue-500 focus:ring-2">
          <input v-model.trim="form.address" type="text" placeholder="Address" class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 outline-none ring-blue-500 focus:ring-2">
          <div class="mt-1 flex flex-wrap gap-2">
            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
              {{ isEditing ? 'Update' : 'Add' }}
            </button>
            <button v-if="isEditing" type="button" class="rounded-md bg-slate-600 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700" @click="resetForm">
              Cancel
            </button>
          </div>
        </form>
      </section>

      <section class="mt-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">Contacts</h2>
        <p v-if="loading" class="mt-3 text-sm text-slate-600">Loading contacts...</p>
        <p v-else-if="contacts.length === 0" class="mt-3 text-sm text-slate-600">No contacts found.</p>
        <ul v-else class="mt-3 divide-y divide-slate-200">
          <li v-for="contact in contacts" :key="contact.id" class="flex flex-wrap items-center justify-between gap-3 py-3">
            <span class="text-sm text-slate-800">{{ contact.first_name }} {{ contact.last_name }} - {{ contact.phone }}</span>
            <div class="flex flex-wrap gap-2">
              <button type="button" class="rounded-md bg-slate-700 px-3 py-1.5 text-sm font-medium text-white hover:bg-slate-800" @click="viewDetails(contact.id)">View</button>
              <button type="button" class="rounded-md bg-amber-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-amber-700" @click="startEdit(contact)">Edit</button>
              <button type="button" class="rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700" @click="deleteContact(contact)">Delete</button>
            </div>
          </li>
        </ul>
      </section>

      <section v-if="selectedContact" class="mt-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">Contact Details</h2>
        <div class="mt-3 space-y-1 text-sm text-slate-700">
          <p><strong>ID:</strong> {{ selectedContact.id }}</p>
          <p><strong>Name:</strong> {{ selectedContact.first_name }} {{ selectedContact.last_name }}</p>
          <p><strong>Phone:</strong> {{ selectedContact.phone }}</p>
          <p><strong>Email:</strong> {{ selectedContact.email || 'N/A' }}</p>
          <p><strong>Address:</strong> {{ selectedContact.address || 'N/A' }}</p>
        </div>
      </section>
    </div>
  </main>
</template>

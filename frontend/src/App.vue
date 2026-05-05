<template>
  <div class="min-h-screen bg-gray-100">
    <header class="bg-blue-600 text-white py-4 px-6 shadow">
      <h1 class="text-2xl font-bold">Sistema de Chamados de Suporte</h1>
    </header>
    <main class="max-w-4xl mx-auto py-8 px-4">
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">Abrir Novo Chamado</h2>
        <form @submit.prevent="createTicket" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Titulo</label>
            <input v-model="form.title" type="text" required
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
              placeholder="Descreva o problema resumidamente" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Descricao</label>
            <textarea v-model="form.description" required rows="3"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
              placeholder="Detalhe o problema..."></textarea>
          </div>
          <div class="flex gap-4">
            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-600 mb-1">Prioridade</label>
              <select v-model="form.priority"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="low">Baixa</option>
                <option value="medium">Media</option>
                <option value="high">Alta</option>
              </select>
            </div>
            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-600 mb-1">Seu Email</label>
              <input v-model="form.requester_email" type="email" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                placeholder="email@exemplo.com" />
            </div>
          </div>
          <button type="submit" :disabled="loading"
            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 disabled:opacity-50 font-medium">
            {{ loading ? 'Enviando...' : 'Abrir Chamado' }}
          </button>
        </form>
        <p v-if="successMessage" class="mt-3 text-green-600 font-medium">{{ successMessage }}</p>
        <p v-if="errorMessage" class="mt-3 text-red-600 font-medium">{{ errorMessage }}</p>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold text-gray-700">Chamados</h2>
          <button @click="loadTickets" class="text-sm text-blue-600 hover:underline">Atualizar</button>
        </div>
        <div v-if="tickets.length === 0" class="text-gray-400 text-center py-8">Nenhum chamado encontrado.</div>
        <div v-for="ticket in tickets" :key="ticket.id"
          class="border border-gray-200 rounded-lg p-4 mb-4 hover:shadow-sm transition">
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <h3 class="font-semibold text-gray-800">{{ ticket.title }}</h3>
              <p class="text-sm text-gray-500 mt-1">{{ ticket.description }}</p>
              <p class="text-xs text-gray-400 mt-2">{{ ticket.requesterEmail }}</p>
            </div>
            <div class="flex flex-col items-end gap-2 ml-4">
              <span :class="priorityClass(ticket.priority)" class="text-xs font-medium px-2 py-1 rounded-full">
                {{ priorityLabel(ticket.priority) }}
              </span>
              <select :value="ticket.status" @change="updateStatus(ticket.id, $event.target.value)"
                :class="statusClass(ticket.status)" class="text-xs border rounded px-2 py-1 cursor-pointer">
                <option value="open">Aberto</option>
                <option value="in_progress">Em andamento</option>
                <option value="resolved">Resolvido</option>
                <option value="closed">Fechado</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const tickets = ref([])
const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const form = ref({ title: '', description: '', priority: 'medium', requester_email: '' })

async function loadTickets() {
  const res = await axios.get('/api/tickets')
  tickets.value = res.data
}

async function createTicket() {
  loading.value = true
  successMessage.value = ''
  errorMessage.value = ''
  try {
    await axios.post('/api/tickets', form.value)
    successMessage.value = 'Chamado aberto com sucesso!'
    form.value = { title: '', description: '', priority: 'medium', requester_email: '' }
    await loadTickets()
  } catch (e) {
    errorMessage.value = 'Erro ao abrir chamado. Verifique os campos.'
  } finally {
    loading.value = false
  }
}

async function updateStatus(id, status) {
  await axios.patch(`/api/tickets/${id}/status`, { status })
  await loadTickets()
}

function priorityClass(p) {
  return { low: 'bg-green-100 text-green-700', medium: 'bg-yellow-100 text-yellow-700', high: 'bg-red-100 text-red-700' }[p]
}
function priorityLabel(p) {
  return { low: 'Baixa', medium: 'Media', high: 'Alta' }[p]
}
function statusClass(s) {
  return { open: 'border-blue-300 text-blue-600', in_progress: 'border-yellow-300 text-yellow-600', resolved: 'border-green-300 text-green-600', closed: 'border-gray-300 text-gray-500' }[s]
}

onMounted(loadTickets)
</script>

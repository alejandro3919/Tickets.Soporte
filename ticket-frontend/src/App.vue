<script setup>
import { ref, computed, onMounted } from 'vue'

// URL base de la API
const API_URL = 'http://localhost:8000/api/tickets'

const tickets = ref([])
const editandoId = ref(null)
const cargando = ref(false)
const errorMsg = ref('')

const form = ref({
  titulo: '',
  solicitante: '',
  correo: '',
  categoria: 'Software',
  prioridad: 'Media',
  estado: 'Abierto',
  descripcion: ''
})

const filtroBusqueda = ref('')
const filtroEstado = ref('')
const filtroPrioridad = ref('')

const obtenerTickets = async () => {
  cargando.value = true
  errorMsg.value = ''
  try {
    const res = await fetch(API_URL)
    if (res.ok) {
      tickets.value = await res.json()
    } else {
      errorMsg.value = 'Error al obtener los datos del servidor'
    }
  } catch (err) {
    errorMsg.value = 'No se pudo conectar al Backend. Verifica que Laravel y XAMPP estén encendidos.'
    console.error(err)
  } finally {
    cargando.value = false
  }
}

const guardarTicket = async () => {
  try {
    const url = editandoId.value ? `${API_URL}/${editandoId.value}` : API_URL
    const method = editandoId.value ? 'PUT' : 'POST'

    const res = await fetch(url, {
      method: method,
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(form.value)
    })

    if (res.ok) {
      await obtenerTickets()
      cancelarEdicion()
    }
  } catch (err) {
    console.error(err)
  }
}

const eliminarTicket = async (id) => {
  if (confirm('¿Seguro que deseas eliminar este ticket?')) {
    try {
      const res = await fetch(`${API_URL}/${id}`, { method: 'DELETE' })
      if (res.ok) await obtenerTickets()
    } catch (err) {
      console.error(err)
    }
  }
}

const cargarEdicion = (ticket) => {
  editandoId.value = ticket.id
  form.value = { ...ticket }
}

const cancelarEdicion = () => {
  editandoId.value = null
  form.value = { titulo: '', solicitante: '', correo: '', categoria: 'Software', prioridad: 'Media', estado: 'Abierto', descripcion: '' }
}

const ticketsFiltrados = computed(() => {
  return tickets.value.filter(ticket => {
    const coincideBusqueda = ticket.titulo.toLowerCase().includes(filtroBusqueda.value.toLowerCase()) || ticket.solicitante.toLowerCase().includes(filtroBusqueda.value.toLowerCase())
    const coincideEstado = filtroEstado.value === '' || ticket.estado === filtroEstado.value
    const coincidePrioridad = filtroPrioridad.value === '' || ticket.prioridad === filtroPrioridad.value
    return coincideBusqueda && coincideEstado && coincidePrioridad
  })
})

const totalTickets = computed(() => tickets.value.length)
const ticketsAbiertos = computed(() => tickets.value.filter(t => t.estado === 'Abierto').length)
const ticketsEnProceso = computed(() => tickets.value.filter(t => t.estado === 'En proceso').length)
const ticketsCerrados = computed(() => tickets.value.filter(t => t.estado === 'Cerrado').length)

onMounted(() => { obtenerTickets() })
</script>

<template>
  <div class="app-container">
    <header class="header">
      <h1>Sistema de Tickets de Soporte</h1>
      <p class="subtitle">Candidato: Alejandro Sanchez Carrizalez</p>
    </header>

    <section class="summary-cards">
      <div class="card bg-total"><h3>Total: {{ totalTickets }}</h3></div>
      <div class="card bg-abierto"><h3>Abiertos: {{ ticketsAbiertos }}</h3></div>
      <div class="card bg-proceso"><h3>Proceso: {{ ticketsEnProceso }}</h3></div>
      <div class="card bg-cerrado"><h3>Cerrados: {{ ticketsCerrados }}</h3></div>
    </section>

    <div class="main-content">
      <!-- FORMULARIO -->
      <section class="form-section">
        <h2>{{ editandoId ? ' Editar Ticket' : ' Capturar Incidencia' }}</h2>
        <form @submit.prevent="guardarTicket">
          <div class="form-group">
            <label>Titulo del problema *</label>
            <input v-model="form.titulo" type="text" required />
          </div>
          <div class="form-group">
            <label>Solicitante *</label>
            <input v-model="form.solicitante" type="text" required />
          </div>
          <div class="form-group">
            <label>Correo electrónico *</label>
            <input v-model="form.correo" type="email" required />
          </div>
          <div class="form-group">
            <label>Categoría *</label>
            <select v-model="form.categoria" required>
              <option value="Hardware">Hardware</option>
              <option value="Software">Software</option>
              <option value="Red">Red</option>
              <option value="Otro">Otro</option>
            </select>
          </div>
          <div class="form-group">
            <label>Prioridad *</label>
            <select v-model="form.prioridad" required>
              <option value="Baja">Baja</option>
              <option value="Media">Media</option>
              <option value="Alta">Alta</option>
            </select>
          </div>
          <div v-if="editandoId" class="form-group">
            <label>Estado *</label>
            <select v-model="form.estado" required>
              <option value="Abierto">Abierto</option>
              <option value="En proceso">En proceso</option>
              <option value="Cerrado">Cerrado</option>
            </select>
          </div>
          <div class="form-group">
            <label>Descripción *</label>
            <textarea v-model="form.descripcion" rows="3" required></textarea>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ editandoId ? 'Actualizar' : 'Registrar' }}</button>
            <button v-if="editandoId" type="button" @click="cancelarEdicion" class="btn btn-secondary">Cancelar</button>
          </div>
        </form>
      </section>

      <!-- LISTADO -->
      <section class="list-section">
        <h2> Listado de Incidencias</h2>
        <div class="filters-container">
          <input v-model="filtroBusqueda" type="text" placeholder=" Buscar por título o nombre" />
          <select v-model="filtroEstado">
            <option value="">Todos los estados</option>
            <option value="Abierto">Abiertos</option>
            <option value="En proceso">En proceso</option>
            <option value="Cerrado">Cerrados</option>
          </select>
          <select v-model="filtroPrioridad">
            <option value="">Todas las prioridades</option>
            <option value="Baja">Baja</option>
            <option value="Media">Media</option>
            <option value="Alta">Alta</option>
          </select>
        </div>

        
        <div v-if="cargando" class="msg-loading">🔄 Cargando</div>
        <div v-if="errorMsg" class="msg-error">⚠️ {{ errorMsg }}</div>

        <div v-if="!cargando && ticketsFiltrados.length === 0" class="no-data">No hay tickets disponibles.</div>

        <div v-if="!cargando" v-for="ticket in ticketsFiltrados" :key="ticket.id" class="ticket-item">
          <div>
            <strong>{{ ticket.titulo }}</strong> - <span> {{ ticket.solicitante }}</span>
            <p>{{ ticket.descripcion }}</p>
            <span class="badge">{{ ticket.prioridad }}</span> | <span class="badge">{{ ticket.estado }}</span>
          </div>
          <div class="action-buttons">
            <button @click="cargarEdicion(ticket)">Editar✏️</button>
            <button @click="eliminarTicket(ticket.id)">Eliminar🗑️</button>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<style scoped>
.app-container { max-width: 1200px; margin: 0 auto; padding: 20px; font-family: sans-serif; background-color: #f9fbfd; }
.header { text-align: center; margin-bottom: 20px; }
.subtitle { color: #010101; }
.summary-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 20px; }
.card { padding: 10px; border-radius: 6px; text-align: center; color: white; }
.bg-total { background-color: #030304; }
.bg-abierto { background-color: #009539; }
.bg-proceso { background-color: #d1e632; }
.bg-cerrado { background-color: #9b0505; }
.main-content { display: grid; grid-template-columns: 1fr 1.5fr; gap: 20px; }
.form-section, .list-section { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
.form-group { margin-bottom: 12px; display: flex; flex-direction: column; }
.form-group label { font-size: 0.9rem; font-weight: bold; margin-bottom: 4px; }
input, select, textarea { padding: 6px 10px; border: 1px solid #cbd5e0; border-radius: 4px; }
.form-actions { display: flex; gap: 8px; margin-top: 10px; }
.btn { padding: 8px 16px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
.btn-primary { background-color: #07080a; color: white; }
.btn-secondary { background-color: #e2e8f0; }
.filters-container { display: flex; flex-direction: column; gap: 8px; margin-bottom: 15px; }
.ticket-item { padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
.badge { font-weight: bold; font-size: 0.8rem; }
.action-buttons button { background: none; border: none; font-size: 1.1rem; cursor: pointer; margin-left: 5px; }
.no-data { text-align: center; color: #718096; padding: 20px; }
.msg-loading { text-align: center; color: #3182ce; font-weight: bold; padding: 15px; }
.msg-error { text-align: center; color: #e53e3e; background-color: #fff5f5; border: 1px solid #fed7d7; padding: 10px; border-radius: 6px; margin-bottom: 15px; }
</style>

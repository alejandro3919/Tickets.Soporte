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

const formatearFecha = (fecha) => {
  if (!fecha) return 'Sin fecha'
  return new Date(fecha).toLocaleDateString('es-MX', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
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
      <h1>Mesa de Soporte</h1>
      <p class="subtitle">Seguimiento y gestión de incidencias técnicas</p>
    </header>

    <section class="stats-bar">
      <div class="stat">
        <span class="stat-value">{{ totalTickets }}</span>
        <span class="stat-label">Total</span>
      </div>
      <div class="stat-divider"></div>
      <div class="stat">
        <span class="stat-value">{{ ticketsAbiertos }}</span>
        <span class="stat-label">Abiertos</span>
      </div>
      <div class="stat-divider"></div>
      <div class="stat">
        <span class="stat-value">{{ ticketsEnProceso }}</span>
        <span class="stat-label">En proceso</span>
      </div>
      <div class="stat-divider"></div>
      <div class="stat">
        <span class="stat-value">{{ ticketsCerrados }}</span>
        <span class="stat-label">Cerrados</span>
      </div>
    </section>

    <div class="main-content">

      <section class="panel form-panel">
        <h2>{{ editandoId ? 'Editar incidencia' : 'Nueva incidencia' }}</h2>
        <form @submit.prevent="guardarTicket">
          <div class="form-group">
            <label>Título del problema</label>
            <input v-model="form.titulo" type="text" required />
          </div>
          <div class="form-group">
            <label>Solicitante</label>
            <input v-model="form.solicitante" type="text" required />
          </div>
          <div class="form-group">
            <label>Correo electrónico</label>
            <input v-model="form.correo" type="email" required />
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Categoría</label>
              <select v-model="form.categoria" required>
                <option value="Hardware">Hardware</option>
                <option value="Software">Software</option>
                <option value="Red">Red</option>
                <option value="Otro">Otro</option>
              </select>
            </div>
            <div class="form-group">
              <label>Prioridad</label>
              <select v-model="form.prioridad" required>
                <option value="Baja">Baja</option>
                <option value="Media">Media</option>
                <option value="Alta">Alta</option>
              </select>
            </div>
          </div>
          <div v-if="editandoId" class="form-group">
            <label>Estado</label>
            <select v-model="form.estado" required>
              <option value="Abierto">Abierto</option>
              <option value="En proceso">En proceso</option>
              <option value="Cerrado">Cerrado</option>
            </select>
          </div>
          <div class="form-group">
            <label>Descripción</label>
            <textarea v-model="form.descripcion" rows="3" required></textarea>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ editandoId ? 'Actualizar' : 'Registrar' }}</button>
            <button v-if="editandoId" type="button" @click="cancelarEdicion" class="btn btn-ghost">Cancelar</button>
          </div>
        </form>
      </section>

      <section class="panel list-panel">
        <div class="list-header">
          <h2>Incidencias</h2>
          <span class="list-count">{{ ticketsFiltrados.length }} de {{ totalTickets }}</span>
        </div>

        <div class="filters">
          <input v-model="filtroBusqueda" type="text" placeholder="Buscar por título o solicitante" class="search-input" />
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

        <div v-if="cargando" class="state-msg">Cargando incidencias…</div>
        <div v-if="errorMsg" class="state-msg state-error">{{ errorMsg }}</div>
        <div v-if="!cargando && !errorMsg && ticketsFiltrados.length === 0" class="state-msg">
          No hay tickets que coincidan con estos filtros.
        </div>

        <ul v-if="!cargando" class="ticket-list">
          <li v-for="ticket in ticketsFiltrados" :key="ticket.id" class="ticket-row">
            <div class="ticket-main">
              <p class="ticket-title">{{ ticket.titulo }}</p>
              <p class="ticket-requester">Reportado por {{ ticket.solicitante }}</p>
              <p class="ticket-desc">{{ ticket.descripcion }}</p>
              <div class="ticket-meta">
                <span class="priority" :data-priority="ticket.prioridad.toLowerCase()">
                  <span class="priority-dot"></span>{{ ticket.prioridad }}
                </span>
                <span class="status" :data-status="ticket.estado.toLowerCase().replace(' ', '-')">{{ ticket.estado }}</span>
                <span class="ticket-date">{{ formatearFecha(ticket.created_at) }}</span>
              </div>
            </div>
            <div class="ticket-actions">
              <button class="icon-btn" @click="cargarEdicion(ticket)" title="Editar" aria-label="Editar">✎</button>
              <button class="icon-btn icon-btn-danger" @click="eliminarTicket(ticket.id)" title="Eliminar" aria-label="Eliminar">🗑</button>
            </div>
          </li>
        </ul>
      </section>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.app-container {
  --bg: #F4F5F2;
  --surface: #FFFFFF;
  --border: #E2E4DF;
  --text: #1A1D1B;
  --text-muted: #667066;
  --accent: #21463D;
  --accent-soft: #E7EDEA;
  --p-alta: #C0392B;
  --p-media: #B8791A;
  --p-baja: #2F7D52;

  max-width: 1180px;
  margin: 0 auto;
  padding: 32px 24px 64px;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  background-color: var(--bg);
  color: var(--text);
  -webkit-font-smoothing: antialiased;
}


.header { margin-bottom: 28px; }
.header h1 { font-size: 1.65rem; font-weight: 700; letter-spacing: -0.01em; margin: 0 0 4px; }
.subtitle { color: var(--text-muted); font-size: 0.95rem; margin: 0; }

.stats-bar {
  display: flex;
  align-items: center;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 18px 24px;
  margin-bottom: 24px;
}
.stat { display: flex; flex-direction: column; gap: 2px; padding-right: 32px; }
.stat-value { font-size: 1.5rem; font-weight: 700; font-variant-numeric: tabular-nums; line-height: 1; }
.stat-label { font-size: 0.8rem; color: var(--text-muted); }
.stat-divider { width: 1px; align-self: stretch; background: var(--border); margin: 0 32px 0 0; }


.main-content { display: grid; grid-template-columns: 340px 1fr; gap: 20px; align-items: start; }
.panel {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 22px;
}
.panel h2 { font-size: 1.05rem; font-weight: 600; margin: 0 0 16px; }

.form-group { margin-bottom: 14px; display: flex; flex-direction: column; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.form-group label { font-size: 0.82rem; font-weight: 500; color: var(--text-muted); margin-bottom: 5px; }
input, select, textarea {
  padding: 9px 11px;
  border: 1px solid var(--border);
  border-radius: 6px;
  font-family: inherit;
  font-size: 0.92rem;
  color: var(--text);
  background: var(--surface);
  transition: border-color 0.15s, box-shadow 0.15s;
}
input:focus, select:focus, textarea:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px var(--accent-soft);
}
textarea { resize: vertical; font-family: inherit; }
.form-actions { display: flex; gap: 8px; margin-top: 6px; }

.btn {
  padding: 9px 18px;
  border: 1px solid transparent;
  border-radius: 6px;
  font-family: inherit;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: background-color 0.15s, border-color 0.15s;
}
.btn-primary { background-color: var(--accent); color: white; }
.btn-primary:hover { background-color: #163029; }
.btn-ghost { background-color: transparent; border-color: var(--border); color: var(--text); }
.btn-ghost:hover { background-color: var(--bg); }

.list-header { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 16px; }
.list-header h2 { margin: 0; }
.list-count { font-size: 0.82rem; color: var(--text-muted); font-variant-numeric: tabular-nums; }

.filters { display: flex; gap: 8px; margin-bottom: 16px; }
.search-input { flex: 1; }
.filters select { min-width: 150px; }

.state-msg {
  text-align: center;
  color: var(--text-muted);
  padding: 32px 12px;
  font-size: 0.92rem;
}
.state-error { color: var(--p-alta); }

.ticket-list { list-style: none; margin: 0; padding: 0; }
.ticket-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
  padding: 16px 4px;
  border-bottom: 1px solid var(--border);
}
.ticket-row:last-child { border-bottom: none; }

.ticket-title { font-weight: 600; font-size: 0.98rem; margin: 0 0 2px; }
.ticket-requester { font-size: 0.82rem; color: var(--text-muted); margin: 0 0 6px; }
.ticket-desc { font-size: 0.88rem; color: var(--text); margin: 0 0 10px; line-height: 1.4; }

.ticket-meta { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }

.priority { display: inline-flex; align-items: center; gap: 6px; font-size: 0.82rem; font-weight: 500; }
.priority-dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
.priority[data-priority="alta"] .priority-dot { background-color: var(--p-alta); }
.priority[data-priority="media"] .priority-dot { background-color: var(--p-media); }
.priority[data-priority="baja"] .priority-dot { background-color: var(--p-baja); }
.priority[data-priority="alta"] { color: var(--p-alta); }
.priority[data-priority="media"] { color: var(--p-media); }
.priority[data-priority="baja"] { color: var(--p-baja); }

.status {
  font-size: 0.78rem;
  font-weight: 600;
  padding: 3px 9px;
  border-radius: 20px;
}
.status[data-status="abierto"] { background: #E4ECF8; color: #24437B; }
.status[data-status="en-proceso"] { background: #FBEFDA; color: #8A5B12; }
.status[data-status="cerrado"] { background: #E7EAE7; color: #4B554E; }

.ticket-date { font-size: 0.78rem; color: var(--text-muted); font-variant-numeric: tabular-nums; }

.ticket-actions { display: flex; gap: 4px; flex-shrink: 0; }
.icon-btn {
  width: 30px; height: 30px;
  display: flex; align-items: center; justify-content: center;
  background: transparent; border: 1px solid var(--border); border-radius: 6px;
  cursor: pointer; font-size: 0.9rem; color: var(--text-muted);
  transition: background-color 0.15s, color 0.15s, border-color 0.15s;
}
.icon-btn:hover { background: var(--bg); color: var(--text); }
.icon-btn-danger:hover { background: #FBEAE8; color: var(--p-alta); border-color: #F0C9C4; }

@media (max-width: 780px) {
  .main-content { grid-template-columns: 1fr; }
  .stats-bar { flex-wrap: wrap; row-gap: 12px; }
  .stat { padding-right: 20px; }
  .stat-divider { margin-right: 20px; }
  .form-row { grid-template-columns: 1fr; }
}
</style>
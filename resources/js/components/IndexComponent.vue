<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="mt-5 col-md-12 text-center">
                <h1><span class="app-title">DotaPro</span></h1>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h3 class="mb-1 text-center">Personas en la empresa:</h3>
                <div v-if="isProcessing" class="mt-5 text-center">
                    <b-spinner size="sm"></b-spinner>
                </div>
                <table v-if="dataPersonas.length > 0" class="mt-5 w-100">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Dispositivos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(persona, index) in dataPersonas" :key="index">
                            <td>{{persona.nombre}}</td>
                            <td>{{persona.correo}}</td>
                            <td class="align-center">
                                <div class="d-inline-block">
                                    <b-button @click="showDispositivosModal(persona)" title="Dispositivos asignados" class="btn p-1" :class=" persona.dispositivos !== undefined ? (persona.dispositivos.length == 0 ? 'btn-secondary' : 'btn-success') : 'btn-secondary'">{{ persona.dispositivos !== undefined ? persona.dispositivos.length : 0}}</b-button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-else>
                    <p>{{mensajePersonas}}</p>
                </div>
                <!-- -->
                <b-modal modal-class="dispositivosModal" size="lg" v-model="showModal" centered hide-footer hide-header no-close-on-backdrop>
                    <div v-if="isModalProcessing || !showModal" class="text-center">
                        <b-spinner size="sm"></b-spinner>
                    </div>
                    <div v-else class="d-block">
                        <div class="col-md-12 mb-3">
                            <h4 class="text-center">Asignación de dispositivos a <span class="app-title">{{personaModal.nombre}}</span></h4>
                        </div>
                        <!--  -->
                        <b-col :cols="12">
                            <b-row>
                                <b-col>
                                    <multiselect 
                                        v-model="dispositivoModal" 
                                        :options="dispositivosInventario" 
                                        :custom-label="dispositivoYTipo" 
                                        placeholder="Buscar dispositivos" 
                                        label="nombre" 
                                        track-by="id"
                                        noResult="No se encontraron elementos. Considere cambiar la consulta de búsqueda"
                                        noOptions="La lista de dispositivos esta vacía"
                                        selectLabel="Presione para seleccionar"
                                        deselectLabel="Presione para remover"
                                    ></multiselect>
                                </b-col>
                                <b-col :cols="2">
                                    <b-button :disabled="!dispositivoModal" variant="warning" class="w-100 bt-asignar" @click="asignarDispositivo">Asignar</b-button>
                                </b-col>
                            </b-row>
                        </b-col>
                        <b-col :cols="12" v-if="isSearchProcessing" class="text-center mt-3">
                            <b-spinner size="sm"></b-spinner>
                        </b-col>
                        
                        <b-col v-if="!dispositivoModalValidoAsignar && dispositivoModalValidoAsignar !== null" :cols="12">
                            <p class="text-danger">{{mensajeBusqueda}}</p>
                        </b-col>
                        <div class="mt-5 col-md-12">
                            <div class="row justify-content-end">
                                <div style="text-align: end;" class="col-md-2">
                                    <button class="btn btn-secondary" @click="showModal = false">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </b-modal>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                dataPersonas: [],
                isProcessing: true,
                mensajePersonas: '',
                //
                // Modal dispositivos
                showModal: false,
                personaModal: null,
                dispositivoModal: null,
                dispositivoModalValidoAsignar: null,
                mensajeBusqueda: '',
                dispositivosInventario: [],
                dispositivosPersona: [],
                isModalProcessing: false,
                isSearchProcessing: false,
            }
        },
        mounted() {
            this.getPersonas();
        },
        methods: {
            getPersonas() {
                let me = this;
                me.isProcessing = true;
                //
                const headers = {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
                let url = '/api/v1/personas/';
                axios.get(url, {}, {
                    headers: headers
                })
                .then(function (response) {
                    if (response.data.length > 0) {
                        me.dataPersonas = response.data;
                        me.isProcessing = false;
                    } else {
                        me.mensajePersonas = 'No hay personas en la base de datos.';
                        me.isProcessing = false;
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            showDispositivosModal(persona) {
                this.dispositivosInventario = [];
                this.dispositivosPersona = [];
                this.dispositivoModal = null;
                this.mensajeBusqueda = '';
                this.dispositivoModalValidoAsignar = null;
                //
                let me = this;
                me.personaModal = persona;
                me.showModal = true;
                me.isModalProcessing = true;
                //
                const headers = {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
                let url = '/api/v1/dispositivos';
                axios.post(url, {
                    personas_id: me.personaModal.id
                }, {
                    headers: headers
                })
                .then(function (response) {
                    console.log(response.data.data);
                    let no_asignados = Object.values(response.data.data.no_asignados);
                    let asignados = Object.values(response.data.data.asignados);
                    if(no_asignados.length > 0) {
                        me.dispositivosInventario = no_asignados;
                    }
                    if (asignados.length > 0) {
                        me.dispositivosPersona = asignados;
                    }
                    me.isModalProcessing = false;    
                })
                .catch((error) => {
                    me.isModalProcessing = false;
                    me.showModal = false;
                    me.$swal.fire({
                        title: 'Hubo un problema recopilando el listado de dispositivos',
                        text: 'Intente nuevamente.',
                        icon: 'error',
                        showCancelButton: false,
                        showDenyButton: false,
                        confirmButtonText: 'Ok',
                    });
                    console.log(error);
                });
            },
            dispositivoYTipo ({ nombre, tipo }) {
                return `${nombre} - (${tipo})`
            },
        },
    }
</script>
<style>
    h1 {
        color: #042475;
    }
    .app-title {
        color: #ffb700;
        font-weight: 600;
    }
    .busqueda-grupo .input-group-text,
    .busqueda-grupo .input-group-append .btn {
        border-radius: 0;
    }
    .busqueda-grupo .input-group-append {
        display: inline-table;
    }
    .busqueda-grupo .input-group-append .btn.btn-outline-warning {
        min-width: 80px;
    }
    .bt-asignar {
        height: 100%;
    }
</style>
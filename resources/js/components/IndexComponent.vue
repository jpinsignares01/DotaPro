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
                    <div v-else class="d-block p-4">
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
                        <b-col class="mt-5" :cols="12">
                            <h5>Dipositivos asignados:</h5>
                            <table v-if="dispositivosPersona.length > 0" class="mt-4 w-100">
                                <thead>
                                    <tr>
                                        <th>Dispositivo</th>
                                        <th>Tipo</th>
                                        <th>Sistema operativo</th>
                                        <th>Asignado</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(dispositivo, index) in dispositivosPersona" :key="index">
                                        <td>{{dispositivo.nombre}}</td>
                                        <td>{{dispositivo.tipo_dispositivo}}</td>
                                        <td>{{dispositivo.sistema_operativo || 'N.A.'}}</td>
                                        <td>{{dispositivo.created_at | date_format}}</td>
                                        <td class="align-middle">
                                            <button class="btn text-danger p-0" @click="desvincularDispositivo(dispositivo.serial)"><b-icon icon="trash"></b-icon></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-else>
                                <h4>No hay dispositivos asignados.</h4>
                            </div>
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
                        // Asignamos data para presentar el modal
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
                // Reinicio de variables
                this.dispositivosInventario = [];
                this.dispositivosPersona = [];
                this.dispositivoModal = null;
                // Asignación de variables
                this.personaModal = persona;
                this.showModal = true;
                this.isModalProcessing = true;
                //
                this.getDispositivosModal(this.personaModal.id);
            },
            asignarDispositivo() {
                let me = this;
                me.isModalProcessing = true;
                //
                const headers = {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
                let url = '/api/v1/dispositivos/asignar';
                axios.post(url, {
                    personas_id: me.personaModal.id,
                    dispositivo: me.dispositivoModal,
                }, {
                    headers: headers
                })
                .then(function (response) {
                    // Se solicita la información de nuevo de personas con dispositivos para conservar la vista actualizada.
                    me.getPersonas();
                    // Agregamos el arreglo del dispositivo al listado de dispositivos de la persona
                    me.dispositivosPersona.push(response.data.data.dispositivo_asignado);
                    // Filtramos el arreglo del dispositivo vinculado para que no se muestre más en lista desplegable
                    me.dispositivosInventario = me.dispositivosInventario.filter(el => el.serial !== response.data.data.dispositivo_asignado.serial);
                })
                .then(function () {
                    me.isModalProcessing = false;
                    me.dispositivoModal = null;
                    //
                    me.$swal.fire({
                        title: 'Asignado',
                        icon: 'success',
                        showCancelButton: false,
                        showDenyButton: false,
                        confirmButtonText: 'Ok',
                    });
                })
                .catch((error) => {
                    me.isModalProcessing = false;
                    me.$swal.fire({
                        title: 'Hubo un problema al asignar el dispositivo',
                        text: error.response.data,
                        icon: 'error',
                        showCancelButton: false,
                        showDenyButton: false,
                        confirmButtonText: 'Ok',
                    });
                });
            },
            desvincularDispositivo(idDispositivo) {
                if (confirm('¿Está seguro de desvincular este dispositivo?')) {
                    let me = this;
                    me.isModalProcessing = true;
                    //
                    const headers = {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                    let url = '/api/v1/dispositivos/desvincular';
                    axios.post(url, {
                        personas_id: me.personaModal.id,
                        dispositivos_id: idDispositivo,
                    }, {
                        headers: headers
                    })
                    .then(function (response) {
                        // Se solicita la información de nuevo de personas con dispositivos para conservar la vista actualizada.
                        me.getPersonas();
                        // Se agrega el arreglo del dispositivo desvinculado a la lista desplegable
                        me.dispositivosInventario.push(me.dispositivosPersona.find(el => el.serial == idDispositivo));
                        // Se elimina el arreglo del dispositivo desvinculado del listado de dispositivos vinculados
                        me.dispositivosPersona = me.dispositivosPersona.filter(el => el.serial !== idDispositivo);
                        //
                        me.isModalProcessing = false;
                        me.dispositivoModal = null;
                        //
                        me.$swal.fire({
                            title: 'Éxito',
                            icon: "Se ha desvinculado el dispositivo.",
                            icon: 'success',
                            showCancelButton: false,
                            showDenyButton: false,
                            confirmButtonText: 'Ok',
                        });
                    })
                    .catch((error) => {
                        me.isModalProcessing = false;
                        me.$swal.fire({
                            title: 'Hubo un problema al desvincular el dispositivo',
                            text: error.response,
                            icon: 'error',
                            showCancelButton: false,
                            showDenyButton: false,
                            confirmButtonText: 'Ok',
                        });
                    });
                }
            },
            getDispositivosModal(personas_id) {
                let me = this;
                const headers = {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
                let url = '/api/v1/dispositivos/'+personas_id;
                axios.get(url, {
                    headers: headers
                })
                .then(function (response) {
                    // Se fuerza la data obtenida a convertirse en arreglos y se asigna a los arrreglos usados en el modal
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
            dispositivoYTipo ({ nombre, tipo_dispositivo }) {
                //Se realiza concatenación para el label de la lista desplegable
                return `${nombre} - (${tipo_dispositivo})`
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
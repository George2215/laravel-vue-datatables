<template>
    <div class="container">
        <div class="mt-5">
            <div class="card-tools">
                <button class="btn btn-success" @click="newModal">Add New
                    <i class="fas fa-user-plus fa-fw"></i>
                </button>
            </div>
            <v-card-title>
              Users Table
                <v-spacer></v-spacer>
                <v-text-field
                    v-model="search"
                    append-icon="search"
                    label="Search"
                    single-line
                    hide-details
                >
                  
                </v-text-field>
            </v-card-title>

            <v-data-table
                :headers="headers"
                :items="users"
                :search="search"
                class="elevation-1"
            >
                <template v-slot:items="props">
                    <td></td>                 
                    <td class="text-xs-left">{{ props.item.id }}</td>
                    <td class="text-xs-left">{{ props.item.name }}</td>
                    <td class="text-xs-left">{{ props.item.email }}</td>
                    <td class="justify-center layout px-0">
                        <v-icon
                        small
                        class="mr-2"
                        color="blue"
                        @click="editItem(props.item)"
                        >
                        edit
                        </v-icon>
                        <v-icon
                        color="red"
                        small
                        @click="deleteItem(props.item)"
                        >
                        delete
                        </v-icon>
                    </td>                    
                </template>
                <template v-slot:no-data>
                    <p class="text-xs-center">Sin registros</p>
                </template>

                <template v-slot:no-results>
                    <v-alert :value="true" color="error" icon="warning">                      
                      Su búsqueda para "{{ search }}" no encontró resultados.
                    </v-alert>
                </template>

            </v-data-table>            
        </div>



        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" v-show="!editmode" id="addNewLabel">Create User</h5>
                <h5 class="modal-title" v-show="editmode" id="addNewLabel">Edit  User's Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>

              <form @submit.prevent="editmode ? updateItem() : createItem()">
              <div class="modal-body">
                
                <div class="form-group">                  
                  <input v-model="form.name" 
                         type="text" 
                         name="name" 
                         placeholder="Name" 
                         class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                  <has-error :form="form" field="name"></has-error>
                </div>

                <div class="form-group">                  
                  <input v-model="form.email" 
                         type="email" 
                         name="email" 
                         placeholder="Email" 
                         class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                  <has-error :form="form" field="email"></has-error>
                </div>                

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
                <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
              </div>
              
              </form>

            </div>
          </div>
        </div>

    </div>
</template>

<script>
    export default {
        data(){
            return{
                dialog: false,
                users:[],
                search: '',
                headers: [
                    {                                            
                        align: 'center',
                        sortable: false,                                        
                    },                    
                    { text: '#', value: 'id' },
                    { text: 'Name', value: 'name' },
                    { text: 'Email', value: 'email' },
                    { text: 'Actions', value: 'name', sortable: false }                
                ],                
                editmode:false,
                form: new Form({
                    id:         '',
                    name:       '',
                    email:      ''                    
                })          
            }
        },        
        methods:{
            getUsers(){                                
                let url ='api/users';
                let me = this;

                axios.get(url).then(function(response){
                    console.log(response.data);                                    
                    me.users = response.data;
                }).catch(function (e) {
                    console.log(e);
                });             
            },
            newModal(){
                this.editmode= false;
                this.form.reset();
                this.form.clear();
                $('#addNew').modal('show');

            },
            createItem(){
                this.form.post('api/users').then(function (response){
                    $('#addNew').modal('hide');
                    Fire.$emit('after-update');
                }).catch(function(e){
                    console.log(e);
                });
            },    
            editItem(item){
                this.editmode= true;
                this.form.reset();
                this.form.clear();
                $('#addNew').modal('show');
                this.form.fill(item);
            },
            updateItem(id){
                //this.$Progress.start();
                this.form.put('api/users/' + this.form.id).then(()=>{
                    $('#addNew').modal('hide');
                    /*swal.fire(
                        'Updated!',
                        'Your information has been updated.',
                        'success'
                    )                   
                    this.$Progress.finish();*/
                    Fire.$emit('after-update');
                }).catch(()=>{
                    //this.$Progress.fail()
                });
                //console.log('editing data');
            },
            deleteItem(id){
              //console.log(id);
              let user= id.id;

              this.form.delete('api/users/' + user).then(function(response){
                Fire.$emit('after-update');
              }).catch(function(e){
                console.log(e);
              })
            }
        },
        created(){
            this.getUsers();
            Fire.$on('after-update', ()=>{
                this.getUsers();
            });
        }
    }
</script>

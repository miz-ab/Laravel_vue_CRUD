<template>
    <div class="container">
        <div class="row justify-content-center">
          <div class="col-12" v-if="this.$gate.is_admin()">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Employee Table</h3>

                <div class="card-tools">
                  <button class="btn btn-success" @click.prevent="open_create_modal">Add New <i class="fas fa-user-plus fa-fw"></i></button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Role</th>
                      <th>Created at</th>
                      <th>Modify</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="user in users.data" :key="user.id">
                      <td>{{user.id}}</td>
                      <td>{{user.name}}</td>
                      <td>{{user.email}}</td>
                      <td>{{user.type | uppercase}}</td> 
                      <td>{{user.created_at | moment("DD MMM YYYY")}}</td>
                      <td>
                          <a href="" @click.prevent="open_edit_user(user)"><i class="fa fa-edit blue"></i></a>
                          &nbsp;&nbsp;&nbsp;
                          <a href="" @click.prevent="delete_user(user.id)"><i class=" fas fa-trash-alt red"></i></a>
                      </td>
                    </tr>
                    
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="users" @pagination-change-page="getResults"></pagination>
              </div>
              <!-- card footer -->
            </div>
            <!-- /.card -->
          </div>

          <!-- template for page not found -->
          <div v-if="!this.$gate.is_admin()">
              <not-found></not-found>
          </div>
        </div>

 

<!-- Modal -->
<div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background:#227DC7;">
        <h5 class="modal-title" id="exampleModalLongTitle" style="color:white;">{{edit_mood ? 'Edit User Info': 'Create New User'}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    
          <div class="card card-primary">
              <!-- /.card-header -->
              <!-- form start -->
              <form @submit.prevent="edit_mood ? update_user() : createUser()" role="form" autocomplete="off">
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Name</label>
                    <input v-model="form.name" type="text" name="name"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Email</label>
                    <input v-model="form.email" type="email" name="email"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                            <has-error :form="form" field="email"></has-error>
                  </div>

                  <div class="form-group">
                      <label for="exampleInputEmail1">Type</label>
                      <select name="type" v-model="form.type" id="type" class="form-control" 
                                            :class="{ 'is-invalid': form.errors.has('type') }">
                        <option value="">Select User Role</option>
                        <option value="admin">Admin</option>
                        <option value="standard">Standard</option>
                        <option value="author">Author</option>
                       </select>
                       <has-error :form="form" field="type"></has-error>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputEmail1">Password</label>
                    <small>{{ edit_mood? '[ leave empty if you don\'t want change ]' :'' }}</small>
                    <input v-model="form.password" type="password" name="password"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                            <has-error :form="form" field="password"></has-error>
                  </div>
                  
                  
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <div class="row">
                    <div class="col-md-4">
                      <button type="button" v-on:click="close_modal" class="btn btn-danger btn-block">Close</button>
                    </div><!-- close modal -->
                    <div class="col-md-8">
                      <button type="submit" class="btn btn-primary btn-block">
                        {{edit_mood ? 'Edit':'Create User'}}</button>
                    </div><!-- save -->
                  </div><!-- end of row -->
                  
                </div>
              </form>
            </div>
            <!-- /.card -->
      </div> <!-- end of modal body -->
    </div> <!-- modal content -->
  </div>
</div>

    </div> <!--end of container-->
</template>

<script>

     import { Form, HasError, AlertError } from 'vform'
     Vue.use(require('vue-moment'));
     import Gate from '../gate';
     //prototype the gate class
     Vue.prototype.$gate = new Gate(window.user);

    export default {


        data(){
            //edit_mood = false
            return{
              is_editing: false,
              edit_mood: false,
              use_id_update:'',
              users:{},
                form: new Form({
                    name: '',
                    email: '',
                    type: '',
                    password: '',
                    photo: ''
                })
            }
        },
        methods:{
          //for pagination
          getResults(page = 1){
              axios.get('api/user?page=' + page)
				          .then(response => {
					          this.users = response.data;
				});
          },
          loaduser(){
            if(this.$gate.is_admin()){
              axios.get('api/user').then(({data}) => (this.users = data));
            }

            },
            createUser(){
                
                this.form.post('api/user')
                .then(() => {
                  //add custom event
                  Fire.$emit('update_user_table');
                  Toast.fire({
                      icon: 'success',
                      title: 'User Created successfully'
                    })
                }).catch(() => {
                    //do some stuff
                });
                
               console.log('craerte user');
                
            },
            open_edit_user(user){
              
              this.edit_mood = true;
              this.form.fill(user);
              this.use_id_update = user.id;
              $('#addnew').modal('show');
              //console.log('edit user');
            },
            open_create_modal(e){
              
              this.edit_mood = false;
              this.form.reset();
              $('#addnew').modal('show');
              //console.log('create user');
            },
            close_modal(e){
              $('#addnew').modal('hide');
            },
            update_user(){
              this.is_editing = true;
              console.log('user id updated ' + this.use_id_update);
              this.$Progress.start();
              this.form.put('api/user/'+this.use_id_update)
              //axios.put('api/user/'+this.use_id_update)
              .then((res) => {
                this.is_editing = false;
                  Toast.fire({
                      icon: 'success',
                      title: 'User Data Updated successfully'
                    })
                this.$Progress.finish();
                $('#addnew').modal('hide');
              }).catch((err) =>{
                this.is_editing = false;
                this.$Progress.fail();
              });

              
            },
            delete_user(user_id){
              Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                  }).then((result) => {
                    //if conf is yes
                    axios.delete('api/user/'+user_id)
                    .then((res) => {
                      Fire.$emit('update_user_table');
                    }).catch((err) => {

                    });
                    if (result.value) {
                      Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                      )
                    }
                  })
            }
            
        },// end of methods
        created() {
            //listen for our custom event that created on paret app.js 
            Fire.$on('search', () => {
              let q = this.$parent.search;
              axios.get('api/findUser?q=' + q)
              .then((res) => {
                console.log(res);
                if(res)
                  this.users = res.data;
              })
              .catch((err) => {

              })
            })
            this.$Progress.start();
            this.loaduser();
            Fire.$on('update_user_table', () => {this.loaduser()});
            this.$Progress.finish();
        }
    }
</script>

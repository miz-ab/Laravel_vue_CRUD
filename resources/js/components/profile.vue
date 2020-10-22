<template>
    <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-10">
            <div class="card card-widget widget-user">
              <!-- Add the bg color to the header using any of the bg-* classes -->
              <div class="widget-user-header text-white"
                   style="background: url('./img/cover_one.png') center center;">
                <h3 class="widget-user-username text-right">{{ this.form.name }} </h3>
                <h5 class="widget-user-desc text-right">{{ this.form.email }}</h5>
              </div>
              <div class="widget-user-image">
                <img class="img-circle"  :src="get_profile_pic()" alt="User Avatar">
              </div>
              <div class="card-footer">
                <div class="row">
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header">3,200</h5>
                      <span class="description-text">SALES</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header">13,000</h5>
                      <span class="description-text">FOLLOWERS</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4">
                    <div class="description-block">
                      <h5 class="description-header">35</h5>
                      <span class="description-text">PRODUCTS</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
            </div>
            </div> <!-- end of col -->








        <div class="col-md-10">
            <div class="card">
              <div class="card-header p-2">
               
                  <h5>Setting</h5>
                
              </div><!-- /.card-header -->
              <div class="card-body">
                
                  
                    <form class="form-horizontal" @submit.prevent="update_profile">
                      <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                          <input v-model="form.name" type="text" name="name" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }"
                           id="inputName" placeholder="Name">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                          <input v-model="form.email" type="email" name="email" class="form-control" id="inputEmail" placeholder="Email"
                          :class="{'is-invalid': form.errors.has('email')}">
                          <has-error :form="form" field="email"></has-error>
                        </div>
                      </div>
                     <div class="form-group row">
                      <label for="exampleInputEmail1"  class="col-sm-2 col-form-label">Type</label>
                      <div class="col-sm-10">
                      <select name="type" v-model="form.type" id="type" class="form-control" 
                                            :class="{ 'is-invalid': form.errors.has('type') }">
                        <option value="">Select User Role</option>
                        <option value="admin">Admin</option>
                        <option value="standard">Standard</option>
                        <option value="author">Author</option>
                       </select>
                       </div>
                       <has-error :form="form" field="type"></has-error>
                  </div>
                   <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">Password</label>
                        <div class="col-sm-10">
                          <small>[ leave empty if you don't want change ]</small>
                          <input type="password" v-model="form.password" name="password" class="form-control" id="inputPassword"
                           placeholder="Password" :class="{ 'is-invalid': form.errors.has('password') }">
                           <has-error :form="form" field="password"></has-error>
                        </div>
                      </div>
                      <div class="form-group row">
                    <label for="exampleInputFile" class="col-sm-2 col-form-label">Photo</label>
                    <div class="col-sm-10">
                  
                        <div class="input-group">
                      <div class="custom-file">
                        <input type="file" name="photo" @change="choose_photo" class="custom-file-input" id="exampleInputFile"
                        :class="{ 'is-invalid': form.errors.has('photo') }">
                        <has-error :form="form" field="photo"></has-error>
                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                      </div>
                    </div> <!-- input group -->

                     </div>
                  </div>
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" @click.prevent="update_profile" class="btn btn-success btn-block">{{ isupdating? 'Updating..' : 'Update' }}</button>
                        </div>
                      </div>
                    </form>
                  
                  
                
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
        </div> <!-- end of row -->
    </div> <!-- end of container -->
</template>

<script>

   // Add the following code if you want the name of the file appear on select
           

  import { Form, HasError, AlertError } from 'vform'
  import { mdbFileInput } from 'mdbvue';
  import Swal from 'sweetalert2'
  import $ from 'jquery'
    export default {
      data(){
          
            return{
              isupdating: false,
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
        mounted() {
             $(".custom-file-input").on("change", function() {
               var fileName = $(this).val().split("\\").pop();
               $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
             });
            
        },
        created(){  
          
          axios.get('api/profile').then(({data}) => (this.form.fill(data))).catch(({err}) => (err));
        },
        methods:{
          get_profile_pic(){
            let photo = (this.form.photo.length > 200) ? this.form.photo : "img/profile/"+this.form.photo;
            //return "img/profile/"+ this.form.photo;
             return photo;
          },
          update_profile(){
            this.$Progress.start();
            this.isupdating = true;
            this.form.put('api/profile')
            .then(() => {
              this.$Progress.finish();
              this.isupdating = false;

              /*
              Toast.fire({
                      icon: 'success',
                      title: 'User Profile Updated successfully'
                    })
              */

             Swal.fire(
                'Good job!',
                'User Profile Updated successfully!',
                'success'
              )


            })
            .catch(() => {

              this.$Progress.fail();
              this.isupdating = false;
              Toast.fire({
                      icon: 'error',
                      title: 'Interruption Occurred'
                    })

            })
          },
          choose_photo(e){
           let file = e.target.files[0];
           let reader = new FileReader();
          if(file['size'] < 2111775){
           reader.onloadend = (file) =>{
             //console.log(reader.result);
             this.form.photo = reader.result;
           }
            reader.readAsDataURL(file);
          }else{

            Toast.fire({
                      icon: 'error',
                      title: 'File Size Must be less than 2MB'
                    })

          }


          }
        }
    }
</script>

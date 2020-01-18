<style scoped>
.widget-user-header {
  background-position: center center;
  background-size: cover;
}
</style>
<template>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-widget widget-user mt-3">
          <!-- Add the bg color to the header using any of the bg-* classes -->
          <div
            class="widget-user-header text-white"
            style="background-image:url('./img/cover.jpg')"
          >
            <h3 class="widget-user-username text-right">{{form.name}}</h3>
            <h5 class="widget-user-desc text-right">Web Designer</h5>
          </div>
          <div class="widget-user-image">
            <img class="img-circle" :src="profilepic()" alt="User Avatar" />
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
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header p-2">
            <ul class="nav nav-pills">
              <li class="nav-item">
                <a class="nav-link active" href="#activity" data-toggle="tab">Activity</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#settings" data-toggle="tab">Settings</a>
              </li>
            </ul>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane active" id="activity">My activity</div>

              <div class="tab-pane" id="settings">
                <form class="form-horizontal">
                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                      <input
                        v-model="form.name"
                        type="text"
                        class="form-control"
                        id="inputName"
                        placeholder="Name"
                        name="name"
                        :class="{
                                        'is-invalid': form.errors.has(
                                            'name'
                                        )
                                    }"
                      />
                      <has-error :form="form" field="name"></has-error>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                      <input
                        v-model="form.email"
                        type="email"
                        disabled
                        class="form-control"
                        id="inputEmail"
                        placeholder="Email"
                        name="email"
                        :class="{
                                        'is-invalid': form.errors.has(
                                            'email'
                                        )
                                    }"
                      />
                      <has-error :form="form" field="email"></has-error>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="password" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-10">
                      <input
                        v-model="form.password"
                        type="password"
                        autocomplete="new_password"
                        class="form-control"
                        id="password"
                        placeholder="Password"
                        name="password"
                        :class="{
                                        'is-invalid': form.errors.has(
                                            'password'
                                        )
                                    }"
                      />
                      <has-error :form="form" field="password"></has-error>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputExperience" class="col-sm-2 col-form-label">Experience</label>
                    <div class="col-sm-10">
                      <textarea
                        v-model="form.bio"
                        class="form-control"
                        id="inputExperience"
                        placeholder="Experience"
                        name="bio"
                        :class="{
                                        'is-invalid': form.errors.has(
                                            'bio'
                                        )
                                    }"
                      ></textarea>
                      <has-error :form="form" field="bio"></has-error>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputSkills" class="col-sm-2 col-form-label">Profile Picture</label>
                    <div class="col-sm-10">
                      <input
                        @change="updatephoto"
                        type="file"
                        class="form-control"
                        id="profileimage"
                        name="photo"
                      />
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="offset-sm-2 col-sm-10">
                      <button
                        type="submit"
                        class="btn btn-success"
                        @click.prevent="updateprofile"
                      >Submit</button>
                    </div>
                  </div>
                </form>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.nav-tabs-custom -->
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: new Form({
        id: "",
        name: "",
        email: "",
        password: "",
        type: "",
        bio: "",
        photo: ""
      })
    };
  },
  created() {
    axios.get("api/profile").then(({ data }) => this.form.fill(data));
    events.$on("profile", () => {
      axios.get("api/profile").then(({ data }) => this.form.fill(data));
    });
  },
  methods: {
    profilepic() {
      let picture =
        this.form.photo.length > 200
          ? this.form.photo
          : "img/profile/" + this.form.photo;
      return picture;
    },
    updatephoto(e) {
      let pic = e.target.files[0];
      console.log(pic);
      let reader = new FileReader();

      if (pic["size"] <= 5242880) {
        reader.onloadend = () => {
          this.form.photo = reader.result;
        };
        reader.readAsDataURL(pic);
      } else {
        Swal.fire("Error!", "The file should be less than 5MB", "warning");
        $("#profileimage").val("");
      }
    },
    updateprofile() {
      this.$Progress.start();
      this.form
        .put("api/profile")
        .then(() => {
          events.$emit("profile"), this.$Progress.finish();
          Toast.fire({
            icon: "success",
            title: "Profile Updated Successfully"
          });
        })
        .catch(() => {
          this.$Progress.fail();
        });
    }
  }
};
</script>

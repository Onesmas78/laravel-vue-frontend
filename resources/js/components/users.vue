<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdmin()">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Users</h3>

                        <div class="card-tools">
                            <button class="btn btn-success" @click="openmodal">
                                Add New
                                <i class="fa fa-user-plus fa-fw"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div
                        class="card-body table-responsive p-0"
                        style="height: 300px;"
                    >
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Registered at</th>
                                    <th>Modify</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users.data" :key="user.id">
                                    <td>{{ user.id }}</td>
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.email }}</td>
                                    <td>{{ user.type | upper }}</td>
                                    <td>{{ user.created_at | datey }}</td>
                                    <td>
                                        <a href="#" @click="edituser(user)">
                                            <i class="fa fa-edit blue"></i>
                                        </a>
                                        /
                                        <a
                                            href="#"
                                            @click="deleteuser(user.id)"
                                        >
                                            <i class="fa fa-trash red"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <pagination
                            :data="users"
                            @pagination-change-page="getResults"
                        ></pagination>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>

        <div v-if="!$gate.isAdmin()">
            <not-found></not-found>
        </div>
        <!-- Modal -->
        <div
            class="modal fade"
            id="addnewuser"
            data-backdrop="static"
            tabindex="-1"
            role="dialog"
            aria-labelledby="addnewuserLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5
                            v-show="!editmode"
                            class="modal-title"
                            id="addnewuserLabel"
                        >
                            Add New User
                        </h5>
                        <h5
                            v-show="editmode"
                            class="modal-title"
                            id="addnewuserLabel"
                        >
                            Edit User
                        </h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form
                            @submit.prevent="
                                editmode ? updateuser() : createuser()
                            "
                        >
                            <div class="form-group">
                                <label>Full name</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    name="name"
                                    placeholder="Full Name"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has('name')
                                    }"
                                />
                                <has-error
                                    :form="form"
                                    field="name"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input
                                    v-model="form.email"
                                    :disabled="editmode"
                                    type="email"
                                    name="email"
                                    placeholder="Email Address"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has('email')
                                    }"
                                />
                                <has-error
                                    :form="form"
                                    field="email"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input
                                    v-model="form.password"
                                    type="password"
                                    name="password"
                                    placeholder="Password"
                                    class="form-control"
                                    autocomplete="new_password"
                                    :required="!editmode"
                                    :class="{
                                        'is-invalid': form.errors.has(
                                            'password'
                                        )
                                    }"
                                />
                                <has-error
                                    :form="form"
                                    field="password"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label>Type</label>
                                <select
                                    v-model="form.type"
                                    name="type"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has('type')
                                    }"
                                >
                                    <option value>Select user roles</option>
                                    <option value="admin">Admin</option>
                                    <option value="subscriber"
                                        >Subscriber</option
                                    >
                                </select>
                                <has-error
                                    :form="form"
                                    field="type"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label>Bio</label>
                                <textarea
                                    v-model="form.bio"
                                    type="text"
                                    name="bio"
                                    placeholder="Bio"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has('bio')
                                    }"
                                ></textarea>
                                <has-error :form="form" field="bio"></has-error>
                            </div>

                            <div class="modal-footer">
                                <button
                                    type="button"
                                    class="btn btn-danger"
                                    data-dismiss="modal"
                                >
                                    Close
                                </button>
                                <button
                                    v-show="!editmode"
                                    type="submit"
                                    class="btn btn-success"
                                >
                                    Save
                                </button>
                                <button
                                    v-show="editmode"
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            editmode: false,
            users: {},
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
    methods: {
        getResults(page = 1) {
            axios.get("api/user?page=" + page).then(response => {
                this.users = response.data;
            });
        },
        updateuser() {
            this.$Progress.start();
            this.form
                .put("api/user/" + this.form.id)
                .then(
                    events.$emit("usercreated"),
                    $("#addnewuser").modal("hide"),
                    Toast.fire({
                        icon: "success",
                        title: "User Updated Successfully"
                    })
                )
                .catch(() => {
                    this.$Progress.fail();
                });

            this.$Progress.finish();
        },
        openmodal() {
            this.form.reset();
            $("#addnewuser").modal("show");
            this.editmode = false;
        },
        edituser(user) {
            this.form.reset();
            $("#addnewuser").modal("show");
            this.form.fill(user);
            this.editmode = true;
        },
        createuser() {
            this.$Progress.start();
            this.form
                .post("api/user")
                .then(
                    events.$emit("usercreated"),
                    $("#addnewuser").modal("hide"),
                    Toast.fire({
                        icon: "success",
                        title: "User Created Successfully"
                    })
                )
                .catch();

            this.$Progress.finish();
        },
        loadusers() {
            if (this.$gate.isAdmin) {
                axios.get("api/user").then(({ data }) => (this.users = data));
            }
        },
        deleteuser(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then(result => {
                this.form
                    .delete("api/user/" + id)
                    .then(() => {
                        if (result.value) {
                            Swal.fire(
                                "User Deleted!",
                                "The user has been deleted.",
                                "success"
                            );
                            events.$emit("usercreated");
                        }
                    })
                    .catch(() => {
                        Swal.fire(
                            "Failed!",
                            "There was something wrong",
                            "warning"
                        );
                    });
            });
        }
    },
    created() {
        this.loadusers();
        events.$on("usercreated", () => {
            this.loadusers();
        });
        events.$on("searching", () => {
            let query = this.$parent.search;
            axios
                .get("api/finduser?q=" + query)
                .then(data => {
                    this.users = data.data;
                })
                .catch(() => {
                    return "no results";
                });
        });
    }
};
</script>

<template>
  <div>
    <v-alert v-if="alert.visible" type="error">{{alert.errorMessage}}</v-alert>
    <v-data-table
      :headers="headers"
      :items="users"
      :options.sync="options"
      :server-items-length="totalUsers"
      :loading="loading"
      :footer-props="{itemsPerPageOptions: [10, 20, 50, 100]}"
      @pagination="paginate"
      class="elevation-1">

      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title>Users</v-toolbar-title>
          <v-divider class="mx-4" inset vertical/>
          <v-spacer/>
          <v-dialog v-model="dialogEdit" max-width="500px">
            <v-card>
              <v-card-title>
                <span class="text-h5">Edit User</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12" sm="6" md="12">
                      <v-text-field v-model="editedUser.id" readonly label="ID"></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="12">
                      <v-text-field v-model="editedUser.name" autofocus label="Name"></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer/>
                <v-btn color="blue darken-1" text @click="closeUpdateDialog">Cancel</v-btn>
                <v-btn color="blue darken-1" text @click="updateUser">Save</v-btn>
              </v-card-actions>

            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogCreate" max-width="500px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">New User</v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="text-h5">New User</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12" sm="6" md="12">
                      <v-text-field v-model="createUserRequest.name" autofocus label="Name"></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="12">
                      <v-text-field v-model="createUserRequest.email" label="Email"></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="12">
                      <v-text-field v-model="createUserRequest.password" label="Password"></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer/>
                <v-btn color="blue darken-1" text @click="closeCreate">Cancel</v-btn>
                <v-btn color="blue darken-1" text @click="createUser">Save</v-btn>
              </v-card-actions>

            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
              <v-card-title class="text-h5">Are you sure you want to delete this item?</v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>

      <template v-slot:item.actions="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
        <v-icon small @click="deleteItem(item)">mdi-delete</v-icon>
      </template>

      <template v-slot:no-data>
        <v-btn color="primary" @click="getDataFromApi">Reset</v-btn>
      </template>
    </v-data-table>
  </div>

</template>

<script lang="ts">

import {Component, Vue, Watch} from "vue-property-decorator";
import {DataOptions, DataTableHeader} from "vuetify";

class Alert {
  visible: Boolean = false;
  errorMessage: String | null = null;

  show(error: String) {
    this.errorMessage = error;
    this.visible = true;
    setTimeout(() => {
      this.errorMessage = null;
      this.visible = false;
    }, 3000);
  }
}

class CreateUserRequest {
  name!: String;
  email!: String;
  password!: String;
}

class User {
  email!: String;
  id!: Number;
  name!: String;
}

class SortOrder {
  property!: String;
  direction!: String;
}

class Sort {
  orders!: SortOrder[];
}

class Pageable {
  page!: Number;
  size!: Number;
  sort!: Sort;
}

class PageableResult {
  results!: User[];
  resultCount!: Number;
}


@Component
export default class Users extends Vue {
  alert: Alert = new Alert();
  defaultUser: User = {id: 0, name: '', email: ''};
  createUserRequest: CreateUserRequest = {name: '', email: '', password: ''};
  headers: DataTableHeader[] = [
    {text: 'ID', align: 'start', sortable: false, value: 'id'},
    {text: 'Name', value: 'name'},
    {text: 'Email', value: 'email'},
    {text: 'Actions', value: 'actions', sortable: false},
  ];

  created() {
    let q = this.$route.query;
    console.log('mounted', q);
    this.page = parseInt(q.page || 1);
    this.size = parseInt(q.size || 10);
    this.options = {
      page: this.page,
      itemsPerPage: this.size,
      multiSort: false,
      mustSort: false,
      groupBy: [],
      groupDesc: [],
      sortBy: ['name'],
      sortDesc: []
    };
    this.paginate({page: this.page, itemsPerPage: this.size});
  }

  paginate(e: {page: number, itemsPerPage: number}) {

      console.log('paginate', e);
      this.page = e.page;
      this.size = e.itemsPerPage;
      const q = 'page=' + this.page + '&size=' + this.size;
      const path = `${this.$route.path}?${q}`
      this.getDataFromApi();
      this.$router.push(path)
  }

  @Watch('dialog', {deep: false, immediate: true})
  closeDialog(val: Boolean, oldValue: Boolean): void {
    val || this.closeUpdateDialog();
  }

  @Watch('dialogDelete', {deep: false, immediate: true})
  closeDeleteDialog(val: Boolean, oldValue: Boolean): void {
    val || this.closeDelete()
  }

  @Watch('dialogCreate', {deep: false, immediate: true})
  closeCreateDialog(val: Boolean, oldValue: Boolean): void {
    val || this.closeCreate()
  }

  dialogEdit: Boolean = false;
  dialogDelete: Boolean = false;
  dialogCreate: Boolean = false;
  loading: Boolean = true;
  options!: DataOptions;
  totalUsers: Number = 0;

  users: User[] = [];
  editedIndex: number = -1;
  editedUser: User = {id: 0, name: '', email: ''};

  private page: any = -1;
  private size: any = -1;



  getDataFromApi() {
    this.loading = true
    this.fetchUsers().then(data => {
      let result: PageableResult = data as PageableResult;
      this.users = result.results
      this.totalUsers = result.resultCount
    }).catch((error: {message: String}) => {
      this.alert.show(error.message);
    }).finally(() => {
      this.loading = false;
    });
  }

  fetchUsers(): Promise<PageableResult> {
    return this.$axios.$get('http://localhost/api/users?page=' + this.page + '&size=' + this.size);
  }


  editItem(user: User) {
    this.editedIndex = this.users.indexOf(user)
    this.editedUser = Object.assign({}, user);
    this.dialogEdit = true
  }

  deleteItem(user: User) {
    this.editedIndex = this.users.indexOf(user)
    this.editedUser = Object.assign({}, user)
    this.dialogDelete = true
  }

  deleteItemConfirm() {
    this.users.splice(this.editedIndex, 1)
    this.closeDelete()
  }

  closeUpdateDialog() {
    this.dialogEdit = false
    this.$nextTick(() => {
      this.editedUser = Object.assign({}, this.defaultUser)
      this.editedIndex = -1
    })
  }

  closeDelete() {
    this.dialogDelete = false
    this.$nextTick(() => {
      this.editedUser = Object.assign({}, this.defaultUser)
      this.editedIndex = -1
    })
  }
  closeCreate() {
    this.dialogCreate = false
    this.$nextTick(() => {
      this.createUserRequest = {name: '', email: '', password: ''};
    })
  }

  createUser(): void {
    // todo create user

    this.$axios.$post('http://localhost/api/users', this.createUserRequest)
      .then(data => {console.log(data); this.getDataFromApi();})
      .catch(error => {this.alert.show(error.message)})
      .finally(() => { this.closeCreate(); })

  }

  updateUser() {
    if (this.editedIndex > -1) {
      Object.assign(this.users[this.editedIndex], this.editedUser)
    } else {
      this.users.push(this.editedUser)
    }
    this.closeUpdateDialog()
  }


}
</script>

<style scoped>

</style>

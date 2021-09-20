<template>
  <div>
    <v-dialog v-model="visible" max-width="500px">
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
          <v-btn color="blue darken-1" text @click="closeDialog">Cancel</v-btn>
          <v-btn color="blue darken-1" text @click="createUser">Save</v-btn>
        </v-card-actions>

      </v-card>
    </v-dialog>
  </div>

</template>

<script lang="ts">
import {Component, Vue} from "vue-property-decorator";
import {initialiseStores, UserModule, NotificationModule} from "~/store";
import CreateUserRequest from "~/model/CreateUserRequest";

@Component
export default class CreateUserDialog extends Vue {
  private visible = false;
  private createUserRequest: CreateUserRequest = new CreateUserRequest();

  created() {
    initialiseStores(this.$store);
  }

  public openDialog() {
    this.createUserRequest = new CreateUserRequest();
    this.$nextTick(() => {
      this.visible = true;
    });
  }

  closeDialog() {
    this.visible = false;
    this.$nextTick(() => {
      this.createUserRequest = new CreateUserRequest();
    });
  }

  async createUser() {
    try {
      const createdUser = await UserModule.createUser(this.createUserRequest);
      NotificationModule.info(`User "${createdUser.name}" created.`);
      this.closeDialog();
      await UserModule.getUsers();
    } catch (e) {
      NotificationModule.genericError(e);
    }
  }
}
</script>

<style scoped>

</style>

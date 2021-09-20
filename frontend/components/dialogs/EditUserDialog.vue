<template>
  <v-dialog v-model="visible" max-width="500px">
    <v-card>
      <v-card-title>
        <span class="text-h5">Edit User</span>
      </v-card-title>

      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12" sm="6" md="12">
              <v-text-field v-model="updateUserRequest.id" readonly label="ID"></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" md="12">
              <v-text-field v-model="updateUserRequest.name" autofocus label="Name"></v-text-field>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>

      <v-card-actions>
        <v-spacer/>
        <v-btn color="blue darken-1" text @click="closeDialog">Cancel</v-btn>
        <v-btn color="blue darken-1" text @click="updateUser">Save</v-btn>
      </v-card-actions>

    </v-card>
  </v-dialog>
</template>

<script lang="ts">
import {Component, Model, Vue} from "vue-property-decorator";
import User from "~/model/User";
import UpdateUserRequest from "~/model/UpdateUserRequest";
import {initialiseStores, NotificationModule, UserModule} from "~/store";

@Component
export default class EditUserDialog extends Vue{
  private visible = false;
  private updateUserRequest: UpdateUserRequest = UpdateUserRequest.empty();

  created() {
    initialiseStores(this.$store);
  }

  public openDialog(user: User) {
    this.updateUserRequest = UpdateUserRequest.ofUser(user);
    this.$nextTick(() => {
      this.visible = true;
    });
  }

  private closeDialog() {
    this.visible = false;
    this.$nextTick(() => {
      this.updateUserRequest = UpdateUserRequest.empty();
    })

  }

  private async updateUser() {
    try {
      await UserModule.updateUser(this.updateUserRequest);
      NotificationModule.info("Successful update!")
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

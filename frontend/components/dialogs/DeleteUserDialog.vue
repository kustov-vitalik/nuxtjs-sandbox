<template>
  <v-dialog v-model="visible" max-width="500px">
    <v-card>
      <v-card-title class="text-h5">Are you sure you want to delete the user "{{ user.name }}"?</v-card-title>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue darken-1" text @click="closeDialog">Cancel</v-btn>
        <v-btn color="blue darken-1" text @click="deleteUser">OK</v-btn>
        <v-spacer></v-spacer>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script lang="ts">
import {Component, Vue} from "vue-property-decorator";
import {initialiseStores, NotificationModule, UserModule} from "~/store";
import User from "~/model/User";

@Component
export default class DeleteUserDialog extends Vue {
  private visible = false;
  private user: User = User.emptyUser();

  created() {
    initialiseStores(this.$store);
  }

  public openDialog(user: User) {
    this.user = user;
    this.$nextTick(() => {
      this.visible = true;
    });
  }

  private closeDialog() {
    this.visible = false;
    this.$nextTick(() => {
      this.user = User.emptyUser();
    });
  }

  private async deleteUser() {
    try {
      await UserModule.deleteUser(this.user);
      NotificationModule.info("User deleted!")
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

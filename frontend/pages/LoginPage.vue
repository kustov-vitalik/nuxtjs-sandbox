<template>
    <v-main>
      <v-container fluid fill-height>
        <v-layout align-center justify-center>
          <v-flex xs12 sm8 md4>
            <div v-if="$auth.loggedIn">
              Hey, {{ $auth.user.name }}!
              <br/>
              You can sign out<br/>
              <v-btn x-large block color="success" @click="signOut">Sign Out</v-btn>
            </div>
            <v-form v-if="!$auth.loggedIn" ref="loginForm" v-model="valid" lazy-validation>
              <v-row>
                <v-col cols="12">
                  <v-text-field v-model="email" :rules="[emailRules.required, emailRules.email]" label="E-mail" required/>
                </v-col>
                <v-col cols="12">
                  <v-text-field v-model="password" :rules="[passwordRules.required, passwordRules.min]" type="password" label="Password"/>
                </v-col>
                <v-col class="d-flex" cols="12" sm="6" xsm="12"/>
                <v-spacer/>
                <v-col class="d-flex" cols="12" sm="3" xsm="12" align-end>
                  <v-btn x-large block :disabled="!valid" color="success" @click="signIn">Sign In</v-btn>
                </v-col>
              </v-row>
            </v-form>
          </v-flex>
        </v-layout>
      </v-container>
    </v-main>
</template>

<script lang="ts">
import {Component, Ref, Vue} from "vue-property-decorator";
import {NotificationModule} from "~/store";

@Component
export default class LoginPage extends Vue {

  @Ref('loginForm') readonly loginFormEl!: HTMLFormElement;

  private valid = false;
  private email = '';
  private password = '';
  private emailRules = {
    required: (v: any) => !!v || "Required",
    email: (v: any) => /.+@.+\..+/.test(v) || "E-mail must be valid"
  };
  private passwordRules = {
    required: (value: any) => !!value || "Required.",
    min: (v: any) => (v && v.length >= 3) || "Min 3 characters"
  };

  async signIn() {
    try {
      if (this.loginFormEl.validate() && !this.$auth.loggedIn) {
        await this.$auth.loginWith('laravelJWT', {
          data: {
            email: this.email,
            password: this.password
          }
        });
      }
    } catch (e) {
      NotificationModule.genericError(e);
    }
  }

  async signOut() {
    try {
      if (this.$auth.loggedIn) {
        await this.$auth.logout();
      }
    } catch (e) {
      NotificationModule.genericError(e);
    }
  }

}
</script>

<style scoped>

</style>

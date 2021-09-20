<template>
  <v-app dark>
    <Toaster/>
    <v-navigation-drawer v-model="drawer" :mini-variant="miniVariant" :clipped="clipped" fixed app>
      <v-list>
        <v-list-item v-for="(item, i) in menuItems" :key="i" :to="item.to" router exact>
          <v-list-item-action>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title v-text="item.title" />
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-app-bar :clipped-left="clipped" fixed app>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-btn icon @click.stop="miniVariant = !miniVariant">
        <v-icon>mdi-{{ `chevron-${miniVariant ? 'right' : 'left'}` }}</v-icon>
      </v-btn>
      <v-btn icon @click.stop="clipped = !clipped">
        <v-icon>mdi-application</v-icon>
      </v-btn>
      <v-toolbar-title v-text="title" />
    </v-app-bar>
    <v-main>
      <v-container>
        <Nuxt />
      </v-container>
    </v-main>
    <v-footer absolute app>
      <span>&copy; {{ new Date().getFullYear() }} Vitaly Kustov</span>
    </v-footer>
  </v-app>
</template>

<script lang="ts">

import {Component, Vue} from "vue-property-decorator";

@Component
export default class Default extends Vue {
  miniVariant = false;
  right = true;
  rightDrawer = false;
  title = 'NuxtJS Sandbox';
  clipped = false;
  drawer = false;
  fixed = false;
  menuItems = [
    {
      icon: 'mdi-apps',
      title: 'Welcome',
      to: '/'
    },
    {
      icon: 'mdi-chart-bubble',
      title: 'Users',
      to: '/UsersPage'
    },
    {
      icon: 'mdi-chart-bubble',
      title: 'Sign In/Out',
      to: '/LoginPage'
    }
  ];
}
</script>

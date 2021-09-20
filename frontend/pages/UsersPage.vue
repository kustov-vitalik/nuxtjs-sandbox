<template>
  <UserTable/>
</template>

<script lang="ts">

import {Component, Vue} from "vue-property-decorator";


@Component({
  middleware: 'auth'
})
export default class UsersPage extends Vue {

}
</script>

<style scoped>

</style>

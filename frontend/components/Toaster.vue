<template>

</template>

<script lang="ts">

import {Component, Vue, Watch} from "vue-property-decorator";
import {initialiseStores, NotificationModule} from "~/store";

@Component
export default class Toaster extends Vue {
  get error() {
    return NotificationModule.error;
  }

  get success() {
    return NotificationModule.success;
  }

  @Watch('error', {deep: true, immediate: true})
  onError(error: string | null) {
    if (error == null) {
      return;
    }

    this.$toast.error(error);
    NotificationModule.genericError(null);
  }

  @Watch('success', {immediate: true})
  onSuccess(message: string|null) {
    if (message == null) {
      return;
    }

    this.$toast.success(message);
    NotificationModule.info(null);
  }

  created() {
    initialiseStores(this.$store);
  }
}
</script>

<style scoped>
</style>

<template>
  <v-data-table
    :headers="headers"
    :items="userPageableResult.results"
    :options.sync="options"
    :server-items-length="userPageableResult.resultCount"
    :loading="loading"
    :footer-props="{itemsPerPageOptions: [10, 20, 50, 100]}"
    @update:options="updatePagingOptions"
    class="elevation-1">

    <template v-slot:top>
      <v-toolbar flat>
        <v-toolbar-title>Users</v-toolbar-title>
        <v-divider class="mx-4" inset vertical/>
        <v-spacer/>
        <EditUserDialog ref="EditUserDialog"/>
        <CreateUserDialog/>
        <DeleteUserDialog ref="DeleteUserDialog"/>
      </v-toolbar>
    </template>

    <template v-slot:item.actions="{ item }">
      <v-icon small class="mr-2" @click="openEditUserDialog(item)">mdi-pencil</v-icon>
      <v-icon small @click="openDeleteUserDialog(item)">mdi-delete</v-icon>
    </template>
  </v-data-table>
</template>

<script lang="ts">
import {Component, Ref, Vue, Watch} from "vue-property-decorator";
import User from "~/model/User";
import {DataOptions, DataTableHeader} from "vuetify";
import EditUserDialog from "~/components/dialogs/EditUserDialog.vue";
import CreateUserDialog from "~/components/dialogs/CreateUserDialog.vue";
import DeleteUserDialog from "~/components/dialogs/DeleteUserDialog.vue";
import {initialiseStores, NotificationModule, UserModule} from "~/store";
import Pageable, {Direction, Sort, SortOrder} from "~/model/Pagination";
import UsersModule from "~/store/UsersModule";

@Component({
  components: {DeleteUserDialog, CreateUserDialog, EditUserDialog},
})
export default class UserTable extends Vue {

  @Ref('EditUserDialog') readonly editUserDialog!: EditUserDialog;
  @Ref('DeleteUserDialog') readonly deleteUserDialog!: DeleteUserDialog;

  private get loading() {
    return UserModule.loading;
  }

  get userPageableResult() {
    return UserModule.userPageableResult;
  }

  options = {};

  private headers: DataTableHeader[] = [
    {text: 'ID', value: 'id', sortable: true, align: 'start'},
    {text: 'Name', value: 'name', sortable: true},
    {text: 'Email', value: 'email', sortable: true},
    {text: 'Actions', value: 'actions', sortable: false},
  ];

  private openEditUserDialog(user: User): void {
    this.editUserDialog.openDialog(user);
  }

  private openDeleteUserDialog(user: User): void {
    this.deleteUserDialog.openDialog(user);
  }

  private async updatePagingOptions(e: {
    page: number,
    itemsPerPage: number,
    sortBy: string[],
    sortDesc: boolean[],
    groupBy: string[],
    groupDesc: boolean[],
    multiSort: boolean,
    mustSort: boolean
  }) {
    let orders = e.sortBy.map((fieldName: string, idx: number) => {
      return new SortOrder(fieldName, e.sortDesc[idx] ? Direction.DESC : Direction.ASC);
    });
    let pageable = new Pageable(e.page, e.itemsPerPage, new Sort(orders));

    await this.doFetchUsers(pageable);
  }

  private async doFetchUsers(pageable: Pageable) {
    try {
      await UserModule.setPageable(pageable);
      await this.$router.push({
        path: this.$route.path,
        query: {
          page: pageable.page.toString(),
          size: pageable.size.toString(),
          sort: pageable.sort.toString()
        }
      });
      await UserModule.getUsers();
    } catch (e) {
      NotificationModule.genericError(e);
    }
  }

  @Watch('$route.query', {immediate: true, deep: true})
  onQueryChanged(newQuery: any, oldQuery: any) {

    if (oldQuery === undefined) {
      return;
    }

    if ((oldQuery.page && newQuery.page === undefined)
      || (oldQuery.size && newQuery.size === undefined)
      || (oldQuery.sort && newQuery.sort === undefined)) {
      this.restartPagination();
    }
  }

  created(): void {
    initialiseStores(this.$store);
    this.restartPagination();
  }

  private restartPagination(): void {
    let pageable = UsersModule.initialPageable();
    const pagingOptions: DataOptions = {
      page: pageable.page,
      itemsPerPage: pageable.size,
      multiSort: true,
      mustSort: false,
      groupBy: [],
      groupDesc: [],
      sortBy: [],
      sortDesc: []
    };

    pageable.sort.orders.forEach((value: SortOrder) => {
      pagingOptions.sortBy.push(value.property);
      pagingOptions.sortDesc.push(value.direction === Direction.DESC);
    });

    this.options = pagingOptions;
  }

}
</script>

<style scoped>
</style>

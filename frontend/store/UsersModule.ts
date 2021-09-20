import {Action, Module, Mutation, VuexModule} from "vuex-module-decorators";
import User from "~/model/User";
import {$axios} from "~/utils/api";
import UpdateUserRequest from "~/model/UpdateUserRequest";
import CreateUserRequest from "~/model/CreateUserRequest";
import Pageable, {Direction, PageableResult, Sort, SortOrder} from "~/model/Pagination";


@Module({
  name: 'UsersModule',
  stateFactory: true,
  namespaced: false
})
export default class UsersModule extends VuexModule {
  private static readonly defaultPage = 1;
  private static readonly defaultSize = 10;
  private static readonly defaultSort = new Sort([new SortOrder('name', Direction.ASC)]);

  private _pageable: Pageable = UsersModule.initialPageable();
  private _usersPageableResult: PageableResult<User> = PageableResult.empty();

  // some read operation in progress
  private _loading: boolean = false;
  // some write operation in progress
  private _committing: boolean = false;

  get committing(): boolean {
    return this._committing;
  }

  get userPageableResult(): PageableResult<User> {
    return this._usersPageableResult;
  }

  get loading(): boolean {
    return this._loading;
  }

  get pageable(): Pageable {
    return this._pageable;
  }

  static initialPageable(): Pageable {
    return Pageable.ofCurrentQueryString(UsersModule.defaultPage, UsersModule.defaultSize, UsersModule.defaultSort);
  }

  @Mutation
  setPageable(pageable: Pageable) {
    this._pageable = pageable;
  }

  @Mutation
  setUsersPageableResult(page: PageableResult<User>) {
    this._usersPageableResult = page;
  }

  @Mutation
  toggleLoading() {
    this._loading = !this._loading;
  }

  @Mutation
  private toggleCommitting() {
    this._committing = !this._committing;
  }

  @Action({rawError: true})
  setLoading(loading: boolean) {
    if (this._loading != loading) {
      this.toggleLoading();
    }
  }

  @Action({rawError: true})
  setCommitting(committing: boolean) {
    if (this._committing != committing) {
      this.toggleCommitting();
    }
  }

  @Action({rawError: true})
  async getUsers(): Promise<void> {
    this.setLoading(true);
    try {
      const page = await $axios.$get('/users?' + this.pageable.toQueryString()) as PageableResult<User>;
      this.setUsersPageableResult(page);
    } finally {
      this.setLoading(false);
    }
  }

  @Action({rawError: true})
  async updateUser(request: UpdateUserRequest): Promise<User> {
    this.setCommitting(true);
    try {
      return await $axios.$put('/users/' + request.id, {name: request.name});
    } finally {
      this.setCommitting(false);
    }
  }

  @Action({rawError: true})
  async deleteUser(user: User): Promise<void> {
    this.setCommitting(true);
    try {
      await $axios.$delete('/users/' + user.id);
    } finally {
      this.setCommitting(false);
    }
  }

  @Action({rawError: true})
  async createUser(request: CreateUserRequest): Promise<User> {
    this.setCommitting(true);
    try {
      return await $axios.$post('/users', request);
    } finally {
      this.setCommitting(false);
    }
  }
}


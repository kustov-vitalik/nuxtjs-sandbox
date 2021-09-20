import { Store } from 'vuex';
import { getModule } from 'vuex-module-decorators';
import UsersModule from '~/store/UsersModule';
import NotificationsModule from "~/store/NotificationsModule";

let UserModule: UsersModule;
let NotificationModule: NotificationsModule;

function initialiseStores(store: Store<any>): void {
  UserModule = getModule(UsersModule, store);
  NotificationModule = getModule(NotificationsModule, store);
}

export { initialiseStores, UserModule, NotificationModule }

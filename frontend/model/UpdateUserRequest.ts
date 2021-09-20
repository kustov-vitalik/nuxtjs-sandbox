import User from "~/model/User";

export default class UpdateUserRequest {
  id: number;
  name: string;

  public static ofUser(user: User): UpdateUserRequest {
    return new UpdateUserRequest(user.id, user.name);
  }

  public static empty(): UpdateUserRequest {
    return new UpdateUserRequest(-1, '');
  }

  constructor(id: number, name: string) {
    this.id = id;
    this.name = name;
  }
}

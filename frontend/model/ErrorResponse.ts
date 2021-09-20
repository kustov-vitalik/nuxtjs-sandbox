export class ErrorResponse {
  private readonly _message!: string;

  constructor(message: string) {
    this._message = message;
  }

  public isValidJsonMessage(): boolean {
    try {
      JSON.parse(this.message);
      return true;
    } catch (e) {
      return false;
    }
  }

  public asObject(): any {
    return JSON.parse(this.message);
  }

  get message(): string {
    return this._message;
  }
}

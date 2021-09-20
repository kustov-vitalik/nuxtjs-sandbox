import {Action, Module, Mutation, VuexModule} from "vuex-module-decorators";
import {AxiosError} from "axios";
import {ErrorResponse} from "~/model/ErrorResponse";

@Module({
  name: 'ErrorsModule',
  stateFactory: true,
  namespaced: false
})
export default class NotificationsModule extends VuexModule {

  private _error: string | null = null;
  private _info: string | null = null;

  @Action({rawError: true})
  public genericError(error: any) {
    if (error == null) {
      this.setError(null);
      return;
    }

    console.error(error);

    if (error.config) {
      let e: AxiosError = error;
      if (e.response) {
        let errorStrings: string[] = [];
        errorStrings.push(`Got an error. Status: ${e?.response?.status}.`);

        let errorResponse = new ErrorResponse(e?.response?.data?.message);
        if (errorResponse.isValidJsonMessage()) {
          let object = errorResponse.asObject();
          for (let key in object) {
            errorStrings.push(`${key}: ${object[key]}`);
          }
          this.setError(errorStrings.join('<br/>'));
        } else {
          errorStrings.push(errorResponse.message);
          this.setError(errorStrings.join('<br/>'));
        }
        return;

      } else if (e.request) {
        // The request was made but no response was received
        // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
        // http.ClientRequest in node.js
        this.setError(JSON.stringify(e.toJSON()));
        return;
      } else {
        // Something happened in setting up the request that triggered an Error
        this.setError(e.message);
        return;
      }
    }

    this.setError(error.message);
  }

  @Action({rawError: true})
  public info(message: string | null) {
    this.setSuccess(message);
  }

  get error(): string | null {
    return this._error;
  }

  get success(): string | null {
    return this._info;
  }

  @Mutation
  private setError(error: string | null) {
    this._error = error;
  }

  @Mutation
  private setSuccess(message: string | null) {
    this._info = message;
  }
}


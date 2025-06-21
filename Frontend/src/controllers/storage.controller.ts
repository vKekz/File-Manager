import { Injectable, Signal } from "@angular/core";
import { ApiResponse } from "../contracts/api.response";
import { Store } from "@ngxs/store";
import { toSignalSync } from "../helpers/to-signal.helper";
import { StorageState } from "../states/storage/storage.state";
import { ResetResponse, SelectStorage } from "../states/storage/storage.actions";

@Injectable({ providedIn: "root" })
export class StorageController {
  public readonly response: Signal<ApiResponse | undefined>;

  constructor(private readonly store: Store) {
    this.response = toSignalSync(this.store.select(StorageState.getResponse));
  }

  public selectStorage(directoryId: string) {
    this.store.dispatch(new SelectStorage(directoryId));
  }

  public reset() {
    this.store.dispatch(new ResetResponse());
  }
}

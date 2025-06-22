import { Injectable, Signal } from "@angular/core";
import { ApiResponse } from "../contracts/api.response";
import { Store } from "@ngxs/store";
import { toSignalSync } from "../helpers/to-signal.helper";
import { StorageState } from "../states/storage/storage.state";
import { ResetResponse, SearchStorage, SelectStorage } from "../states/storage/storage.actions";
import { SearchStorageResponse } from "../contracts/search-storage.response";

@Injectable({ providedIn: "root" })
export class StorageController {
  public readonly searchResult: Signal<SearchStorageResponse | undefined>;
  public readonly searchQuery: Signal<string | undefined>;
  public readonly response: Signal<ApiResponse | undefined>;

  public readonly itemsFound: Signal<number>;

  constructor(private readonly store: Store) {
    this.searchResult = toSignalSync(this.store.select(StorageState.getSearchResults));
    this.searchQuery = toSignalSync(this.store.select(StorageState.getSearchQuery));
    this.response = toSignalSync(this.store.select(StorageState.getResponse));
    this.itemsFound = toSignalSync(this.store.select(StorageState.getElementsFound));
  }

  public selectStorage(directoryId: string) {
    this.store.dispatch(new SelectStorage(directoryId));
  }

  public search(name: string) {
    this.store.dispatch(new SearchStorage(name));
  }

  public reset() {
    this.store.dispatch(new ResetResponse());
  }
}

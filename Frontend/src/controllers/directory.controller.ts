import { Injectable, Signal } from "@angular/core";
import { DirectoryDto } from "../dtos/directory.dto";
import { ApiResponse } from "../contracts/api.response";
import { Store } from "@ngxs/store";
import {
  CreateDirectory,
  DeleteDirectory,
  ResetResponse,
  SelectDirectory,
} from "../states/directory/directory.actions";
import { DirectoryState } from "../states/directory/directory.state";
import { toSignalSync } from "../helpers/to-signal.helper";

@Injectable({ providedIn: "root" })
export class DirectoryController {
  public readonly currentDirectory: Signal<DirectoryDto | undefined>;
  public readonly directories: Signal<DirectoryDto[]>;
  public readonly response: Signal<ApiResponse | undefined>;

  constructor(private readonly store: Store) {
    this.currentDirectory = toSignalSync(this.store.select(DirectoryState.getCurrentDirectory));
    this.directories = toSignalSync(this.store.select(DirectoryState.getDirectories));
    this.response = toSignalSync(this.store.select(DirectoryState.getResponse));
  }

  public createDirectory(name: string) {
    this.store.dispatch(new CreateDirectory(name));
  }

  public deleteDirectory(id: string) {
    this.store.dispatch(new DeleteDirectory(id));
  }

  public selectDirectory(id: string) {
    this.store.dispatch(new SelectDirectory(id));
  }

  public goBack() {
    const current = this.currentDirectory();
    if (!current) {
      return;
    }

    this.selectDirectory(current.parentId);
  }

  public reset() {
    this.store.dispatch(new ResetResponse());
  }
}

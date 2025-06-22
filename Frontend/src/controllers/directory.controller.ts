import { Injectable, Signal } from "@angular/core";
import { DirectoryDto } from "../dtos/directory.dto";
import { Store } from "@ngxs/store";
import { CreateDirectory, DeleteDirectory } from "../states/storage/directory/directory.actions";
import { toSignalSync } from "../helpers/to-signal.helper";
import { StorageState } from "../states/storage/storage.state";
import { StorageController } from "./storage.controller";

@Injectable({ providedIn: "root" })
export class DirectoryController {
  public readonly currentDirectory: Signal<DirectoryDto | undefined>;
  public readonly directories: Signal<DirectoryDto[]>;

  constructor(
    private readonly storageController: StorageController,
    private readonly store: Store
  ) {
    this.currentDirectory = toSignalSync(this.store.select(StorageState.getCurrentDirectory));
    this.directories = toSignalSync(this.store.select(StorageState.getDirectories));
  }

  public createDirectory(name: string) {
    this.store.dispatch(new CreateDirectory(name));
  }

  public deleteDirectory(id: string) {
    this.store.dispatch(new DeleteDirectory(id));
  }

  public goBack() {
    const current = this.currentDirectory();
    if (!current) {
      return;
    }

    this.storageController.selectStorage(current.parentId);
  }
}

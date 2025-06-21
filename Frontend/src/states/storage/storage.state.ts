import { Injectable } from "@angular/core";
import { Action, Selector, State, StateContext, Store } from "@ngxs/store";
import { StorageStateModel } from "./storage-state.model";
import { StorageService } from "../../services/storage.service";
import { DirectoryDtoWithContent } from "../../dtos/directory.dto";
import { FetchStorage, ResetResponse, SelectStorage } from "./storage.actions";
import { CreateDirectory, DeleteDirectory } from "./directory/directory.actions";
import { AuthController } from "../../controllers/auth.controller";
import { calculateCompactFileSize } from "../../helpers/compact-file-size.helper";
import { CreateFile, DeleteFile } from "./file/file.actions";
import { DirectoryStateHandler } from "./directory/directory-state.handler";
import { FileStateHandler } from "./file/file-state.handler";

@State<StorageStateModel>({
  name: "storage",
  defaults: {
    directories: [],
    files: [],
  } as StorageStateModel,
})
@Injectable({ providedIn: "root" })
export class StorageState {
  private readonly storageCache: Map<string, DirectoryDtoWithContent>;

  constructor(
    private readonly storageService: StorageService,
    private readonly fileStateHandler: FileStateHandler,
    private readonly directoryStateHandler: DirectoryStateHandler,
    private readonly authController: AuthController,
    private readonly store: Store
  ) {
    this.storageCache = new Map<string, DirectoryDtoWithContent>();
  }

  @Selector()
  static getCurrentDirectory(state: StorageStateModel) {
    return state.currentDirectory;
  }

  @Selector()
  static getDirectories(state: StorageStateModel) {
    return state.directories;
  }

  @Selector()
  static getFiles(state: StorageStateModel) {
    return state.files;
  }

  @Selector()
  static getResponse(state: StorageStateModel) {
    return state.response;
  }

  @Action(FetchStorage)
  async fetchStorage(context: StateContext<StorageStateModel>, {}: FetchStorage) {
    const parentId = this.getParentId(context.getState());
    if (!parentId) {
      return;
    }

    this.store.dispatch(new SelectStorage(parentId));
  }

  @Action(SelectStorage)
  async selectStorage(context: StateContext<StorageStateModel>, { directoryId }: SelectStorage) {
    let cached = this.storageCache.get(directoryId);
    if (!cached) {
      cached = await this.storageService.getDirectoryWithContent(directoryId);

      // Save cache if it did not exist
      this.storageCache.set(directoryId, cached);
    }

    // Calculate compact file size based on raw byte size
    const files = cached.files;
    for (const file of files) {
      file.compactSize = calculateCompactFileSize(file.size);
    }

    context.patchState({
      currentDirectory: {
        ...cached,
      },

      // Instead of passing the children directly, we need to make a copy to prevent reference errors
      directories: [...cached.children],
      files: [...files],
    });
  }

  @Action(CreateDirectory)
  async createDirectory(context: StateContext<StorageStateModel>, { name }: CreateDirectory) {
    const parentId = this.getParentId(context.getState());
    if (!parentId) {
      return;
    }

    await this.directoryStateHandler.createDirectory(context, name, parentId, this.getCache(parentId));
  }

  @Action(DeleteDirectory)
  async deleteDirectory(context: StateContext<StorageStateModel>, { id }: DeleteDirectory) {
    const parentId = this.getParentId(context.getState());
    if (!parentId) {
      return;
    }

    await this.directoryStateHandler.deleteDirectory(context, id, this.getCache(parentId));
  }

  @Action(CreateFile)
  async uploadFile(context: StateContext<StorageStateModel>, { file }: CreateFile) {
    const parentId = this.getParentId(context.getState());
    if (!parentId) {
      return;
    }

    await this.fileStateHandler.createFile(context, parentId, file, this.getCache(parentId));
  }

  @Action(DeleteFile)
  async deleteFile(context: StateContext<StorageStateModel>, { id }: DeleteFile) {
    const parentId = this.getParentId(context.getState());
    if (!parentId) {
      return;
    }

    await this.fileStateHandler.deleteFile(context, id, this.getCache(parentId));
  }

  @Action(ResetResponse)
  resetResponse(context: StateContext<StorageStateModel>, {}: ResetResponse) {
    context.patchState({
      response: undefined,
    });
  }

  private getCache(directoryId: string) {
    return this.storageCache.get(directoryId);
  }

  private getParentId(state: StorageStateModel) {
    const user = this.authController.getUser();
    if (!user) {
      return null;
    }

    return state.currentDirectory?.id ?? user.id;
  }
}

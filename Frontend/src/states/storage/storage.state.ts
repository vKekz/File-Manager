import { Injectable } from "@angular/core";
import { Action, Selector, State, StateContext } from "@ngxs/store";
import { StorageStateModel } from "./storage-state.model";
import { StorageService } from "../../services/storage.service";
import { DirectoryDtoWithContent } from "../../dtos/directory.dto";
import { FetchStorage, ResetResponse, SearchStorage, SelectStorage } from "./storage.actions";
import { CreateDirectory, DeleteDirectory } from "./directory/directory.actions";
import { AuthController } from "../../controllers/auth.controller";
import { calculateCompactFileSize } from "../../helpers/compact-file-size.helper";
import { CreateFile, DeleteFile } from "./file/file.actions";
import { DirectoryStateHandler } from "./directory/directory-state.handler";
import { FileStateHandler } from "./file/file-state.handler";
import { calculateCompactDate } from "../../helpers/compact-date.helper";

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
    private readonly authController: AuthController
  ) {
    this.storageCache = new Map<string, DirectoryDtoWithContent>();
  }

  @Selector()
  static getCurrentDirectory(state: StorageStateModel) {
    return state.currentDirectory;
  }

  @Selector()
  static getDirectories(state: StorageStateModel) {
    return state.searchResults?.directories ?? state.directories;
  }

  @Selector()
  static getFiles(state: StorageStateModel) {
    return state.searchResults?.files ?? state.files;
  }

  @Selector()
  static getSearchResults(state: StorageStateModel) {
    return state.searchResults;
  }

  @Selector()
  static getSearchQuery(state: StorageStateModel) {
    return state.searchQuery;
  }

  @Selector()
  static getElementsFound(state: StorageStateModel) {
    return (state.searchResults?.files.length ?? 0) + (state.searchResults?.directories.length ?? 0);
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

    context.dispatch(new SelectStorage(parentId));
  }

  @Action(SelectStorage)
  async selectStorage(context: StateContext<StorageStateModel>, { directoryId }: SelectStorage) {
    let cached = this.storageCache.get(directoryId);
    let children = cached?.children ?? [];
    let files = cached?.files ?? [];

    if (!cached) {
      cached = await this.storageService.getDirectoryWithContent(directoryId);

      // Calculate compact dates
      children = cached.children;
      for (const directory of children) {
        directory.compactDate = calculateCompactDate(directory.createdAt);
      }

      // Calculate compact file size based on raw byte size
      files = cached.files;
      for (const file of files) {
        file.compactSize = calculateCompactFileSize(file.size);
        file.compactDate = calculateCompactDate(file.uploadedAt);
      }

      // Save cache if it did not exist
      this.storageCache.set(directoryId, cached);
    }

    context.patchState({
      currentDirectory: {
        ...cached,
      },

      // Instead of passing the children directly, we need to make a copy to prevent reference errors
      directories: [...children],
      files: [...files],
      searchResults: undefined,
      searchQuery: undefined,
    });
  }

  @Action(SearchStorage)
  async searchStorage(context: StateContext<StorageStateModel>, { name }: SearchStorage) {
    if (name.length === 0) {
      context.patchState({
        searchResults: undefined,
        searchQuery: undefined,
      });
      return;
    }

    const parentId = this.getParentId(context.getState());
    if (!parentId) {
      return;
    }

    const response = await this.storageService.search(name, parentId);
    for (const directory of response.directories) {
      directory.compactDate = calculateCompactDate(directory.createdAt);
    }

    for (const file of response.files) {
      file.compactSize = calculateCompactFileSize(file.size);
      file.compactDate = calculateCompactDate(file.uploadedAt);
    }

    context.patchState({
      searchResults: response,
      searchQuery: name,
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

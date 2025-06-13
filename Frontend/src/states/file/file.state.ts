import { Action, Selector, State, StateContext, Store } from "@ngxs/store";
import { Injectable } from "@angular/core";
import { FileStateModel } from "./model/file-state.model";
import { FetchFiles, CreateFile, DeleteFile } from "./file.actions";
import { FileService } from "../../services/file.service";
import { DirectoryState } from "../directory/directory.state";
import { firstValueFrom } from "rxjs";
import { FileDto } from "../../dtos/file.dto";
import { calculateCompactFileSize } from "../../helpers/compact-file-size.helper";

@State<FileStateModel>({
  name: "file",
  defaults: {
    files: [],
  } as FileStateModel,
})
@Injectable({ providedIn: "root" })
export class FileState {
  private readonly fileCache: Map<string, FileDto[]>;

  constructor(
    private readonly fileService: FileService,
    private readonly store: Store
  ) {
    this.fileCache = new Map<string, FileDto[]>();
  }

  @Selector()
  static getFiles(state: FileStateModel) {
    return state.files;
  }

  @Selector()
  static getResponse(state: FileStateModel) {
    return state.response;
  }

  @Action(FetchFiles)
  async fetchFiles(context: StateContext<FileStateModel>, { directoryId }: FetchFiles) {
    let files = this.fileCache.get(directoryId);
    if (!files) {
      files = await this.fileService.getDirectoryFiles(directoryId);
      this.fileCache.set(directoryId, files);
    }

    // Calculate compact file size based on raw byte size
    for (const file of files) {
      file.compactSize = calculateCompactFileSize(file.size);
    }

    context.patchState({
      files: [...files],
    });
  }

  @Action(CreateFile)
  async uploadFile(context: StateContext<FileStateModel>, { file }: CreateFile) {
    const directoryId = (await firstValueFrom(this.store.select(DirectoryState.getCurrentDirectory)))?.id;
    if (!directoryId) {
      return;
    }

    const response = await this.fileService.createFile(directoryId, file);
    if ("message" in response) {
      context.patchState({
        response: response,
      });
      return;
    }

    // Calculate compact size for new file
    response.compactSize = calculateCompactFileSize(response.size);

    // Update cache
    this.fileCache.get(directoryId)?.push(response);

    context.patchState({
      files: [...context.getState().files, response],
    });
  }

  @Action(DeleteFile)
  async deleteFile(context: StateContext<FileStateModel>, { id }: DeleteFile) {
    const response = await this.fileService.deleteFile(id);
    if ("message" in response) {
      context.patchState({
        response: response,
      });
      return;
    }

    const directoryId = (await firstValueFrom(this.store.select(DirectoryState.getCurrentDirectory)))?.id;
    if (!directoryId) {
      return;
    }

    // Update cache
    const cache = this.fileCache.get(directoryId);
    if (!cache) {
      return;
    }

    const cacheIndex = cache.findIndex((file) => file.id === id);
    if (cacheIndex === -1) {
      return;
    }

    cache.splice(cacheIndex, 1);

    // Update real state
    const files = context.getState().files;
    const index = files.findIndex((file) => file.id === id);
    if (index === -1) {
      return;
    }

    files.splice(index, 1);

    context.patchState({
      files: [...files],
    });
  }
}

import { Action, Selector, State, StateContext, Store } from "@ngxs/store";
import { Injectable } from "@angular/core";
import { FileStateModel } from "./model/file-state.model";
import { FetchFiles, UploadFile } from "./file.actions";
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

  @Action(UploadFile)
  async uploadFile(context: StateContext<FileStateModel>, { file }: UploadFile) {
    const directoryId = (await firstValueFrom(this.store.select(DirectoryState.getCurrentDirectory)))?.id;
    if (!directoryId) {
      return;
    }

    const response = await this.fileService.uploadFile(directoryId, file);
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
}

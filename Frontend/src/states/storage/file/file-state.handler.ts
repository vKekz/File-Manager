import { StateContext } from "@ngxs/store";
import { StorageStateModel } from "../storage-state.model";
import { FileService } from "../../../services/file.service";
import { DirectoryDtoWithContent } from "../../../dtos/directory.dto";
import { calculateCompactFileSize } from "../../../helpers/compact-file-size.helper";
import { FileDto } from "../../../dtos/file.dto";
import { Injectable } from "@angular/core";
import { calculateCompactDate } from "../../../helpers/compact-date.helper";

@Injectable({ providedIn: "root" })
export class FileStateHandler {
  constructor(private readonly fileService: FileService) {}

  public async createFile(
    context: StateContext<StorageStateModel>,
    directoryId: string,
    file: File,
    cache?: DirectoryDtoWithContent
  ) {
    const response = await this.fileService.createFile(directoryId, file);
    if ("message" in response) {
      context.patchState({
        response: response,
      });
      return;
    }

    const files = context.getState().files;
    const existingFile = files.find((file) => file.id === response.id);
    if (existingFile) {
      this.replaceFile(existingFile, response, cache);

      context.patchState({
        files: [...files],
      });
      return;
    }

    // Calculate compact details for new file
    response.compactSize = calculateCompactFileSize(response.size);
    response.compactDate = calculateCompactDate(response.uploadedAt);

    // Update cache
    cache?.files.push(response);

    context.patchState({
      files: [...files, response],
    });
  }

  public async deleteFile(context: StateContext<StorageStateModel>, id: string, cache?: DirectoryDtoWithContent) {
    const response = await this.fileService.deleteFile(id);
    if ("message" in response) {
      context.patchState({
        response: response,
      });
      return;
    }

    const cacheIndex = cache?.files.findIndex((file) => file.id === response.id);
    if (cacheIndex === undefined || cacheIndex === -1) {
      return;
    }

    cache?.files.splice(cacheIndex, 1);

    // Update real state
    const files = context.getState().files;
    const index = files.findIndex((file) => file.id === response.id);
    if (index === -1) {
      return;
    }

    files.splice(index, 1);

    context.patchState({
      files: [...files],
    });
  }

  private replaceFile(existingFile: FileDto, newFile: FileDto, cache?: DirectoryDtoWithContent) {
    existingFile.hash = newFile.hash;
    existingFile.size = newFile.size;
    existingFile.compactSize = calculateCompactFileSize(existingFile.size);
    existingFile.uploadedAt = newFile.uploadedAt;
    existingFile.compactDate = calculateCompactDate(newFile.uploadedAt);

    const cachedFile = cache?.files?.find((file) => file.id === newFile.id);
    if (cachedFile) {
      cachedFile.hash = newFile.hash;
      cachedFile.size = newFile.size;
      cachedFile.compactSize = calculateCompactFileSize(cachedFile.size);
      cachedFile.uploadedAt = newFile.uploadedAt;
      cachedFile.compactDate = calculateCompactDate(newFile.uploadedAt);
      return;
    }

    cache?.files?.push(existingFile);
  }
}

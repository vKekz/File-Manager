import { StateContext } from "@ngxs/store";
import { StorageStateModel } from "../storage-state.model";
import { DirectoryService } from "../../../services/directory.service";
import { DirectoryDtoWithContent } from "../../../dtos/directory.dto";
import { Injectable } from "@angular/core";
import { calculateCompactDate } from "../../../helpers/compact-date.helper";

@Injectable({ providedIn: "root" })
export class DirectoryStateHandler {
  constructor(private readonly directoryService: DirectoryService) {}

  public async createDirectory(
    context: StateContext<StorageStateModel>,
    name: string,
    parentId: string,
    cache?: DirectoryDtoWithContent
  ) {
    const response = await this.directoryService.createDirectory(name, parentId);
    if ("message" in response) {
      context.patchState({
        response: response,
      });
      return;
    }

    // Update compact details
    response.compactDate = calculateCompactDate(response.createdAt);

    // Update cache
    cache?.children.push(response);

    const directories = context.getState().directories;
    context.patchState({
      directories: [...directories, response],
      response: undefined,
    });
  }

  public async deleteDirectory(context: StateContext<StorageStateModel>, id: string, cache?: DirectoryDtoWithContent) {
    const response = await this.directoryService.deleteDirectory(id);
    if ("message" in response) {
      context.patchState({
        response: response,
      });
      return;
    }

    const cacheIndex = cache?.children.findIndex((directory) => directory.id === id);
    if (cacheIndex === undefined || cacheIndex === -1) {
      return;
    }

    cache?.children.splice(cacheIndex, 1);

    // Update real state
    const directories = context.getState().directories;
    const index = directories.findIndex((directory) => directory.id === id);
    if (index === -1) {
      return;
    }

    directories.splice(index, 1);

    context.patchState({
      directories: [...directories],
    });
  }
}

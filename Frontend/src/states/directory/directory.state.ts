import { Injectable } from "@angular/core";
import { Action, Selector, State, StateContext, Store } from "@ngxs/store";
import { DirectoryStateModel } from "./model/directory-state.model";
import { CreateDirectory, FetchDirectories, ResetResponse, SelectDirectory } from "./directory.actions";
import { DirectoryService } from "../../services/directory.service";
import { DirectoryDtoWithChildren } from "../../dtos/directory.dto";
import { AuthController } from "../../controllers/auth.controller";
import { FetchFiles } from "../file/file.actions";

@State<DirectoryStateModel>({
  name: "directory",
  defaults: {
    directories: [],
  } as DirectoryStateModel,
})
@Injectable({ providedIn: "root" })
export class DirectoryState {
  private readonly directoryCache: Map<string, DirectoryDtoWithChildren>;

  constructor(
    private readonly directoryService: DirectoryService,
    private readonly authController: AuthController,
    private readonly store: Store
  ) {
    this.directoryCache = new Map<string, DirectoryDtoWithChildren>();
  }

  @Selector()
  static getDirectories(state: DirectoryStateModel) {
    return state.directories;
  }

  @Selector()
  static getCurrentDirectory(state: DirectoryStateModel) {
    return state.currentDirectory;
  }

  @Selector()
  static getResponse(state: DirectoryStateModel) {
    return state.response;
  }

  @Action(FetchDirectories)
  fetchDirectories(context: StateContext<DirectoryStateModel>, {}: FetchDirectories) {
    const state = context.getState();
    const parentId = this.getParentId(state);
    if (!parentId) {
      return;
    }

    this.store.dispatch(new SelectDirectory(parentId));
  }

  @Action(SelectDirectory)
  async selectDirectory(context: StateContext<DirectoryStateModel>, { id }: SelectDirectory) {
    // Pre-fetch files of directory
    this.store.dispatch(new FetchFiles(id));

    let directory = this.directoryCache.get(id);
    if (!directory) {
      directory = await this.directoryService.getDirectoryWithChildren(id);
      this.directoryCache.set(id, directory);
    }

    context.setState({
      currentDirectory: {
        ...directory,
      },
      // Instead of passing the children directly, we need to make a copy to prevent reference errors
      directories: [...directory.children],
    });
  }

  @Action(CreateDirectory)
  async createDirectory(context: StateContext<DirectoryStateModel>, { name }: CreateDirectory) {
    const state = context.getState();
    const parentId = this.getParentId(state);
    if (!parentId) {
      return;
    }

    const response = await this.directoryService.createDirectory(name, parentId);
    if ("message" in response) {
      context.patchState({
        response: response,
      });
      return;
    }

    // Update cache
    this.directoryCache.get(parentId)?.children.push(response);

    const directories = state.directories;
    context.patchState({
      directories: [...directories, response],
      response: undefined,
    });
  }

  @Action(ResetResponse)
  resetResponse(context: StateContext<DirectoryStateModel>, {}: ResetResponse) {
    context.patchState({
      response: undefined,
    });
  }

  private getParentId(state: DirectoryStateModel) {
    const user = this.authController.getUser();
    if (!user) {
      return null;
    }

    return state.currentDirectory?.id ?? user.id;
  }
}

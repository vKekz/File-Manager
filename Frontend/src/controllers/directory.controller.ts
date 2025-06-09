import { Injectable, signal, WritableSignal } from "@angular/core";
import { DirectoryService } from "../services/directory.service";
import { DirectoryDto, DirectoryDtoWithChildren } from "../dtos/directory.dto";
import { AuthController } from "./auth.controller";
import { ApiResponse } from "../contracts/api.response";

@Injectable({ providedIn: "root" })
export class DirectoryController {
  private readonly directoryCache: Map<string, DirectoryDtoWithChildren> = new Map<string, DirectoryDtoWithChildren>();

  public currentDirectory?: DirectoryDto;
  public readonly directories: WritableSignal<DirectoryDto[]> = signal([]);
  public readonly latestResponse: WritableSignal<ApiResponse | null> = signal(null);

  constructor(
    private readonly directoryService: DirectoryService,
    private readonly authController: AuthController
  ) {}

  public fetchDirectories() {
    const parentId = this.getParentId();
    if (!parentId) {
      return;
    }

    this.selectDirectory(parentId);
  }

  public async createDirectory(name: string) {
    const parentId = this.getParentId();
    if (!parentId) {
      return;
    }

    const response = await this.directoryService.createDirectory(name, parentId);
    if ("message" in response) {
      this.latestResponse.set(response);
      return;
    }

    this.directories.update((data) => {
      return [...data, response];
    });
    this.directoryCache.get(parentId)?.children.push(response);
    this.resetResponse();
  }

  public selectDirectory(id: string) {
    const cachedDirectory = this.directoryCache.get(id);
    if (cachedDirectory) {
      this.currentDirectory = {
        ...cachedDirectory,
      };
      this.directories.set(cachedDirectory.children);
      return;
    }

    this.directoryService.getDirectoryByIdWithChildren(id).then((directory) => {
      this.currentDirectory = directory;
      this.directories.set(directory.children);
      this.directoryCache.set(id, directory);
    });
  }

  public goBack() {
    const current = this.currentDirectory;
    if (!current) {
      return;
    }

    this.selectDirectory(current.parentId);
  }

  public resetResponse() {
    this.latestResponse.set(null);
  }

  public getParentId() {
    const user = this.authController.getUser();
    if (!user) {
      return null;
    }

    return this.currentDirectory?.id ?? user.id;
  }
}

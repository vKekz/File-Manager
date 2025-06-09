import { Injectable, signal, WritableSignal } from "@angular/core";
import { DirectoryService } from "../services/directory.service";
import { DirectoryDto } from "../dtos/directory.dto";
import { AuthController } from "./auth.controller";
import { ApiResponse } from "../contracts/api.response";

@Injectable({ providedIn: "root" })
export class DirectoryController {
  public currentDirectory?: DirectoryDto;
  public readonly directories: WritableSignal<DirectoryDto[]> = signal([]);
  public readonly latestResponse: WritableSignal<ApiResponse | null> = signal(null);

  private readonly cachedDirectories: Map<string, DirectoryDto> = new Map<string, DirectoryDto>();

  constructor(
    private readonly directoryService: DirectoryService,
    private readonly authController: AuthController
  ) {
    this.fetchDirectories();
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
    this.latestResponse.set(null);
  }

  public selectDirectory(id: string) {
    this.directoryService.getDirectoryById(id).then((data) => {
      this.currentDirectory = data;
    });

    this.directoryService.getChildrenOfParentDirectory(id).then((data) => {
      this.directories.set(data);
    });
  }

  public getParentId() {
    const user = this.authController.getUser();
    if (!user) {
      return null;
    }

    return this.currentDirectory?.id ?? user.id;
  }

  private fetchDirectories() {
    const parentId = this.getParentId();
    if (!parentId) {
      return;
    }

    this.selectDirectory(parentId);
  }
}

import { Injectable, signal, WritableSignal } from "@angular/core";
import { DirectoryService } from "../services/directory.service";
import { DirectoryDto } from "../dtos/directory.dto";
import { AuthController } from "./auth.controller";

@Injectable({ providedIn: "root" })
export class DirectoryController {
  public currentDirectory?: DirectoryDto;
  public readonly directories: WritableSignal<DirectoryDto[]> = signal([]);

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
    this.directories.update((data) => {
      return [...data, response];
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

    this.directoryService.getChildrenOfParentDirectory(parentId).then((data) => {
      this.directories.set(data);
    });

    this.directoryService.getDirectoryById(parentId).then((data) => {
      this.currentDirectory = data;
    });
  }
}

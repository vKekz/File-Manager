import { Injectable } from "@angular/core";
import { DirectoryService } from "../services/directory.service";
import { DirectoryDto } from "../dtos/directory.dto";

@Injectable({ providedIn: "root" })
export class DirectoryController {
  public currentDirectory?: DirectoryDto;

  constructor(private readonly directoryService: DirectoryService) {
    this.directoryService.getDirectories().then((data) => {
      console.log(data);
    });
  }
}

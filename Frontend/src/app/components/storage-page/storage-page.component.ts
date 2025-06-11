import { Component } from "@angular/core";
import { DirectoryController } from "../../../controllers/directory.controller";
import { FileController } from "../../../controllers/file.controller";
import { DropDownToggleComponent } from "../drop-down-toggle/drop-down-toggle.component";
import { DropDownType } from "../../../enums/drop-down-type.enum";

@Component({
  selector: "app-storage-page",
  imports: [DropDownToggleComponent],
  templateUrl: "./storage-page.component.html",
  styleUrl: "./storage-page.component.css",
})
export class StoragePageComponent {
  constructor(
    protected readonly directoryController: DirectoryController,
    protected readonly fileController: FileController
  ) {}

  protected isRoot() {
    const directory = this.directoryController.currentDirectory();
    return directory !== undefined && !directory.isRoot;
  }

  protected readonly DropDownType = DropDownType;
}

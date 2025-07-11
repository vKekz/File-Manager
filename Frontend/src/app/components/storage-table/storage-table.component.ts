import { Component } from "@angular/core";
import { DropDownToggleComponent } from "../drop-down-toggle/drop-down-toggle.component";
import { FileTypeIconComponent } from "../file-type-icon/file-type-icon.component";
import { DropDownType } from "../../../enums/drop-down-type.enum";
import { DROPDOWN_TOGGLE_ID } from "../../../constants/id.constants";
import { DirectoryController } from "../../../controllers/directory.controller";
import { FileController } from "../../../controllers/file.controller";
import { SettingsService } from "../../../services/settings.service";
import { FileDto } from "../../../dtos/file.dto";
import { StorageController } from "../../../controllers/storage.controller";

@Component({
  selector: "app-storage-table",
  imports: [DropDownToggleComponent, FileTypeIconComponent],
  templateUrl: "./storage-table.component.html",
  styleUrl: "./storage-table.component.css",
})
export class StorageTableComponent {
  constructor(
    protected readonly storageController: StorageController,
    protected readonly directoryController: DirectoryController,
    protected readonly fileController: FileController,
    protected readonly settingsService: SettingsService
  ) {}

  protected highlightMatch(name: string): string {
    const query = this.storageController.searchQuery();
    if (!query) {
      return name;
    }

    const escapedQuery = query.replace(/[-\/\\^$*+?.()|[\]{}]/g, "\\$&");
    const regex = new RegExp(`(${escapedQuery})`, "ig");

    return name.replace(regex, "<mark>$1</mark>");
  }

  protected downloadFile(file: FileDto, anchor: HTMLAnchorElement) {
    return this.fileController.downloadFileViaAnchor(file, anchor);
  }

  protected isRootDirectory() {
    const directory = this.directoryController.currentDirectory();
    return directory !== undefined && !directory.isRoot;
  }

  protected readonly DropDownType = DropDownType;
  protected readonly DROPDOWN_TOGGLE_ID = DROPDOWN_TOGGLE_ID;
}

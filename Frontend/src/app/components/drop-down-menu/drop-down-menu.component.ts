import { Component, Input } from "@angular/core";
import { DropDownType } from "../../../enums/drop-down-type.enum";
import { DROPDOWN_MENU_ID } from "../../../constants/id.constants";
import { FileDto } from "../../../dtos/file.dto";
import { DirectoryDto } from "../../../dtos/directory.dto";
import { DirectoryController } from "../../../controllers/directory.controller";
import { copyTextToClipboard } from "../../../helpers/clipboard.helper";
import { DropDownToggleComponent } from "../drop-down-toggle/drop-down-toggle.component";
import { FileController } from "../../../controllers/file.controller";
import { StorageController } from "../../../controllers/storage.controller";

@Component({
  selector: "app-drop-down-menu",
  imports: [],
  templateUrl: "./drop-down-menu.component.html",
  styleUrl: "./drop-down-menu.component.css",
})
export class DropDownMenuComponent {
  @Input({ required: true })
  public isOpen: boolean = false;

  @Input({ required: true })
  public toggle!: DropDownToggleComponent;

  @Input({ required: true })
  public type: DropDownType = DropDownType.File;

  @Input({ required: true })
  public file?: FileDto;

  @Input({ required: true })
  public directory?: DirectoryDto;

  constructor(
    private readonly storageController: StorageController,
    private readonly directoryController: DirectoryController,
    protected readonly fileController: FileController
  ) {}

  protected selectStorage() {
    if (!this.directory) {
      return;
    }

    this.storageController.selectStorage(this.directory.id);
  }

  protected deleteDirectory() {
    this.directoryController.deleteDirectory(this.directory!.id);
  }

  protected downloadFile(anchor: HTMLAnchorElement) {
    return this.fileController.downloadFileViaAnchor(this.file!, anchor);
  }

  protected async copyHash() {
    await copyTextToClipboard(this.file?.hash);
    this.toggle.toggleMenu();
  }

  protected deleteFile() {
    this.fileController.deleteFile(this.file!.id);
  }

  protected readonly DropDownType = DropDownType;
  protected readonly DROPDOWN_MENU_ID = DROPDOWN_MENU_ID;
}

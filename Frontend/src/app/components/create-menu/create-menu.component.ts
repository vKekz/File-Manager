import { Component, ElementRef, Input, ViewChild } from "@angular/core";
import { DirectoryController } from "../../../controllers/directory.controller";
import { FileController } from "../../../controllers/file.controller";
import { CREATE_MENU_ID } from "../../../constants/id.constants";

@Component({
  selector: "app-create-menu",
  imports: [],
  templateUrl: "./create-menu.component.html",
  styleUrl: "./create-menu.component.css",
})
export class CreateMenuComponent {
  @Input({ required: true })
  public isOpen: boolean = false;

  @ViewChild("nameInput")
  private nameInput?: ElementRef;

  constructor(
    protected readonly directoryController: DirectoryController,
    protected readonly fileController: FileController
  ) {}

  protected async handleCreateDirectory(event: SubmitEvent) {
    event.preventDefault();

    const name = (this.nameInput?.nativeElement as HTMLInputElement).value;
    await this.directoryController.createDirectory(name);
  }

  protected handleFileUpload(event: Event) {
    const input = event.target as HTMLInputElement;
    const files = input.files;
    if (!files) {
      return;
    }

    const file = files[0];
    this.fileController.uploadFile(file);

    input.value = "";
  }

  protected readonly CREATE_MENU_ID = CREATE_MENU_ID;
}

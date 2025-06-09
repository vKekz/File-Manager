import { Component, ElementRef, ViewChild } from "@angular/core";
import { DirectoryController } from "../../../controllers/directory.controller";

@Component({
  selector: "app-create-menu",
  imports: [],
  templateUrl: "./create-menu.component.html",
  styleUrl: "./create-menu.component.css",
})
export class CreateMenuComponent {
  @ViewChild("nameInput")
  private nameInput?: ElementRef;

  constructor(protected readonly directoryController: DirectoryController) {}

  protected async handleCreateDirectory(event: SubmitEvent) {
    event.preventDefault();

    const name = (this.nameInput?.nativeElement as HTMLInputElement).value;
    await this.directoryController.createDirectory(name);
  }
}

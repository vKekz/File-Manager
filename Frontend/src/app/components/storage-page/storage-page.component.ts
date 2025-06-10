import { Component } from "@angular/core";
import { DirectoryController } from "../../../controllers/directory.controller";

@Component({
  selector: "app-storage-page",
  imports: [],
  templateUrl: "./storage-page.component.html",
  styleUrl: "./storage-page.component.css",
})
export class StoragePageComponent {
  constructor(protected readonly directoryController: DirectoryController) {}
}

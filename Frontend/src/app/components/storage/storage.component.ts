import { Component } from "@angular/core";
import { DirectoryController } from "../../../controllers/directory.controller";

@Component({
  selector: "app-storage",
  imports: [],
  templateUrl: "./storage.component.html",
  styleUrl: "./storage.component.css",
})
export class StorageComponent {
  constructor(protected readonly directoryController: DirectoryController) {}
}

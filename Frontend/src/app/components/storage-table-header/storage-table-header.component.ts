import { Component } from "@angular/core";
import { DirectoryController } from "../../../controllers/directory.controller";

@Component({
  selector: "app-storage-table-header",
  imports: [],
  templateUrl: "./storage-table-header.component.html",
  styleUrl: "./storage-table-header.component.css",
})
export class StorageTableHeaderComponent {
  constructor(protected readonly directoryController: DirectoryController) {}
}

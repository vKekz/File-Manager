import { Component } from "@angular/core";
import { DirectoryController } from "../../../controllers/directory.controller";
import { StorageSearchFilterComponent } from "../storage-search-filter/storage-search-filter.component";
import { StorageController } from "../../../controllers/storage.controller";

@Component({
  selector: "app-storage-table-header",
  imports: [StorageSearchFilterComponent],
  templateUrl: "./storage-table-header.component.html",
  styleUrl: "./storage-table-header.component.css",
})
export class StorageTableHeaderComponent {
  constructor(
    protected readonly storageController: StorageController,
    protected readonly directoryController: DirectoryController
  ) {}
}

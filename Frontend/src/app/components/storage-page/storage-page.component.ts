import { Component } from "@angular/core";
import { StorageTableComponent } from "../storage-table/storage-table.component";
import { StorageTableHeaderComponent } from "../storage-table-header/storage-table-header.component";

@Component({
  selector: "app-storage-page",
  imports: [StorageTableComponent, StorageTableHeaderComponent],
  templateUrl: "./storage-page.component.html",
  styleUrl: "./storage-page.component.css",
})
export class StoragePageComponent {}

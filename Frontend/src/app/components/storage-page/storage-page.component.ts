import { Component, OnInit } from "@angular/core";
import { DirectoryController } from "../../../controllers/directory.controller";

@Component({
  selector: "app-storage-page",
  imports: [],
  templateUrl: "./storage-page.component.html",
  styleUrl: "./storage-page.component.css",
})
export class StoragePageComponent implements OnInit {
  constructor(protected readonly directoryController: DirectoryController) {}

  ngOnInit(): void {
    this.directoryController.fetchDirectories();
  }
}

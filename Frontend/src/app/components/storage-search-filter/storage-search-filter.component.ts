import { Component, OnDestroy, OnInit } from "@angular/core";
import { debounceTime, distinctUntilChanged, Subject } from "rxjs";
import { StorageController } from "../../../controllers/storage.controller";

@Component({
  selector: "app-storage-search-filter",
  imports: [],
  templateUrl: "./storage-search-filter.component.html",
  styleUrl: "./storage-search-filter.component.css",
})
export class StorageSearchFilterComponent implements OnInit, OnDestroy {
  private readonly searchSubject: Subject<string> = new Subject<string>();

  constructor(private readonly storageController: StorageController) {}

  ngOnInit(): void {
    this.searchSubject.pipe(debounceTime(200), distinctUntilChanged()).subscribe((value) => {
      this.handleSearch(value);
    });
  }

  ngOnDestroy(): void {
    this.searchSubject.unsubscribe();
  }

  protected handleSearchInput(event: Event) {
    const inputElement = event.target as HTMLInputElement;
    const value = inputElement.value;
    this.searchSubject.next(value);
  }

  private handleSearch(searchedName: string) {
    this.storageController.search(searchedName);
  }
}

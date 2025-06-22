import { Component, ElementRef, OnDestroy, OnInit, ViewChild } from "@angular/core";
import { debounceTime, distinctUntilChanged, Subject } from "rxjs";
import { StorageController } from "../../../controllers/storage.controller";

@Component({
  selector: "app-storage-search-filter",
  imports: [],
  templateUrl: "./storage-search-filter.component.html",
  styleUrl: "./storage-search-filter.component.css",
})
export class StorageSearchFilterComponent implements OnInit, OnDestroy {
  @ViewChild("searchInput")
  private readonly searchInput?: ElementRef;
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

  protected handleSearchInput() {
    const value = (this.searchInput?.nativeElement as HTMLInputElement).value;
    this.searchSubject.next(value);
  }

  protected handleClearSearch() {
    (this.searchInput?.nativeElement as HTMLInputElement).value = "";
    this.searchSubject.next("");
  }

  private handleSearch(searchedName: string) {
    this.storageController.search(searchedName);
  }
}

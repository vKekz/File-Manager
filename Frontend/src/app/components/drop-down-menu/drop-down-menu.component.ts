import { AfterViewInit, Component, ElementRef, Input, OnDestroy, ViewChild } from "@angular/core";
import { DropDownType } from "../../../enums/drop-down-type.enum";
import { fromEvent, Subscription } from "rxjs";
import { DROPDOWN_MENU_ID } from "../../../constants/id.constants";
import { FileDto } from "../../../dtos/file.dto";
import { DirectoryDto } from "../../../dtos/directory.dto";
import { DirectoryController } from "../../../controllers/directory.controller";
import { copyTextToClipboard } from "../../../helpers/clipboard.helper";
import { DropDownToggleComponent } from "../drop-down-toggle/drop-down-toggle.component";

@Component({
  selector: "app-drop-down-menu",
  imports: [],
  templateUrl: "./drop-down-menu.component.html",
  styleUrl: "./drop-down-menu.component.css",
})
export class DropDownMenuComponent implements AfterViewInit, OnDestroy {
  @Input({ required: true })
  public isOpen: boolean = false;

  @Input({ required: true })
  public toggle!: DropDownToggleComponent;

  @Input({ required: true })
  public type: DropDownType = DropDownType.File;

  @Input({ required: true })
  public file?: FileDto;

  @Input({ required: true })
  public directory?: DirectoryDto;

  @ViewChild("menuElement")
  private menuElement?: ElementRef;

  private resizeEvent: Subscription;
  private initialRect?: DOMRect;

  constructor(private readonly directoryController: DirectoryController) {
    this.resizeEvent = fromEvent(window, "resize", () => {
      this.handleResize();
    }).subscribe();
  }

  ngAfterViewInit(): void {
    this.handleInitialPosition();
  }

  ngOnDestroy(): void {
    this.resizeEvent.unsubscribe();
  }

  protected selectDirectory() {
    if (!this.directory) {
      return;
    }

    this.directoryController.selectDirectory(this.directory.id);
  }

  protected async copyHash() {
    await copyTextToClipboard(this.file?.hash);
    this.toggle.toggleMenu();
  }

  private handleInitialPosition() {
    const element = this.menuElement?.nativeElement as HTMLDivElement;
    if (!element) {
      return;
    }

    this.initialRect = element.getBoundingClientRect();
    this.handleResize();
  }

  private handleResize() {
    const element = this.menuElement?.nativeElement as HTMLDivElement;
    if (!element) {
      return;
    }

    const initialRect = this.initialRect;
    if (!initialRect) {
      return;
    }

    const rect = element.getBoundingClientRect();
    if (rect.left < 0) {
      element.style.left = "1rem";
    }

    if (rect.right > window.innerWidth) {
      element.style.right = "1rem";
    }

    if (rect.bottom < 0) {
      element.style.bottom = "1rem";
    }

    if (rect.top > window.innerHeight) {
      element.style.top = "1rem";
    }

    if (rect.left > initialRect.left) {
      element.style.right = "auto";
    }
  }

  protected readonly DropDownType = DropDownType;
  protected readonly DROPDOWN_MENU_ID = DROPDOWN_MENU_ID;
}

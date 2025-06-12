import { Component, Input, OnDestroy } from "@angular/core";
import { DropDownType } from "../../../enums/drop-down-type.enum";
import { DropDownMenuComponent } from "../drop-down-menu/drop-down-menu.component";
import { fromEvent, Subscription } from "rxjs";
import { DROPDOWN_MENU_ID, DROPDOWN_TOGGLE_ID } from "../../../constants/id.constants";
import { FileDto } from "../../../dtos/file.dto";
import { DirectoryDto } from "../../../dtos/directory.dto";

@Component({
  selector: "app-drop-down-toggle",
  imports: [DropDownMenuComponent],
  templateUrl: "./drop-down-toggle.component.html",
  styleUrl: "./drop-down-toggle.component.css",
})
export class DropDownToggleComponent implements OnDestroy {
  @Input({ required: true })
  public type: DropDownType = DropDownType.File;

  @Input()
  public file!: FileDto;

  @Input()
  public directory!: DirectoryDto;

  private readonly mouseEvent: Subscription;
  protected isOpen: boolean = false;

  constructor() {
    this.mouseEvent = fromEvent(window, "mouseup").subscribe((event) => {
      this.handleAutoClose(event);
    });
  }

  ngOnDestroy(): void {
    this.mouseEvent.unsubscribe();
  }

  public toggleMenu() {
    this.isOpen = !this.isOpen;
  }

  private handleAutoClose(event: Event) {
    if (!this.isOpen) {
      return;
    }

    const target = event.target as HTMLElement;
    const parent = target.offsetParent;
    if (parent?.id === DROPDOWN_MENU_ID || parent?.id === DROPDOWN_TOGGLE_ID) {
      return;
    }

    this.toggleMenu();
  }
}

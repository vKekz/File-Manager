import { Component, OnDestroy } from "@angular/core";
import { CreateMenuComponent } from "../create-menu/create-menu.component";
import { NgClass } from "@angular/common";
import { fromEvent, Subscription } from "rxjs";
import { DirectoryController } from "../../../controllers/directory.controller";
import { BOTTOM_NAV_ID, CREATE_MENU_ID } from "../../../constants/id.constants";

@Component({
  selector: "app-create-button",
  imports: [CreateMenuComponent, NgClass],
  templateUrl: "./create-button.component.html",
  styleUrl: "./create-button.component.css",
})
export class CreateButtonComponent implements OnDestroy {
  protected isOpen: boolean = false;

  private readonly mouseEvent: Subscription;
  private readonly keyEvent: Subscription;

  constructor(private readonly directoryController: DirectoryController) {
    this.mouseEvent = fromEvent(window, "mouseup").subscribe((event) => {
      this.handleMouseUp(event);
    });
    this.keyEvent = fromEvent(window, "keyup").subscribe((event) => {
      this.handleKeyUp(event);
    });
  }

  ngOnDestroy(): void {
    this.mouseEvent.unsubscribe();
    this.keyEvent.unsubscribe();
  }

  protected toggleMenu() {
    if (this.isOpen) {
      this.directoryController.reset();
    }

    this.isOpen = !this.isOpen;
  }

  private handleMouseUp(event: Event) {
    if (!this.isOpen) {
      return;
    }

    const target = event.target as HTMLElement;
    const parent = target.offsetParent;
    if (parent?.id === CREATE_MENU_ID || parent?.id === BOTTOM_NAV_ID) {
      return;
    }

    this.toggleMenu();
  }

  private handleKeyUp(event: Event) {
    if (!this.isOpen) {
      return;
    }

    const keyboardEvent = event as KeyboardEvent;
    if (keyboardEvent.key === "Escape") {
      this.toggleMenu();
    }
  }
}

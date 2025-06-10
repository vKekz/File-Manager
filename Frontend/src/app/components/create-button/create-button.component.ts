import { Component, OnDestroy } from "@angular/core";
import { CreateMenuComponent } from "../create-menu/create-menu.component";
import { NgClass } from "@angular/common";
import { fromEvent, Subscription } from "rxjs";
import { DirectoryController } from "../../../controllers/directory.controller";

@Component({
  selector: "app-create-button",
  imports: [CreateMenuComponent, NgClass],
  templateUrl: "./create-button.component.html",
  styleUrl: "./create-button.component.css",
})
export class CreateButtonComponent implements OnDestroy {
  protected isToggled: boolean = false;

  private readonly mouseEvent: Subscription;
  private readonly keyEvent: Subscription;

  constructor(private readonly directoryController: DirectoryController) {
    this.mouseEvent = fromEvent(window, "mousedown").subscribe((event) => {
      this.handleMouseDown(event);
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
    if (this.isToggled) {
      this.directoryController.reset();
    }

    this.isToggled = !this.isToggled;
  }

  private handleMouseDown(event: Event) {
    if (!this.isToggled) {
      return;
    }

    const target = event.target as HTMLElement;
    const parent = target.offsetParent;
    if (parent?.id === "create-menu" || parent?.id === "bottom-nav") {
      return;
    }

    this.toggleMenu();
  }

  private handleKeyUp(event: Event) {
    if (!this.isToggled) {
      return;
    }

    const keyboardEvent = event as KeyboardEvent;
    if (keyboardEvent.key === "Escape") {
      this.toggleMenu();
    }
  }
}

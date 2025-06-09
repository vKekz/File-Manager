import { Component, OnDestroy } from "@angular/core";
import { CreateMenuComponent } from "../create-menu/create-menu.component";
import { NgClass } from "@angular/common";
import { fromEvent, Subscription } from "rxjs";

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

  constructor() {
    this.mouseEvent = fromEvent(window, "mousedown").subscribe((event) => {
      this.handleAutoClose(event);
    });
    this.keyEvent = fromEvent(window, "keyup").subscribe((event) => {
      this.handleClose(event);
    });
  }

  ngOnDestroy(): void {
    this.mouseEvent.unsubscribe();
    this.keyEvent.unsubscribe();
  }

  protected toggleMenu() {
    this.isToggled = !this.isToggled;
  }

  private handleAutoClose(event: Event) {
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

  private handleClose(event: Event) {
    if (!this.isToggled) {
      return;
    }

    const keyboardEvent = event as KeyboardEvent;
    if (keyboardEvent.key === "Escape") {
      this.toggleMenu();
    }
  }
}
